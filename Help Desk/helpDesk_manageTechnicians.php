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

use Gibbon\Tables\DataTable;
use Gibbon\Services\Format;
use Gibbon\Module\HelpDesk\Domain\TechnicianGateway;
use Gibbon\Module\HelpDesk\Domain\IssueGateway;

$page->breadcrumbs->add(__('Manage Technicians'));

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageTechnicians.php')) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    $technicianGateway = $container->get(technicianGateway::class);
    $issueGateway = $container->get(IssueGateway::class); 

    $formatIssues = function($row) use ($guid, $issueGateway) {
        $issues = $issueGateway->selectIssueByTechnician($row['technicianID'])->fetchAll();
        if (count($issues) < 1) {
            return __('None');
        }

        $issues = array_map(function($issue) use ($guid) {
            return Format::link('./index.php?q=/modules/' . $_SESSION[$guid]['module'] . '/issues_discussView.php&issueID='. $issue['issueID'], $issue['issueName']);
        }, $issues);

        return implode(', ', $issues);
    };

    $table = DataTable::create('technicians');
    $table->setTitle('Technicians');

    $table->addHeaderAction('add', __('Add'))
            ->setURL('/modules/' . $_SESSION[$guid]['module'] . '/helpDesk_createTechnician.php')
            ->displayLabel();

    $table->addColumn('name', __('Name'))
            ->format(Format::using('name', ['title', 'preferredName', 'surname', 'Student', false, false]));
    
    $table->addColumn('workingOn', __('Working On'))->format($formatIssues);
    
    $table->addColumn('groupName', __('Group'));

    $table->addActionColumn()
            ->addParam('technicianID')
            ->format(function ($technician, $actions) use ($guid) {
                $actions->addAction('edit', __('Edit'))
                        ->setURL('/modules/' . $_SESSION[$guid]['module'] . '/helpDesk_setTechGroup.php');

                $actions->addAction('stats', __('Stats'))
                        ->setIcon('internalAssessment')
                        ->setURL('/modules/' . $_SESSION[$guid]['module'] . '/helpDesk_technicianStats.php');

                $actions->addAction('delete', __('Delete'))
                        ->setURL('/modules/' . $_SESSION[$guid]['module'] . '/helpDesk_technicianDelete.php');
            });

    echo $table->render($technicianGateway->selectTechnicians()->toDataSet());
}
?>
