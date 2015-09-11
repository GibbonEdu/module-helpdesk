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
include "./modules/" . $_SESSION[$guid]["module"] . "/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicians.php")==FALSE) {
  //Acess denied
  print "<div class='error'>" ;
    print _("You do not have access to this action.") ;
  print "</div>" ;
}
else {
	$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) ;

  //Proceed!
  print "<div class='trail'>" ;
  print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Statistics') . "</div>" ;
  print "</div>" ;
  
  if (isset($_GET["addReturn"])) { $addReturn=$_GET["addReturn"] ; } else { $addReturn="" ; }
  $addReturnMessage="" ;
  $class="error" ;
  if (!($addReturn=="")) {
    if ($addReturn=="fail0") {
      $addReturnMessage=_("Your request failed because you do not have access to this action.") ;
    }
    else if ($addReturn=="fail2") {
      $addReturnMessage=_("Your request failed due to a database error.") ;
    }
    else if ($addReturn=="fail3") {
      $addReturnMessage=_("Your request failed because your inputs were invalid.") ;
    }
    else if ($addReturn=="fail4") {
      $addReturnMessage="Your request failed because your inputs were invalid." ;
    }
    else if ($addReturn=="fail5") {
      $addReturnMessage="Your request was successful, but some data was not properly saved." ;
    }
    else if ($addReturn=="success0") {
      $addReturnMessage=_("Your request was completed successfully. You can now add another record if you wish.") ;
      $class="success" ;
    }
    print "<div class='$class'>" ;
    print $addReturnMessage;
    print "</div>" ;
  }
  	
	print "<h3>" ;
		print _("Filter") ;
	print "</h3>" ;

	$d = new DateTime('first day of this month');
	$startDate=dateConvertBack($guid, $d->format('Y-m-d')) ;
	$endDate=dateConvertBack($guid, date("Y-m-d")) ;

	if (isset($_POST["startDate"])) {
		$startDate=$_POST["startDate"] ;
	}
	if (isset($_POST["endDate"])) {
		$endDate=$_POST["endDate"] ;
	}
	
	$stats = array();
	include "./version.php";
	if($version>=11){
		$result = getLog($connection2, $_SESSION[$guid]["gibbonSchoolYearID"], getModuleIDFromName($connection2, "Help Desk"), null, null, $startDate, $endDate, null, null);
	}
	else if($version<11 && $version>=10) {
		$tempEndDate = str_replace('/', '-', $endDate);
		$tempEndDate = date("Y-m-d", strtotime($tempEndDate . " +1 day"));
		$result = getLog($connection2, $_SESSION[$guid]["gibbonSchoolYearID"], getModuleIDFromName($connection2, "Help Desk"), null, null, $startDate, $tempEndDate);	
	}

	while($row = $result->fetch()) {
		if(isset($stats[$row['title']])) {
			$stats[$row['title']] = $stats[$row['title']]+1;
		}
		else {
			$stats[$row['title']] = 1;
		}
	}
  ksort($stats);
  print "<form method='post' action='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "'>" ;
		print"<table class='noIntBorder' cellspacing='0' style='width: 100%'>" ;
			print "<tr>";
				print "<td> ";
					print "<b>". _('Start Date Filter') ."</b><br/>";
					print "<span style=\"font-size: 90%\"><i></i></span>";
				print "</td>";
				print "<td class=\"right\">";
					$output="";
					$output.="<input name='startDate' id='startDate' maxlength=10 value='" . $startDate . "' type='text' style='height: 22px; width:100px; margin-right: 0px; float: none'> " ;
						$output.="<script type=\"text/javascript\">" ;
							$output.="var ttDate=new LiveValidation('startDate');" ;
							$output.="ttDate.add( Validate.Format, {pattern:" ; if ($_SESSION[$guid]["i18n"]["dateFormatRegEx"]=="") {  $output.="/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i" ; } else { $output.=$_SESSION[$guid]["i18n"]["dateFormatRegEx"] ; } $output.=", failureMessage: \"Use " ; if ($_SESSION[$guid]["i18n"]["dateFormat"]=="") { $output.="dd/mm/yyyy" ; } else { $output.=$_SESSION[$guid]["i18n"]["dateFormat"] ; } $output.=".\" } );" ;
						 $output.="</script>" ;
						 $output.="<script type=\"text/javascript\">" ;
							$output.="$(function() {" ;
								$output.="$(\"#startDate\").datepicker();" ;
							$output.="});" ;
						$output.="</script>" ;
					print $output;
				print "</td>";
			print "</tr>";
			print "<tr>";
				print "<td> ";
					print "<b>".  _('End Date Filter') ."</b><br/>";
					print "<span style=\"font-size: 90%\"><i></i></span>";
				print "</td>";
				print "<td class=\"right\">";
					$output="";
					$output.="<input name='endDate' id='endDate' maxlength=10 value='" . $endDate . "' type='text' style='height: 22px; width:100px; margin-right: 0px; float: none'> " ;
						$output.="<script type=\"text/javascript\">" ;
							$output.="var ttDate=new LiveValidation('startDate');" ;
							$output.="ttDate.add( Validate.Format, {pattern:" ; if ($_SESSION[$guid]["i18n"]["dateFormatRegEx"]=="") {  $output.="/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)\d\d$/i" ; } else { $output.=$_SESSION[$guid]["i18n"]["dateFormatRegEx"] ; } $output.=", failureMessage: \"Use " ; if ($_SESSION[$guid]["i18n"]["dateFormat"]=="") { $output.="dd/mm/yyyy" ; } else { $output.=$_SESSION[$guid]["i18n"]["dateFormat"] ; } $output.=".\" } );" ;
						 $output.="</script>" ;
						 $output.="<script type=\"text/javascript\">" ;
							$output.="$(function() {" ;
								$output.="$(\"#endDate\").datepicker();" ;
							$output.="});" ;
						$output.="</script>" ;
					print $output;
				print "</td>";
			print "</tr>";
			print "<tr>" ;
				print "<td class='right' colspan=2>" ;
					print "<input type='submit' value='" . _('Go') . "'>" ;
				print "</td>" ;
			print "</tr>" ;
		print"</table>" ;
	print "</form>" ;
  
  print "<h3>";
    print "Statistics" ;
  print "</h3>";
  print "<table cellspacing='0' style='width: 100%'>" ;
    print "<tr class='head'>" ;
      print "<th>" ;
        print _("Name") ;
      print "</th>" ;
      print "<th>" ;
        print _("Value") ;
      print "</th>" ;
  print "</tr>" ;


  if (! $result->rowcount() == 0){
  	$rowCount=0;
    foreach($stats as $key => $val){
		$class = "odd";
		if($rowCount%2 == 0) {
			$class = "even";
		}
        print "<tr class='$class'>";
        	print "<td>";
        		print "<a href='" . $URL . "/helpDesk_statisticsDetail.php&title=" . $key . "&startDate=" . $startDate . "&endDate=" . $endDate . "'>" . $key . "</a>";
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
        print _("There are no records to display.");
      print "</td>";
    print "</tr>";
  }

  print "</table>" ;

}
?>
