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

$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=".$_GET["issueID"] ;

if(isset($_GET["issueID"])) {
  $issueID = $_GET["issueID"];
}
else {
  $URL=$URL . "&addReturn=fail1" ;
  header("Location: {$URL}");
}


if (!relatedToIssue($connection2, $issueID, $_SESSION[$guid]["gibbonPersonID"]) && !getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "resolveIssue") || getIssueStatus($connection2, $issueID)=="Resolved") {
  //Fail 0 aka No permission
  $URL=$URL . "&addReturn=fail0" ;
  header("Location: {$URL}");
}
else {
  //Proceed!
  if(isset($_POST["comment"])) {
    $comment=$_POST["comment"] ;
  }
  else {
    $URL=$URL . "&addReturn=fail1" ;
    header("Location: {$URL}");
  }
  //Write to database

  $isTech = isTechnician($_SESSION[$guid]["gibbonPersonID"], $connection2) && !isPersonsIssue($connection2, $issueID, $_SESSION[$guid]["gibbonPersonID"]);

  try {
    $data=array("issueDiscussID"=>0 , "issueID"=>$issueID, "comment"=>$comment, "timestamp" => date("Y-m-d H:i:a"), "gibbonPersonID" => $_SESSION[$guid]["gibbonPersonID"]) ;
    $sql="INSERT INTO helpDeskIssueDiscuss SET issueDiscussID=:issueDiscussID, issueID=:issueID, comment=:comment, timestamp=:timestamp, gibbonPersonID=:gibbonPersonID" ;
    $result=$connection2->prepare($sql);
    $result->execute($data);
    
    $data2=array("issueID"=>$issueID) ;
    $sql2="SELECT issueName FROM helpDeskIssue WHERE issueID=:issueID" ;
    $result2=$connection2->prepare($sql2);
    $result2->execute($data2);
    $row = $result2->fetch();
  } catch(PDOException $e) {
    //Fail 2
    $URL=$URL . "&addReturn=fail2" ;
    header("Location: {$URL}");
    break ;
  }

$isTech = isTechnician($_SESSION[$guid]["gibbonPersonID"], $connection2) && !isPersonsIssue($connection2, $issueID, $_SESSION[$guid]["gibbonPersonID"]);

  $message = "A new message has been left for you ";
  if($isTech) {
  	$message.= "by the technician working on your issue";
  }
  else {
  	$message.= "by the person who has the issue";
  }
  
  
  $message.=" (" . $row["issueName"] . ").";
  $personID = 0000000000;
  if($isTech) {
      $personID = getOwnerOfIssue($connection2, $issueID);
  }
  else {
    	$personID = getTechWorkingOnIssue($connection2, $issueID);
  }
  setNotification($connection2, $guid, $personID, $message, "Help Desk", "/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=" . $issueID);  
  
  //Success 2 aka Posted
  $URL=$URL . "&addReturn=success2" ;
  header("Location: {$URL}");
}
?>
