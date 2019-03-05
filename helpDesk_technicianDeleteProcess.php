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

$URL = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/helpDesk_manageTechnicians.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicians.php") == false) {
    //Fail 0
    $URL = $URL . "&return=error0" ;
    header("Location: {$URL}");
} else {
    //Proceed!
    if (isset($_GET["technicianID"])) {
        $technicianID = $_GET["technicianID"] ;
    } else {
        $URL = $URL . "&return=error1" ;
        header("Location: {$URL}");
    }

    //Write to database
    try {
        $gibbonModuleID = getModuleIDFromName($connection2, "Help Desk");
        if ($gibbonModuleID == null) {
            throw new PDOException("Invalid gibbonModuleID.");
        }

        $data = array("technicianID" => $technicianID);

        $sql = "SELECT gibbonPersonID FROM helpDeskTechnicians WHERE helpDeskTechnicians.technicianID=:technicianID" ;
        $result = $connection2->prepare($sql3);
        $result->execute($data);

        $sql2 = "DELETE FROM helpDeskTechnicians WHERE helpDeskTechnicians.technicianID=:technicianID" ;
        $result2 = $connection2->prepare($sql);
        $result2->execute($data);

        $sql3 = "UPDATE helpDeskIssue SET helpDeskIssue.technicianID=null, helpDeskIssue.status='Unassigned' WHERE helpDeskIssue.technicianID=:technicianID" ;
        $result3 = $connection2->prepare($sql2);
        $result3->execute($data);


    } catch (PDOException $e) {
        //Fail 2
        $URL = $URL."&return=error2" ;
        header("Location: {$URL}");
    }

    $row = $result->fetch();
    setLog($connection2, $_SESSION[$guid]["gibbonSchoolYearID"], $gibbonModuleID, $_SESSION[$guid]["gibbonPersonID"], "Technician Removed", array("gibbonPersonID" => $row['gibbonPersonID']), null);

    //Success 0
    $URL = $URL . "&return=success0" ;
    header("Location: {$URL}");
}
?>
