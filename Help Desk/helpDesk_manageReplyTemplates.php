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
use Gibbon\Services\Format;
use Gibbon\Tables\DataTable;

require_once __DIR__ . '/moduleFunctions.php';

$page->breadcrumbs->add(__('Reply Templates'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageReplyTemplates.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    $moduleName = $session->get('module');

    $replyTemplateGateway = $container->get(ReplyTemplateGateway::class);

    $criteria = $replyTemplateGateway->newQueryCriteria()
        ->sortBy(['name'])
        ->fromPOST();

    $table = DataTable::createPaginated('replytemplates', $criteria);
    $table->setTitle(__('Reply Templates'));

    $table->addHeaderAction('add', __('Add'))
        ->setURL('/modules/' . $session->get('module') . '/helpDesk_addReplyTemplate.php')
        ->displayLabel();
    
    $table->addExpandableColumn('body')
        ->format(function ($replyTemplate) {
            $output = '';

            $output .= formatExpandableSection(__('Reply Template Content'), $replyTemplate['body']);

            return $output;
        });
    
    $table->addColumn('name', __('Reply Template Name'));

    $table->addActionColumn()
        ->addParam('helpDeskReplyTemplateID')
        ->format(function ($riskTemplate, $actions) use ($moduleName) {
            $actions->addAction('edit', __('Edit'))
                ->setURL('/modules/' . $moduleName . '/helpDesk_editReplyTemplate.php');

            $actions->addAction('delete', __('Delete'))
                ->setURL('/modules/' . $moduleName . '/helpDesk_deleteReplyTemplate.php');
        });

    echo $table->render($replyTemplateGateway->queryTemplates($criteria));
}   
?>
