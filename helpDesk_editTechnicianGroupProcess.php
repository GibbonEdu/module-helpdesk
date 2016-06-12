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
$pdo = new Gibbon\sqlConnection();
$connection2 = $pdo->getConnection();

@session_start() ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

$URL = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/helpDesk_editTechnicianGroup.php" ;

if (isset($_GET["groupID"])) {
	$groupID = $_GET["groupID"];
	$URL = $URL . "&groupID=$groupID";
} else {
	$URL = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/helpDesk_manageTechnicianGroup.php&return=error1";
	header("Location: {$URL}");
}

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicianGroup.php") == FALSE) {
	//Fail 0
	$URL = $URL . "&return=error0" ;
	header("Location: {$URL}");
} else {

	if (!(isset($_POST["viewIssue"]) || isset($_POST["groupName"]) || isset($_POST["viewIssueStatus"]))) {
		$URL = $URL . "&return=error1" ;
		header("Location: {$URL}");
		exit();
	}
	
	$groupName = $_POST["groupName"];
	$viewIssueStatus = $_POST["viewIssueStatus"];

	$viewIssue = true;
	if (!isset($_POST["viewIssue"])) {
		$viewIssue = false;
	}

	$assignIssue = false;
	if (isset($_POST["assignIssue"])) {
		$assignIssue = true;
	}

	$acceptIssue = true;
	if (!isset($_POST["acceptIssue"])) {
		$acceptIssue = false;
	}
 
	$resolveIssue = true;
	if (!isset($_POST["resolveIssue"])) {
		$resolveIssue = false;
	}

	$createIssueForOther = true;
	if (!isset($_POST["createIssueForOther"])) {
		$createIssueForOther = false;
	}

	$reassignIssue = false;
	if (isset($_POST["reassignIssue"])) {
		$reassignIssue = true;
	}

	$reincarnateIssue = true;
	if (!isset($_POST["reincarnateIssue"])) {
		$reincarnateIssue = false;
	}

	$fullAccess = false;
	if (isset($_POST["fullAccess"])) {
		$fullAccess = true;
	}

	try {
		$gibbonModuleID = getModuleIDFromName($connection2, "Help Desk");
		if ($gibbonModuleID == null) {
			throw new PDOException("Invalid gibbonModuleID.");
		}
		$data = array("groupID" => $groupID, "groupName" => $groupName, "viewIssue" => $viewIssue, "viewIssueStatus" => $viewIssueStatus, "assignIssue" => $assignIssue, "acceptIssue" => $acceptIssue, "resolveIssue" => $resolveIssue, "createIssueForOther" => $createIssueForOther, "fullAccess" => $fullAccess, "reassignIssue" => $reassignIssue, "reincarnateIssue" => $reincarnateIssue); 
		$sql = "UPDATE helpDeskTechGroups SET viewIssue = :viewIssue, groupName = :groupName, viewIssueStatus = :viewIssueStatus, assignIssue = :assignIssue, acceptIssue = :acceptIssue, resolveIssue = :resolveIssue, createIssueForOther = :createIssueForOther, fullAccess = :fullAccess, reassignIssue = :reassignIssue, reincarnateIssue = :reincarnateIssue WHERE groupID = :groupID" ;
		$result = $connection2->prepare($sql);
		$result->execute($data);
	} catch (PDOException $e) { 
		$URL = $URL . "&return=error2" ;
		header("Location: {$URL}");
		exit();
	}

	//Success 0
	setLog($connection2, $_SESSION[$guid]["gibbonSchoolYearID"], $gibbonModuleID, $_SESSION[$guid]["gibbonPersonID"], "Technician Group Edited", array("groupID" => $groupID), null);
   	
   	$URL = $URL . "&return=success0" ;
	header("Location: {$URL}");
}
?>