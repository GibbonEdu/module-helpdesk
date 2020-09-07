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
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

function explodeTrim($commaSeperatedString) {
    return array_filter(array_map('trim', explode(',', $commaSeperatedString)));
}

function notifyTechnican($connection2, $guid, $issueID, $name, $personID)
{
    try {
        $data = array();
        $sql = "SELECT * FROM helpDeskTechnicians";
        $result = $connection2->prepare($sql);
        $result->execute($data);
    } catch (PDOException $e) {
    }

    while ($row = $result->fetch()) {
        $permission = getPermissionValue($connection2, $row["gibbonPersonID"], "viewIssueStatus");
        if ($row["gibbonPersonID"] != $gibbon->session->get('gibbonPersonID') && $row["gibbonPersonID"] != $personID && ($permission == "UP" || $permission == "All")) {
            setNotification($connection2, $guid, $row["gibbonPersonID"], "A new issue has been added (" . $name . ").", "Help Desk", "/index.php?q=/modules/Help Desk/issues_discussView.php&issueID=" . $issueID);
        }
    }
}

function relatedToIssue($connection2, $issueID, $gibbonPersonID)
{
    $isRelated = false;

    $personIDs = getPeopleInvolved($connection2, $issueID);
    foreach ($personIDs as $personID) {
        if ($personID == $gibbonPersonID) {
            $isRelated = true;
        }
    }

    if (getPermissionValue($connection2, $gibbonPersonID, "fullAccess")) {
        $isRelated = true;
    }

    return $isRelated;
}

//Only used in this file, to be removed when dependent functions are removed
function getPermissionValue($connection2, $gibbonPersonID, $permission)
{
    try {
        $data = array("gibbonPersonID"=> $gibbonPersonID);
        $sql = "SELECT helpDeskTechnicians.groupID, helpDeskTechGroups.* FROM helpDeskTechnicians JOIN helpDeskTechGroups ON (helpDeskTechnicians.groupID=helpDeskTechGroups.groupID) WHERE helpDeskTechnicians.gibbonPersonID=:gibbonPersonID";
        $result = $connection2->prepare($sql);
        $result->execute($data);
        $row = $result->fetch();
    }
    catch (PDOException $e) {
    }

    if ($row['fullAccess'] == true) {
        if ($permission=="viewIssueStatus") {
            return "All";
        } else {
            return true;
        }
    }
    return $row[$permission];
}

function getPeopleInvolved($connection2, $issueID)
{
    try {
        $data = array("issueID"=> $issueID);
        $sql = "SELECT helpDeskIssue.gibbonPersonID AS personID1, helpDeskTechnicians.gibbonPersonID AS personID2 FROM helpDeskIssue LEFT JOIN helpDeskTechnicians ON (helpDeskIssue.technicianID = helpDeskTechnicians.technicianID) WHERE issueID=:issueID";
        $result = $connection2->prepare($sql);
        $result->execute($data);

        $sql2 = "SELECT gibbonPersonID AS personID FROM helpDeskIssueDiscuss WHERE issueID=:issueID;";
        $result2 = $connection2->prepare($sql2);
        $result2->execute($data);
    } catch (PDOException $e) {
    }

    $personIDs = array();
    $row = $result->fetch();
    if (isset($row["personID1"])) {
        array_push($personIDs, $row["personID1"]);
    }
    if (isset($row["personID2"])) {
        array_push($personIDs, $row["personID2"]);
    }
    while ($row2 = $result2->fetch()) {
        if (isset($row2["personID"])) {
            if (getPermissionValue($connection2, $row2["personID"], "fullAccess") && !in_array($row2["personID"], $personIDs)) {
                array_push($personIDs, $row2["personID"]);
            }
        }
    }
    return $personIDs;
}
?>
