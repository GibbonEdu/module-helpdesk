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
use Gibbon\Services\Format;

$page->breadcrumbs
        ->add(__('Manage Technicians'), 'helpDesk_manageTechnicians.php')
        ->add(__('Create Technician'));

if (!isActionAccessible($guid, $connection2, '/modules/' . $_SESSION[$guid]['module'] . '/helpDesk_manageTechnicians.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], $_SESSION[$guid]["absoluteURL"] . '/index.php?q=/modules/' . $_SESSION[$guid]['module'] . '/helpDesk_manageTechnicians.php', null);
    }

    $data = array();
    $groupSql = "SELECT groupID as value, groupName as name 
                    FROM helpDeskTechGroups 
                    ORDER BY helpDeskTechGroups.groupID ASC";

    $peopleSql = "SELECT gibbonPerson.gibbonPersonID, title, surname, preferredName, username, gibbonRole.category
                        FROM gibbonPerson 
                        JOIN gibbonRole ON (gibbonRole.gibbonRoleID=gibbonPerson.gibbonRoleIDPrimary)
                        LEFT JOIN helpDeskTechnicians ON (helpDeskTechnicians.gibbonPersonID=gibbonPerson.gibbonPersonID) 
                        WHERE status='Full' 
                        AND helpDeskTechnicians.gibbonPersonID IS NULL
                        ORDER BY surname, preferredName";

    $result = $pdo->executeQuery(array(), $peopleSql);
    $users = array_reduce($result->fetchAll(), function ($group, $item) {
        $group[$item['gibbonPersonID']] = Format::name('', $item['preferredName'], $item['surname'], 'Student', true).' ('.$item['username'].', '.__($item['category']).')';
        return $group;
    }, array());

    $form = Form::create('createTechnician',  $_SESSION[$guid]['absoluteURL'].'/modules/'.$_SESSION[$guid]['module'].'/helpDesk_createTechnicianProcess.php', 'post');
    $form->addHiddenValue('address', $_SESSION[$guid]['address']);

    $row = $form->addRow();
        $row->addLabel('person', __('Person'));
        $row->addSelectPerson('person')
            ->fromArray($users)
            ->placeholder()
            ->isRequired();

    $row = $form->addRow();
        $row->addLabel('group', __('Technician Group'));
        $row->addSelect('group')
            ->fromQuery($pdo, $groupSql, $data)
            ->placeholder()
            ->isRequired(); 

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();
}
?>
