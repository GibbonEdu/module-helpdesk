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

$URL = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/helpDesk_createTechnician.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicians.php") == false) {
    $URL = $URL."&return=error0" ;
    header("Location: {$URL}");
} else {
    //Proceed!
    if (isset($_POST["person"])) {
        $person = $_POST["person"] ;
    }

    if (isset($_POST["group"])) {
        $group = $_POST["group"] ;
    }

    if ($person == "" || $group == "") {
        $URL = $URL."&return=error1" ;
        header("Location: {$URL}");
    } elseif (isTechnician($connection2, $person)) {
        $URL = $URL."&return=error3" ;
        header("Location: {$URL}");
    } else {
        //Write to database
        try {
            $gibbonModuleID = getModuleIDFromName($connection2, "Help Desk");
            if($gibbonModuleID == null) {
                throw new PDOException("Invalid gibbonModuleID.");
            }
            $data = array("gibbonPersonID" => $person, "groupID" => $group);
            $sql = "INSERT INTO helpDeskTechnicians SET gibbonPersonID = :gibbonPersonID, groupID = :groupID" ;
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            print $e;
            $URL = $URL."&return=error2" ;
            header("Location: {$URL}");
            exit();
        }

        $technicianID = $connection2->lastInsertId();
        setLog($connection2, $_SESSION[$guid]["gibbonSchoolYearID"], $gibbonModuleID, $_SESSION[$guid]["gibbonPersonID"], "Technician Added", array("gibbonPersonID"=>$person, "technicianID"=>$technicianID), null);

        //Success 0
        $URL = $URL."&return=success0" ;
        header("Location: {$URL}");
    }
}
?>
