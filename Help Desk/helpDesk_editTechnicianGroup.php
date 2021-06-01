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
use Gibbon\Forms\Form;
use Gibbon\Services\Format;
use Gibbon\Module\HelpDesk\Domain\DepartmentGateway;
use Gibbon\Module\HelpDesk\Domain\GroupDepartmentGateway;
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;

$page->breadcrumbs
    ->add(__('Manage Technician Groups'), 'helpDesk_manageTechnicianGroup.php')
    ->add(__('Edit Technician Group')); 

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageTechnicianGroup.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    $groupID = $_GET['groupID'] ?? '';
    
    $techGroupGateway = $container->get(TechGroupGateway::class);
    $values = $techGroupGateway->getByID($groupID);

    if (empty($values)) {
        $page->addError(__('No Group Selected.'));
    } else {
        $departmentGateway = $container->get(DepartmentGateway::class);
        $departmentData = $departmentGateway->selectDepartments()->toDataSet();

        $groupDepartmentGateway = $container->get(GroupDepartmentGateway::class);
        $groupDepartments = $groupDepartmentGateway->selectGroupDepartments($groupID)->toDataSet()->getColumn('departmentID');

        $statuses = [
            'All'       =>  __('All'),
            'UP'        =>  __('Unassigned & Pending'),
            'PR'        =>  __('Pending & Resolved'), 
            'Pending'   =>  __('Pending')
        ];

        $form = Form::create('editTechnicianGroup', $gibbon->session->get('absoluteURL') . '/modules/' . $gibbon->session->get('module') . '/helpDesk_editTechnicianGroupProcess.php?groupID=' . $groupID , 'post');
        $form->addHiddenValue('address', $gibbon->session->get('address'));
        $form->setTitle($values['groupName']);

        $form->addRow()->addHeading(__('Settings'));

        $row = $form->addRow();
            $row->addLabel('groupName', __('Group Name'));
            $row->addTextField('groupName')
                ->uniqueField('./modules/' . $gibbon->session->get('module') . '/helpDesk_createTechnicianGroupAjax.php', ['currentGroupName' => $values['groupName']])
                ->required()
                ->setValue($values['groupName']);

        $settingGateway = $container->get(SettingGateway::class);
        if (count($departmentData) > 0 && !$settingGateway->getSettingByScope('Help Desk', 'simpleCategories')) {
            $row = $form->addRow();
                $row->addLabel('departmentID', __('Department'))
                    ->description(__('Assigning a Department to a Tech Group will only allow techs in the group to work on issues in the department.'));
                $row->addSelect('departmentID')
                    ->fromDataset($departmentData, 'departmentID', 'departmentName')
                    ->selectMultiple()
                    ->placeholder()
                    ->selected($groupDepartments);
        }

        $form->addRow()->addHeading(__('Permissons'));

        $row = $form->addRow();
            $row->addLabel('viewIssue', __('Allow View All Issues'))
                ->description(__('Allow the technician to see all the issues instead of just their issues and the issues they working on.') . '<br/>' . Format::bold(__('Note: This overrides the "View Issue Status" setting (i.e. shows all issues regardless of status).')));
            $row->addCheckbox('viewIssue')
                ->setValue($values['viewIssue']);

        $row = $form->addRow();
            $row->addLabel('viewIssueStatus', __('View Issues Status'))
                ->description(__('Choose what issue statuses the technicians can view.') . '<br/>' . Format::bold(__('Note: The "All" setting does not act like the "Allow View All Issues" setting (i.e. The option will only show the technician\'s own issues and the isssues they are assigned).')));
            $row->addSelect('viewIssueStatus')
                ->fromArray($statuses)
                ->required()
                ->selected($values['viewIssueStatus']);

        $row = $form->addRow();
            $row->addLabel('assignIssue', __('Allow Assign Issues'))
                ->description(__('Allow the technician to assign issues to other technicians.'));
            $row->addCheckbox('assignIssue')
                ->setValue($values['assignIssue']);

        $row = $form->addRow();
            $row->addLabel('acceptIssue', __('Allow Accept Issues'))
                ->description(__('Allow the technician to accept issues to work on. '));
            $row->addCheckbox('acceptIssue')
                ->setValue($values['acceptIssue']);

        $row = $form->addRow();
            $row->addLabel('resolveIssue', __('Allow Resolve Issues'))
                ->description(__('Allow the technician to resolve an issue they are working on.'));
            $row->addCheckbox('resolveIssue')
                ->setValue($values['resolveIssue']);

        $row = $form->addRow();
            $row->addLabel('createIssueForOther', __('Allow Create Issues For Other'))
                ->description(__('Allow the technician to create issues issues on behalf of others.'));
            $row->addCheckbox('createIssueForOther')
                ->setValue($values['createIssueForOther']);

        $row = $form->addRow();
            $row->addLabel('reassignIssue', __('Reassign Issue'))
                ->description(__('This will allow the technician to reassign an issue to another technician.'));
            $row->addCheckbox('reassignIssue')
                ->setValue($values['reassignIssue']);

        $row = $form->addRow();
            $row->addLabel('reincarnateIssue', __('Reincarnate Issue'))
                ->description(__('This will allow the technician to bring back an issue that has been resolved.'));
            $row->addCheckbox('reincarnateIssue')
                ->setValue($values['reincarnateIssue']);

        $row = $form->addRow();
            $row->addLabel('fullAccess', __('Full Access'))
                ->description(__('Enabling this will give the technician full access. This will override almost all the checks the system has in place. It will allow the technician to resolve any issues, work on issues they are not assigned to and all the other things listed above.'));
            $row->addCheckbox('fullAccess')
                ->setValue($values['fullAccess']);

        $form->loadAllValuesFrom($values);
        
        $row = $form->addRow();
            $row->addFooter();
            $row->addSubmit();

        echo $form->getOutput();
    }
}
?>
