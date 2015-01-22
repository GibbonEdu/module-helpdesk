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

$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_POST["address"]) ;

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/issues_create.php")==FALSE) {
	$URL.= "&addReturn=fail0";
	header("Location: {$URL}");
}
else {

	$personID = $_SESSION[$guid]["gibbonPersonID"];
	//Proceed!
	if(isset($_POST["name"])) {
	  $name=$_POST["name"] ;
	}
	$category = "";
	if(isset($_POST["category"])) {
	  $category=$_POST["category"] ;
	}
	if(isset($_POST["description"])) {
	  $description=$_POST["description"] ;
	}
	$priority = "";
	if(isset($_POST["priority"])) {
	  $priority=$_POST["priority"] ;
	}
	
	$createdByID = $_SESSION[$guid]["gibbonPersonID"];
	$personID = $_SESSION[$guid]["gibbonPersonID"];
	if(isset($_POST["createFor"])) {
	  if($_POST["createFor"] != 0) {
		  $personID = $_POST["createFor"];
		  $createdByID = $_SESSION[$guid]["gibbonPersonID"];
	  }
	}

	if ($name=="" || $description=="") {
		//Fail 3
		$URL=$URL  . "/issues_create.php&addReturn=fail1" ;
		header("Location: {$URL}");
	}
	else {
		//Write to database
		try {
			$data=array("issueID"=> 0, "technicianID"=>null, "gibbonPersonID"=> $personID, "name"=> $name, "description"=> $description, "date" => date("Y-m-d"), "status"=> "Unassigned", "category"=> $category, "priority"=> $priority, "gibbonSchoolYearID"=> $_SESSION[$guid]["gibbonSchoolYearID"], "createdByID"=> $createdByID);
			$sql="INSERT INTO helpDeskIssue SET issueID=:issueID, technicianID=:technicianID, gibbonPersonID=:gibbonPersonID, issueName=:name, description=:description, date=:date, status=:status, category=:category, priority=:priority, gibbonSchoolYearID=:gibbonSchoolYearID, createdByID=:createdByID" ;
      		$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) {
			$URL=$URL . "/issues_create.php&addReturn=fail2";
			header("Location: {$URL}");
			break ;
		}

		$issueID = $connection2->lastInsertId();
		setNotification($connection2, $guid, $personID, "A new issue has been created on your behalf.", "Help Desk", "/index.php?q=/modules/Help Desk/issues_discuss_view.php&issueID=" . $issueID);
		notifyTechnican($connection2, $guid, $issueID);
		//Success 0 aka Created
		$URL=$URL . "/issues_discuss_view.php&issueID=" . $issueID . "&addReturn=success0" ;
		header("Location: {$URL}");

	}
}
?>
