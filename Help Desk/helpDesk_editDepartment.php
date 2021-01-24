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
use Gibbon\Module\HelpDesk\Domain\HelpdeskPermissionsGateway;
use Gibbon\Domain\User\RoleGateway;


use Gibbon\Tables\DataTable;

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageDepartments.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    $departmentID = $_GET['departmentID'] ?? '';

    $departmentGateway = $container->get(DepartmentGateway::class);
    $department = $departmentGateway->getByID($departmentID);
    
    if (empty($departmentID) || empty($department)) {
        $page->addError(__('No Department Selected.'));
    } else {
        $page->breadcrumbs
            ->add(__('Manage Departments'), 'helpDesk_manageDepartments.php')
            ->add(__('Edit Department'));

        $moduleName = $gibbon->session->get('module');

        if (isset($_GET['return'])) {
            returnProcess($guid, $_GET['return'], null, null);
        }

        $form = Form::create('createDepartment',  $gibbon->session->get('absoluteURL') . '/modules/' . $moduleName . '/helpDesk_editDepartmentProcess.php', 'post');
        $form->setTitle($department['departmentName']);
        $form->addHiddenValue('address', $gibbon->session->get('address'));
        $form->addHiddenValue('departmentID', $departmentID);

        $row = $form->addRow();
            $row->addLabel('departmentName', __('Department Name'));
            $row->addTextField('departmentName')
                ->maxLength(55)
                ->uniqueField('./modules/' . $moduleName . '/helpDesk_createDepartmentAjax.php', ['currentDepartmentName' => $department['departmentName']])
                ->isRequired();
            
        $row = $form->addRow();
            $row->addLabel('departmentDesc', __('Department Description'));
            $row->addTextField('departmentDesc')
                ->maxLength(128)
                ->isRequired();
             
            
        $roleGateway = $container->get(RoleGateway::class);
        // CRITERIA
        $criteria = $roleGateway->newQueryCriteria()
        ->sortBy(['gibbonRole.name']);
        $arrRoles = array();
        $roles = $roleGateway->queryRoles($criteria);

        $row = $form->addRow();
        foreach ($roles AS $role) {
            $arrRoles[$role['gibbonRoleID']] = __($role['name'])." (".__($role['category']).")";
        } 
        
        $HelpdeskPermissionsGateway = $container->get(HelpdeskPermissionsGateway::class);
        // CRITERIA
        $criteria = $HelpdeskPermissionsGateway->newQueryCriteria();
        $roles = $HelpdeskPermissionsGateway->queryDeptPerms($criteria);
        //TODO: selected based off queryDeptPerms
        $row->addLabel('roles[]', __('Select Roles'))->description(__('Which roles can create issues for this department'));
        $row->addSelect('roles[]')->fromArray($arrRoles)->selectMultiple()->setSize(6)->required()->selected($HelpdeskPermissionsGateway->queryDeptPerms($criteria)->getColumn('gibbonRoleID'));
        
        $form->loadAllValuesFrom($department);
     
        $row = $form->addRow();
            $row->addFooter();
            $row->addSubmit();

        echo $form->getOutput();

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
