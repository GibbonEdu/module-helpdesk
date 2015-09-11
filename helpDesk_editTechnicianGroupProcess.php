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

$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_POST["address"]) . "/helpDesk_manageTechnicianGroup.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicianGroup.php")==FALSE) {
	//Fail 0
	$URL=$URL . "&updateReturn=fail0" ;
	header("Location: {$URL}");
}
else {

	if(!(isset($_POST["viewIssue"]) || isset($_POST["groupName"]) || isset($_GET["groupID"]))) {
		$URL=$URL . "&updateReturn=fail1" ;
		header("Location: {$URL}");
		exit();
	}
	
	$groupID = $_GET["groupID"];
	$groupName = $_POST["groupName"];
	$viewIssue = true;
	if(!isset($_POST["viewIssue"])) { $viewIssue = false; }
	$viewIssueStatus = $_POST["viewIssueStatus"];
	$assignIssue = false;
	if(isset($_POST["assignIssue"])) { $assignIssue = true; }
	$acceptIssue = true;
	if(!isset($_POST["acceptIssue"])) { $acceptIssue = false; }
	$resolveIssue = true;
	if(!isset($_POST["resolveIssue"])) { $resolveIssue = false; }
	$createIssueForOther = true;
	if(!isset($_POST["createIssueForOther"])) { $createIssueForOther = false; }
	$reassignIssue = false;
	if(isset($_POST["reassignIssue"])) { $reassignIssue = true; }
	$reincarnateIssue = true;
	if(!isset($_POST["reincarnateIssue"])) { $reincarnateIssue = false; }
	$fullAccess = false;
	if(isset($_POST["fullAccess"])) { $fullAccess = true; }
	$fail=FALSE;
	try {
		$gibbonModuleID = getModuleIDFromName($connection2, "Help Desk");
		if($gibbonModuleID == null) {
			throw new PDOException("Invalid gibbonModuleID.");
		}
		$data=array("groupID"=>$groupID, "groupName"=>$groupName, "viewIssue"=>$viewIssue, "viewIssueStatus"=>$viewIssueStatus, "assignIssue"=>$assignIssue, "acceptIssue"=>$acceptIssue, "resolveIssue"=>$resolveIssue, "createIssueForOther"=>$createIssueForOther, "fullAccess"=>$fullAccess, "reassignIssue"=>$reassignIssue, "reincarnateIssue"=>$reincarnateIssue); 
		$sql="UPDATE helpDeskTechGroups SET viewIssue=:viewIssue, groupName=:groupName, viewIssueStatus=:viewIssueStatus, assignIssue=:assignIssue, acceptIssue=:acceptIssue, resolveIssue=:resolveIssue, createIssueForOther=:createIssueForOther, fullAccess=:fullAccess, reassignIssue=:reassignIssue, reincarnateIssue=:reincarnateIssue WHERE groupID=:groupID" ;
		$result=$connection2->prepare($sql);
		$result->execute($data);
	}
	catch(PDOException $e) { 
		$fail=TRUE ;
	}

	


	if ($fail==TRUE) {
		//Fail 2
		$URL=$URL . "&updateReturn=fail2" ;
		header("Location: {$URL}");
	}
	else {
		include "../../version.php";
		//Success 0
		if($version>=11) {
			setLog($connection2, $_SESSION[$guid]["gibbonSchoolYearID"], $gibbonModuleID, $_SESSION[$guid]["gibbonPersonID"], "Technician Group Edited", array("groupID"=>$groupID), null);
	    }
	    else if($version<11 && $version >=10) {
			setLog($connection2, $_SESSION[$guid]["gibbonSchoolYearID"], $gibbonModuleID, $_SESSION[$guid]["gibbonPersonID"], "Technician Group Edited", array("groupID"=>$groupID));
	    }
		$URL=$URL . "&updateReturn=success0" ;
		header("Location: {$URL}");
	}
}
?>