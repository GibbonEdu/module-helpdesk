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
use Gibbon\Tables\DataTable;
use Gibbon\Forms\Form;
use Gibbon\Services\Format;
use Gibbon\Module\HelpDesk\Domain\TechnicianGateway;

$page->breadcrumbs
    ->add(__('Manage Technicians'), 'helpDesk_manageTechnicians.php')
    ->add(__('Techncian Statistics'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageTechnicians.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    $technicianID = $_GET['technicianID'] ?? '';

    $technicianGateway = $container->get(TechnicianGateway::class);
    $technician = $technicianGateway->getTechnician($technicianID);

    if (empty($technicianID) || $technician->isEmpty()) {
        $page->addError(__('No Technician Selected.'));
    } else {
        $technician = $technician->fetch();
        echo '<h3>';
            echo Format::name($technician['title'], $technician['preferredName'], $technician['surname'], 'Student');
        echo '</h3>';

        //Default Data
        $d = new DateTime('first day of this month');
        $startDate = isset($_GET['startDate']) ? Format::dateConvert($_GET['startDate']) : $d->format('Y-m-d');
        $endDate = isset($_GET['endDate']) ? Format::dateConvert($_GET['endDate']) : date('Y-m-d');

        //Filter
        $form = Form::create('helpDeskStatistics', $gibbon->session->get('absoluteURL') . '/index.php', 'get');

        $form->setTitle('Filter');
        $form->addHiddenValue('q', '/modules/' . $gibbon->session->get('module') . '/helpdesk_technicianStats.php');
        $form->addHiddenValue('technicianID', $technicianID);

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

        //Stats collection

        //TODO: Migrate this to a gateway and honestly fix it, because it seems like it doesn't work anyways.
        $logGateway = $container->get(LogGateway::class);

        $logCriteria = $logGateway->newQueryCriteria(false)
            ->filterBy('module', 'Help Desk')
            ->filterBy('startDate', $startDate)
            ->filterBy('endDate', date('Y-m-d 23:59:59', strtotime($endDate)))
            ->filterBy('array', serialize(['technicianID' => $technicianID]))
            ->sortBy(['timestamp']);

        $result = $logGateway->queryLogs($logCriteria, $gibbon->session->get('gibbonSchoolYearID'));
        $rArray = $result->toArray();

        $items = [];

        foreach($rArray as $row) {
            if (!isset($items[$row['title']])) {
                $items[$row['title']] = 1;
            } else {
                $items[$row['title']] = $items[$row['title']]+1;
            }
        }
        ksort($items);

        $display = [];
        foreach ($items as $key => $value) {
            array_push($display, ['name' => $key, 'value' => $value]);
        }

        $table = DataTable::create('simpleStats');
        $table->setTitle(__('Simple Statistics'));

        $table->addColumn('name', __('Action Title'));

        $table->addColumn('value', __('Action Count'));

        echo $table->render($display);

        $table = DataTable::create('detailedStats');
        $table->setTitle('Detailed Statistics');

        $table->addColumn('timestamp', __('Timestamp'))
            ->format(Format::using('dateTime', ['timestamp']));

        $table->addColumn('title', __('Action Title'));

        echo $table->render($rArray);
    }
}
?>
