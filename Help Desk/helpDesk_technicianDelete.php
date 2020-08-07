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
use Gibbon\Forms\Prefab\DeleteForm;
@session_start() ;

include './modules/'.$_SESSION[$guid]['module'].'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicians.php") == false) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    $page->breadcrumbs->add(__('Manage Technicians'), 'helpDesk_manageTechnicians.php');
    $page->breadcrumbs->add(__('Delete Technician'));

    $highestAction = getHighestGroupedAction($guid, "/modules/Help Desk/helpDesk_manageTechnicians.php", $connection2) ;
    if ($highestAction == false) {
        $page->addError(__('The highest grouped action cannot be determined.'));
        exit();
    }

    if ($highestAction != "Manage Technicians") {
        $page->addError(__('You do not have access to this action.'));
        exit();
    }

    $technicianID = null;
    if (isset($_GET["technicianID"])) {
        $technicianID = $_GET["technicianID"];
    } else {
        $page->addError(__('No technician selected.'));
        exit();
    }
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }
   
    $form = DeleteForm::createForm($_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module']."/helpDesk_technicianDeleteProcess.php?technicianID=" . $technicianID);
    echo $form->getOutput();
}
?>
