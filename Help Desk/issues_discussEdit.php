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

if (!isActionAccessible($guid, $connection2, "/modules/Help Desk/issues_view.php")) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    $issueID = $_GET['issueID'] ?? '';

    $issueGateway = $container->get(IssueGateway::class);
    $issue = $issueGateway->getByID($issueID);

    if (empty($issueID) || empty($issue)) {
        $page->addError(__('No issue selected.'));
    } else {
        $page->breadcrumbs
                ->add(__("Discuss Issue"), 'issues_discussView.php', ['issueID' => $issueID])
                ->add(__('Edit Privacy'));

        $gibbonPersonID = $gibbon->session->get('gibbonPersonID');

        $techGroupGateway = $conatiner->get(TechGroupGateway::class);

        if ($issue['gibbonPersonID'] == $gibbonPersonID || $techGroupGateway->getPermissionValue($gibbonPersonID, 'fullAccess')) {
            $privacyOptions = array('Everyone', 'Related', 'Owner', 'No one');

            $form = Form::create('editPrivacy', $gibbon->session->get('absoluteURL') . '/modules/' . $gibbon->session->get('module') . '/issues_discussEditProcess.php?issueID=' . $issueID, 'post'); 
            $form->addHiddenValue('address', $gibbon->session->get('address')); 
            
            //have a ->selected or setValue going on here          
            $row = $form->addRow();
                $row->addLabel('privacySetting', __('Privacy Settings'))
                    ->description(__('If this Issue will or may contain any private information you may choose the privacy of this for when it is completed.'));
                $row->addSelect('privacySetting')
                    ->fromArray($privacyOptions)
                    ->selected($issue['privacySetting'])
                    ->isRequired(); 
                
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
