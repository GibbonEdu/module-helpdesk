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

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicianGroup.php")==FALSE) {
	$URL = $URL."/helpDesk_createTechnicianGroup.php&addReturn=fail0" ; 
  header("Location: {$URL}");
}
else {
  //Proceed!
  if(isset($_POST["groupName"])) {
    $groupName=$_POST["groupName"] ;
  }

  if ($groupName == "") {
  	$URL = $URL."/helpDesk_createTechnicianGroup.php&addReturn=fail1" ; 
    header("Location: {$URL}");
  }
  else {
    //Write to database

    try {
    	$gibbonModuleID = getModuleIDFromName($connection2, "Help Desk");
		if($gibbonModuleID == null) {
			throw new PDOException("Invalid gibbonModuleID.");
		}
		
      $data=array("groupName"=>$groupName);
      $sql="INSERT INTO helpDeskTechGroups SET groupName=:groupName" ;
      $result=$connection2->prepare($sql);
      $result->execute($data);
    }
    catch(PDOException $e) {
      $URL = $URL."/helpDesk_createTechnicianGroup.php&addReturn=fail2" ; 
      header("Location: {$URL}");
      exit();
    }
    
    $groupID = $connection2->lastInsertId();

	setLog($connection2, $_SESSION[$guid]["gibbonSchoolYearID"], $gibbonModuleID, $_SESSION[$guid]["gibbonPersonID"], "Technician Group Added", array("groupID"=>$groupID));

    //Success 0
    $URL = $URL."/helpDesk_editTechnicianGroup.php&groupID=$groupID&addReturn=success0" ; 
    header("Location: {$URL}");

  }
}
?>
