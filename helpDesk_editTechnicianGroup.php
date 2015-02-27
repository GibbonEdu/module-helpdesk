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

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicianGroup.php")==FALSE) {
  //Acess denied
  print "<div class='error'>" ;
    print _("You do not have access to this action.") ;
  print "</div>" ;
}
else {
  //Proceed!
  print "<div class='trail'>" ;
  print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Assign Issue') . "</div>" ;
  print "</div>" ;  
  
  if (isset($_GET["addReturn"])) { $addReturn=$_GET["addReturn"] ; } else { $addReturn="" ; }
  	if(isset($_GET["groupID"])) {
		$groupID = $_GET["groupID"];
	}
	else {
		$addReturn = "fail3";
	}
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
    if($addReturn == "fail3") exit();
  }

  try {
    $data=array("groupID" => $groupID);
    $sql="SELECT * FROM helpDeskTechGroups WHERE groupID=:groupID";
    $result=$connection2->prepare($sql);
    $result->execute($data);
    $row = $result->fetch();
    
  } catch(PDOException $e) {
    print $e;
  }
	print "<h3>";
		print "Permissons for Technician Group: " . $row["groupName"];
  	print "</h3>";
  	?>
  	<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_editTechnicianGroupProcess.php?groupID=$groupID" ?>">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
			<tr>
				<td style='width: 275px'>
					<b>Group Name</b><br/>
					<span style="font-size: 90%"><i></i></span>
				</td>
				<td class="right">
					<input name="groupName" id="groupName" maxlength=100 value="<?php print $row["groupName"] ?>" type="text" data-minlength="1" style="width: 300px">
					<script type="text/javascript">
						var groupName=new LiveValidation('groupName');
						groupName.add(Validate.Presence);
					</script>
				</td>
			</tr>
			<tr>
				<td style='width: 275px'>
					<b>View Issues</b><br/>
					<span style="font-size: 90%"><i></i></span>
				</td>
				<td class="right">
					<?php
						$checked = '';
						if($row['viewIssue'] == TRUE) { $checked = 'checked'; }
						print "<input type='checkbox' name='viewIssue' id='viewIssue' $checked />" ;
					?>
				</td>
			</tr>
			<tr>
				<td style='width: 275px'>
					<b>View Issues Status</b><br/>
					<span style="font-size: 90%"><i></i></span>
				</td>
				<td class="right">
					<select name='viewIssueStatus' id='viewIssueStatus' style='width:302px'>
					<?php
							if($row['viewIssueStatus'] == "All") { print "<option selected value='All'>All</option>"; }
							else { print "<option value='All'>All</option>"; }
							if($row['viewIssueStatus'] == "UP") { print "<option selected value='UP'>Unassigned & Pending</option>"; }
							else { print "<option value='UP'>Unassigned & Pending</option>"; }
							if($row['viewIssueStatus'] == "PR") { print "<option selected value='PR'>Pending & Resolved</option>"; }
							else { print "<option value='PR'>Pending & Resolved</option>"; }
							if($row['viewIssueStatus'] == "Pending") { print "<option selected value='Pending'>Pending</option>"; }
							else { print "<option value='Pending'>Pending</option>"; }	
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td style='width: 275px'>
					<b>Assign Issues</b><br/>
					<span style="font-size: 90%"><i></i></span>
				</td>
				<td class="right">
					<?php
						$checked = '';
						if($row['assignIssue'] == TRUE) { $checked = 'checked'; }
						print "<input type='checkbox' name='assignIssue' id='assignIssue' $checked />" ;
					?>
				</td>
			</tr>
			<tr>
				<td style='width: 275px'>
					<b>Accept Issues</b><br/>
					<span style="font-size: 90%"><i></i></span>
				</td>
				<td class="right">
					<?php
						$checked = 'checked';
						if($row['acceptIssue'] == FALSE) { $checked = ''; }
						print "<input type='checkbox' name='acceptIssue' id='acceptIssue' $checked />" ;
					?>
				</td>
			</tr>
			<tr>
				<td style='width: 275px'>
					<b>Resolve Issues</b><br/>
					<span style="font-size: 90%"><i></i></span>
				</td>
				<td class="right">
					<?php
						$checked = 'checked';
						if($row['resolveIssue'] == FALSE) { $checked = ''; }
						print "<input type='checkbox' name='resolveIssue' id='resolveIssue' $checked />" ;
					?>
				</td>
			</tr>
			<tr>
				<td style='width: 275px'>
					<b>Create Issues For Other</b><br/>
					<span style="font-size: 90%"><i></i></span>
				</td>
				<td class="right">
					<?php
						$checked = 'checked';
						if($row['createIssueForOther'] == FALSE) { $checked = ''; }
						print "<input type='checkbox' name='createIssueForOther' id='createIssueForOther' $checked />" ;
					?>
				</td>
			</tr>
			<tr>
				<td style='width: 275px'>
					<b>Full Access</b><br/>
					<span style="font-size: 90%"><i></i></span>
				</td>
				<td class="right">
					<?php
						$checked = '';
						if($row['fullAccess'] == TRUE) { $checked = 'checked'; }
						print "<input type='checkbox' name='fullAccess' id='fullAccess' $checked />" ;
					?>
				</td>
			</tr>
			<tr>
				<td>
					<span style="font-size: 90%"><i>* <?php print _("denotes a required field") ; ?></i></span>
				</td>
				<td class="right">
					<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
					<input type="submit" value="<?php print _("Submit") ; ?>">
				</td>
			</tr>
		</table>
	</form>
  	<!-- 

print "<table cellspacing='0' style='width: 100%'>" ;
   		print "<tr class='head'>" ;
    		print "<th style='width:50%'>" ;
        		print _("Permission Name") ;
      		print "</th>" ;
     		print "<th style='width:50%'>" ;
       			print _("Setting") ;
    		print "</th>" ;
 		print "</tr>" ;
      	print "<tr>" ;
      		print "<td> ";
				print "<b>View Issues</b>" ;
				print "<span style='font-size: 90%'><i></i></span>";
			print "</td>";
        	print "<td>";
        	
        	print "</td>";
      print "</tr>" ;
      print "<tr>" ;
      		print "<td> ";
				print "<b>View Issues</b>" ;
				print "<span style='font-size: 90%'><i></i></span>";
			print "</td>";
        	print "<td>";
        	
        	print "</td>";
      print "</tr>" ;
      print "<tr>" ;
      		print "<td> ";
				print "<b>View Issues</b>" ;
				print "<span style='font-size: 90%'><i></i></span>";
			print "</td>";
        	print "<td>";
        	
        	print "</td>";
      print "</tr>" ;
  print "</table>" ;
 -->
<?php
}
?>
