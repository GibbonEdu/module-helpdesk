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
use Gibbon\Module\HelpDesk\Domain\DepartmentPermissionsGateway;

require_once '../../gibbon.php';

$URL = $gibbon->session->get('absoluteURL') . '/index.php?q=/modules/' . $gibbon->session->get('module');

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageDepartments.php')) {
    $URL .= '/issues_view.php&return=error0';
    header("Location: {$URL}");
    exit();
} else {
    $URL .= '/helpDesk_manageDepartments.php';
    
    $departmentID = $_POST['departmentID'] ?? '';

    $departmentGateway = $container->get(DepartmentGateway::class);
    $departmentPermissionsGateway = $container->get(DepartmentPermissionsGateway::class);
    
    if (empty($departmentID) || !$departmentGateway->exists($departmentID)) {
        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit();
    }

    if (!$departmentGateway->deleteDepartment($departmentID)) {
        $URL .= '&return=error2';
        header("Location: {$URL}");
        exit();
    }

    $URL .= '&return=success0';
    header("Location: {$URL}");
    exit();
}
?>
