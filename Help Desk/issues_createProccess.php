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

use Gibbon\Module\HelpDesk\Domain\IssueGateway;

include '../../gibbon.php' ;

include './moduleFunctions.php' ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]['timezone']);

$URL = $_SESSION[$guid]['absoluteURL'] . '/index.php?q=/modules/' . $_SESSION[$guid]['module'];

if (!isActionAccessible($guid, $connection2, '/modules/' . $_SESSION[$guid]['module'] . '/issues_create.php')) {
    $URL .= '/issues_view.php&return=error0';
    header('Location: {$URL}');
    exit();
} else {
    //Proceed!    
    $URL .= '/issues_create.php';

    $data = array(
        //Default data
        'gibbonPersonID' => $_SESSION[$guid]['gibbonPersonID'],
        'createdByID' => $_SESSION[$guid]['gibbonPersonID'],
        'status' => 'Unassigned',
        'gibbonSchoolYearID' => $_SESSION[$guid]['gibbonSchoolYearID'],
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

    $createdOnBehalf = false;
    if (isset($_POST['createFor']) && $_POST['createFor'] != 0 && getPermissionValue($connection2, $_SESSION[$guid]['gibbonPersonID'], 'createIssueForOther')) {
        $data['gibbonPersonID'] = $_POST['createFor'];
        $createdOnBehalf = true;
    }

    if (empty($data['privacySetting'])) {
        $data['privacySetting'] = getSettingByScope($connection2, 'Help Desk', 'resolvedIssuePrivacy', false);
    }

    if (empty($data['issueName']) || empty($data['description'])) {
        //Fail 3
        $URL .= '&return=error1' ;
        header('Location: {$URL}');
        exit();
    } else {
        //Write to database
        try {
            $gibbonModuleID = getModuleIDFromName($connection2, 'Help Desk');
            if ($gibbonModuleID == null) {
                throw new PDOException('Invalid gibbonModuleID.');
            }

            $issueGateway = $container->get(IssueGateway::class);
            $issueGateway->insert($data);
        } catch (PDOException $e) {
            $URL .= '&return=error2' ;
            header('Location: {$URL}');
            exit();
        }

        $issueID = $connection2->lastInsertId();
        if ($createdOnBehalf) {
            setNotification($connection2, $guid, $data['gibbonPersonID'], 'A new issue has been created on your behalf (' . $data['issueName'] . ').', 'Help Desk', '/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=' . $issueID);
        }
        notifyTechnican($connection2, $guid, $issueID, $data['issueName'], $data['gibbonPersonID']);

        $array = array('issueID' =>$issueID);
        $title = 'Issue Created';
        if ($createdOnBehalf) {
            $array['technicianID'] = getTechnicianID($connection2, $createdByID);
            $title = 'Issue Created (for Another Person)';
        }

        setLog($connection2, $_SESSION[$guid]['gibbonSchoolYearID'], $gibbonModuleID, $_SESSION[$guid]['gibbonPersonID'], $title, $array, null);

        //Success 0 aka Created
        $URL .= '&issueID=' . $issueID . '&return=success0' ;
        header('Location: {$URL}');
    }
}
?>
