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

include "../../functions.php" ;
include "../../config.php" ;

include "./moduleFunctions.php" ;

//New PDO DB connection
try {
  	$connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}

@session_start() ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/" ;

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/issues_view.php")==FALSE) {
	//Fail 0
	$URL=$URL . "issues_view.php&addReturn=fail0" ;
	header("Location: {$URL}");
}
else {
	//Proceed!
	$issueID = intval($_GET["issueID"]) ;
	if ($issueID=="" || hasTechnicianAssigned($connection2, $issueID)) {
		//Fail 3
		$URL=$URL . "issues_view.php&addReturn=fail1" ;
		header("Location: {$URL}");
	}
	else {
		if (isTechnician($connection2, $_SESSION[$guid]["gibbonPersonID"]) && getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "acceptIssue")) {
			$technicianID = getTechnicianID($connection2, $_SESSION[$guid]["gibbonPersonID"]);

			//Write to database
			try {
				$gibbonModuleID = getModuleIDFromName($connection2, "Help Desk");
				if($gibbonModuleID == null) {
					throw new PDOException("Invalid gibbonModuleID.");
				}
				
				$data=array("issueID"=> $issueID, "technicianID"=> $technicianID, "status"=> "Pending");
				$sql="UPDATE helpDeskIssue SET technicianID=:technicianID, status=:status WHERE issueID=:issueID" ;
				$result=$connection2->prepare($sql);
				$result->execute($data);
			}
			catch(PDOException $e) {
				//Fail 2q
				print $e;
				$URL=$URL . "issues_view.php&addReturn=fail2" ;
				header("Location: {$URL}");
				break ;
			}

			include "../../version.php";
		
			setNotification($connection2, $guid, getOwnerOfIssue($connection2, $issueID)['gibbonPersonID'], "A technician has started working on your isuse.", "Help Desk", "/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=" . $issueID);
			if($version>=11) {
				setLog($connection2, $_SESSION[$guid]["gibbonSchoolYearID"], $gibbonModuleID, $_SESSION[$guid]["gibbonPersonID"], "Issue Accepted", array("issueID"=>$issueID, "technicianID"=>$technicianID), null);
			}
			else if($version<11 && $version >=10) {
				setLog($connection2, $_SESSION[$guid]["gibbonSchoolYearID"], $gibbonModuleID, $_SESSION[$guid]["gibbonPersonID"], "Issue Accepted", array("issueID"=>$issueID, "technicianID"=>$technicianID));
			}

			//Success 1 aka Accepted
			$URL=$URL . "issues_discussView.php&issueID=" . $issueID . "&addReturn=success1" ;
			header("Location: {$URL}");
		}
		else
		{
			$URL=$URL . "issues_view.php&addReturn=fail0" ;
			header("Location: {$URL}");
		}
	}
}
?>