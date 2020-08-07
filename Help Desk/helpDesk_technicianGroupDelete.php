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
@session_start() ;

include './modules/'.$_SESSION[$guid]['module'].'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicianGroup.php") == false) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    $page->breadcrumbs->add(__('Manage Technician Groups'), 'helpDesk_manageTechnicianGroup.php');
    $page->breadcrumbs->add(__('Delete Technician Group'));

    $highestAction = getHighestGroupedAction($guid, "/modules/Help Desk/helpDesk_manageTechnicianGroup.php", $connection2) ;
    if ($highestAction == false) {
        $page->addError(__('The highest grouped action cannot be determined.'));
        exit();
    }

    if ($highestAction != "Manage Technician Groups") {
        $page->addError(__('You do not have access to this action.'));
        exit();
    }

    $groupID = null;
    if (isset($_GET["groupID"])) {
        $groupID = $_GET["groupID"];
    } else {
        $page->addError(__('No group selected.'));
        exit();
    }
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }
   
    //excludes selected group such that you can't migrate technicians to the group that you're deleting
    $data = array("groupID" => $groupID);
    $sql = "SELECT groupID as value, groupName as name FROM helpDeskTechGroups WHERE groupID!=:groupID ORDER BY helpDeskTechGroups.groupID ASC"; 
    $result = $connection2->prepare($sql);
    $result->execute($data);

    //Make sure that there are other groups aside from the group being deleted
    if ($result->rowcount() == 0) {
        $page->addError(__('Cannot delete last technician group.'));
        exit();
    }

    //TODO: Add reference to the group that they have selected to delete, and the fancy red text warning that they're about to delete a group
    //Also TODO: yell at ray and then regret the life decisions that led me to working on this
    //Another possible function: Option to delete technicians entirely rather than migrate
    $form = Form::create('groupDelete',  $_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module']."/helpDesk_technicianGroupDeleteProcess.php?groupID=" . $groupID, 'post');
    $form->addHiddenValue('address', $_SESSION[$guid]['address']);

    $form->setFactory(DatabaseFormFactory::create($pdo));
    $row = $form->addRow();
    $row = $form->addRow();
        $row->addLabel('group', __('New Technician Group'))->description(__('The group to migrate any existing technicians of the old group to'));
        $row->addSelect('group')->fromQuery($pdo, $sql, $data)->placeholder()->isRequired(); 

    $row = $form->addRow();
    $row->addFooter();
    $row->addSubmit();

    echo $form->getOutput();
}
?>
