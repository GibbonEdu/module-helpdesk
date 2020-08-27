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
@session_start() ;

include './modules/'.$_SESSION[$guid]['module'].'/moduleFunctions.php';

if (isModuleAccessible($guid, $connection2) == false) {
    //Acess denied
    $page->addError('You do not have access to this action.');
    exit();
} else {
    $page->breadcrumbs->add(__('Create Issue'));

    if (isset($_GET['return'])) {
        $editLink = null;
        if (isset($_GET['issueID'])) {
            $issueID = $_GET['issueID'];
            $editLink = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=$issueID";
        }
        returnProcess($guid, $_GET['return'], $editLink, null);
    }

    $settings = getHelpDeskSettings($connection2);
    $priorityOptions = array();
    $priorityName = null;
    $categoryOptions = array();

    while ($value = $settings->fetch()) {
        if ($value["name"] == "issuePriority") {
            foreach (explode(",", $value["value"]) as $type) {
                if ($type != "") {
                    array_push($priorityOptions, $type);
                }
            }
        } else if ($value["name"] == "issuePriorityName") {
            $priorityName = $value["value"];
        } else if ($value["name"] == "issueCategory") {
            foreach (explode(",", $value["value"]) as $type) {
                if ($type != "") {
                    array_push($categoryOptions, $type);
                }
            }
        }
    }
    $form = Form::create('createIssue', $_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module'].'/issues_createProccess.php');
    $form->setFactory(DatabaseFormFactory::create($pdo));     
    $form->addHiddenValue('address', $_SESSION[$guid]['address']);
    
    $row = $form->addRow();
    $row->addLabel('name', __('Issue Name'));
    $row->addTextField('name')->required()->maxLength(55);
    
    if (count($categoryOptions)>0) {
        $row = $form->addRow();
            $row->addLabel('category', __('Category'));
            $row->addSelect('category')->fromArray($categoryOptions)->placeholder()->isRequired();
    }
    
    $row = $form->addRow();
        $column = $row->addColumn();
        $column->addLabel('description', __('Description'));
        $column->addEditor('description', $guid)->setRows(5)->showMedia()->isRequired();
        
    if (count($priorityOptions)>0) {
        $row = $form->addRow();
            $row->addLabel('priority', __('Priority'));
            $row->addSelect('priority')->fromArray($priorityOptions)->placeholder()->isRequired();
    }
    
    if (getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "createIssueForOther")) {
        $row = $form->addRow();
            $row->addLabel('createFor', __('Create on behalf of'))->description(__('Leave blank if creating issue for self.'));
            $row->addSelectStaff('createFor')->placeholder();
    }

    //I'mma be honest, I only have a vague idea of what the legacy code was doing here, so I'm just hoping that the way I've adapted it works.
    //TODO: Figure it out and perhaps improve idk               
    $data = array();
    $sql = "SELECT * FROM gibbonSetting WHERE scope='Help Desk' AND name='resolvedIssuePrivacy'" ;
    $result = $connection2->prepare($sql);
    $result->execute($data);
    $values = $result->fetch() ;
    $privacySetting = $values['value'];
    $options = array("Everyone", "Related", "Owner", "No one");
                        
    $row = $form->addRow();
        $row->addLabel('privacySetting', __('Privacy Settings'))->description(__('If this Issue will or may contain any private information you may choose the privacy of this for when it is completed.'));
        $row->addSelect('privacySetting')->fromArray($options)->setValue($privacySetting)->isRequired(); 
        
    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();
}
?>
