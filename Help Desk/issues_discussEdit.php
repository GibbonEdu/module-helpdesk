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

require_once __DIR__ . '/moduleFunctions.php';

if (!isActionAccessible($guid, $connection2, "/modules/Help Desk/issues_view.php") || !(isPersonsIssue($connection2, $_GET["issueID"], $_SESSION[$guid]["gibbonPersonID"]) || getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "fullAccess"))) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    if (isset($_GET["issueID"])) {
        $issueID = $_GET["issueID"];

        $page->breadcrumbs
            ->add(__("Discuss Issue"), 'issues_discussView.php', ['issueID' => $issueID])
            ->add(__('Edit Privacy'));

        $options = array("Everyone", "Related", "Owner", "No one");

        $issueGateway = $container->get(IssueGateway::class); 
        $issue = $issueGateway->getByID($issueID);

        $form = Form::create('editPrivacy', $_SESSION[$guid]['absoluteURL'] . '/modules/' . $_SESSION[$guid]['module'] . '/issues_discussEditProcess.php?issueID=' . $issueID, 'post'); 
        $form->addHiddenValue('address', $_SESSION[$guid]['address']); 
        
        //have a ->selected or setValue going on here          
        $row = $form->addRow();
            $row->addLabel('privacySetting', __('Privacy Settings'))
                ->description(__('If this Issue will or may contain any private information you may choose the privacy of this for when it is completed.'));
            $row->addSelect('privacySetting')
                ->fromArray($options)
                ->selected($issue['privacySetting'])
                ->isRequired(); 
            
        $row = $form->addRow();
            $row->addFooter();
            $row->addSubmit();

        echo $form->getOutput();
    } else {
        $page->addError(__('No issue selected.'));
    }
}
?>
