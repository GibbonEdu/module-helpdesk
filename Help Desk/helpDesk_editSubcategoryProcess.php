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
use Gibbon\Module\HelpDesk\Domain\DepartmentGateway;
use Gibbon\Module\HelpDesk\Domain\IssueGateway;
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
    
    //Check that department exists
    if (empty($departmentID) || !$departmentGateway->exists($departmentID)) {
        $URL .= '/helpDesk_manageDepartments.php&return=error1';
        header("Location: {$URL}");
        exit();
    }

    $URL .= "/helpDesk_editDepartment.php&departmentID=$departmentID";

    $subcategoryID = $_POST['subcategoryID'] ?? '';

    $subcategoryGateway = $container->get(SubcategoryGateway::class);
    $subcategory = $subcategoryGateway->getByID($subcategoryID);

    //Check that subcategory exists and is within department
    if (empty($subcategory) || $subcategory['departmentID'] != $departmentID) {
        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit();
    }

    $subcategoryName = $_POST['subcategoryName'] ?? '';

    //Check that subcategory name is valid
    if (empty($subcategoryName) || strlen($subcategoryName) > 55) {
    	$URL .= '&return=error1';
        header("Location: {$URL}");
        exit();
    }

    $data = ['subcategoryName' => $subcategoryName, 'departmentID' => $departmentID];

    //Check that subcategory name is unique (within department)
    if (!$subcategoryGateway->unique($data, ['subcategoryName', 'departmentID'], $subcategoryID)) {
    	$URL .= '&return=error7';
        header("Location: {$URL}");
        exit();
    }

    //Update subcategory
    if (!$subcategoryGateway->update($subcategoryID, $data)) {
        $URL .= '&return=error2';
        header("Location: {$URL}");
        exit();
    }

    //Log
    $logGateway = $container->get(LogGateway::class);
    $logGateway->addLog($session->get('gibbonSchoolYearID'), 'Help Desk', $session->get('gibbonPersonID'), 'Subcategory Edited', ['subcategoryID' => $subcategoryID]);

    $URL .= '&return=success0';
    header("Location: {$URL}");
    exit();
}
?>
