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

use Gibbon\Domain\System\LogGateway;
use Gibbon\Domain\System\SettingGateway;
use Gibbon\Module\HelpDesk\Domain\DepartmentGateway;
use Gibbon\Module\HelpDesk\Domain\GroupDepartmentGateway;
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;

require_once '../../gibbon.php';

$URL = $session->get('absoluteURL') . '/index.php?q=/modules/' . $session->get('module');

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
        $departments = $_POST['departmentID'] ?? [];

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
            ];

            foreach ($settings as $setting) {
                $data[$setting] = isset($_POST[$setting]);
            }

            if (!$techGroupGateway->unique($data, ['groupName'], $groupID)) {
                $URL .= '&return=error7';
                header("Location: {$URL}");
                exit();
            }

            $settingGateway = $container->get(SettingGateway::class);
            if (!$settingGateway->getSettingByScope('Help Desk', 'simpleCategories')) {
                $groupDepartmentGateway = $container->get(GroupDepartmentGateway::class);
                $groupDepartmentGateway->deleteWhere(['groupID' => $groupID]);

                foreach ($departments as $departmentID) {
                    $groupDepartmentGateway->insert(['groupID' => $groupID, 'departmentID' => $departmentID]);
                }
            }

            if (!$techGroupGateway->update($groupID, $data)) {
                $URL .= '&return=error2';
                header("Location: {$URL}");
                exit();
            }

            //Success 0
            $logGateway = $container->get(LogGateway::class);
            $logGateway->addLog($session->get('gibbonSchoolYearID'), 'Help Desk', $session->get('gibbonPersonID'), 'Technician Group Edited', ['groupID' => $groupID]);

            $URL .= '&return=success0';
            header("Location: {$URL}");
            exit();
        }
    }
}
?>
