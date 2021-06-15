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

use Gibbon\Domain\System\LogGateway;
use Gibbon\Forms\Form;
use Gibbon\Services\Format;
use Gibbon\Tables\DataTable;

require_once __DIR__ . '/moduleFunctions.php';

$page->breadcrumbs->add(__('Statistics'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_statistics.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    //Default Data
    $d = new DateTime('first day of this month');
    $startDate = isset($_GET['startDate']) ? Format::dateConvert($_GET['startDate']) : $d->format('Y-m-d');
    $endDate = isset($_GET['endDate']) ? Format::dateConvert($_GET['endDate']) : date('Y-m-d');

    //Filter
    $form = Form::create('helpDeskStatistics', $session->get('absoluteURL') . '/index.php', 'get');
    $form->addHiddenValue('q', '/modules/' . $session->get('module') . '/helpdesk_statistics.php');
    $form->setTitle('Filter');

    $row = $form->addRow();
        $row->addLabel('startDate', __('Start Date Filter'));
        $row->addDate('startDate')
            ->setDateFromValue($startDate)
            ->chainedTo('endDate')
            ->required();

    $row = $form->addRow();
        $row->addLabel('endDate', __('End Date Filter'));
        $row->addDate('endDate')
            ->setDateFromValue($endDate)
            ->chainedFrom('startDate')
            ->required();

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();

    //Stat Collection
    $logGateway = $container->get(LogGateway::class);
    $criteria = $logGateway->newQueryCriteria()
        ->filterBy('module', 'Help Desk')
        ->filterBy('startDate', $startDate)
        ->filterBy('endDate', date('Y-m-d 23:59:59', strtotime($endDate)))
        ->sortBy('timestamp', 'DESC');

    $logs = $logGateway->queryLogs($criteria, $session->get('gibbonSchoolYearID'));

    $stats = statsOverview($logs);

    //Stat Table
    $table = DataTable::create('statistics');
    $table->setTitle('Statistics');

    $data = [
        'q' => '/modules/' . $session->get('module') . '/helpDesk_statisticsDetail.php',
        'title' => '',
        'startDate' => $startDate,
        'endDate' => $endDate,
    ];

    $table->addColumn('name', __('Name'))
        ->format(function ($row) use ($session, $data) {
            $data['title'] = $row['name'];
            return Format::link($session->get('absoluteURL') . '/index.php?' . http_build_query($data), $row['name']);
        });

    $table->addColumn('value', __('Value'));

    echo $table->render($stats);
}
?>
