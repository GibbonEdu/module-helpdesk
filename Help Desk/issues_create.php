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
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
use Gibbon\Forms\DatabaseFormFactory;
use Gibbon\Forms\Form;

require_once __DIR__ . '/moduleFunctions.php';

$page->breadcrumbs
    ->add(__('Create Issue'));

if (!isModuleAccessible($guid, $connection2)) {
    //Acess denied
    $page->addError('You do not have access to this action.');
} else {
    if (isset($_GET['return'])) {
        $editLink = null;
        if (isset($_GET['issueID'])) {
            $issueID = $_GET['issueID'];
            $editLink = $_SESSION[$guid]["absoluteURL"] . '/index.php?q=/modules/' . $_SESSION[$guid]['module'] . '/issues_discussView.php&issueID=' . $issueID;
        }
        returnProcess($guid, $_GET['return'], $editLink, null);
    }

    $priorityOptions = array_filter(array_map('trim', explode(',', getSettingByScope($connection2, $_SESSION[$guid]['module'], 'issuePriority', false))));
    $categoryOptions = array_filter(array_map('trim', explode(',', getSettingByScope($connection2, $_SESSION[$guid]['module'], 'issueCategory', false))));
    $privacyOptions = array("Everyone", "Related", "Owner", "No one");

    $form = Form::create('createIssue', $_SESSION[$guid]['absoluteURL'] . '/modules/' . $_SESSION[$guid]['module'] . '/issues_createProccess.php', 'post');
    $form->setFactory(DatabaseFormFactory::create($pdo));     
    $form->addHiddenValue('address', $_SESSION[$guid]['address']);
    
    $row = $form->addRow();
        $row->addLabel('issueName', __('Issue Name'));
        $row->addTextField('issueName')
            ->required()
            ->maxLength(55);
    
    if (count($categoryOptions) > 0) {
        $row = $form->addRow();
            $row->addLabel('category', __('Category'));
            $row->addSelect('category')
                ->fromArray($categoryOptions)
                ->placeholder()
                ->isRequired();
    }
    
    $row = $form->addRow();
        $column = $row->addColumn();
            $column->addLabel('description', __('Description'));
            $column->addEditor('description', $guid)
                    ->setRows(5)
                    ->showMedia()
                    ->isRequired();
        
    if (count($priorityOptions) > 0) {
        $row = $form->addRow();
            $row->addLabel('priority', __(getSettingByScope($connection2, $_SESSION[$guid]['module'], 'issuePriorityName', false)));
            $row->addSelect('priority')
                ->fromArray($priorityOptions)
                ->placeholder()
                ->isRequired();
    }
    
    if (getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "createIssueForOther")) {
        $row = $form->addRow();
            $row->addLabel('createFor', __('Create on behalf of'))
                ->description(__('Leave blank if creating issue for self.'));
            $row->addSelectStaff('createFor')
                ->placeholder();
    }
                        
    $row = $form->addRow();
        $row->addLabel('privacySetting', __('Privacy Settings'))
            ->description(__('If this Issue will or may contain any private information you may choose the privacy of this for when it is completed.'));
        $row->addSelect('privacySetting')
            ->fromArray($privacyOptions)
            ->selected(getSettingByScope($connection2, $_SESSION[$guid]['module'], 'resolvedIssuePrivacy', false))
            ->isRequired(); 
        
    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();
}
?>
