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

use Gibbon\Forms\Form;
use Gibbon\Services\Format;

@session_start() ;

include './modules/'.$_SESSION[$guid]['module'].'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicians.php") == false) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {

    //Proceed!
    $page->breadcrumbs->add(__('Statistics'));

    print "<h3>" ;
        print __("Filter") ;
    print "</h3>" ;

    $d = new DateTime('first day of this month');
    $startDate = isset($_GET['startDate']) ? Format::dateConvert($_GET['startDate']) : $d->format('Y-m-d');
    $endDate = isset($_GET['endDate']) ? Format::dateConvert($_GET['endDate']) : date("Y-m-d");

    $stats = array();
    $result = getLog($connection2, $_SESSION[$guid]["gibbonSchoolYearID"], getModuleIDFromName($connection2, "Help Desk"), null, null, $startDate, $endDate, null, null);

    while ($row = $result->fetch()) {
        if (isset($stats[$row['title']])) {
            $stats[$row['title']] = $stats[$row['title']]+1;
        } else {
            $stats[$row['title']] = 1;
        }
    }
    ksort($stats);

    $form = Form::create('helpDeskStatistics', $_SESSION[$guid]['absoluteURL'].'/index.php', 'get');

    $form->addHiddenValue('q', '/modules/'.$_SESSION[$guid]['module'].'/helpdesk_statistics.php');

    $row = $form->addRow();
        $row->addLabel('startDate', __("Start Date Filter"));
        $row->addDate('startDate')
            ->setDateFromValue($startDate)
            ->chainedTo('endDate');

    $row = $form->addRow();
        $row->addLabel('endDate', __("End Date Filter"));
        $row->addDate('endDate')
            ->setDateFromValue($endDate)
            ->chainedFrom('startDate');

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();

    print "<h3>";
        print "Statistics" ;
    print "</h3>";
    print "<table cellspacing='0' style='width: 100%'>" ;
        print "<tr class='head'>" ;
            print "<th>" ;
                print __("Name") ;
            print "</th>" ;
            print "<th>" ;
                print __("Value") ;
            print "</th>" ;
        print "</tr>" ;


        if (!$result->rowcount() == 0) {
            $rowCount = 0;
            $URL = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/helpDesk_statisticsDetail.php" ;
            foreach ($stats as $key => $val){
                $class = "odd";
                if ($rowCount%2 == 0) {
                    $class = "even";
                }
                print "<tr class='$class'>";
                    print "<td>";
                        print "<a href='" . $URL . "&title=" . $key . "&startDate=" . $startDate . "&endDate=" . $endDate . "'>" . $key . "</a>";
                    print "</td>";
                    print "<td>";
                        print $val;
                    print "</td>";
                print "</tr>" ;
                $rowCount++;
            }
        } else {
            print "<tr>";
                print "<td colspan= 2>";
                    print __("There are no records to display.");
                print "</td>";
            print "</tr>";
        }
    print "</table>" ;
}
?>
