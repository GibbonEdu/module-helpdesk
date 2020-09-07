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
    //This could, in theory, be made for effiicent, however, I don't care to do so.
    return array_filter(array_map('trim', explode(',', $commaSeperatedString)));
}

function privacyOptions() {
    return array(
        'Everyone',
        'Related',
        'Owner',
        'No one',
    );
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
