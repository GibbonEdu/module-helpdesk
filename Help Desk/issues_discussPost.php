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

//Module includes
require_once __DIR__ . '/moduleFunctions.php';

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/issues_view.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    $issueID = $_GET['issueID'] ?? '';
    $issueGateway = $container->get(IssueGateway::class);

    if (empty($issueID) || !$issueGateway->exists($issueID)) {
        $page->addError(__('No Issue Selected.'));
    } else {
        $page->breadcrumbs
            ->add(__('Discuss Issue'), 'issues_discussView.php', ['issueID' => $issueID])
            ->add(__('Post Discuss'));

        if (relatedToIssue($connection2, $issueID, $gibbon->session->get('gibbonPersonID'))) {
            $form = Form::create('issueDiscuss',  $gibbon->session->get('absoluteURL') . '/modules/' . $gibbon->session->get('module') . '/issues_discussPostProccess.php?issueID=' . $issueID, 'post');
            $form->addHiddenValue('address', $gibbon->session->get('address'));
            
            $row = $form->addRow();
                $column = $row->addColumn();
                $column->addLabel('comment', __('Comment'));
                $column->addEditor('comment', $guid)
                    ->setRows(5)
                    ->showMedia()
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
