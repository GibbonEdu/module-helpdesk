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

use Gibbon\Domain\System\SettingGateway;
use Gibbon\Tables\DataTable;
use Gibbon\Services\Format;
use Gibbon\Module\HelpDesk\Domain\DepartmentGateway;
use Gibbon\Module\HelpDesk\Domain\GroupDepartmentGateway;
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;
use Gibbon\Module\HelpDesk\Domain\TechnicianGateway;

$page->breadcrumbs->add(__('Manage Technician Groups'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageTechnicianGroup.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, ['errorA' => 'Cannot delete last technician group.']);
    }

    $manageTechnicians = isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageTechnicians.php');
    $moduleName = $gibbon->session->get('module');

    $techGroupGateway = $container->get(TechGroupGateway::class);
    $technicianGateway = $container->get(TechnicianGateway::class);
    $departmentGateway = $container->get(DepartmentGateway::class); 
    $groupDepartmentGateway = $container->get(GroupDepartmentGateway::class);

    $canEditDepartment = isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageDepartments.php');
    $techGroupData = $techGroupGateway->selectTechGroups()->toDataSet();

    $table = DataTable::create('techGroups');
    $table->setTitle('Technician Groups');

    $table->addHeaderAction('add', __('Add'))
            ->setURL('/modules/' . $gibbon->session->get('module') . '/helpDesk_createTechnicianGroup.php');

    $table->addColumn('groupName', __('Group Name'));

    $settingGateway = $container->get(SettingGateway::class);
    
    if ($departmentGateway->countAll() > 0 && !$settingGateway->getSettingByScope('Help Desk', 'simpleCategories')) {
        $table->addColumn('department', __('Department'))
            ->format(function ($techGroup) use ($gibbon, $groupDepartmentGateway, $canEditDepartment) {
                $departments = $groupDepartmentGateway->selectGroupDepartments($techGroup['groupID'])->fetchAll();

                if (count($departments) < 1) {
                    return __('No departments assigned to this group');
                }

                return implode(', ', array_map(function ($department) use ($gibbon, $canEditDepartment) {
                    if ($canEditDepartment) {
                        return Format::link('./index.php?q=/modules/' . $gibbon->session->get('module') . '/helpDesk_editDepartment.php&departmentID='. $department['departmentID'], $department['departmentName']);
                    } else {
                        return $department['departmentName'];
                    }
                }, $departments));
            });
    }

    $table->addColumn('techs', __('Technicians in group'))
        ->format(function($row) use ($technicianGateway, $manageTechnicians, $moduleName) {
            $technicians = $technicianGateway->selectTechniciansByTechGroup($row['groupID'])->fetchAll();
            if (count($technicians) < 1) {
                return __('No one is currently in this group.');
            }

            return implode(', ', array_map(function ($row) use ($manageTechnicians, $moduleName) {
                $name = Format::name($row['title'], $row['preferredName'], $row['surname'], 'Student', false, false);
                if ($manageTechnicians) {
                    return Format::link('./index.php?q=/modules/' . $moduleName . '/helpDesk_setTechGroup.php&technicianID=' . $row['technicianID'], $name);
                } else {
                    return $name;
                }
            }, $technicians));
        });

    $table->addActionColumn()
            ->addParam('groupID')
            ->format(function ($techGroup, $actions) use ($gibbon, $techGroupData) {
                $actions->addAction('edit', __('Edit'))
                        ->setURL('/modules/' . $gibbon->session->get('module') . '/helpDesk_editTechnicianGroup.php');

                if (count($techGroupData) > 1) {
                    $actions->addAction('delete', __('Delete'))
                            ->setURL('/modules/' . $gibbon->session->get('module') . '/helpDesk_technicianGroupDelete.php');
                }
            });

    echo $table->render($techGroupData);    
}
?>
