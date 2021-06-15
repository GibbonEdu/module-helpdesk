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
use Gibbon\Module\HelpDesk\Domain\DepartmentGateway;
use Gibbon\Module\HelpDesk\Domain\SubcategoryGateway;

require_once '../../gibbon.php';

$URL = $session->get('absoluteURL') . '/index.php?q=/modules/' . $session->get('module');

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageDepartments.php')) {
    $URL .= '/issues_view.php&return=error0';
    header("Location: {$URL}");
    exit();
} else {
    $departmentID = $_POST['departmentID'] ?? '';

    $departmentGateway = $container->get(DepartmentGateway::class);

    //Check if department exists
    if (empty($departmentID) || !$departmentGateway->exists($departmentID)) {
        $URL .= '/helpDesk_manageDepartments.php&return=error1';
        header("Location: {$URL}");
        exit();
    }

    $URL .= "/helpDesk_editDepartment.php&departmentID=$departmentID";

    $subcategoryName = $_POST['subcategoryName'] ?? '';

    //Check if name is valid
    if (empty($subcategoryName) || strlen($subcategoryName) > 55) {
        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit();
    }

    $data = ['subcategoryName' => $subcategoryName, 'departmentID' => $departmentID];

    $subcategoryGateway = $container->get(SubcategoryGateway::class);

    //Check if subcategory is unique (in department)
    if (!$subcategoryGateway->unique($data, ['subcategoryName', 'departmentID'])) {
        $URL .= '&return=error7';
        header("Location: {$URL}");
        exit();
    }

    //Insert subcategory
    $subcategoryID = $subcategoryGateway->insert($data);
    if ($subcategoryID === false) {
        $URL .= '&return=error2';
        header("Location: {$URL}");
        exit();
    }

    //Log
    $logGateway = $container->get(LogGateway::class);
    $logGateway->addLog($session->get('gibbonSchoolYearID'), 'Help Desk', $session->get('gibbonPersonID'), 'Subcategory Added', ['subcategoryID' => $subcategoryID]);

    $URL .= "&subcategoryID=$subcategoryID&return=success0";
    header("Location: {$URL}");
    exit();
}
?>
