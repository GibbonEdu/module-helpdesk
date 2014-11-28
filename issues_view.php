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

//Module includes
include "./modules/" . $_SESSION[$guid]["module"] . "/moduleFunctions.php" ;

if (isModuleAccessible($guid, $connection2)==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print "You do not have access to this action." ;
	print "</div>" ;
}
else {
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('View Issues') . "</div>" ;
	print "</div>" ;
	print "<h3>" ;
	print _("Filter") ;
	print "</h3>" ;
	$highestAction=getHighestGroupedAction($guid, $_GET["q"], $connection2) ;
	if ($highestAction==FALSE) {
		print "<div class='error'>" ;
		print _("The highest grouped action cannot be determined.") ;
		print "</div>" ;
	}

	$filter=NULL ;
	$filter2=NULL ;
	if (isset($_GET["filter"])) {
		$filter=$_GET["filter"] ;
	}
	else if (isset($_POST["filter"])) {
		$filter=$_POST["filter"] ;
	}
	if (isset($_GET["filter2"])) {
		$filter2=$_GET["filter2"] ;
	}
	else if (isset($_POST["filter2"])) {
		$filter2=$_POST["filter2"] ;
	}

	$issueFilters = array("My Issues");
	if(isTechnician($_SESSION[$guid]["gibbonPersonID"], $connection2)) array_push($issueFilters, "My Working");
	if($highestAction=="View issues_All" || $highestAction=="View issues_All&Assign") array_push($issueFilters, "All");
	$statusFilters = array("All", "Unassigned", "Pending", "Resolved");
	$dataIssue["gibbonSchoolYearID"]=$_SESSION[$guid]["gibbonSchoolYearID"];
	$and="" ;
	$whereIssue = "";
	if ($filter=="") {
		$filter=$issueFilters[0];
	}

	if ($filter=="My Issues") {
		$dataIssue["helpDeskGibbonPersonID"] = $_SESSION[$guid]["gibbonPersonID"];
		$whereIssue.= " AND helpDeskIssue.gibbonPersonID=:helpDeskGibbonPersonID";
	}
	else if ($filter=="My Working") {
		$dataIssue["helpDeskTechnicianID"] = getTechnicianID($_SESSION[$guid]["gibbonPersonID"], $connection2);
		$whereIssue.= " AND helpDeskIssue.technicianID=:helpDeskTechnicianID";
	}
	if ($filter2=="") {
		$filter2=$statusFilters[0];
	}
	if ($filter2=="Unassigned") {
		$dataIssue["helpDeskStatus"] = 'Unassigned';
		$whereIssue.= " AND helpDeskIssue.status=:helpDeskStatus";
	}
	else if ($filter2=="Pending") {
		$dataIssue["helpDeskStatus"] = 'Pending';
		$whereIssue.= " AND helpDeskIssue.status=:helpDeskStatus";
	}
	else if ($filter2=="Resolved") {
		$dataIssue["helpDeskStatus"] = 'Resolved';
		$whereIssue.= " AND helpDeskIssue.status=:helpDeskStatus";
	}

	print "<form method='post' action='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "'>" ;
		print"<table class='noIntBorder' cellspacing='0' style='width: 100%'>" ;
		if(count($issueFilters)>1)
		{
			print "<tr>";
				print "<td> ";
					print "<b>". _('Issue Filter') ."</b><br/>";
					print "<span style=\"font-size: 90%\"><i></i></span>";
				print "</td>";
				print "<td class=\"right\">";
					print "<select name='filter' id='filter' style='width:302px'>" ;

						foreach($issueFilters as $option) {
							$selected="" ;
							if ($option==$filter) {
								$selected="selected" ;
							}
							print "<option $selected value='" . $option . "'>". $option ."</option>" ;
						}
					print "</select>" ;
				print "</td>";
			print "</tr>";
		}
			print "<tr>";
					print "<td> ";
						print "<b>".  _('Status Filter') ."</b><br/>";
						print "<span style=\"font-size: 90%\"><i></i></span>";
					print "</td>";
					print "<td class=\"right\">";
						print "<select name='filter2' id='filter2' style='width:302px'>" ;
							foreach($statusFilters as $option) {
								$selected="" ;
								if ($option==$filter2) {
									$selected="selected" ;
								}
								print "<option $selected value='" . $option . "'>". $option ."</option>" ;
							}
						print "</select>" ;
					print "</td>";
				print "</tr>";
				print "<tr>" ;
				print "<td class='right' colspan=2>" ;
					print "<input type='submit' value='" . _('Go') . "'>" ;
				print "</td>" ;
			print "</tr>" ;
		print"</table>" ;
	print "</form>" ;

  try {
    $sqlIssue="SELECT helpDeskIssue.* , surname , preferredName, gibbonPerson.title FROM helpDeskIssue JOIN gibbonPerson ON (helpDeskIssue.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE gibbonSchoolYearID=:gibbonSchoolYearID " . $whereIssue ;
    $resultIssue=$connection2->prepare($sqlIssue);
    $resultIssue->execute($dataIssue);
  }
  catch(PDOException $e) {
	print $e;
  }
  	print "<h3>" ;
	print _("Issues") ;
	print "</h3>" ;
    print "<table class = 'smallIntBorder' cellspacing = '0' style = 'width: 100% !important'>";
    print "<tr> <th>Title</th> <th>Description</th> <th>Name</th> <th>Status</th> <th>Date</th> <th>Action</th> </tr>";
	if ($resultIssue->rowCount()==0){
    	print "<tr>";
    	print "<td colspan=5>";
    	print _("There are no records to display.");
		print "<td>";
		print "</tr>";
    }
    else {
		foreach($resultIssue as $row){
		  print "<tr>";
		  printf("<td>" .$row['issueName']. "</td>");
		  printf("<td>" .$row['description']. "</td>");
		  printf("<td>" .$row['title'].$row['surname'].", ".$row['preferredName']. "</td>");
		  printf("<td>" .$row['status']. "</td>");
		  printf("<td>" .dateConvertBack($guid, $row["date"]). "</td>");
		  print "<td>";
		  if($row['technicianID']==null && isTechnician($_SESSION[$guid]["gibbonPersonID"], $connection2))
		  {
		    print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_acceptProcess.php&issueID=". $row["issueID"] . "'><img title=" . _('Accept ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png'/></a>";
			print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_discuss_view.php&issueID=". $row["issueID"] . "'><img title=" . _('View ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/zoom.png'/></a>";
		  }
		  else if($row['technicianID']==getTechnicianID($_SESSION[$guid]["gibbonPersonID"], $connection2))
		  {
			print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_discuss_view.php&issueID=". $row["issueID"] . "'><img title=" . _('Work ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/zoom.png'/></a>";
		  }
		  if($row['technicianID']==null && $highestAction=="View issues_All&Assign")
		  {
		    print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_assign.php&issueID=". $row["issueID"] . "'><img title=" . _('Assign ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/attendance.png'/></a>";
		  }
		  print "</td>";
		  print "</tr>";
		}
	}
    print "</table>";

}
?>
