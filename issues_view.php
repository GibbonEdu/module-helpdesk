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
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Issues') . "</div>" ;
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
	$highestAction=getHighestGroupedAction($guid, $_GET["q"], $connection2) ;
	if ($highestAction==FALSE) {
		print "<div class='error'>" ;
		print _("The highest grouped action cannot be determined.") ;
		print "</div>" ;
	}

	$filter=NULL ;
	$filter2=NULL ;
	$filter3=NULL ;
	$filter4=NULL ;
	$IDFilter="" ;


	if (isset($_POST["filter"])) {
		$filter=$_POST["filter"] ;
	}
	if (isset($_POST["filter2"])) {
		$filter2=$_POST["filter2"] ;
	}
	if (isset($_POST["filter3"])) {
		$filter3=$_POST["filter3"] ;
	}
	if (isset($_POST["filter4"])) {
		$filter4=$_POST["filter4"] ;
	}
	
	if (isset($_POST["IDFilter"])) {
		$IDFilter=intval($_POST["IDFilter"]) ;
	}

	try {
		$data=array();
		$sql="SELECT * FROM gibbonSetting WHERE scope='Help Desk' AND name='issuePriority'" ;
		$result=$connection2->prepare($sql);
		$result->execute($data);
	}
	catch(PDOException $e) { }
	$row=$result->fetch() ;
	$priorityFilters = array("All");
	foreach (explode(",", $row["value"]) as $type) {
		if(!($type=="")) {
		  array_push($priorityFilters, $type);
		}
	}
	$renderPriority = count($priorityFilters)>1;
	try {
		$data=array();
		$sql="SELECT * FROM gibbonSetting WHERE scope='Help Desk' AND name='issuePriorityName'" ;
		$result=$connection2->prepare($sql);
		$result->execute($data);
	}
	catch(PDOException $e) { }
	$row=$result->fetch() ;
	$priorityName = $row["value"];
	try {
		$data=array();
		$sql="SELECT * FROM gibbonSetting WHERE scope='Help Desk' AND name='issueCategory'" ;
		$result=$connection2->prepare($sql);
		$result->execute($data);
	}
	catch(PDOException $e) { }
	$row=$result->fetch() ;
	$categoryFilters = array("All");
	foreach (explode(",", $row["value"]) as $type) {
		if(!($type=="")) {
		  array_push($categoryFilters, $type);
		}
	}
	$renderCategory = count($categoryFilters)>1;

	$issueFilters = array();
	if(getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "viewIssue")) array_push($issueFilters, "All");
	if(isTechnician($_SESSION[$guid]["gibbonPersonID"], $connection2)) array_push($issueFilters, "My Working");
	array_push($issueFilters, "My Issues");
	if(isTechnician($_SESSION[$guid]["gibbonPersonID"], $connection2)) {	
		$statusFilters = array("Pending");
		if(getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "viewIssueStatus")=="All") { $statusFilters = array("All", "Unassigned", "Pending", "Resolved"); }
		else if(getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "viewIssueStatus")=="UP") { $statusFilters = array("Unassigned and Pending", "Unassigned", "Pending"); }
		else if(getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "viewIssueStatus")=="PR") { $statusFilters = array("Pending and Resolved", "Pending", "Resolved"); }
	}
	else {
		$statusFilters = array("All", "Unassigned", "Pending", "Resolved");
	}
	$dataIssue["gibbonSchoolYearID"]=$_SESSION[$guid]["gibbonSchoolYearID"];
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
		$dataIssue["status"] = 'Resolved';
		$whereIssue.= " AND helpDeskIssue.technicianID=:helpDeskTechnicianID AND NOT helpDeskIssue.status=:status";
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
	else if ($filter2=="Unassigned and Pending") {
		$dataIssue["helpDeskStatus1"] = 'Unassigned';
		$dataIssue["helpDeskStatus2"] = 'Pending';
		$whereIssue.= " AND (helpDeskIssue.status=:helpDeskStatus1 OR helpDeskIssue.status=:helpDeskStatus2)";
	}
	else if ($filter2=="Pending and Resolved") {
		$dataIssue["helpDeskStatus1"] = 'Pending';
		$dataIssue["helpDeskStatus2"] = 'Resolved';
		$whereIssue.= " AND (helpDeskIssue.status=:helpDeskStatus1 OR helpDeskIssue.status=:helpDeskStatus2)";
	}

	if ($filter3=="") {
		$filter3=$categoryFilters[0];
	}
	if ($filter3!="All") {
		$dataIssue["helpDeskCategory"] = $filter3;
		$whereIssue.= " AND helpDeskIssue.category=:helpDeskCategory";
	}

	if ($filter4=="") {
		$filter4=$priorityFilters[0];
	}
	if ($filter4!="All") {
		$dataIssue["helpDeskPriority"] = $filter4;
		$whereIssue.= " AND helpDeskIssue.priority=:helpDeskPriority";
	}
	
	if (intval($IDFilter)>0) {
		$dataIssue["issueID"] = $IDFilter;
		$whereIssue.=" AND issueID=:issueID";
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
			if(count($categoryFilters)>1) {
				print "<tr>";
					print "<td> ";
						print "<b>". _('Category') ."</b><br/>";
						print "<span style=\"font-size: 90%\"><i></i></span>";
					print "</td>";
					print "<td class=\"right\">";
						print "<select name='filter3' id='filter3' style='width:302px'>" ;

							foreach($categoryFilters as $option) {
								$selected="" ;
								if ($option==$filter3) {
									$selected="selected" ;
								}
								print "<option $selected value='" . $option . "'>". $option ."</option>" ;
							}
						print "</select>" ;
					print "</td>";
				print "</tr>";
			}
			if($renderPriority) {
				print "<tr>";
					print "<td> ";
						print "<b>". $priorityName ."</b><br/>";
						print "<span style=\"font-size: 90%\"><i></i></span>";
					print "</td>";
					print "<td class=\"right\">";
						print "<select name='filter4' id='filter4' style='width:302px'>" ;

							foreach($priorityFilters as $option) {
								$selected="" ;
								if ($option==$filter4) {
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
					print "<b>Issue ID Filter</b><br/>";
					print "<span style=\"font-size: 90%\"><i></i></span>";
				print "</td>";
				print "<td class='right'>";
					print "<input type='text' value='". $IDFilter . "' id='IDFilter' name='IDFilter' style='width: 300px'>";
				print "</td>";
			print "</tr>";
			print "<tr>" ;
				print "<td class='right' colspan=2>" ;
					print "<input type='submit' value='" . _('Go') . "'>" ;
					?>
					<script type="text/javascript">
						var IDFilter=new LiveValidation('IDFilter');
						IDFilter.add(Validate.Numericality);
					</script>
					<?php
				print "</td>" ;
			print "</tr>" ;
		print"</table>" ;
	print "</form>" ;

  try {
    $sqlIssue="SELECT helpDeskIssue.* , surname , preferredName, gibbonPerson.title FROM helpDeskIssue JOIN gibbonPerson ON (helpDeskIssue.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE gibbonSchoolYearID=:gibbonSchoolYearID " . $whereIssue . " ORDER BY date DESC" ;
    $resultIssue=$connection2->prepare($sqlIssue);
    $resultIssue->execute($dataIssue);
  }
  catch(PDOException $e) {
	print $e;
  }

  print "<h3>" ;
	print _("Issues") ;
  print "</h3>" ;
  print "<div class='linkTop'>" ;
    print "<a style='position:relative; bottom:10px;float:right;' href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_create.php'>" .  _('Create');
    print "<img style='margin-left: 2px' title=" . _('Create ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_new.png'/></a>";
  print "</div>" ;
    print "<table cellspacing = '0' style = 'width: 100% !important'>";
   	print "<tr>";
   	print "<th>ID</th>";
    print "<th>Title<br/>";
    print "<span style='font-size: 85%; font-style: italic'>" . _('Description') . "</span>" ;
    print "</th>";
    print "<th>Owner";
  	if($renderCategory) { print "<br/><span style='font-size: 85%; font-style: italic'>" . _('Category') . "</span>"; }
  	print "</th>";
  	if($renderPriority) { print "<th>$priorityName</th>"; }
  	print "<th>Status<br/>";
  	print "<span style='font-size: 85%; font-style: italic'>" . _('Date') . "</span>";
  	print "</th>";
    print "<th>Action</th> </tr>";
	if ($resultIssue->rowCount()==0){
    	print "<tr>";
    	$colspan = 5;
    	if(!$renderCategory) { $colspan-=1; }
    	if(!$renderPriority) { $colspan-=1; }
    	print "<td colspan=$colspan>";
    	print _("There are no records to display.");
		print "<td>";
		print "</tr>";
    }
    else {
    	$rowCount = 0;
		foreach($resultIssue as $row){
		  if($rowCount%2 == 0) {
		 	 print "<tr class='even'>";
		  }
		  else {
		 	 print "<tr class='odd'>";
		  }
		  
		  print "<td style='text-align: center;'><b>" . intval($row['issueID']) . "</b></td>";
		  $issueName = $row['issueName'];
		  if(strlen($issueName)>15) $issueName = substr($issueName, 0, 15) . "...";
		  printf("<td><b>" .$issueName . "</b><br/>");
		  $descriptionText = $row['description'];
		  if(strlen($descriptionText)>15) $descriptionText = substr($descriptionText, 0, 15) . "...";
		  print "<span style='font-size: 85%; font-style: italic'>" . $descriptionText . "</span></td>" ;
		  		  printf("<td><b>" .formatName($row['title'],$row['preferredName'],$row['surname'], "Student", FALSE, FALSE) . "</b>");
		  if($renderCategory) { print "<br/><span style='font-size: 85%; font-style: italic'>" . $row['category'] . "</span>" ;}
		  print "</td>";
		  if($renderPriority) { printf("<td><b>" .$row['priority']. "</b></td>"); }
		  printf("<td><b>" .$row['status']. "</b><br/>");
		  print "<span style='font-size: 85%; font-style: italic'>" . dateConvertBack($guid, $row["date"]) . "</span></td>" ;
		  print "<td style='width:16%'>";
		  $openCreated = false;
		  $resolveCreated = false;
		  if(isTechnician($_SESSION[$guid]["gibbonPersonID"], $connection2) && !relatedToIssue($connection2, intval($row['issueID']), $_SESSION[$guid]["gibbonPersonID"]))
		  {
			  if($row['technicianID']==null && !($row['status']=="Resolved"))
			  {
				?><input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>"><?php
				if(getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "acceptIssue")) { print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/issues_acceptProcess.php?issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Accept ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_new.png'/></a>"; }
			 	print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_discussView.php&issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Open ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/zoom.png'/></a>";
			 	$openCreated = true;
			  }
		  }
		  if(relatedToIssue($connection2, intval($row['issueID']), $_SESSION[$guid]["gibbonPersonID"]) && !($row['status']=="Resolved"))
		  {
		      if(!$openCreated) { 
		      	print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_discussView.php&issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Open ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/zoom.png'/></a>"; 
		      	$openCreated = true;
		      }
		      if(getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "resolveIssue") && $row['status']=="Pending") { 
		      	print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/issues_resolveProcess.php?issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Resolve ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/iconTick.png'/></a>";
		      	$resolveCreated = true;
		      }
		  }
		  if(!($row['status']=="Resolved"))
		  {
		    if($row['technicianID']==null && getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "assignIssue")) { print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_assign.php&issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Assign ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/attendance.png'/></a>"; }
		  	else if(getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "reassignIssue")) { print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_assign.php&issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Reassign ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/attendance.png'/></a>"; }
		  }
		  if(getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "fullAccess")) {
		      if(!$openCreated && !($row['status']=="Resolved")) { 
		      	print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_discussView.php&issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Open ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/zoom.png'/></a>"; 
		      	$openCreated = true;
		      }
		      if(!$resolveCreated && $row['status']=="Pending") { 
		      	print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/issues_resolveProcess.php?issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Resolve ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/iconTick.png'/></a>"; 
		      	$resolveCreated = true;
		      }
		  }
		  if($row['status']=="Resolved" && relatedToIssue($connection2, intval($row['issueID']), $_SESSION[$guid]["gibbonPersonID"])) {
		  	print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_discussView.php&issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Open ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/zoom.png'/></a>";
		 	$openCreated = true;
		 }
		  print "</td>";
		  print "</tr>";
		  $rowCount++;
		}
	}
    print "</table>";

}
?>
