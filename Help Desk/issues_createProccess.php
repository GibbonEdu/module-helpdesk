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

use Gibbon\Comms\NotificationSender;
use Gibbon\Domain\System\LogGateway;
use Gibbon\Domain\System\NotificationGateway;
use Gibbon\Domain\System\SettingGateway;
use Gibbon\Module\HelpDesk\Domain\IssueGateway;
use Gibbon\Module\HelpDesk\Domain\SubcategoryGateway;
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

    $data = [
        //Default data
        'gibbonPersonID' => $gibbonPersonID,
        'createdByID' => $gibbonPersonID,
        'status' => 'Unassigned',
        'gibbonSchoolYearID' => $gibbon->session->get('gibbonSchoolYearID'),
        'date' => date('Y-m-d'),
        //Data to get from Post or getSettingByScope
        'issueName' => '',
        'category' => '',
        'description' => '',
        'gibbonSpaceID' => null,
        'priority' => '',
        'subcategoryID' => null,
    ];

    foreach ($data as $key => $value) {
        if (empty($value) && isset($_POST[$key])) {
            $data[$key] = $_POST[$key];
        }
    }

    $settingGateway = $container->get(SettingGateway::class);

    $priorityOptions = explodeTrim($settingGateway->getSettingByScope($moduleName, 'issuePriority'));
    $categoryOptions = explodeTrim($settingGateway->getSettingByScope($moduleName, 'issueCategory'));
    $simpleCategories = ($settingGateway->getSettingByScope($moduleName, 'simpleCategories') == '1');

    $techGroupGateway = $container->get(TechGroupGateway::class);

    $createdOnBehalf = false;
    if (isset($_POST['createFor']) && $_POST['createFor'] != 0 && $techGroupGateway->getPermissionValue($gibbonPersonID, 'createIssueForOther')) {
        $data['gibbonPersonID'] = $_POST['createFor'];
        $createdOnBehalf = true;
    }

    $subcategoryGateway = $container->get(SubcategoryGateway::class);
    
    if (empty($data['issueName'])
        || empty($data['description']) 
        || (!in_array($data['category'], $categoryOptions) && count($categoryOptions) > 0 && $simpleCategories)
        || (!$subcategoryGateway->exists($data['subcategoryID']) && !$simpleCategories)
        || (!in_array($data['priority'], $priorityOptions) && count($priorityOptions) > 0)) {

        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit();
    } else {
        $issueGateway = $container->get(IssueGateway::class);
        $issueID = $issueGateway->insert($data);
        if ($issueID === false) {
            $URL .= '&return=error2';
            header("Location: {$URL}");
            exit();
        }

        //Send Notification
        $notificationGateway = $container->get(NotificationGateway::class);
        $notificationSender = new NotificationSender($notificationGateway, $gibbon->session); 

        //Notify issue owner, if created on their behalf
        if ($createdOnBehalf) {
            $message = __('A new issue has been created on your behalf, Issue #') . $issueID . '(' . $data['issueName'] . ').';
            $notificationSender->addNotification($data['gibbonPersonID'], $message, 'Help Desk', $absoluteURL . '/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=' . $issueID);
        }

        //Notify Techicians
        $technicianGateway = $container->get(TechnicianGateway::class);

        if ($simpleCategories) {
            $techs = $technicianGateway->selectTechnicians()->fetchAll();
        } else {
            $departmentID = $subcategoryGateway->getByID($data['subcategoryID'])['departmentID'];
            $techs = $technicianGateway->selectTechniciansByDepartment($departmentID)->fetchAll();
        }

        $techs = array_column($techs, 'gibbonPersonID');

        $message = __('A new issue has been added') . ' (' . $data['issueName'] . ').';

        foreach ($techs as $techPersonID) {
            $permission = $techGroupGateway->getPermissionValue($techPersonID, 'viewIssueStatus');
            if ($techPersonID != $gibbon->session->get('gibbonPersonID') && $techPersonID != $data['gibbonPersonID'] && in_array($permission, ['UP', 'All'])) {
                $notificationSender->addNotification($techPersonID, $message, 'Help Desk', $absoluteURL . '/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=' . $issueID);
            }
        }

        $notificationSender->sendNotifications();

        //Log
        $array = ['issueID' => $issueID];
        $title = 'Issue Created';
        if ($createdOnBehalf) {
            $array['technicianID'] = $technicianGateway->getTechnicianByPersonID($gibbonPersonID)->fetch()['technicianID'];
            $title = 'Issue Created (for Another Person)';
        }

        $logGateway = $container->get(LogGateway::class);
        $logGateway->addLog($gibbon->session->get('gibbonSchoolYearID'), 'Help Desk', $gibbonPersonID, $title, $array);

        $URL .= "&issueID=$issueID&return=success0";
        header("Location: {$URL}");
        exit();
    }
}
?>
