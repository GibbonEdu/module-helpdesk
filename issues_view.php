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
	
	$issueFilters = array("My Issues", "All");
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
	
	if($filter=="My Issue" && $filter2!="All"){
		#and = " AND"
	}
		
	print "<form method='post' action='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "'>" ;
		print"<table class='noIntBorder' cellspacing='0' style='width: 100%'>" ;	
		if($highestAction=="View issues_All")
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
    print "<table class = 'smallIntBorder' cellspacing = '0' style = 'width: 100% !important'>";
    print "<tr> <th>Title</th> <th>Description</th> <th>Name</th> <th>Status</th> <th>Date</th> <th>Action</th> </tr>";
    foreach($resultIssue as $row){
      printf($row);
      print "<tr>";
      printf("<td>" .$row['issueName']. "</td>");
      printf("<td>" .$row['description']. "</td>");
      printf("<td>" .$row['title'].$row['surname'].", ".$row['preferredName']. "</td>");
      printf("<td>" .$row['status']. "</td>");
      printf("<td>" ."". "</td>");
      printf("<td>" ."". "</td>");
      print "</tr>";
    }
    print "</table>";	

}
?>
