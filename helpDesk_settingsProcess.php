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

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

$URL = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/helpDesk_settings.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_settings.php") == false) {
    //Fail 0
    $URL = $URL . "&return=error0" ;
    header("Location: {$URL}");
} else {

    if (!(isset($_POST["issuePriority"]) || isset($_POST["issueCategory"]) || isset($_POST["issuePriorityName"]) || isset($_POST["resolvedIssuePrivacy"]))) {
        $URL = $URL . "&return=error1" ;
        header("Location: {$URL}");
    }
    //Proceed!
    $issuePriority = "" ;
    foreach (explode(",", $_POST["issuePriority"]) as $type) {
        $issuePriority .= trim($type) . "," ;
    }
    $issuePriority = substr($issuePriority,0,-1) ;
    $issueCategory = "" ;
    foreach (explode(",", $_POST["issueCategory"]) as $type) {
        $issueCategory .= trim($type) . "," ;
    }
    $issueCategory = substr($issueCategory,0,-1) ;
    $fails = 0;

    $gibbonModuleID = getModuleIDFromName($connection2, "Help Desk");
    if ($gibbonModuleID == null) {
        $URL = $URL . "&return=error2" ;
        header("Location: {$URL}");
    }

    try {
        $data = array("value" => $issuePriority);
        $sql = "UPDATE gibbonSetting SET value=:value WHERE scope='Help Desk' AND name='issuePriority'" ;
        $result = $connection2->prepare($sql);
        $result->execute($data);
    } catch (PDOException $e) { 
        $fails++;
    }

    try {
        $data = array("value" => $issueCategory);
        $sql = "UPDATE gibbonSetting SET value=:value WHERE scope='Help Desk' AND name='issueCategory'" ;
        $result = $connection2->prepare($sql);
        $result->execute($data);
    } catch (PDOException $e) { 
        $fails++;
    }

    try {
        $data = array("value" => $_POST["issuePriorityName"]);
        $sql = "UPDATE gibbonSetting SET value=:value WHERE scope='Help Desk' AND name='issuePriorityName'" ;
        $result = $connection2->prepare($sql);
        $result->execute($data);
    } catch (PDOException $e) { 
        $fails++;
    }

    try {
        $data = array("value" => $_POST["resolvedIssuePrivacy"]);
        $sql = "UPDATE gibbonSetting SET value=:value WHERE scope='Help Desk' AND name='resolvedIssuePrivacy'" ;
        $result = $connection2->prepare($sql);
        $result->execute($data);
    } catch (PDOException $e) { 
        $fails++;
    }

    if ($fails == 4) {
        //Fail 2
        $URL = $URL . "&return=error2" ;
        header("Location: {$URL}");
    } else {

        setLog($connection2, $_SESSION[$guid]["gibbonSchoolYearID"], $gibbonModuleID, $_SESSION[$guid]["gibbonPersonID"], "Help Desk Settings Edited", null);

        getSystemSettings($guid, $connection2) ;
        $return = "success0";
        if ($fails > 0) {
            $return = "warning1";
        }
        $URL=$URL . "&return=$return" ;
        header("Location: {$URL}");
    }
}
?>
