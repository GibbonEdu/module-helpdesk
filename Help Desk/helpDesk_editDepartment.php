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
use Gibbon\Module\HelpDesk\Domain\SubcategoryGateway;
use Gibbon\Module\HelpDesk\Domain\DepartmentPermissionsGateway;
use Gibbon\Tables\DataTable;

require_once __DIR__ . '/moduleFunctions.php';

$page->breadcrumbs
    ->add(__('Manage Departments'), 'helpDesk_manageDepartments.php')
    ->add(__('Edit Department'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageDepartments.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    $departmentID = $_GET['departmentID'] ?? '';

    $departmentGateway = $container->get(DepartmentGateway::class);
    $department = $departmentGateway->getByID($departmentID);
    
    if (empty($department)) {
        $page->addError(__('No Department Selected.'));
    } else {
        $moduleName = $session->get('module');

        $departmentPermissionsGateway = $container->get(DepartmentPermissionsGateway::class);
        $selectedRoles = $departmentPermissionsGateway->selectBy(['departmentID' => $departmentID])->toDataSet()->getColumn('gibbonRoleID');

        //Edit Department Form
        $form = Form::create('createDepartment',  $session->get('absoluteURL') . '/modules/' . $moduleName . '/helpDesk_editDepartmentProcess.php', 'post');
        $form->setTitle($department['departmentName']);
        $form->addHiddenValue('address', $session->get('address'));
        $form->addHiddenValue('departmentID', $departmentID);

        $row = $form->addRow();
            $row->addLabel('departmentName', __('Department Name'));
            $row->addTextField('departmentName')
                ->maxLength(55)
                ->uniqueField('./modules/' . $moduleName . '/helpDesk_createDepartmentAjax.php', ['currentDepartmentName' => $department['departmentName']])
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
                ->required()
                ->selected($selectedRoles);
             
        $row = $form->addRow();
            $row->addFooter();
            $row->addSubmit();

        $form->loadAllValuesFrom($department);

        echo $form->getOutput();

        //Subcategory table
        $subcategoryGateway = $container->get(SubcategoryGateway::class);

        $criteria = $subcategoryGateway->newQueryCriteria(true)
            ->filterBy('departmentID', $departmentID)
            ->sortBy(['subcategoryName'])
            ->fromPOST();

        $subcategoryData = $subcategoryGateway->querySubcategories($criteria);

        $table = DataTable::create('subcategories');
        $table->setTitle(__('Subcategories'));

        $table->addHeaderAction('add', __('Create'))
                ->addParam('departmentID', $departmentID)
                ->modalWindow()
                ->displayLabel()
                ->setURL('/modules/' . $moduleName . '/helpDesk_createSubcategory.php');

        $table->addColumn('subcategoryName', __('Name'));

        $table->addActionColumn()
                ->addParam('departmentID')
                ->addParam('subcategoryID')
                ->format(function ($subcategory, $actions) use ($moduleName) {
                    $actions->addAction('edit', __('Edit'))
                            ->modalWindow()
                            ->setURL('/modules/' . $moduleName . '/helpDesk_editSubcategory.php');

                    $actions->addAction('delete', __('Delete'))
                            ->setURL('/modules/' . $moduleName . '/helpDesk_deleteSubcategory.php');
                });

        echo $table->render($subcategoryData);
    }
}
?>
