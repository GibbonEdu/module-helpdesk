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
    $noData = false;

    $URL = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk" ;

    if (isset($_GET["technicianID"])) {
        $technicianID = $_GET["technicianID"];
    } else {
        $noData = true;
    }

    //Proceed!
    $page->breadcrumbs->add(__('Manage Technicians'), 'helpDesk_manageTechnicians.php');
    $page->breadcrumbs->add(__('Techncian Statistics'));

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

    $result = getLog($connection2, $_SESSION[$guid]["gibbonSchoolYearID"], getModuleIDFromName($connection2, "Help Desk"), null, null, $startDate, $endDate, null, array("technicianID"=>$technicianID));
    $rArray = $result->fetchAll();
    print "<h3>" ;
        print __("Filter") ;
    print "</h3>" ;
    print "<form method='post' action='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "&technicianID=$technicianID'>" ;
        print"<table class='noIntBorder' cellspacing='0' style='width: 100%'>" ;
            print "<tr>";
                print "<td> ";
                    print "<b>". __('Start Date Filter') ."</b><br/>";
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
                    print "<b>".  __('End Date Filter') ."</b><br/>";
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
                    print "<input type='submit' value='" . __('Go') . "'>" ;
                print "</td>" ;
            print "</tr>" ;
        print"</table>" ;
    print "</form>" ;

    $techName = getTechnicianName($connection2, $technicianID);
    $name = $techName['preferredName'] . " " . $techName['surname'] . "'";
    if (substr($techName['surname'], -1) != 's') {
        $name .= "s";
    }

    print "<h3>";
        print "$name Simple Statistics" ;
    print "</h3>";
    print "<table cellspacing='0' style='width: 100%'>" ;
        print "<tr class='head'>" ;
            print "<th>" ;
                print __("Action Title") ;
            print "</th>" ;
            print "<th>" ;
                print __("Action Count") ;
            print "</th>" ;
        print "</tr>" ;

        $times = array();

        foreach($rArray as $row) {
            if (!isset($items[$row['title']])) {
                $items[$row['title']] = 1;
            } else {
                $items[$row['title']] = $items[$row['title']]+1;
            }
        }

        if (!$result->rowcount() == 0) {
            $rowCount = 0;
            foreach ($items as $key=>$val) {
                $class = "odd";
                if ($rowCount%2 == 0) {
                    $class = "even";
                }
                print "<tr class='$class'>";
                    print "<td>";
                        print $key;
                    print "</td>";
                    print "<td>";
                        print $val;
                    print "</td>";
                print "</tr>" ;
                $rowCount++;
            }
        } else {
            print "<tr>";
                print "<td colspan=2>";
                    print __("There are no records to display.");
                print "</td>";
            print "</tr>";
        }
    print "</table>" ;

    print "<h3>";
        print "$name Detailed Statistics" ;
    print "</h3>";
    print "<table cellspacing='0' style='width: 100%'>" ;
        print "<tr class='head'>" ;
            print "<th>" ;
                print __("Timestamp") ;
            print "</th>" ;
            print "<th>" ;
                print __("Action Title") ;
            print "</th>" ;
        print "</tr>" ;

        if (!$result->rowcount() == 0) {
            $rowCount = 0;
            foreach ($rArray as $row) {
            $class = "odd";
            if ($rowCount%2 == 0) {
                $class = "even";
            }
            print "<tr class='$class'>";
                print "<td>";
                    print $row['timestamp'];
                print "</td>";
                print "<td>";
                    print $row['title'];
                print "</td>";
            print "</tr>" ;
            $rowCount++;
        }
    } else {
        print "<tr>";
            print "<td colspan=2>";
                print __("There are no records to display.");
            print "</td>";
        print "</tr>";
    }
    print "</table>" ;
}
?>
