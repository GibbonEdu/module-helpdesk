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

use Gibbon\Module\HelpDesk\Domain\TechGroupGateway;

include "../../functions.php" ;
include "../../config.php" ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

$URL = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/helpDesk_editTechnicianGroup.php" ;

//Check that groupID is given
if (isset($_GET["groupID"])) {
    $groupID = $_GET["groupID"];
    $URL = $URL . "&groupID=$groupID";
} else {
    $URL = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/helpDesk_manageTechnicianGroup.php&return=error1";
    header("Location: {$URL}");
    exit();
}

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicianGroup.php") == false) {
    //Fail 0
    $URL = $URL . "&return=error0" ;
    header("Location: {$URL}");
    exit();
} else {

    if (!isset($_POST["groupName"]) || !isset($_POST["viewIssueStatus"])) {
        $URL = $URL . "&return=error1" ;
        header("Location: {$URL}");
        exit();
    }


    $groupName = $_POST["groupName"];
    $viewIssueStatus = $_POST["viewIssueStatus"];

    $settings = array('viewIssue', 'assignIssue', 'acceptIssue', 'resolveIssue', 'createIssueForOther', 'reassignIssue', 'reincarnateIssue', 'fullAccess');

    $data = array(
        'groupName' => $groupName,
        'viewIssueStatus' => $viewIssueStatus,
    );

    foreach ($settings as $setting) {
        $data[$setting] = isset($_POST[$setting]);
    }

    try {
        $gibbonModuleID = getModuleIDFromName($connection2, "Help Desk");
        if ($gibbonModuleID == null) {
            throw new PDOException("Invalid gibbonModuleID.");
        }

        $techGroupGateway = $container->get(TechGroupGateway::class);

        if (!$techGroupGateway->unique($data, ['groupName'], $groupID)) {
            $URL .= '&return=error7';
            header("Location: {$URL}");
            exit();
        }

        $techGroupGateway->update($groupID, $data);
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
