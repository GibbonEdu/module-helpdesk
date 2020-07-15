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

include __DIR__ . '/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicians.php") == false) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    $page->breadcrumbs->add(__('Manage Technicians'), 'helpDesk_manageTechnicians.php');
    $page->breadcrumbs->add(__('Edit Technician'));

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    if (isset($_GET["technicianID"])) {
        $technicianID = $_GET["technicianID"];
    } else {
        $page->addError(__('No Technician selected.'));
        exit();
    }

        $allPeople = getAllPeople($connection2, true);
        $data = array();
        $sql = "SELECT groupID as value, groupName as name FROM helpDeskTechGroups ORDER BY helpDeskTechGroups.groupID ASC";
        $data2 = array("technicianID"=>$technicianID);
        $sql2 = "SELECT * FROM helpDeskTechnicians WHERE technicianID = :technicianID";
        $result2 = $connection2->prepare($sql2);
        $result2->execute($data2);
        $values=$result2->fetch();


        $form = Form::create('setTechGroup',  $_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module'].'/helpDesk_setTechGroupProcess.php?technicianID=' . $technicianID, 'post');
        $form->addHiddenValue('address', $_SESSION[$guid]['address']);
        $form->setFactory(DatabaseFormFactory::create($pdo));

        $row = $form->addRow();
            $row->addLabel('group', __('Technician Group'));
            $row->addSelect('group')->fromQuery($pdo, $sql, $data)->setValue($values['groupID'])->isRequired(); 

        $form->loadAllValuesFrom($values);
        $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

        echo $form->getOutput();
}
?>
