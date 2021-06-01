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
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;
use Gibbon\Module\HelpDesk\Domain\TechnicianGateway;
use Gibbon\Services\Format;

$page->breadcrumbs
    ->add(__('Manage Technicians'), 'helpDesk_manageTechnicians.php')
    ->add(__('Create Technician'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageTechnicians.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    $page->return->setEditLink($gibbon->session->get('absoluteURL') . '/index.php?q=/modules/' . $gibbon->session->get('module') . '/helpDesk_manageTechnicians.php');

    //Get Tech Groups    
    $techGroupGateway = $container->get(TechGroupGateway::class);
    $groups = $techGroupGateway->selectBy([], ['groupID as value', 'groupName as name']);

    //Get Non-Technicians
    $technicianGateway = $container->get(TechnicianGateway::class);

    $users = array_reduce($technicianGateway->selectNonTechnicians()->fetchAll(), function ($group, $item) {
        $group[$item['gibbonPersonID']] = Format::name('', $item['preferredName'], $item['surname'], 'Student', true) . ' (' . $item['username'] . ', ' . __($item['category']) . ')';
        return $group;
    }, []);

    $form = Form::create('createTechnician',  $gibbon->session->get('absoluteURL') . '/modules/' . $gibbon->session->get('module') . '/helpDesk_createTechnicianProcess.php', 'post');
    $form->addHiddenValue('address', $gibbon->session->get('address'));

    $row = $form->addRow();
        $row->addLabel('person', __('Person'));
        $row->addSelectPerson('person')
            ->fromArray($users)
            ->placeholder()
            ->required();

    $row = $form->addRow();
        $row->addLabel('group', __('Technician Group'));
        $row->addSelect('group')
            ->fromResults($groups)
            ->placeholder()
            ->required(); 

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();
}
?>
