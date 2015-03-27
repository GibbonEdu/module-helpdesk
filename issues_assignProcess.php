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

$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/issues_view.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/issues_assign.php")==FALSE && !getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "assignIssue")) {
	//Fail 0
  $URL = $URL."&addReturn=fail0" ;
	header("Location: {$URL}");
	exit();
}
else {
	//Proceed!
	if(isset($_POST["technician"])) {
		$gibbonPersonID = $_POST["technician"];
		$technicianID = getTechnicianID($gibbonPersonID, $connection2);
	}
	else {
    $URL = $URL."&addReturn=fail1" ;
	  header("Location: {$URL}");
	  exit();
	}
	if(!getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "assignIssue") && !getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "reassignIssue")) {
    $URL = $URL."&addReturn=fail0" ;
	  header("Location: {$URL}");
	  exit();
	}

	if($technicianID==null && !getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "reassignIssue")){
    $URL = $URL."&addReturn=fail1" ;
 	  header("Location: {$URL}");
 	  exit();
 	}

	if(isset($_GET["issueID"])) {
	  $issueID = (int) $_GET["issueID"];
	}
	else {
    $URL = $URL."&addReturn=fail1" ;
	  header("Location: {$URL}");
	  exit();
	}
	
	$isReassign = false;
	if(hasTechnicianAssigned($issueID, $connection2)) {
		$isReassign = true;
	}

	try {
		$data=array("issueID"=> $issueID, "technicianID"=> $technicianID, "status"=> "Pending");
		$sql="UPDATE helpDeskIssue SET technicianID=:technicianID, status=:status WHERE issueID=:issueID" ;
		$result=$connection2->prepare($sql);
		$result->execute($data);
	}
	catch(PDOException $e) {
    $URL = $URL."&addReturn=fail2" ;
    header("Location: {$URL}");
    exit();
	}
	
	$assign = "assigned";
	if($isReassign) { $assign = "reassigned"; }
	setNotification($connection2, $guid, getOwnerOfIssue($connection2, $issueID), "Your issue has been " . $assign . " to a technician.", "Help Desk", "/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=" . $issueID);
	setNotification($connection2, $guid, getTechWorkingOnIssue($connection2, $issueID), "An issue has been " . $assign . " to you.", "Help Desk", "/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=" . $issueID);

  	$URL = $URL."&addReturn=success0" ; 
	header("Location: {$URL}");


}
?>
