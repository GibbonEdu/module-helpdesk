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

use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;

require_once '../../gibbon.php';

$URL = $_SESSION[$guid]['absoluteURL'] . '/index.php?q=/modules/' . $_SESSION[$guid]['module'] . '/helpDesk_editTechnicianGroup.php' ;

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageTechnicianGroup.php')) {
    //Fail 0
    $URL .= '&return=error0' ;
    header("Location: {$URL}");
    exit();
} else {
    $groupID = $_GET['groupID'] ?? '';

    if (empty($groupID)) {
        $URL = $_SESSION[$guid]['absoluteURL'] . '/index.php?q=/modules/' . $_SESSION[$guid]['module'] . '/helpDesk_manageTechnicianGroup.php&return=error1';
        header("Location: {$URL}");
        exit();
    } else {
        $URL .= "&groupID=$groupID";

        $groupName = $_POST['groupName'] ?? '';
        $viewIssueStatus =  $_POST['viewIssueStatus' ?? '';

        if (empty($groupName) || empty($viewIssueStatus)) {
            $URL .= '&return=error1' ;
            header("Location: {$URL}");
            exit();
        } else {
            $settings = array('viewIssue', 'assignIssue', 'acceptIssue', 'resolveIssue', 'createIssueForOther', 'reassignIssue', 'reincarnateIssue', 'fullAccess');

            $data = array(
                'groupName' => $groupName,
                'viewIssueStatus' => $viewIssueStatus,
            );

            foreach ($settings as $setting) {
                $data[$setting] = isset($_POST[$setting]);
            }

            try {
                $gibbonModuleID = getModuleIDFromName($connection2, 'Help Desk');
                if ($gibbonModuleID == null) {
                    throw new PDOException('Invalid gibbonModuleID.');
                }

                $techGroupGateway = $container->get(TechGroupGateway::class);

                if (!$techGroupGateway->unique($data, ['groupName'], $groupID)) {
                    $URL .= '&return=error7';
                    header("Location: {$URL}");
                    exit();
                }

                $techGroupGateway->update($groupID, $data);
            } catch (PDOException $e) { 
                $URL .= '&return=error2' ;
                header("Location: {$URL}");
                exit();
            }

            //Success 0
            setLog($connection2, $_SESSION[$guid]['gibbonSchoolYearID'], $gibbonModuleID, $_SESSION[$guid]['gibbonPersonID'], 'Technician Group Edited', array('groupID' => $groupID), null);

            $URL .= '&return=success0' ;
            header("Location: {$URL}");
        }
    }
}
?>
