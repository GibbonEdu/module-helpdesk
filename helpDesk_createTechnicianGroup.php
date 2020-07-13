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

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicianGroup.php") == FALSE) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    $page->breadcrumbs
        ->add(__('Manage Technician Groups'), 'helpDesk_manageTechnicianGroup.php')
        ->add(__('Create Technician Group'));

    if (isset($_GET['return'])) {
        $editLink = null;
        if (isset($_GET['groupID'])) {
            $groupID = $_GET['groupID'];
            $editLink = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/helpDesk_editTechnicianGroup.php&groupID=$groupID";
        }
        returnProcess($guid, $_GET['return'], $editLink, null);
    }

    $form = Form::create('createTechnicianGroup',  $_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module'].'/helpDesk_createTechnicianGroupProcess.php', 'post');
    $form->addHiddenValue('address', $_SESSION[$guid]['address']);
    $row = $form->addRow();
        $row->addLabel('groupName', __('Group Name'));
        $row->addTextField('groupName')->isRequired();
    $row = $form->addRow();
    $row->addFooter();
    $row->addSubmit();

    echo $form->getOutput();
}
?>