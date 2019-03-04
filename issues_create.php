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
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

@session_start() ;

include "./modules/Help Desk/moduleFunctions.php" ;

if (isModuleAccessible($guid, $connection2) == FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print "You do not have access to this action." ;
	print "</div>" ;
	exit();
} else {
	print "<div class='trail'>" ;
		print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . __("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . __(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . __('Create Issue') . "</div>" ;
	print "</div>" ;

	if (isset($_GET['return'])) {
		$editLink = null;
		if (isset($_GET['issueID'])) {
			$issueID = $_GET['issueID'];
			$editLink = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=$issueID";
		}
        returnProcess($guid, $_GET['return'], $editLink, null);
    }

    $settings = getHelpDeskSettings($connection2);
    $priorityOptions = array();
    $priorityName = null;
    $categoryOptions = array();

    while ($row = $settings->fetch()) {
    	if ($row["name"] == "issuePriority") {
			foreach (explode(",", $row["value"]) as $type) {
				if ($type != "") {
					array_push($priorityOptions, $type);
				}
			}
    	} else if ($row["name"] == "issuePriorityName") {
			$priorityName = $row["value"];
    	} else if ($row["name"] == "issueCategory") {
    		foreach (explode(",", $row["value"]) as $type) {
				if ($type != "") {
					array_push($categoryOptions, $type);
				}
			}
    	}
    }
?>
<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/issues_createProccess.php" ?>">
	<table class='smallIntBorder' cellspacing='0' style="width: 100%">
		<tr>
			<td style='width: 275px'>
				<b>
					<?php print __('Issue Name') . " *" ?>
				</b><br/>
			</td>
			<td class="right" colspan=2>
				<input name="name" id="name" maxlength=55 value="" type="text" style="width: 300px">
				<script type="text/javascript">
					var name = new LiveValidation('name');
					name.add(Validate.Presence);
				</script>
			</td>
		</tr>
		<?php
			if(count($categoryOptions)>0) {
				print "<tr>";
					print "<td> ";
						print "<b>". __('Category') ." *</b><br/>";
						print "<span style=\"font-size: 90%\"><i></i></span>";
					print "</td>";
					print "<td class=\"right\" colspan=2>";
						print "<select name='category' id='category' style='width:302px'>" ;
							print "<option value=''>Please select...</option>" ;
							foreach ($categoryOptions as $option) {
								$selected = "" ;
								if ($option == $filter) {
									$selected = "selected" ;
								}
								print "<option $selected value='" . $option . "'>". $option ."</option>" ;
							}
						print "</select>" ;
						?>
							<script type="text/javascript">
								var name2=new LiveValidation('category');
								name2.add(Validate.Presence);
							</script>
						<?php
					print "</td>";
				print "</tr>";
			}
		?>
		<tr>
			<td colspan=2>
				<b>
					<?php print __('Description') . " *" ?>
				</b><br/>
				<?php print getEditor($guid, TRUE, "description", "", 5, true, true, false); ?>
			</td>
		</tr>
		<?php
			if(count($priorityOptions)>0) {
				print "<tr>";
					print "<td> ";
						print "<b>". $priorityName ." *</b><br/>";
						print "<span style=\"font-size: 90%\"><i></i></span>";
					print "</td>";
					print "<td class=\"right\" colspan=2>";
						print "<select name='priority' id='priority' style='width:302px'>" ;
							print "<option value=''>Please select...</option>" ;
							foreach ($priorityOptions as $option) {
								$selected = "" ;
								if ($option == $filter) {
									$selected = "selected" ;
								}
								print "<option $selected value='" . $option . "'>". $option ."</option>" ;
							}
						print "</select>" ;
						?>
							<script type="text/javascript">
								var name4=new LiveValidation('priority');
								name4.add(Validate.Presence);
							</script>
						<?php
					print "</td>";
				print "</tr>";
			}
			if(getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "createIssueForOther")) {
				?>
					<tr>
						<td>
							<b>Create on behalf of</b><br/>
							<span style=\"font-size: 90%\"><i>Leave blank if creating issue for self.</i></span>
						</td>
						<td class="right">
							<select name='createFor' id='createFor' style='width:302px'>
								<option value=''>Select...</option>
								<?php
									$allPeople = getAllPeople($connection2, false);
									foreach ($allPeople as $row) {
										if (intval($row["gibbonPersonID"]) != $_SESSION[$guid]["gibbonPersonID"]) {
											print "<option value='" . $row["gibbonPersonID"] . "'>". $row['surname'] . ", " . $row['preferredName'] ."</option>" ;
										}
									}
								?>
							</select>
						</td>
					</tr>
				<?php
			}
		?>
		<tr>
			<td>
				<b>Privacy Setting *</b><br/>
				<span style=\"font-size: 90%\"><i>If this Issue will or may contain any private information you may choose the privacy of this for when it is completed.</i></span>
			</td>
			<td class="right">
				<select name='privacySetting' id='privacySetting' style='width:302px'>
					<?php
						try {
							$data = array(); 
							$sql = "SELECT * FROM gibbonSetting WHERE scope='Help Desk' AND name='resolvedIssuePrivacy'" ;
							$result = $connection2->prepare($sql);
							$result->execute($data);
						}
						catch (PDOException $e) {
						}

						$row = $result->fetch() ;
						$privacySetting = $row['value'];
						print "<option value='" . $privacySetting . "'>". $privacySetting ."</option>" ;
						$options = array("Everyone", "Related", "Owner", "No one");
						foreach ($options as $option) {
							if($option != $privacySetting) {
								print "<option value='" . $option . "'>". $option ."</option>" ;
							}
						}
					?>
				</select>
				<script type="text/javascript">
					var name5=new LiveValidation('privacySetting');
					name5.add(Validate.Presence);
				</script>
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
