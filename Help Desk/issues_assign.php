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
use Gibbon\Module\HelpDesk\Domain\IssueGateway;
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;

require_once __DIR__ . '/moduleFunctions.php';

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

        $title = $isReassign ? __('Reassign Issue') : __('Assign Issue');
        $page->breadcrumbs->add($title);

        $permission = $isReassign ? 'reassignIssue' : 'assignIssue';

        $techGroupGateway = $container->get(TechGroupGateway::class);

        if ($techGroupGateway->getPermissionValue($gibbon->session->get('gibbonPersonID'), $permission)) {
            if (isset($_GET['return'])) {
                returnProcess($guid, $_GET['return'], null, null);
            }
            
            $form = Form::create('assignIssue',  $gibbon->session->get('absoluteURL') . '/modules/' . $gibbon->session->get('module') . '/issues_assignProcess.php?issueID=' . $issueID . '&permission=' . $permission, 'post');
            $form->addHiddenValue('address', $gibbon->session->get('address'));
            
            $data = array('issueID' => $issueID);
            
            //TODO: Fix module_function getAllTechnicians to work here
            $sql = 'SELECT helpDeskTechnicians.gibbonPersonID, technicianID AS value, concat(surname, ", ", preferredName) AS name FROM helpDeskTechnicians JOIN gibbonPerson ON (helpDeskTechnicians.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE status="Full" ORDER BY surname, preferredName ASC';
            
            //Find curently assigned technician TODO: fix the getTechWorkingOnIssue function in module_functions so that it actually works and this can be replaced
            $sqlvalues = 'SELECT helpDeskTechnicians.gibbonPersonID AS personID, helpDeskTechnicians.technicianID AS value, concat(surname, ", ", preferredName) AS name FROM helpDeskIssue JOIN helpDeskTechnicians ON (helpDeskIssue.technicianID=helpDeskTechnicians.technicianID) JOIN gibbonPerson ON (helpDeskTechnicians.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE gibbonPerson.status="Full" AND issueID=:issueID ORDER BY name ASC';
            $result = $connection2->prepare($sqlvalues);
                $result->execute($data);
                $values = $result->fetch();
                
            $row = $form->addRow();
                $row->addLabel('technician', __('Technician'));
                $row->addSelect('technician')
                    ->fromQuery($pdo, $sql, $data)
                    ->placeholder()
                    ->isRequired()
                    ->selected($values['value']); 
            
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
