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
use Gibbon\Module\HelpDesk\Domain\HelpdeskPermissionsGateway;
use Gibbon\Domain\User\RoleGateway;
require_once '../../gibbon.php';

require_once './moduleFunctions.php';

$URL = $gibbon->session->get('absoluteURL') . '/index.php?q=/modules/' . $gibbon->session->get('module') . '/helpDesk_createDepartment.php';

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageDepartments.php')) {
    $URL .= '&return=error0';
    header("Location: {$URL}");
    exit();
} else {
    $departmentName = $_POST['departmentName'] ?? '';
    $departmentDesc = $_POST['departmentDesc'] ?? '';
    $roles = $_POST['roles'] ?? '';
    if (empty($departmentName) || strlen($departmentName) > 55 || empty($departmentDesc) || strlen($departmentDesc) > 128 || empty($roles)) {
        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit();
    } else {
        //Write to database

        try {
            $gibbonModuleID = getModuleIDFromName($connection2, 'Help Desk');
            if ($gibbonModuleID == null) {
                throw new PDOException('Invalid gibbonModuleID.');
            }

            $data = array('departmentName' => $departmentName, 'departmentDesc' => $departmentDesc);

            $departmentGateway = $container->get(DepartmentGateway::class);

            if (!$departmentGateway->unique($data, ['departmentName'])) {
                $URL .= '&return=error7';
                header("Location: {$URL}");
                exit();
            }

            $departmentID = $departmentGateway->insert($data);
            if ($departmentID === false) {
                throw new PDOException('Could not insert group.');
            }
            $HelpdeskPermissionsGateway = $container->get(HelpdeskPermissionsGateway::class);

            foreach ($roles AS $role) {
                $data = array('departmentID' => $departmentID, 'gibbonRoleID' => $role);
                $HelpdeskPermissionsGateway->insert($data);
            }
            
            
            
        } catch (PDOException $e) {
            $URL .= '&return=error2';
            header("Location: {$URL}");
            exit();
        }

        setLog($connection2, $gibbon->session->get('gibbonSchoolYearID'), $gibbonModuleID, $gibbon->session->get('gibbonPersonID'), 'Department Added', array('departmentID' => $departmentID), null);

        //Success 0
        $URL .= "&departmentID=$departmentID&return=success0";
        header("Location: {$URL}");
    }
}

?>
