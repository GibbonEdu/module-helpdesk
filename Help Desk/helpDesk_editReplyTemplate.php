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

use Gibbon\Module\HelpDesk\Domain\ReplyTemplateGateway;
use Gibbon\Forms\Form;

$page->breadcrumbs
    ->add(__('Reply Templates'), 'helpDesk_manageReplyTemplates.php')
    ->add(__('Edit Template'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageReplyTemplates.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    $replyTemplateGateway = $container->get(ReplyTemplateGateway::class);

    $helpDeskReplyTemplateID = $_GET['helpDeskReplyTemplateID'] ?? '';
    $replyTemplate = $replyTemplateGateway->getByID($helpDeskReplyTemplateID);

    if (empty($replyTemplate)) {
        $page->addError(__('Invalid Reply Template.'));
    } else {
        $form = Form::create('editRiskTemplate', $session->get('absoluteURL') . '/modules/' . $session->get('module') . '/helpDesk_editReplyTemplateProcess.php');
        $form->addHiddenValue('address', $session->get('address'));
        $form->addHiddenValue('helpDeskReplyTemplateID', $helpDeskReplyTemplateID);
        $form->setTitle('Edit Reply Template');

        $row = $form->addRow();
            $row->addLabel('name', 'Name');
            $row->addTextfield('name')
                ->setRequired(true)
                ->maxLength(30)
                ->uniqueField('./modules/' . $session->get('module') . '/helpDesk_addReplyTemplateAjax.php', ['currentTemplateName' => $replyTemplate['name']])
                ->setValue($replyTemplate['name']);

        $row = $form->addRow();
            $column = $row->addColumn();
            $column->addLabel('body', 'Body');
            $column->addEditor('body', $guid)
                ->setRequired(true)
                ->showMedia()
                ->setValue($replyTemplate['body']);

        $row = $form->addRow();
            $row->addFooter();
            $row->addSubmit();

        print $form->getOutput();
    }
}   
?>
