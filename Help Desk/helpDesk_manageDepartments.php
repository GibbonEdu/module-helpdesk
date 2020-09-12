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
use Gibbon\Tables\DataTable;
use Gibbon\Module\HelpDesk\Domain\DepartmentGateway;
use Gibbon\Module\HelpDesk\Domain\SubcategoryGateway;

$page->breadcrumbs->add(__('Manage Departments'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageDepartments.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    
    $departmentGateway = $container->get(DepartmentGateway::class);
    $subcategoryGateway = $container->get(SubcategoryGateway::class);   

    $departmentData = $departmentGateway->selectDepartments()->toDataSet();

    $formatCategoryList = function($row) use ($subcategoryGateway) {
        $categories = $subcategoryGateway->selectBy(['departmentID' => $row['departmentID']])->fetchAll();
        if (count($categories) < 1) {
            return __('This department does not have any subcategories.');
        }
        return implode(', ', array_column($categories, 'subcategoryName'));
    };
    
    $table = DataTable::create('departments');
    $table->setTitle('Departments');

    $table->addHeaderAction('add', __('Add'))
            ->setURL('/modules/' . $gibbon->session->get('module') . '/helpDesk_createDepartment.php');

    $table->addColumn('departmentName', __('Department Name'));

    $table->addColumn('departmentDesc', __('Department Description'));

    $table->addColumn('categories', __('Subcategories in department'))->format($formatCategoryList);;

    $table->addActionColumn()
            ->addParam('departmentID')
            ->format(function ($department, $actions) use ($gibbon, $departmentData) {
                $actions->addAction('edit', __('Edit'))
                        ->setURL('/modules/' . $gibbon->session->get('module') . '/helpDesk_editDepartments.php');
                $actions->addAction('delete', __('Delete'))
                        ->modalWindow()
                        ->setURL('/modules/' . $gibbon->session->get('module') . '/helpDesk_deleteDepartments.php');
            });
    
    echo $table->render($departmentData);    
}
?>
