<?php
/*
Gibbon: the flexible, open school platform
Founded by Ross Parker at ICHK Secondary. Built by Ross Parker, Sandra Kuipers and the Gibbon community (https://gibbonedu.org/about/)
Copyright © 2010, Gibbon Foundation
Gibbon™, Gibbon Education Ltd. (Hong Kong)

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
use Gibbon\Services\Format;
use Gibbon\Module\HelpDesk\Domain\TechnicianGateway;

$page->breadcrumbs
        ->add(__('Manage Technicians'), 'helpDesk_manageTechnicians.php')
        ->add(__('Delete Technician'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageTechnicians.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    $technicianID = $_GET['technicianID'] ?? '';

    $technicianGateway = $container->get(TechnicianGateway::class);
    $values = $technicianGateway->getByID($technicianID);

    if (empty($technicianID) || empty($values)) {
        $page->addError(__('No Technician selected.'));
    } else {
        $techs = array_reduce($technicianGateway->selectTechnicians()->fetchAll(), function ($group, $item) {
            $group[$item['technicianID']] = Format::name($item['title'], $item['preferredName'], $item['surname'], 'Student', true) . ' (' . $item['groupName'] . ')';
            return $group;
        }, []);

        unset($techs[$technicianID]);
        $form = DeleteForm::createForm($session->get('absoluteURL') . '/modules/' . $session->get('module') . '/helpDesk_technicianDeleteProcess.php?technicianID=' . $technicianID, false, false);

        $form->addHiddenValue('address', $session->get('address'));
        $row = $form->addRow();
            $row->addLabel('newTechnicianID', __('New Technician'))
                ->description(__('Optionally select a new technician to reassign the to-be-deleted technician\'s issues. Note, if no technician is selected, assigned issues that are pending will be unassigned.'));
            $row->addSelect('newTechnicianID')
                ->fromArray($techs)
                ->placeholder();

        $row = $form->addRow();
            $row->addSubmit();

        echo $form->getOutput();
    } 
}
?>
