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

include "./modules/" . $_SESSION[$guid]["module"] . "/moduleFunctions.php" ;

if (isModuleAccessible($guid, $connection2)==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print "You do not have access to this action." ;
	print "</div>" ;
}
else {
	//New PDO DB connection.
	//Gibbon uses PDO to connect to databases, rather than the PHP mysql classes, as they provide paramaterised connections, which are more secure.
	try {
		$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
		$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Create Issue') . "</div>" ;
	print "</div>" ;

	if (isset($_GET["addReturn"])) { $addReturn=$_GET["addReturn"] ; } else { $addReturn="" ; }
	$addReturnMessage="" ;
	$class="error" ;
	if (!($addReturn=="")) {
		if ($addReturn=="fail0") {
			$addReturnMessage=_("You do not have access to this action.") ;	
		}
		else if ($addReturn=="fail1") {
			$addReturnMessage=_("Your request failed because your inputs were invalid.") ;	
		}
		else if ($addReturn=="fail2") {
			$addReturnMessage=_("Your request failed due to a database error.") ;	
		}
		print "<div class='$class'>" ;
			print $addReturnMessage;
		print "</div>" ;
	} 

	try {
		$data=array(); 
		$sql="SELECT * FROM gibbonSetting WHERE scope='Help Desk' AND name='issuePriority'" ;
		$result=$connection2->prepare($sql);
		$result->execute($data);
	}
	catch(PDOException $e) { }
	$row=$result->fetch() ;
	$priorityOptions = array();
	foreach (explode(",", $row["value"]) as $type) {
		if(!($type=="")) {
			array_push($priorityOptions, $type);
		}
	}
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
	$categoryOptions = array();
	foreach (explode(",", $row["value"]) as $type) {
		if(!($type=="")) {
			array_push($categoryOptions, $type);
		}
	}

	?>

	<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/issues_createProccess.php" ?>">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%">
			<tr>
				<td style='width: 275px'>
					<b><?php print _('Issue Name') ?> *</b><br/>
				</td>
				<td class="right">
					<input name="name" id="name" maxlength=55 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var name=new LiveValidation('name');
						name.add(Validate.Presence);
					</script>
				</td>
			</tr>
			<?php
			if(count($categoryOptions)>0) {
				print "<tr>";
					print "<td> ";
						print "<b>". _('Category') ." *</b><br/>";
						print "<span style=\"font-size: 90%\"><i></i></span>";
					print "</td>";
					print "<td class=\"right\">";
						print "<select name='category' id='category' style='width:302px'>" ;
							print "<option value=''>Please select...</option>" ;						
							foreach($categoryOptions as $option) {
								$selected="" ;
								if ($option==$filter) {
									$selected="selected" ;
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
				<td>
					<b><?php print _('Description') ?> *</b><br/>
				</td>
				<td class="right">
					<textarea name='description' id='description' maxlength=1000 rows=5 style='width: 300px'></textarea>
					<script type="text/javascript">
						var name3=new LiveValidation('description');
						name3.add(Validate.Presence);
					</script>
				</td>
			</tr>
			<?php
			if(count($priorityOptions)>0) {
				print "<tr>";
					print "<td> ";
						print "<b>". $priorityName ." *</b><br/>";
						print "<span style=\"font-size: 90%\"><i></i></span>";
					print "</td>";
					print "<td class=\"right\">";
						print "<select name='priority' id='priority' style='width:302px'>" ;
							print "<option value=''>Please select...</option>" ;							
							foreach($priorityOptions as $option) {
								$selected="" ;
								if ($option==$filter) {
									$selected="selected" ;
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
			if(isTechnician($_SESSION[$guid]["gibbonPersonID"], $connection2)) {
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
								try {
									$data=array(); 
									$sql="SELECT gibbonPersonID, surname, preferredName, title FROM gibbonPerson WHERE status='Full'" ;
									$result=$connection2->prepare($sql);
									$result->execute($data);
								}
								catch(PDOException $e) { }
								while(($row = $result->fetch())!=null) {
									if(intval($row["gibbonPersonID"])!=$_SESSION[$guid]["gibbonPersonID"]) {
										print "<option value='" . $row["gibbonPersonID"] . "'>". formatName($row['title'],$row['preferredName'],$row['surname'], "Student", FALSE, FALSE) ."</option>" ; 
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
