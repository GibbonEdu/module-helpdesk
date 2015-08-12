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
	if(isTechnician($connection2, $_SESSION[$guid]["gibbonPersonID"])) array_push($issueFilters, "My Working");
	array_push($issueFilters, "My Issues");
	if(isTechnician($connection2, $_SESSION[$guid]["gibbonPersonID"])) {	
		$statusFilters = array("Pending");
		if(getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "viewIssueStatus")=="All") { $statusFilters = array("All", "Unassigned", "Pending", "Resolved"); }
		else if(getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "viewIssueStatus")=="UP") { $statusFilters = array("Unassigned and Pending", "Unassigned", "Pending"); }
		else if(getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "viewIssueStatus")=="PR") { $statusFilters = array("Pending and Resolved", "Pending", "Resolved"); }
	}
	else {
		$statusFilters = array("All", "Unassigned", "Pending", "Resolved");
	}
	$dataIssue["gibbonSchoolYearID"]=$_SESSION[$guid]["gibbonSchoolYearID"];
	$dataIssue["helpDeskGibbonPersonID"] = $_SESSION[$guid]["gibbonPersonID"];
	$whereIssue = "";
	if ($filter=="") {
		$filter=$issueFilters[0];
	}

	if ($filter=="My Issues") {
		$whereIssue.= " AND helpDeskIssue.gibbonPersonID=:helpDeskGibbonPersonID";
	}
	else if ($filter=="My Working") {
		$dataIssue["helpDeskTechnicianID"] = getTechnicianID($connection2, $_SESSION[$guid]["gibbonPersonID"]);
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
    $sqlIssue="SELECT helpDeskIssue.* FROM helpDeskIssue WHERE gibbonSchoolYearID=:gibbonSchoolYearID " . $whereIssue;
   	$sqlIssue.=" UNION ";
   	$sqlIssue.="SELECT helpDeskIssue.* FROM helpDeskIssue WHERE helpDeskIssue.gibbonPersonID=:helpDeskGibbonPersonID AND gibbonSchoolYearID=:gibbonSchoolYearID";
    $sqlIssue.=" UNION ";
    $sqlIssue.="SELECT helpDeskIssue.* FROM helpDeskIssue WHERE helpDeskIssue.privacySetting='Everyone' AND gibbonSchoolYearID=:gibbonSchoolYearID";
    $sqlIssue.=" ORDER BY FIELD(status, 'Unassigned', 'Pending', 'Resolved'), ";
  	if($renderPriority) {
  		$sqlIssue.= "FIELD(priority";
  		foreach($priorityFilters as $priority) {
  			$sqlIssue.=", '" . $priority . "'";
  		}
  		$sqlIssue.= ", ''), ";
  	}
    $sqlIssue.="date DESC, issueID DESC;";
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
  	print "<th>Assigned Technician</th>";
  	print "<th>Status<br/>";
  	print "<span style='font-size: 85%; font-style: italic'>" . _('Date') . "</span>";
  	print "</th>";
    print "<th>Action</th> </tr>";
	if ($resultIssue->rowCount()==0){
    	print "<tr>";
    	$colspan = 7;
    	if(!$renderCategory) { $colspan-=1; }
    	if(!$renderPriority) { $colspan-=1; }
    	print "<td colspan=$colspan>";
    	print _("There are no records to display.");
		print "</td>";
		print "</tr>";
    }
    else {
    	$nameLength = 15;
    	$descriptionLength = 50;
		foreach($resultIssue as $row){
			$person = getOwnerOfIssue($connection2, $row['issueID']);
			$class = "error";
			if($row['status']=='Pending') {
				$class = "warning";
			}
			else if($row['status']=='Resolved') {
				$class = "current";
			}
			try {
				$data=array("issueID"=>$row["issueID"]); 
				$sql="SELECT privacySetting FROM helpDeskIssue WHERE issueID=:issueID" ;
				$result=$connection2->prepare($sql);
				$result->execute($data);
			}
			catch(PDOException $e) { }
			$row2=$result->fetch() ;
			$privacySetting = $row2['privacySetting'];
		  print "<tr class='$class'>";
			  print "<td style='text-align: center;'><b>" . intval($row['issueID']) . "</b></td>";
			  $issueName = $row['issueName'];
			  if(strlen($issueName)>$nameLength) $issueName = substr($issueName, 0, $nameLength) . "...";
			  print "<td><b>" .$issueName . "</b><br/>";
			  $descriptionText = strip_tags($row['description']);
			  if(strlen($descriptionText)>$descriptionLength) $descriptionText = substr($descriptionText, 0, $descriptionLength) . "...";
			  if($row['status']=="Resolved") {
			  	if($privacySetting == "Everyone") {
			 	 print "<span style='font-size: 85%; font-style: italic'>" . $descriptionText . "</span></td>" ;
					$openCreated = true;
				}
				else if($privacySetting == "Related" && relatedToIssue($connection2, intval($row['issueID']), $_SESSION[$guid]["gibbonPersonID"])) {
			 	 print "<span style='font-size: 85%; font-style: italic'>" . $descriptionText . "</span></td>" ;
					$openCreated = true;
				}
				else if($privacySetting == "Owner" && isPersonsIssue($connection2, intval($row['issueID']), $_SESSION[$guid]["gibbonPersonID"])) {
			 	 print "<span style='font-size: 85%; font-style: italic'>" . $descriptionText . "</span></td>" ;
				}
				else if(getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "fullAccess")) {
					 print "<span style='font-size: 85%; font-style: italic'>" . $descriptionText . "</span></td>" ;
				}
			  }	
			  else {
				 print "<span style='font-size: 85%; font-style: italic'>" . $descriptionText . "</span></td>" ;
			  }		 
			  print "<td><b>" .formatName($person['title'],$person['preferredName'],$person['surname'], "Student", FALSE, FALSE) . "</b>";
			  if($renderCategory) { print "<br/><span style='font-size: 85%; font-style: italic'>" . $row['category'] . "</span>" ;}
			  print "</td>";
			  if($renderPriority) { print "<td style='width: 8%'><b>" .$row['priority']. "</b></td>"; }
			  $technician = getTechWorkingOnIssue($connection2, $row['issueID']);
			  print "<td style='width: 15%'><b>" . $technician["preferredName"] . " " . $technician["surname"] . "</b></td>";
			  print "<td style='width: 10%'><b>" .$row['status']. "</b><br/>";
			  print "<span style='font-size: 85%; font-style: italic'>" . dateConvertBack($guid, $row["date"]) . "</span></td>" ;
			  print "<td style='width:17%'>";
			  $openCreated = false;
			  $resolveCreated = false; 
			  
			  if(relatedToIssue($connection2, intval($row['issueID']), $_SESSION[$guid]["gibbonPersonID"]) && !($row['status']=="Resolved")) {
				  if(!$openCreated) { 
					print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_discussView.php&issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Open ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/zoom.png'/></a>"; 
					$openCreated = true;
				  }
			  }
			  
			  if($row['status']=="Resolved") {
			  	if($privacySetting == "Everyone" && !$openCreated) {
					print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_discussView.php&issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Open ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/zoom.png'/></a>";
					$openCreated = true;
				}
				else if($privacySetting == "Related" && relatedToIssue($connection2, intval($row['issueID']), $_SESSION[$guid]["gibbonPersonID"]) && !$openCreated) {
					print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_discussView.php&issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Open ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/zoom.png'/></a>";
					$openCreated = true;
				}
				else if($privacySetting == "Owner" && isPersonsIssue($connection2, intval($row['issueID']), $_SESSION[$guid]["gibbonPersonID"]) && !$openCreated) {
					print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_discussView.php&issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Open ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/zoom.png'/></a>";
					$openCreated = true;
				}
			  }
			  
			  if(isTechnician($connection2, $_SESSION[$guid]["gibbonPersonID"]) || getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "fullAccess")) {
				  if($row['technicianID']==null && $row['status']!="Resolved" ) {
					if(!$openCreated) {
						print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_discussView.php&issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Open ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/zoom.png'/></a>";
						$openCreated = true;
					}
				  }
			  }		
			  
			  if(getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "fullAccess")) {
				  if(!$openCreated && !($row['status']=="Resolved")) { 
					print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_discussView.php&issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Open ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/zoom.png'/></a>"; 
					$openCreated = true;
				  }
			  }
			  
			  if(isPersonsIssue($connection2, $row["issueID"], $_SESSION[$guid]["gibbonPersonID"]) || getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "fullAccess")) {
			  	print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_discussEdit.php&issueID=". $row['issueID'] ."&returnAddress=issues_view.php'><img title=" . _('Edit ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a>";
			  }
			  
			  if(isTechnician($connection2, $_SESSION[$guid]["gibbonPersonID"]) || getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "fullAccess")) {
				  if($row['technicianID']==null && $row['status']!="Resolved" ) {
					?><input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>"><?php
					if(getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "acceptIssue") && !isPersonsIssue($connection2, intval($row['issueID']), $_SESSION[$guid]["gibbonPersonID"])) { print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/issues_acceptProcess.php?issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Accept ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_new.png'/></a>"; }
				  }
			  }	
			  			  
			  //Not Resolved
			  if(!($row['status']=="Resolved")) {
				if($row['technicianID']==null && getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "assignIssue")) { print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_assign.php&issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Assign ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/attendance.png'/></a>"; }
				else if(getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "reassignIssue")) { print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_assign.php&issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Reassign ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/attendance.png'/></a>"; }
			  }
			  
			  if(relatedToIssue($connection2, intval($row['issueID']), $_SESSION[$guid]["gibbonPersonID"]) && !($row['status']=="Resolved")) {
				  if(getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "resolveIssue") && $row['status']=="Pending") { 
					print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/issues_resolveProcess.php?issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Resolve ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/iconTick.png'/></a>";
					$resolveCreated = true;
				  }
			  }
			  
			  //Full Access
			  if(getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "fullAccess")) {
				  if(!$resolveCreated && $row['status']=="Pending") { 
					print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/issues_resolveProcess.php?issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Resolve ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/iconTick.png'/></a>"; 
					$resolveCreated = true;
				  }
			  }
			  
			  //Resolved
			  if($row['status']=="Resolved") {
			 	if(getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "reincarnateIssue") || isPersonsIssue($connection2, intval($row['issueID']), $_SESSION[$guid]["gibbonPersonID"])) {
			 		print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/issues_reincarnateProcess.php?issueID=". $row["issueID"] . "'><img style='margin-left: 5px' title=" . _('Reincarnate ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/reincarnate.png'/></a>";
			 	}
			  }			 	
			  
			  print "</td>";
		  print "</tr>";
		}
	}
    print "</table>";

}
?>
