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

@session_start() ;

include './modules/'.$_SESSION[$guid]['module'].'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicians.php") == false) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
    print "</div>" ;
} else {
    $page->breadcrumbs->add(__('Statistics'), 'helpDesk_statistics.php');
    $page->breadcrumbs->add(__('Detailed Statistics'));

    if (isset($_GET["title"])) {
        $title = $_GET["title"];
        $URL = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk" ;

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
        $form = Form::create('helpDeskStatistics', $_SESSION[$guid]['absoluteURL'].'/index.php', 'get');

        $form->setTitle('Filter');
        $form->addHiddenValue('q', '/modules/'.$_SESSION[$guid]['module'].'/helpdesk_statisticsDetail.php');
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

        $result = getLog($connection2, $_SESSION[$guid]["gibbonSchoolYearID"], getModuleIDFromName($connection2, "Help Desk"), null, $title, $startDate, $endDate, null, null);

        print "<h3>";
            print "$title Statistics" ;
        print "</h3>";
        print "<table cellspacing='0' style='width: 100%'>" ;
            print "<tr class='head'>" ;
                print "<th>" ;
                    print __("Timestamp") ;
                print "</th>" ;
                print "<th>" ;
                    print __("Person") ;
                print "</th>" ;
                foreach ($extras as $extraArray) {
                    print "<th>" ;
                        print $extraArray['extra'] ;
                    print "</th>" ;
                }
            print "</tr>" ;

            if (!$result->rowcount() == 0) {
                $rowCount = 0;
                while ($row = $result->fetch()){
                    $class = "odd";
                    if ($rowCount%2 == 0) {
                        $class = "even";
                    }
                    print "<tr class='$class'>";
                        print "<td>";
                            print $row['timestamp'];
                        print "</td>";
                        print "<td>";
                            $row2 = getPersonName($connection2, $row['gibbonPersonID']);
                            print $row2['preferredName'] . " " . $row2['surname'];
                        print "</td>";
                        $array = unserialize($row['serialisedArray']);
                        if (!empty($array) && !empty($extras)) {
                            foreach ($extras as $extraArray) {
                                print "<td>";
                                    $eString = str_replace("%extraInfo%", $array[$extraArray['extraKey']], $extraArray['extraString']);

                                    if (strpos($eString, "%groupName%") !== false) {
                                        $eString = str_replace("%groupName%", getGroup($connection2, $array[$extraArray['extraKey']])['groupName'], $eString);
                                    }

                                    if (strpos($eString, "%techName%") !== false) {
                                        $techName = getTechnicianName($connection2, $array[$extraArray['extraKey']]);
                                        $eString = str_replace("%techName%", $techName['preferredName'] . " " . $techName['surname'], $eString);
                                    }

                                    if (strpos($eString, "%personName%") !== false) {
                                        $personName = getPersonName($connection2, $array[$extraArray['extraKey']]);
                                        $eString = str_replace("%personName%", $personName['preferredName'] . " " .$personName['surname'], $eString);
                                    }

                                    if (strpos($eString, "%IDfromPost%") !== false) {
                                        $issueID = getIssueIDFromPost($connection2, $array[$extraArray['extraKey']]);
                                        $eString = str_replace("%IDfromPost%", $issueID, $eString);
                                    }

                                    print $eString;
                                print "</td>";
                            }
                        }
                    print "</tr>" ;
                    $rowCount++;
                }
            } else {
                $colspan = 2 + count($extras);
                print "<tr>";
                    print "<td colspan= $colspan>";
                        print __("There are no records to display.");
                    print "</td>";
                print "</tr>";
            }
        print "</table>" ;
    } else {
        $page->addError(__('No statistics selected.'));
    }
}
?>
