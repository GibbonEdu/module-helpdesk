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
$pdo = new Gibbon\sqlConnection();
$connection2 = $pdo->getConnection();

@session_start() ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

$URL = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/" ;

if (isset($_GET["issueID"])) {
    $issueID = $_GET["issueID"];
    $URL .= "issues_discussView.php&issueID=" . $issueID;
} else {
    $URL .= "issues_view.php&return=error1" ;
    header("Location: {$URL}");
    exit();
}

if (!relatedToIssue($connection2, $issueID, $_SESSION[$guid]["gibbonPersonID"]) && !getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], "resolveIssue") || getIssueStatus($connection2, $issueID) == "Resolved") {
    //Fail 0 aka No permission
    $URL .= "&return=error0" ;
    header("Location: {$URL}");
} else {
  //Proceed!
    if (isset($_POST["comment"])) {
        $comment = $_POST["comment"] ;
    } else {
        $URL .= "&return=error1" ;
        header("Location: {$URL}");
        exit();
    }

    try {
        $gibbonModuleID = getModuleIDFromName($connection2, "Help Desk");
        if ($gibbonModuleID == null) {
            throw new PDOException("Invalid gibbonModuleID.");
        }

        $data = array("issueID" => $issueID, "comment" => $comment, "timestamp" => date("Y-m-d H:i:a"), "gibbonPersonID" => $_SESSION[$guid]["gibbonPersonID"]) ;
        $sql = "INSERT INTO helpDeskIssueDiscuss SET issueID=:issueID, comment=:comment, timestamp=:timestamp, gibbonPersonID=:gibbonPersonID" ;
        $result = $connection2->prepare($sql);
        $result->execute($data);
        $issueDiscussID = $connection2->lastInsertId();

        $data2 = array("issueID" => $issueID) ;
        $sql2 = "SELECT issueName FROM helpDeskIssue WHERE issueID=:issueID" ;
        $result2 = $connection2->prepare($sql2);
        $result2->execute($data2);

    } catch (PDOException $e) {
        //Fail 2
        $URL=$URL . "&return=error2" ;
        header("Location: {$URL}");
        exit();
    }

    $row = $result2->fetch();
    $isTech = isTechnician($connection2, $_SESSION[$guid]["gibbonPersonID"]) && !isPersonsIssue($connection2, $issueID, $_SESSION[$guid]["gibbonPersonID"]);

    $message = "A new message has been added to Issue ";
    $message .= $issueID;
    $message .= " (" . $row["issueName"] . ").";

    $personIDs = getPeopleInvolved($connection2, $issueID);

    foreach ($personIDs as $personID) {
        if ($personID != $_SESSION[$guid]["gibbonPersonID"]) {
            setNotification($connection2, $guid, $personID, $message, "Help Desk", "/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=" . $issueID);
        } 
    }

    $array = array("issueDiscussID" => $issueDiscussID);

    if ($isTech) {
        $array['technicianID'] = getTechnicianID($connection2, $_SESSION[$guid]["gibbonPersonID"]);
    } 

    setLog($connection2, $_SESSION[$guid]["gibbonSchoolYearID"], $gibbonModuleID, $_SESSION[$guid]["gibbonPersonID"], "Discussion Posted", $array, null);

    $URL .= "&return=success0" ;
    header("Location: {$URL}");
}
?>
