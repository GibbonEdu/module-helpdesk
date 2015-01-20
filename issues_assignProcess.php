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


if (isActionAccessible($guid, $connection2, "/modules/Help Desk/issues_assign.php")==FALSE) {
	//Fail 0
	header("Location: {$URL}");
	exit();
}
else {
	//Proceed!
	if(isset($_POST["technician"])) {
		$technician = $_POST["technician"];
	}
	else {
	  header("Location: {$URL}");
	  exit();
	}
	$technicianID = getTechnicianIDViaName($connection2, $technician);
	$highestAction=getHighestGroupedAction($guid, "/modules/Help Desk/issues_assign.php", $connection2) ;
	if ($highestAction==FALSE) {
		print "<div class='error'>" ;
		print _("The highest grouped action cannot be determined.") ;
		print "</div>" ;
	}
	if(!($highestAction=="View issues_All&Assign")) {
	  header("Location: {$URL}");
	  exit();
	}

	if($technicianID==null){
 	  header("Location: {$URL}");
 	  exit();
 	}

	if(isset($_GET["issueID"])) {
	  $issueID = (int) $_GET["issueID"];
	}
	else {
	  header("Location: {$URL}");
	  exit();
	}

	try {
		$data=array("issueID"=> $issueID, "technicianID"=> $technicianID, "status"=> "Pending");
		$sql="UPDATE helpDeskIssue SET technicianID=:technicianID, status=:status WHERE issueID=:issueID" ;
		$result=$connection2->prepare($sql);
		$result->execute($data);
	}
	catch(PDOException $e) {
		//Fail 2q
// 		$fail = TRUE;
	}

	header("Location: {$URL}");

// 	if ($fail==TRUE) {
		//Fail 2
// 		header("Location: {$URL}");
// 	}
// 	else {
		//Success 0
// 	header("Location: {$URL}");
// 	}
}
?>
