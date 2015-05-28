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


if(isset($_GET["returnAddress"])) {
	$URL = $URL.$_GET["returnAddress"] ;
}
else {
	$URL = $URL."issues_view.php&addReturn=fail1" ;
	header("Location: {$URL}");
	exit();
}


if (isActionAccessible($guid, $connection2, "/modules/Help Desk/issues_assign.php")==FALSE && !getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "assignIssue")) {
	//Fail 0
  $URL = $URL."&addReturn=fail0" ;
	header("Location: {$URL}");
	exit();
}
else {
	//Proceed!
	
	if(isset($_POST["privacySetting"])) {
		$privacySetting = $_POST["privacySetting"];
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

	
	if(isset($_GET["issueID"])) {
		$issueID = $_GET["issueID"];
		if($_GET["returnAddress"]=="issues_discussView.php") { $URL = $URL."&issueID=$issueID" ; }
	}
	else {
    	$URL = $URL."&addReturn=fail1" ;
		header("Location: {$URL}");
		exit();
	}
		
	
	
	
	try {
		$data=array("privacySetting"=> $privacySetting, "issueID"=>$issueID);
		$sql="UPDATE helpDeskIssue SET privacySetting=:privacySetting WHERE issueID=:issueID" ;
		$result=$connection2->prepare($sql);
		$result->execute($data);
	}
	catch(PDOException $e) {
    	$URL = $URL."&addReturn=fail2" ;
    	header("Location: {$URL}");
    	exit();
	}
	
  	$URL = $URL."&addReturn=success3" ; 
	header("Location: {$URL}");


}
?>
