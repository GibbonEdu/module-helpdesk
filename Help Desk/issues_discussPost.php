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
@session_start() ;

//Module includes
include './modules/'.$_SESSION[$guid]['module'].'/moduleFunctions.php';

if (isset($_GET['issueID'])) {
    $issueID = $_GET['issueID'];
    if (isActionAccessible($guid, $connection2, '/modules/Help Desk/issues_view.php') == false || !relatedToIssue($connection2, $issueID, $_SESSION[$guid]['gibbonPersonID'])) {
        //Acess denied
        $page->addError('You do not have access to this action.');
    } else {
        $page->breadcrumbs->add(__('Discuss Issue'), 'issues_discussView.php', ['issueID' => $issueID]);
        $page->breadcrumbs->add(__('Post Discuss'));
        
         $form = Form::create('issueDiscuss',  $_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module'].'/issues_discussPostProccess.php?issueID=' . $issueID, 'post');
            $form->addHiddenValue('address', $_SESSION[$guid]['address']);
            
            $row = $form->addRow();
            $column = $row->addColumn();
            $column->addLabel('comment', __('Comment'));
            $column->addEditor('comment', $guid)->setRows(5)->showMedia()->isRequired();
            
            $row = $form->addRow();
            $row->addFooter();
            $row->addSubmit();

        echo $form->getOutput();
    }
} else {
    $page->addError('No Issue Selected.');
}
?>
