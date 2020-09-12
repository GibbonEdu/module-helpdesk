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

use Gibbon\Forms\Prefab\DeleteForm;
use Gibbon\Module\HelpDesk\Domain\SubcategoryGateway;

$page->breadcrumbs->add(__('Delete a Subcategory'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageDepartments.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    $departmentID = $_GET['departmentID'] ?? '';
    $subcategoryID = $_GET['subcategoryID'] ?? '';

    $subcategoryGateway = $container->get(SubcategoryGateway::class);
    $subcategory = $subcategoryGateway->getByID($subcategoryID);

    if (empty($departmentID) || empty($subcategoryID) || empty($subcategory) || $subcategory['departmentID'] != $departmentID) {
        $page->addError(__('Invalid Data Provided'));
    } else {
        $form = DeleteForm::createForm($gibbon->session->get('absoluteURL') . '/modules/' . $gibbon->session->get('module') . "/helpDesk_deleteSubcategoryProcess.php");
        $form->addHiddenValue('address', $gibbon->session->get('address'));
        $form->addHiddenValue('departmentID', $departmentID);
        $form->addHiddenValue('subcategoryID', $subcategoryID);

        echo $form->getOutput();
    }
}
?>
