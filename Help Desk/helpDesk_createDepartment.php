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

use Gibbon\Forms\Form;

require_once __DIR__ . '/moduleFunctions.php';

$page->breadcrumbs
    ->add(__('Manage Departments'), 'helpDesk_manageDepartments.php')
    ->add(__('Create Department'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageDepartments.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    if (isset($_GET['departmentID'])) {
        $page->return->setEditLink($session->get('absoluteURL') . '/index.php?q=/modules/' . $session->get('module') . '/helpDesk_editDepartment.php&departmentID=' . $_GET['departmentID']);
    }

    $form = Form::create('createDepartment',  $session->get('absoluteURL') . '/modules/' . $session->get('module') . '/helpDesk_createDepartmentProcess.php', 'post');
    $form->addHiddenValue('address', $session->get('address'));

    $row = $form->addRow();
        $row->addLabel('departmentName', __('Department Name'));
        $row->addTextField('departmentName')
            ->uniqueField('./modules/' . $session->get('module') . '/helpDesk_createDepartmentAjax.php')
            ->maxLength(55)
            ->required();
            
    $row = $form->addRow();
        $row->addLabel('departmentDesc', __('Department Description'));
        $row->addTextField('departmentDesc')
            ->maxLength(128)
            ->required();

    $row = $form->addRow();
        $row->addLabel('roles', __('Select Roles'))
            ->description(__('Which roles can create issues for this department'));
        $row->addSelect('roles')
            ->fromArray(getRoles($container))
            ->selectMultiple()
            ->setSize(6)
            ->required();
    
    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();
}
?>
