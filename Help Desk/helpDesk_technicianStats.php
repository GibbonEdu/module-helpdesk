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
} else {
    //Proceed!
    $page->breadcrumbs->add(__('Manage Technicians'), 'helpDesk_manageTechnicians.php');
    $page->breadcrumbs->add(__('Techncian Statistics'));

    if (isset($_GET["technicianID"])) {
        $technicianID = $_GET["technicianID"];

        $techName = getTechnicianName($connection2, $technicianID);
        echo '<h3>';
        echo Format::name($techName['title'], $techName['preferredName'], $techName['surname'], 'Student');
        echo '</h3>';

        //Default Data
        $d = new DateTime('first day of this month');
        $startDate = isset($_GET['startDate']) ? Format::dateConvert($_GET['startDate']) : $d->format('Y-m-d');
        $endDate = isset($_GET['endDate']) ? Format::dateConvert($_GET['endDate']) : date("Y-m-d");

        //Filter
        $form = Form::create('helpDeskStatistics', $_SESSION[$guid]['absoluteURL'].'/index.php', 'get');

        $form->setTitle('Filter');
        $form->addHiddenValue('q', '/modules/'.$_SESSION[$guid]['module'].'/helpdesk_technicianStats.php');
        $form->addHiddenValue('technicianID', $technicianID);

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

        $result = getLog($connection2, $_SESSION[$guid]["gibbonSchoolYearID"], getModuleIDFromName($connection2, "Help Desk"), null, null, $startDate, $endDate, null, array("technicianID"=>$technicianID));
        $rArray = $result->fetchAll();

        print "<h3>";
            print "Simple Statistics" ;
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
            print "Detailed Statistics" ;
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
    } else {
        $page->addError(__('No Technician Selected.'));
    }
}
?>
