<?php
/*
Gibbon: the flexible, open school platform
Founded by Ross Parker at ICHK Secondary. Built by Ross Parker, Sandra Kuipers and the Gibbon community (https://gibbonedu.org/about/)
Copyright © 2010, Gibbon Foundation
Gibbon™, Gibbon Education Ltd. (Hong Kong)

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
use Gibbon\Domain\User\RoleGateway;
use Gibbon\Module\HelpDesk\Domain\DepartmentGateway;
use Gibbon\Module\HelpDesk\Domain\DepartmentPermissionsGateway;

require_once '../../gibbon.php';

$URL = $session->get('absoluteURL') . '/index.php?q=/modules/' . $session->get('module') . '/helpDesk_createDepartment.php';

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
        $data = ['departmentName' => $departmentName, 'departmentDesc' => $departmentDesc];

        $departmentGateway = $container->get(DepartmentGateway::class);

        //Check if name is unique
        if (!$departmentGateway->unique($data, ['departmentName'])) {
            $URL .= '&return=error7';
            header("Location: {$URL}");
            exit();
        }

        //Insert Department
        $departmentID = $departmentGateway->insert($data);
        if ($departmentID === false) {
            $URL .= '&return=error2';
            header("Location: {$URL}");
            exit();
        }

        //Associate roles with department
        $departmentPermissionsGateway = $container->get(DepartmentPermissionsGateway::class);
        $return = 'success0';

        foreach ($roles as $role) {
            $data = ['departmentID' => $departmentID, 'gibbonRoleID' => $role];
            if ($departmentPermissionsGateway->insert($data) === false) {
                $return = 'warning1';
            }
        }
            
        //Log
        $logGateway = $container->get(LogGateway::class);
        $logGateway->addLog($session->get('gibbonSchoolYearID'), 'Help Desk', $session->get('gibbonPersonID'), 'Department Added', ['departmentID' => $departmentID]);

        //Success 0
        $URL .= "&departmentID=$departmentID&return=$return";
        header("Location: {$URL}");
    }
}

?>
