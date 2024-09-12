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

$page->breadcrumbs
    ->add(__('Manage Technician Groups'), 'helpDesk_manageTechnicianGroup.php')
    ->add(__('Create Technician Group'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageTechnicianGroup.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    if (isset($_GET['groupID'])) {
        $page->return->setEditLink($session->get('absoluteURL') . '/index.php?q=/modules/'  . $session->get('module') .  '/helpDesk_editTechnicianGroup.php&groupID=' . $_GET['groupID']);
    }

    $form = Form::create('createTechnicianGroup',  $session->get('absoluteURL') . '/modules/' . $session->get('module') . '/helpDesk_createTechnicianGroupProcess.php', 'post');
    $form->addHiddenValue('address', $session->get('address'));

    $row = $form->addRow();
        $row->addLabel('groupName', __('Group Name'));
        $row->addTextField('groupName')
            ->uniqueField('./modules/' . $session->get('module') . '/helpDesk_createTechnicianGroupAjax.php')
            ->required();

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();
}
?>
