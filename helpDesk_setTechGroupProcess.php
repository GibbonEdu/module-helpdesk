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

$URL = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/helpDesk_setTechGroup.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicians.php") == false) {
    $URL = $URL."&return=fail0" ;
    header("Location: {$URL}");
} else {
    //Proceed!
    $techFail = false;
    if (isset($_GET["technicianID"])) {
        $technicianID=$_GET["technicianID"] ;
        if ($group == "") {
            $techFail = true;
        }
    }
    else {
        $techFail = true;
    }

    if ($techFail) {
        $URL = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/helpDesk_manageTechnicians.php&return=error1";
        header("Location: {$URL}");
        exit();
    }
    else {
        $URL .= "&technicianID=$technicianID";
    }

    $groupFail = false;
    if (isset($_POST["group"])) {
        $group = $_POST["group"] ;
        if ($group == "") {
            $groupFail = true;
        }
    }
    else {
        $groupFail = true;
    }

    if ($groupFail) {
        $URL .= "&return=error1";
        header("Location: {$URL}");
        exit();
    }

    //Write to database
    try {
        $gibbonModuleID = getModuleIDFromName($connection2, "Help Desk");
        if ($gibbonModuleID == null) {
            throw new PDOException("Invalid gibbonModuleID.");
        }

        $data = array("technicianID" => $technicianID, "groupID" => $group);
        $sql = "UPDATE helpDeskTechnicians SET groupID=:groupID WHERE technicianID=:technicianID;" ;
        $result = $connection2->prepare($sql);
        $result->execute($data);
    }
    catch (PDOException $e) {
        $URL .= "&return=fail2" ;
        header("Location: {$URL}");
        exit();
    }

    setLog($connection2, $_SESSION[$guid]["gibbonSchoolYearID"], $gibbonModuleID, $_SESSION[$guid]["gibbonPersonID"], "Technician Group Set", array("technicianID"=>$technicianID, "groupID"=>$group), null);

    //Success 0
    $URL = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/helpDesk_manageTechnicians.php&return=success0" ;
    header("Location: {$URL}");
}
?>
