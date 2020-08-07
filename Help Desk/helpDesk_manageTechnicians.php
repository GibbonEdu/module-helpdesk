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

@session_start() ;

include './modules/'.$_SESSION[$guid]['module'].'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicians.php") == false) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    //Proceed!
    $page->breadcrumbs->add(__('Manage Technicians'));

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    try {
        $data = array();
        $sql = "SELECT helpDeskTechnicians.* , surname, preferredName, title, groupName FROM helpDeskTechnicians JOIN gibbonPerson ON (helpDeskTechnicians.gibbonPersonID=gibbonPerson.gibbonPersonID) JOIN helpDeskTechGroups ON (helpDeskTechnicians.groupID=helpDeskTechGroups.groupID) ORDER BY helpDeskTechnicians.technicianID ASC";
        $result = $connection2->prepare($sql);
        $result->execute($data);

        $sql2 = "SELECT helpDeskIssue.technicianID , issueName , issueID FROM helpDeskIssue JOIN helpDeskTechnicians ON (helpDeskTechnicians.technicianID=helpDeskIssue.technicianID) ORDER BY helpDeskIssue.issueID ASC";
        $result2 = $connection2->prepare($sql2);
        $result2->execute($data);
    } catch (PDOException $e) {
    }

    print "<div class='linkTop'>" ;
        print "<a style='position:relative; bottom:5px;float:right;' href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_createTechnician.php'><img style='margin-left: 2px' title=" . __('Create ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_new.png'/></a>";
        print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/helpDesk_createTechnician.php'>" .  __('Create') . "</a>";
    print "</div>" ;

    print "<h3>";
        print "Technicians" ;
    print "</h3>";
    print "<table cellspacing='0' style='width: 100%'>" ;
        print "<tr class='head'>" ;
            print "<th>" ;
                print __("Name") ;
            print "</th>" ;
            print "<th>" ;
                print __("Working On") ;
            print "</th>" ;
            print "<th>" ;
                print __("Group") ;
            print "</th>" ;
            print "<th>" ;
                print __("Action") ;
            print "</th>" ;
        print "</tr>" ;

        if (!$result->rowcount() == 0) {
            $rowCount = 0;
            while ($row = $result->fetch()) {
                if ($rowCount % 2 == 0) {
                    $class = "even";
                } else {
                    $class = "odd";
                }
                print"<tr class=$class>";
                    print "<td>". formatName($row['title'],$row['preferredName'],$row['surname'], "Student", false, false) ."</td>" ;
                    print "<td> ";
                        $issues = "";
                        while($row2 = $result2->fetch()){
                            $issues .= "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=". $row2["issueID"] . "'>". $row2["issueName"] . "</a>, ";
                        }
                        $issue = substr($issues, 0, strlen($issues)-2);
                        if (strlen($issue) > 0) {
                            print $issue;
                        } else {
                            print "None";
                        }
                    print "</td>";
                    print "<td>";
                        print $row["groupName"];
                    print "</td>";
                    print "<td>";
                        print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_setTechGroup.php&technicianID=". $row['technicianID'] ."'><img title=" . __('Edit ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a>";
                        print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_technicianStats.php&technicianID=". $row['technicianID'] ."'><img title=" . __('Stats  ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/internalAssessment.png'/></a>";
                        print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_technicianDelete.php&technicianID=". $row['technicianID'] ."'><img title=" . __('Delete  ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/garbage.png'/></a>";
                    print "</td>";
                print "</tr>" ;
                $rowCount++;
            }
        } else {
            print "<tr>";
                print "<td colspan= 4>";
                    print __("There are no records to display.");
                print "</td>";
            print "</tr>";
        }
    print "</table>" ;
}
?>
