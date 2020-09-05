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
use Gibbon\Forms\Form;
use Gibbon\Services\Format;
use Gibbon\Domain\System\LogGateway;
use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;
use Gibbon\Module\HelpDesk\Domain\IssueDiscussGateway;

require_once __DIR__ . '/moduleFunctions.php';

$page->breadcrumbs
    ->add(__('Statistics'), 'helpDesk_statistics.php')
    ->add(__('Detailed Statistics'));

if (!isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicians.php")) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    if (isset($_GET["title"])) {
        $title = $_GET["title"];
        $URL = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]['module'];

        $extras = array();

        if ($title == "Issue Created" || $title == "Issue Accepted" || $title == "Issue Reincarnated" || $title == "Issue Resolved") {
            $extra = "Issue ID";
            $extraKey = "issueID";
            $extraString = "<a href='" . $URL . "/issues_discussView.php&issueID=%extraInfo%" ."'>%extraInfo%</a>";
            $extras[0] = array('extra' => $extra, 'extraKey' => $extraKey, 'extraString' => $extraString);
        } else if ($title == "Issue Created (for Another Person)") {
            $extra = "Issue ID";
            $extraKey = "issueID";
            $extraString = "<a href='" . $URL . "/issues_discussView.php&issueID=%extraInfo%" ."'>%extraInfo%</a>";
            $extras[0] = array('extra' => $extra, 'extraKey' => $extraKey, 'extraString' => $extraString);

            $extra = "Technician Name";
            $extraKey = "technicainID";
            $extraString = "%techName%";
            $extras[1] = array('extra' => $extra, 'extraKey' => $extraKey, 'extraString' => $extraString);
        } else if ($title == "Technician Assigned") {
            $extra = "Issue ID";
            $extraKey = "issueID";
            $extraString = "<a href='" . $URL . "/issues_discussView.php&issueID=%extraInfo%" ."'>%extraInfo%</a>";
            $extras[0] = array('extra' => $extra, 'extraKey' => $extraKey, 'extraString' => $extraString);

            $extra = "Technician Name";
            $extraKey = "technicainID";
            $extraString = "%techName%";
            $extras[1] = array('extra' => $extra, 'extraKey' => $extraKey, 'extraString' => $extraString);
        } else if ($title == "Discussion Posted") {
            $extra = "Issue Discuss ID";
            $extraKey = "issueDiscussID";
            $extraString = "<a href='" . $URL . "/issues_discussView.php&issueID=%IDfromPost%&issueDiscussID=%extraInfo%" ."'>View</a>";
            $extras[0] = array('extra' => $extra, 'extraKey' => $extraKey, 'extraString' => $extraString);
        } else if ($title == "Technician Group Added" || $title == "Technician Group Edited") {
            $extra = "Group";
            $extraKey = "groupID";
            $extraString = "<a href='" . $URL . "/helpDesk_manageTechnicianGroup.php&groupID=%extraInfo%" ."'>%groupName%</a>";
            $extras[0] = array('extra' => $extra, 'extraKey' => $extraKey, 'extraString' => $extraString);
        } else if ($title == "Technician Added") {
            $extra = "Technician Name";
            $extraKey = "gibbonPersonID";
            $extraString = "%personName%";
            $extras[0] = array('extra' => $extra, 'extraKey' => $extraKey, 'extraString' => $extraString);
        } else if ($title == "Technician Group Set") {
            $extra = "Group";
            $extraKey = "groupID";
            $extraString = "<a href='" . $URL . "/helpDesk_manageTechnicianGroup.php&groupID=%extraInfo%" ."'>%groupName%</a>";
            $extras[0] = array('extra' => $extra, 'extraKey' => $extraKey, 'extraString' => $extraString);

            $extra = "Technician Name";
            $extraKey = "technicianID";
            $extraString = "%techName%";
            $extras[1] = array('extra' => $extra, 'extraKey' => $extraKey, 'extraString' => $extraString);
        } else if ($title == "Technician Removed") {
            $extra = "Person";
            $extraKey = "gibbonPersonID";
            $extraString = "%personName%";
            $extras[0] = array('extra' => $extra, 'extraKey' => $extraKey, 'extraString' => $extraString);
        } else if ($title == "Technician Group Removed") {
            $extra = "New Group";
            $extraKey = "newGroupID";
            $extraString = "<a href='" . $URL . "/helpDesk_manageTechnicianGroup.php&groupID=%extraInfo%" ."'>%groupName%</a>";
            $extras[0] = array('extra' => $extra, 'extraKey' => $extraKey, 'extraString' => $extraString);
        }

        //Default Data
        $d = new DateTime('first day of this month');
        $startDate = isset($_GET['startDate']) ? Format::dateConvert($_GET['startDate']) : $d->format('Y-m-d');
        $endDate = isset($_GET['endDate']) ? Format::dateConvert($_GET['endDate']) : date("Y-m-d");

        //Filter
        $form = Form::create('helpDeskStatistics', $_SESSION[$guid]['absoluteURL'] . '/index.php', 'get');

        $form->setTitle('Filter');
        $form->addHiddenValue('q', '/modules/' . $_SESSION[$guid]['module'] . '/helpdesk_statisticsDetail.php');
        $form->addHiddenValue('title', $title);

        $row = $form->addRow();
            $row->addLabel('startDate', __("Start Date Filter"));
            $row->addDate('startDate')
                ->setDateFromValue($startDate)
                ->chainedTo('endDate')
                ->required();

        $row = $form->addRow();
            $row->addLabel('endDate', __("End Date Filter"));
            $row->addDate('endDate')
                ->setDateFromValue($endDate)
                ->chainedFrom('startDate')
                ->required();

        $row = $form->addRow();
            $row->addFooter();
            $row->addSubmit();

        echo $form->getOutput();

        $logGateway = $container->get(LogGateway::class);
        $criteria = $logGateway->newQueryCriteria(true)
            ->sortBy('timestamp', 'DESC')
            ->filterBy('module', 'Help Desk')
            ->filterBy('title', $title)
            ->filterBy('startDate', $startDate)
            ->filterBy('endDate', date('Y-m-d 23:59:59', strtotime($endDate)))
            ->fromPOST();

        $criteria->addFilterRules([
            'module' => function ($query, $module) {
                return $query
                    ->where('gibbonModule.name = :module')
                    ->bindValue('module', $module);
            },
            'startDate' => function ($query, $startDate) {
                return $query
                    ->where('timestamp >= :startDate')
                    ->bindValue('startDate', $startDate);
            },
            'endDate' => function ($query, $endDate) {
                return $query
                    ->where('timestamp <= :endDate')
                    ->bindValue('endDate', $endDate);
            },
        ]);

        $logs = $logGateway->queryLogs($criteria, $gibbon->session->get('gibbonSchoolYearID'));
        
        $table = DataTable::createPaginated('detailedStats', $criteria);
        $table->setTitle(__($title));

        $table->addColumn('timestamp', __('Timestamp'))
                ->format(Format::using('dateTime', ['timestamp']));

        $table->addColumn('person', __('Person'))
                ->format(Format::using('name', ['title', 'preferredName', 'surname', 'Student']));

        $techGroupGateway = $container->get(TechGroupGateway::class);
        $issueDisucssGateway = $container->get(IssueDiscussGateway::class);
        //TODO: This is silly and doesn't seem to work for all things. Theres barely any error catching or really any good code here.
        //At some point this needs to be scrapped or built up from the ground, until then, too bad!
        foreach ($extras as $extra) {
            $table->addColumn($extra['extraKey'], __($extra['extra']))
                    ->format(function ($row) use ($connection2, $extra, $techGroupGateway, $issueDisucssGateway) {
                        $array = unserialize($row['serialisedArray']);
                        $eString = str_replace("%extraInfo%", $array[$extra['extraKey']], $extra['extraString']);

                        if (strpos($eString, "%groupName%") !== false) {
                            $eString = str_replace("%groupName%", $techGroupGateway->getByID($array[$extra['extraKey']])['groupName'], $eString);
                        }

                        if (strpos($eString, "%techName%") !== false) {
                            $techName = getTechnicianName($connection2, $array[$extra['extraKey']]);
                            $eString = str_replace("%techName%", Format::name($techName['title'], $techName['preferredName'], $techName['surname'], 'Student'), $eString);
                        }

                        if (strpos($eString, "%personName%") !== false) {
                            $personName = getPersonName($connection2, $array[$extra['extraKey']]);
                            $eString = str_replace("%personName%", Format::name($personName['title'], $personName['preferredName'], $personName['surname'], 'Student'), $eString);
                        }

                        if (strpos($eString, "%IDfromPost%") !== false) {
                            $issueID = $issueDisucssGateway->getByID($array[$extra['extraKey']])['issueID'];
                            $eString = str_replace("%IDfromPost%", $issueID, $eString);
                        }

                        return $eString;
                    });
        }

        echo $table->render($logs);
    } else {
        $page->addError(__('No statistics selected.'));
    }
}
?>
