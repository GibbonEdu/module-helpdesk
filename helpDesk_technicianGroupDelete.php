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
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_manageTechnicianGroup.php'>" . _("Manage Technician Groups") . "</a> > </div><div class='trailEnd'>" . _('Delete Technician Group') . "</div>" ;
	print "</div>" ;
	
	
	if (isset($_GET["updateReturn"])) { $updateReturn=$_GET["updateReturn"] ; } else { $updateReturn="" ; }
	
	$highestAction=getHighestGroupedAction($guid, "/modules/Help Desk/helpDesk_manageTechnicianGroup.php", $connection2) ;
	if ($highestAction==FALSE) {
		print "<div class='error'>" ;
		print _("The highest grouped action cannot be determined.") ;
		print "</div>" ;
		exit();
	}
	if(!($highestAction=="Manage Technician Groups")) { $updateReturn = "fail0"; }
	$groupID = null;
	if(isset($_GET["groupID"])){ 
		$groupID = $_GET["groupID"]; 
	} 
	try {
		$data=array();
		$sql="SELECT * FROM helpDeskTechGroups ORDER BY helpDeskTechGroups.groupID ASC";
		$result=$connection2->prepare($sql);
		$result->execute($data);
  	} catch(PDOException $e) {
		print $e;
	}
	if($result->rowcount() == 1) {
		$updateReturn="&addReturn=fail4";
	}
	
	$updateReturnMessage="" ;
	$class="error" ;
	if (!($updateReturn=="")) {
		if ($updateReturn=="fail0") {
			$updateReturnMessage=_("Your request failed because you do not have access to this action.") ;	
		}
		else if ($updateReturn=="fail1") {
			$updateReturnMessage=_("Your request failed because your inputs were invalid.") ;	
		}
		else if ($updateReturn=="fail2") {
			$updateReturnMessage=_("One or more of the fields in your request failed due to a database error.") ;	
		}
		else if ($updateReturn=="fail3") {
			$updateReturnMessage=_("Your request failed because your inputs were invalid.") ;	
		}
		else if ($updateReturn=="fail4") {
			$updateReturnMessage=_("Not enough groups to delete this group.") ;	
		}
		else if ($updateReturn=="success0") {
			$updateReturnMessage=_("Your request was completed successfully.") ;	
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $updateReturnMessage;
		print "</div>" ;
		if($updateReturn=="fail0" || $updateReturn=="fail4") { exit();}
	} 
	
	try {
		$data=array();
		$sql="SELECT * FROM helpDeskTechGroups";
		$result=$connection2->prepare($sql);
		$result->execute($data);
	  }
	  catch(PDOException $e) {
		   print $e;
	  }
	
	?>
	
	<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "	/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_technicianGroupDeleteProcess.php?groupID=" . $groupID ?>">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
			<tr>
					<td>
						<?php print "<b>". _('New Technician Group') ." *</b><br/>";?>
						<span style=\"font-size: 90%\"><i></i></span>
					</td>
					<td class=\"right\">
						<select name='group' id='group' style='width:302px'>
							<option value=''>Please select...</option>
						<?php
							while($option=$result->fetch()) {
								if($groupID != $option["groupID"])print "<option $selected value='" . $option["groupID"] . "'>". $option["groupName"] ."</option>" ;
							}
						?>
						</select>
						<script type="text/javascript">
							var name2=new LiveValidation('group');
							name2.add(Validate.Presence);
						</script>
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
<?php
}
?>