<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

use Gibbon\Domain\System\SettingGateway;
use Gibbon\Module\HelpDesk\Domain\IssueGateway;
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;
use Gibbon\Module\HelpDesk\Domain\TechnicianGateway;

require_once '../../gibbon.php';

require_once './moduleFunctions.php';

$moduleName = $gibbon->session->get('module');

$URL = $gibbon->session->get('absoluteURL') . '/index.php?q=/modules/' . $moduleName;

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/issues_create.php')) {
    $URL .= '/issues_view.php&return=error0';
    header("Location: {$URL}");
    exit();
} else {
    //Proceed!    
    $URL .= '/issues_create.php';

    $gibbonPersonID = $gibbon->session->get('gibbonPersonID');

    $data = array(
        //Default data
        'gibbonPersonID' => $gibbonPersonID,
        'createdByID' => $gibbonPersonID,
        'status' => 'Unassigned',
        'gibbonSchoolYearID' => $gibbonPersonID,
        'date' => date('Y-m-d'),
        //Data to get from Post or getSettingByScope
        'issueName' => '',
        'category' => '',
        'description' => '',
        'priority' => '',
        'privacySetting' => '',
    );

    foreach ($data as $key => $value) {
        if (empty($value) && isset($_POST[$key])) {
            $data[$key] = $_POST[$key];
        }
    }

    $settingGateway = $container->get(SettingGateway::class);

    $priorityOptions = explodeTrim($settingGateway->getSettingByScope($moduleName, 'issuePriority'));
    $categoryOptions = explodeTrim($settingGateway->getSettingByScope($moduleName, 'issueCategory'));

    $techGroupGateway = $container->get(TechGroupGateway::class);

    $createdOnBehalf = false;
    if (isset($_POST['createFor']) && $_POST['createFor'] != 0 && $techGroupGateway->getPermissionValue($gibbonPersonID, 'createIssueForOther')) {
        $data['gibbonPersonID'] = $_POST['createFor'];
        $createdOnBehalf = true;
    }

    if (!in_array($data['privacySetting'], privacyOptions())) {
        $data['privacySetting'] = $settingGateway->getSettingByScope($moduleName, 'resolvedIssuePrivacy');
    }

    if (empty($data['issueName'])
        || empty($data['description']) 
        || (!in_array($data['category'], $categoryOptions) && count($categoryOptions) > 0) 
        || (!in_array($data['priority'], $priorityOptions) && count($priorityOptions) > 0)) {

        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit();
    } else {
        try {
            $gibbonModuleID = getModuleIDFromName($connection2, $moduleName);
            if ($gibbonModuleID == null) {
                throw new PDOException('Invalid gibbonModuleID.');
            }

            $issueGateway = $container->get(IssueGateway::class);
            $issueID = $issueGateway->insert($data);
            if ($issueID === false) {
                throw new PDOException('Could not insert issue.');
            }
        } catch (PDOException $e) {
            $URL .= '&return=error2';
            header("Location: {$URL}");
            exit();
        }

        //Notify issue owner, if created on their behalf
        if ($createdOnBehalf) {
            setNotification($connection2, $guid, $data['gibbonPersonID'], 'A new issue has been created on your behalf (' . $data['issueName'] . ').', 'Help Desk', "/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=$issueID");
        }

        //Notify Techicians
        $technicianGateway = $container->get(TechnicianGateway::class);
        $technicians = $technicianGateway->selectTechnicians();

        while ($row = $technicians->fetch()) {
            $permission = $techGroupGateway->getPermissionValue($row['gibbonPersonID'], 'viewIssueStatus');
            if ($row['gibbonPersonID'] != $gibbon->session->get('gibbonPersonID') && $row['gibbonPersonID'] != $data['gibbonPersonID'] && ($permission == "UP" || $permission == "All")) {
                setNotification($connection2, $guid, $row['gibbonPersonID'], 'A new issue has been added (' . $data['issueName'] . ').', $moduleName, "/index.php?q=/modules/$moduleName/issues_discussView.php&issueID=$issueID");
            }
        }

        //Log
        $array = array('issueID' => $issueID);
        $title = 'Issue Created';
        if ($createdOnBehalf) {
            $array['technicianID'] = $technicianGateway->getTechnicianByPersonID($gibbonPersonID)->fetch()['technicianID'];
            $title = 'Issue Created (for Another Person)';
        }

        setLog($connection2, $gibbon->session->get('gibbonSchoolYearID'), $gibbonModuleID, $gibbonPersonID, $title, $array, null);

        $URL .= "&issueID=$issueID&return=success0";
        header("Location: {$URL}");
        exit();
    }
}
?>
