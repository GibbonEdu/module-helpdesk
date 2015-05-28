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
include "./modules/Help Desk/moduleFunctions.php" ;


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

	if(!(isPersonsIssue($connection2, $_GET["issueID"], $_SESSION[$guid]["gibbonPersonID"]) || getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "fullAccess"))) {
		print "<div class='error'>" ;
			print _("You do not have access to this action.") ;
		print "</div>" ;
		exit();
	}
	
	if(isset($_GET["returnAddress"])) {
		$returnAddress = $_GET["returnAddress"] ;
	}
	else {
		$returnAddress = "issues_view.php";
	}
	
	//Proceed!
  print "<div class='trail'>" ;
  print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_discussView.php&issueID=". $_GET["issueID"] . "'>" . _("Discuss Issue") . "</a> > </div><div class='trailEnd'>" . _('Edit Privacy') . "</div>" ;
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
	?>
	
	<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "	/modules/" . $_SESSION[$guid]["module"] . "/issues_discussEditProcess.php?issueID=" . $issueID . "&returnAddress=$returnAddress"; ?>">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
			<tr>
					<td>
						<?php print "<b>". _('Privacy Setting') ." *</b><br/>";?>
						<span style=\"font-size: 90%\"><i></i></span>
					</td>
					<td class=\"right\">
						<select name='privacySetting' id='privacySetting' style='width:302px'>
						<?php
							try {
								$data=array("issueID"=>$issueID); 
								$sql="SELECT privacySetting FROM helpDeskIssue WHERE issueID=:issueID" ;
								$result=$connection2->prepare($sql);
								$result->execute($data);
							}
							catch(PDOException $e) { }
							$row=$result->fetch() ;
							$privacySetting = $row['privacySetting'];
							print "<option value='" . $privacySetting . "'>". $privacySetting ."</option>" ;
							$options = array("Everyone", "Related", "Owner", "No one");
							foreach($options as $option) {
								if($option != $privacySetting)print "<option value='" . $option . "'>". $option ."</option>" ;
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