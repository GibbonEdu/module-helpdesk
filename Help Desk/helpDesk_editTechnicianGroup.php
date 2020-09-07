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
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;

$page->breadcrumbs
    ->add(__('Manage Technician Groups'), 'helpDesk_manageTechnicianGroup.php')
    ->add(__('Edit Technician Group')); 

require_once __DIR__ . '/moduleFunctions.php';

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageTechnicianGroup.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    $groupID = $_GET['groupID'] ?? '';
    
    $techGroupGateway = $container->get(TechGroupGateway::class);
    $values = $techGroupGateway->getByID($groupID);

    if (empty($groupID) || empty($values)) {
        $page->addError(__('No Group Selected.'));
    } else {
        $statuses = array(
            'All' =>__('All'),
            'UP' =>__('Unassigned & Pending'),
            'PR' =>__('Pending & Resolved'), 
            'Pending' =>__('Pending')
        );

        $form = Form::create('editTechnicianGroup', $gibbon->session->get('absoluteURL') . '/modules/' . $gibbon->session->get('module') . '/helpDesk_editTechnicianGroupProcess.php?groupID=' . $groupID , 'post');
        $form->addHiddenValue('address', $gibbon->session->get('address'));

        $form->addRow()
            ->addHeading(__('Permissons for Technician Group: ') . $values['groupName']);

        $row = $form->addRow();
            $row->addLabel('groupName', __('Group Name'));
            $row->addTextField('groupName')
                ->uniqueField('./modules/' . $gibbon->session->get('module') . '/helpDesk_createTechnicianGroupAjax.php', array('currentGroupName' => $values['groupName']))
                ->isRequired()
                ->setValue($values['groupName']);

        $row = $form->addRow();
            $row->addLabel('viewIssue', __('Allow View All Issues'))
                ->description(__('Allow the technician to see all the issues instead of just their issues and the issues they working on.'));
            $row->addCheckbox('viewIssue')
                ->setValue($values['viewIssue']);

        $row = $form->addRow();
            $row->addLabel('viewIssueStatus', __('View Issues Status Name'))
                ->description(__('Choose what issue statuses the technicians can view.'));
            $row->addSelect('viewIssueStatus')
                ->fromArray($statuses)
                ->isRequired()
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
