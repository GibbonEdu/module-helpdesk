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
use Gibbon\Forms\Form;

$page->breadcrumbs
    ->add(__('Manage Departments'), 'helpDesk_manageDepartments.php')
    ->add(__('Create Department'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageDepartments.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    $form = Form::create('createDepartment',  $gibbon->session->get('absoluteURL') . '/modules/' . $gibbon->session->get('module') . '/helpDesk_createDepartmentProcess.php', 'post');
    $form->addHiddenValue('address', $gibbon->session->get('address'));

    $row = $form->addRow();
        $row->addLabel('departmentName', __('Department Name'));
        $row->addTextField('departmentName')
            ->uniqueField('./modules/' . $gibbon->session->get('module') . '/helpDesk_createDepartmentProcessAjax.php')->maxLength(55)
            ->isRequired();
            
    $row = $form->addRow();
        $row->addLabel('departmentDesc', __('Department Description'));
        $row->addTextField('departmentDesc')->maxLength(128)
            ->isRequired();
    
    //TODO: ADD STUFF FOR SUBCATEGORIES
    
    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();

}
?>
