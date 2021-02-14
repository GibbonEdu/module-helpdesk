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

use Gibbon\Module\HelpDesk\Domain\DepartmentGateway;
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;

require_once '../../gibbon.php';

$URL = $gibbon->session->get('absoluteURL') . '/index.php?q=/modules/' . $gibbon->session->get('module');

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageTechnicianGroup.php')) {
    //Fail 0
    $URL .= '/issues_view.php&return=error0';
    header("Location: {$URL}");
    exit();
} else {
    $groupID = $_GET['groupID'] ?? '';

    $techGroupGateway = $container->get(TechGroupGateway::class);

    if (empty($groupID) || !$techGroupGateway->exists($groupID)) {
        $URL .= '/helpDesk_manageTechnicianGroup.php&return=error1';
        header("Location: {$URL}");
        exit();
    } else {
        $URL .= "/helpDesk_editTechnicianGroup.php&groupID=$groupID";

        $departmentGateway = $container->get(DepartmentGateway::class);

        $groupName = $_POST['groupName'] ?? '';
        $departmentID = $_POST['departmentID'] ?? null;
        if (empty($departmentID)) {
            $departmentID = null;
        }

        $viewIssueStatus =  $_POST['viewIssueStatus'] ?? '';

        if (empty($groupName) || empty($viewIssueStatus) || ($departmentID != null && !$departmentGateway->exists($departmentID))) {
            $URL .= '&return=error1';
            header("Location: {$URL}");
            exit();
        } else {
            $settings = ['viewIssue', 'assignIssue', 'acceptIssue', 'resolveIssue', 'createIssueForOther', 'reassignIssue', 'reincarnateIssue', 'fullAccess'];

            $data = [
                'groupName' => $groupName,
                'viewIssueStatus' => $viewIssueStatus,
                'departmentID' => $departmentID,
            ];

            foreach ($settings as $setting) {
                $data[$setting] = isset($_POST[$setting]);
            }

            try {
                $gibbonModuleID = getModuleIDFromName($connection2, 'Help Desk');
                if ($gibbonModuleID == null) {
                    throw new PDOException('Invalid gibbonModuleID.');
                }

                if (!$techGroupGateway->unique($data, ['groupName'], $groupID)) {
                    $URL .= '&return=error7';
                    header("Location: {$URL}");
                    exit();
                }

                if (!$techGroupGateway->update($groupID, $data)) {
                    throw new PDOException('Could not update group.');
                }
            } catch (PDOException $e) { 
                $URL .= '&return=error2';
                header("Location: {$URL}");
                exit();
            }

            //Success 0
            setLog($connection2, $gibbon->session->get('gibbonSchoolYearID'), $gibbonModuleID, $gibbon->session->get('gibbonPersonID'), 'Technician Group Edited', ['groupID' => $groupID], null);

            $URL .= '&return=success0';
            header("Location: {$URL}");
            exit();
        }
    }
}
?>
