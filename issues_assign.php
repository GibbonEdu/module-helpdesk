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
use Gibbon\Forms\DatabaseFormFactory;
@session_start();

include './modules/'.$_SESSION[$guid]['module'].'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/issues_view.php") == false) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
    print "</div>" ;
} else {
    $issueID = null;
    if (isset($_GET["issueID"])) {
        $issueID = $_GET["issueID"];
    } else {
        $page->addError(__('No issue selected.'));
        exit();
    }

    $isReassign = false;
    if (hasTechnicianAssigned($connection2, $issueID)) {
        $isReassign = true;
    }

    $permission = "assignIssue";

    if ($isReassign) {
        $permission = "reassignIssue";
    }

    if (!getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], $permission)) {
        $page->addError(__('You do not have access to this action.'));
        exit();
    }

    //Proceed!
    $title = $isReassign ? __('Reassign Issue') : __('Assign Issue');
    $page->breadcrumbs->add($title);

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    


    $form = Form::create('assignIssue',  $_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module'].'/issues_assignProcess.php?issueID=' . $issueID . '&permission=' . $permission, 'post');
        $form->addHiddenValue('address', $_SESSION[$guid]['address']);
        $form->setFactory(DatabaseFormFactory::create($pdo));

        $data = array();
        $sql = "SELECT helpDeskTechnicians.gibbonPersonID, technicianID as value, concat(surname, ', ' ,preferredName) as name FROM helpDeskTechnicians JOIN gibbonPerson ON (helpDeskTechnicians.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE status='Full' ORDER BY surname, preferredName ASC";
        //TODO: bring back if (!isPersonsIssue($connection2, $issueID, $option["gibbonPersonID"])) and if ($isReassign) using a ->selected()
        $row = $form->addRow();
            $row->addLabel('technician', __('Technicians'));
            $row->addSelect('technician')->fromQuery($pdo, $sql, $data)->placeholder()->isRequired(); 

        $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

        echo $form->getOutput();
}
?>
