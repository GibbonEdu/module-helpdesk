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
use Gibbon\Forms\DatabaseFormFactory;
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;

$page->breadcrumbs
    ->add(__('Manage Technician Groups'), 'helpDesk_manageTechnicianGroup.php')
    ->add(__('Delete Technician Group'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageTechnicianGroup.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    $groupID = $_GET['groupID'] ?? '';

    $techGroupGateway = $container->get(TechGroupGateway::class);
    $values = $techGroupGateway->getByID($groupID);
    //Proceed!
    if (empty($groupID) || !$techGroupGateway->exists($groupID)) {
        $page->addError(__('No group selected.'));
    } else {
        $data = ['groupID' => $groupID];
        $sql = 'SELECT groupID as value, groupName as name FROM helpDeskTechGroups WHERE groupID!=:groupID ORDER BY helpDeskTechGroups.groupID ASC'; 

        //Make sure that there are other groups aside from the group being deleted
        if ($techGroupGateway->countAll() > 1) {
            $form = DeleteForm::createForm($session->get('absoluteURL') . '/modules/' . $session->get('module') . "/helpDesk_technicianGroupDeleteProcess.php", false, false);
            $form->addHiddenValue('groupID', $groupID);
            $form->addHiddenValue('address', $session->get('address'));
            $form->setTitle(__($values['groupName']));
            $row = $form->addRow();
                $row->addLabel('group', __('New Technician Group'))
                    ->description(__('The group to migrate any existing technicians of the old group to'));
                $row->addSelect('group')
                    ->fromQuery($pdo, $sql, $data)
                    ->placeholder()
                    ->required(); 

            $row = $form->addRow();
                $row->addFooter();
                $row->addSubmit();

            echo $form->getOutput();
        } else {
            $page->addError(__('Cannot delete last technician group.'));
        }
    }
}
?>
