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
use Gibbon\Services\Format;
use Gibbon\Module\HelpDesk\Domain\IssueGateway;
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;
use Gibbon\Module\HelpDesk\Domain\TechnicianGateway;

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/issues_view.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    $issueID = $_GET['issueID'] ?? '';

    $issueGateway = $container->get(IssueGateway::class);
    $issue = $issueGateway->getByID($issueID);

    if (empty($issueID) || empty($issue)) {
        $page->addError(__('No issue selected.'));
    } else {
        $isReassign = $issue['technicianID'] != null;

        $title = $isReassign ? 'Reassign Issue' : 'Assign Issue';
        $page->breadcrumbs->add(__($title));

        $permission = $isReassign ? 'reassignIssue' : 'assignIssue';

        $techGroupGateway = $container->get(TechGroupGateway::class);

        if ($techGroupGateway->getPermissionValue($session->get('gibbonPersonID'), $permission)) {
            $technicianGateway = $container->get(TechnicianGateway::class);

            $techs = array_reduce($technicianGateway->selectTechnicians()->fetchAll(), function ($group, $item) {
                $group[$item['technicianID']] = Format::name($item['title'], $item['preferredName'], $item['surname'], 'Student', true) . ' (' . $item['groupName'] . ')';
                return $group;
            }, []);

            $ownerTech = $technicianGateway->getTechnicianByPersonID($issue['gibbonPersonID']);
            if($ownerTech->isNotEmpty()) {
                unset($techs[$ownerTech->fetch()['technicianID']]);
            }  
            
            $form = Form::create('assignIssue',  $session->get('absoluteURL') . '/modules/' . $session->get('module') . '/issues_assignProcess.php?issueID=' . $issueID . '&permission=' . $permission, 'post');
            $form->addHiddenValue('address', $session->get('address'));

            $row = $form->addRow();
                $row->addLabel('technician', __('Technician'));
                $select = $row->addSelect('technician')
                    ->fromArray($techs)
                    ->required();
                
                if ($isReassign) {
                    $select->selected($issue['technicianID']); 
                } else {
                    $select->placeholder();
                }

            
            $row = $form->addRow();
                $row->addFooter();
                $row->addSubmit();

            echo $form->getOutput();
        } else {
            $page->addError(__('You do not have access to this action.'));
        }
    }
}
?>
