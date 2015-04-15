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


if (isActionAccessible($guid, $connection2, "/modules/Help Desk/issues_view.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	$issueID = null;
	if(isset($_GET["issueID"])){ 
		$issueID = $_GET["issueID"]; 
	} 
	$isReassign = false;
	if(hasTechnicianAssigned($connection2, $issueID)) {
		$isReassign = true;
	}
	
	if($isReassign) {
		if(!getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "reassignIssue")) {
			print "<div class='error'>" ;
				print _("You do not have access to this action.") ;
			print "</div>" ;
			exit();
		}
	}
	else {
		if(!getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "assignIssue")) {
			print "<div class='error'>" ;
				print _("You do not have access to this action.") ;
			print "</div>" ;
			exit();
		}
	}
	
	//Proceed!
	print "<div class='trail'>" ;
	if($isReassign) { print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Reassign Issue') . "</div>" ; }
	else { print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Assign Issue') . "</div>" ; }
	print "</div>" ;
	
	
	if (isset($_GET["updateReturn"])) { $updateReturn=$_GET["updateReturn"] ; } else { $updateReturn="" ; }
	
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
		else if ($updateReturn=="success0") {
			$updateReturnMessage=_("Your request was completed successfully.") ;	
			$class="success" ;
		}
		print "<div class='class'>" ;
			print $updateReturnMessage;
		print "</div>" ;
	} 
	
	$technicians = getAllTechnicians($connection2);
	
	?>
	
	<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "	/modules/" . $_SESSION[$guid]["module"] . "/issues_assignProcess.php?issueID=" . $issueID ?>">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
			<tr>
					<td>
						<?php print "<b>". _('Technicians') ." *</b><br/>";?>
						<span style=\"font-size: 90%\"><i></i></span>
					</td>
					<td class=\"right\">
						<select name='technician' id='technician' style='width:302px'>
						<?php
							foreach($technicians as $option) {
								if(!isPersonsIssue($connection2, $issueID, $option["gibbonPersonID"])) { 
									if($isReassign) {
										if(getTechWorkingOnIssue($connection2, $issueID) != $option["gibbonPersonID"]) { print "<option $selected value='" . $option["gibbonPersonID"] . "'>". $option["surname"]. ", ". $option["preferredName"] ."</option>" ;  }
									}
									else {								
										print "<option $selected value='" . $option["gibbonPersonID"] . "'>". $option["surname"]. ", ". $option["preferredName"] ."</option>" ; 
									}
								}
							}
						?>
						</select>
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