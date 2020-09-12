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
use Gibbon\Module\HelpDesk\Domain\DepartmentGateway;

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageDepartments.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    $departmentID = $_GET['departmentID'] ?? '';

    $departmentGateway = $container->get(DepartmentGateway::class);
    if (empty($departmentID) || !$departmentGateway->exists($departmentID)) {
        $page->addError(__('No Department Selected.'));
    } else {
        $page->breadcrumbs
            ->add(__('Edit Department'), 'helpDesk_editDepartment.php', ['departmentID' => $departmentID])
            ->add(__('Create a Subcategory'));

        $form = Form::create('createSubcategory',  $gibbon->session->get('absoluteURL') . '/modules/' . $gibbon->session->get('module') . '/helpDesk_createSubcategoryProcess.php', 'post');
        $form->addHiddenValue('address', $gibbon->session->get('address'));
        $form->addHiddenValue('departmentID', $departmentID);

        $row = $form->addRow();
            $row->addLabel('subcategoryName', __('Subcategory Name'));
            $row->addTextField('subcategoryName')
                ->isRequired()
                ->uniqueField('./modules/' . $gibbon->session->get('module') . '/helpDesk_createSubcategoryAjax.php', array('departmentID' => $departmentID))
                ->maxLength(55);

        $row = $form->addRow();
            $row->addFooter();
            $row->addSubmit();

        echo $form->getOutput();
    }
}
?>
