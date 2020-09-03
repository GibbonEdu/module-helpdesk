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

$page->breadcrumbs
        ->add(__('Manage Technicians'), 'helpDesk_manageTechnicians.php')
        ->add(__('Delete Technician'));

if (!isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicians.php")) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    if (isset($_GET["technicianID"])) {
        $technicianID = $_GET["technicianID"];
        $form = DeleteForm::createForm($_SESSION[$guid]['absoluteURL'] . '/modules/' . $_SESSION[$guid]['module'] . "/helpDesk_technicianDeleteProcess.php?technicianID=" . $technicianID);
        echo $form->getOutput();
    } else {
        $page->addError(__('No technician selected.'));
    }
}
?>
