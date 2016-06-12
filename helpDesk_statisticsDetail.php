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

include "./modules/Help Desk/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicians.php") == FALSE) {
    //Acess denied
    print "<div class='error'>" ;
        print __($guid, "You do not have access to this action.") ;
    print "</div>" ;
} else {
    $URL = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk" ;
    if (isset($_GET["title"])) {
        $title = $_GET["title"];
    } else {
        print "<div class='error'>" ;
            print __($guid, "No statistics selected.") ;
        print "</div>" ;
        exit();
    }
    //Proceed!
    print "<div class='trail'>" ;
        print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . __($guid, "Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . __($guid, getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_statistics.php'> Statistics</a> >      </div><div class='trailEnd'>" . __($guid, 'Detailed Statistics') . "</div>" ;
    print "</div>" ;

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

    $d = new DateTime('first day of this month');
    $startDate = dateConvertBack($guid, $d->format('Y-m-d H:i:s')) ;
    $endDate = dateConvertBack($guid, date("Y-m-d H:i:s")) ;

    if (isset($_POST["startDate"])) {
        $startDate = $_POST["startDate"];
    } else if (isset($_GET["startDate"])) {
        $startDate = $_GET["startDate"];
    }

    if (isset($_POST["endDate"])) {
        $endDate = $_POST["endDate"];
    } else if (isset($_GET["endDate"])) {
        $endDate = $_GET["endDate"];
    }

    $result = getLog($connection2, $_SESSION[$guid]["gibbonSchoolYearID"], getModuleIDFromName($connection2, "Help Desk"), null, $title, $startDate, $endDate, null, null);
    
    print "<h3>" ;
        print __($guid, "Filter") ;
    print "</h3>" ;
        print "<form method='post' action='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&title=$title'>" ;
            print"<table class='noIntBorder' cellspacing='0' style='width: 100%'>" ;
                print "<tr>";
                print "<td> ";
                    print "<b>". __($guid, 'Start Date Filter') ."</b><br/>";
                    print "<span style=\"font-size: 90%\"><i></i></span>";
                print "</td>";
                print "<td class=\"right\">";
                    print "<input name='startDate' id='startDate' maxlength=10 value='" . $startDate . "' type='text' style='height: 22px; width:100px; margin-right: 0px; float: none'></input>" ;
                    print "<script type=\"text/javascript\">" ;
                        print "var ttDate1=new LiveValidation('startDate');" ;
                        print "ttDate1.add( Validate.Format, {pattern:" ; 
                            if ($_SESSION[$guid]["i18n"]["dateFormatRegEx"] == "") {
                                print "/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i" ; 
                            } else { 
                                print $_SESSION[$guid]["i18n"]["dateFormatRegEx"] ; 
                            } 
                            print ", failureMessage: \"Use " ; 
                            if ($_SESSION[$guid]["i18n"]["dateFormat"] == "") {
                                print "dd/mm/yyyy" ; 
                            } else { 
                                print $_SESSION[$guid]["i18n"]["dateFormat"] ; 
                            } 
                        print ".\" } );" ;
                    print "</script>" ;
                    print "<script type=\"text/javascript\">" ;
                        print "$(function() {" ;
                            print "$(\"#startDate\").datepicker();" ;
                        print "});" ;
                    print "</script>" ;
                print "</td>";
            print "</tr>";
            print "<tr>";
                print "<td> ";
                    print "<b>".  __($guid, 'End Date Filter') ."</b><br/>";
                    print "<span style=\"font-size: 90%\"><i></i></span>";
                print "</td>";
                print "<td class=\"right\">";
                    print "<input name='endDate' id='endDate' maxlength=10 value='" . $endDate . "' type='text' style='height: 22px; width:100px; margin-right: 0px; float: none'></input>" ;
                    print "<script type=\"text/javascript\">" ;
                        print "var ttDate2=new LiveValidation('endDate');" ;
                        print "ttDate2.add( Validate.Format, {pattern:" ; 
                            if ($_SESSION[$guid]["i18n"]["dateFormatRegEx"] == "") {
                                print "/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i" ; 
                            } else { 
                                print $_SESSION[$guid]["i18n"]["dateFormatRegEx"] ; 
                            } 
                            print ", failureMessage: \"Use " ; 
                            if ($_SESSION[$guid]["i18n"]["dateFormat"] == "") {
                                print "dd/mm/yyyy" ; 
                            } else { 
                                print $_SESSION[$guid]["i18n"]["dateFormat"] ; 
                            } 
                        print ".\" } );" ;                       
                    print "</script>" ;
                    print "<script type=\"text/javascript\">" ;
                        print "$(function() {" ;
                            print "$(\"#endDate\").datepicker();" ;
                        print "});" ;
                    print "</script>" ;
                print "</td>";
            print "</tr>";
            print "<tr>" ;
                print "<td class='right' colspan=2>" ;
                    print "<input type='submit' value='" . __($guid, 'Go') . "'>" ;
                print "</td>" ;
            print "</tr>" ;
        print"</table>" ;
    print "</form>" ;
  
    print "<h3>";
        print "$title Statistics" ;
    print "</h3>";
    print "<table cellspacing='0' style='width: 100%'>" ;
        print "<tr class='head'>" ;
            print "<th>" ;
                print __($guid, "Timestamp") ;
            print "</th>" ;
            print "<th>" ;
                print __($guid, "Person") ;
            print "</th>" ;
            foreach ($extras as $extraArray) {
                print "<th>" ;
                    print $extraArray['extra'] ;
                print "</th>" ;
            }
        print "</tr>" ;

        if (!$result->rowcount() == 0){
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
                    print __($guid, "There are no records to display.");
                print "</td>";
            print "</tr>";
        }
    print "</table>" ;
}
?>
