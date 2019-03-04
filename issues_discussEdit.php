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


if (isActionAccessible($guid, $connection2, "/modules/Help Desk/issues_view.php") == FALSE || !(isPersonsIssue($connection2, $_GET["issueID"], $_SESSION[$guid]["gibbonPersonID"]) || getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "fullAccess"))) {
	//Acess denied
	print "<div class='error'>" ;
		print __("You do not have access to this action.") ;
	print "</div>" ;
} else {

	$issueID = null;
	if (isset($_GET["issueID"])){ 
		$issueID = $_GET["issueID"]; 
	} else {
		print "<div class='error'>" ;
			print __("No issue selected.") ;
		print "</div>" ;
		exit();
	}
	
	//Proceed!
  	print "<div class='trail'>" ;
  		print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . __("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . __(getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_discussView.php&issueID=". $_GET["issueID"] . "'>" . __("Discuss Issue") . "</a> > </div><div class='trailEnd'>" . __('Edit Privacy') . "</div>" ;
  	print "</div>" ;
?>
<form method="post" action="<?php print $_SESSION[$guid]['absoluteURL'] . '	/modules/Help Desk/issues_discussEditProcess.php?issueID=' . $issueID; ?>">
	<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
		<tr>
			<td>
				<b>
					<?php print __('Privacy Setting') ." *";?>
				</b><br>
			</td>
			<td class=\"right\">
				<select name='privacySetting' id='privacySetting' style='width:302px'>
					<?php
						try {
							$data = array("issueID"=>$issueID); 
							$sql = "SELECT privacySetting FROM helpDeskIssue WHERE issueID=:issueID" ;
							$result = $connection2->prepare($sql);
							$result->execute($data);
						}
						catch (PDOException $e) {
						}

						$row = $result->fetch() ;
						$privacySetting = $row['privacySetting'];
						print "<option value='" . $privacySetting . "'>". $privacySetting ."</option>" ;
						$options = array("Everyone", "Related", "Owner", "No one");
						foreach ($options as $option) {
							if ($option != $privacySetting) {
								print "<option value='" . $option . "'>". $option ."</option>" ;
							}
						}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<span style="font-size: 90%"><i>* <?php print __("denotes a required field") ; ?></i></span>
			</td>
			<td class="right">
				<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
				<input type="submit" value="<?php print __("Submit") ; ?>">
			</td>
		</tr>
	</table>
</form>
<?php
}
?>