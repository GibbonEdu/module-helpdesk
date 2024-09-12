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

use Gibbon\Forms\Prefab\DeleteForm;
use Gibbon\Module\HelpDesk\Domain\DepartmentGateway;
use Gibbon\Module\HelpDesk\Domain\SubcategoryGateway;

$page->breadcrumbs->add(__('Delete a Subcategory'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageDepartments.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    $departmentID = $_GET['departmentID'] ?? '';
    $subcategoryID = $_GET['subcategoryID'] ?? '';

    $departmentGateway = $container->get(DepartmentGateway::class);

    $subcategoryGateway = $container->get(SubcategoryGateway::class);
    $subcategory = $subcategoryGateway->getByID($subcategoryID);

    if (empty($departmentID) || !$departmentGateway->exists($departmentID) || empty($subcategory) || $subcategory['departmentID'] != $departmentID) {
        $page->addError(__('Invalid Data Provided'));
    } else {
        $form = DeleteForm::createForm($session->get('absoluteURL') . '/modules/' . $session->get('module') . "/helpDesk_deleteSubcategoryProcess.php");
        $form->addHiddenValue('address', $session->get('address'));
        $form->addHiddenValue('departmentID', $departmentID);
        $form->addHiddenValue('subcategoryID', $subcategoryID);

        echo $form->getOutput();
    }
}
?>
