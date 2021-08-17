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

$page->breadcrumbs
    ->add(__('Reply Templates'), 'helpDesk_manageReplyTemplates.php')
    ->add(__('Add Template'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageReplyTemplates.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    if(isset($_GET['helpDeskReplyTemplateID'])) {
        $page->return->setEditLink($session->get('absoluteURL') . '/index.php?q=/modules/' . $session->get('module') . '/helpDesk_editReplyTemplate.php&helpDeskReplyTemplateID=' . $_GET['helpDeskReplyTemplateID']);
    }

    $form = Form::create('addReplyTemplate', $session->get('absoluteURL') . '/modules/' . $session->get('module') . '/helpDesk_addReplyTemplateProcess.php');
    $form->addHiddenValue('address', $session->get('address'));
    $form->setTitle('Add Reply Template');

    $row = $form->addRow();
        $row->addLabel('name', 'Name');
        $row->addTextfield('name')
            ->setRequired(true)
            ->maxLength(30)
            ->uniqueField('./modules/' . $session->get('module') . '/helpDesk_addReplyTemplateAjax.php');

    $row = $form->addRow();
        $column = $row->addColumn();
        $column->addLabel('body', 'Body');
        $column->addEditor('body', $guid)
            ->showMedia()
            ->setRequired(true);

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    print $form->getOutput();
}   
?>
