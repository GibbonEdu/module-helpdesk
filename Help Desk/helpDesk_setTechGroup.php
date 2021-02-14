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
use Gibbon\Module\HelpDesk\Domain\TechnicianGateway;

$page->breadcrumbs
    ->add(__('Manage Technicians'), 'helpDesk_manageTechnicians.php')
    ->add(__('Edit Technician'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageTechnicians.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    $technicianID = $_GET['technicianID'] ?? '';

    $technicianGateway = $container->get(TechnicianGateway::class);
    $values = $technicianGateway->getByID($technicianID);

    if (empty($technicianID) || empty($values)) {
        $page->addError(__('No Technician selected.'));
    } else {
        $sql = 'SELECT groupID as value, groupName as name FROM helpDeskTechGroups ORDER BY helpDeskTechGroups.groupID ASC';

        $form = Form::create('setTechGroup',  $gibbon->session->get('absoluteURL') . '/modules/' . $gibbon->session->get('module') . '/helpDesk_setTechGroupProcess.php?technicianID=' . $technicianID, 'post');
        $form->addHiddenValue('address', $gibbon->session->get('address'));

        $row = $form->addRow();
            $row->addLabel('group', __('Technician Group'));
            $row->addSelect('group')
                ->fromQuery($pdo, $sql, [])
                ->selected($values['groupID'])
                ->required(); 

        $form->loadAllValuesFrom($values);
        
        $row = $form->addRow();
            $row->addFooter();
            $row->addSubmit();

        echo $form->getOutput();
    }
}
?>
