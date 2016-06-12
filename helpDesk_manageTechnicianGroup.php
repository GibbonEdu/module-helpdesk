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

@session_start() ;

include "./modules/Help Desk/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicianGroup.php")==FALSE) {
    //Acess denied
    print "<div class='error'>" ;
        print __($guid, "You do not have access to this action.") ;
    print "</div>" ;
} else {
    //Proceed!
    print "<div class='trail'>" ;
        print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . __($guid, "Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . __($guid, getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . __($guid, 'Manage Technician Groups') . "</div>" ;
    print "</div>" ;
  
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, array("errorA" => "Cannot delete last technician group."));
    }

    $groupID = null;  
    $data=array();
    $whereGroup = "";
    if(isset($_GET['groupID'])) {
        $groupID = $_GET['groupID'];
        $data['groupID'] = $groupID;
        $whereGroup = " WHERE groupID=:groupID";
    }

    try {
        $sql="SELECT * FROM helpDeskTechGroups" . $whereGroup . " ORDER BY helpDeskTechGroups.groupID ASC";
        $result=$connection2->prepare($sql);
        $result->execute($data);
    
        $sql2="SELECT * FROM helpDeskTechnicians JOIN gibbonPerson ON (helpDeskTechnicians.gibbonPersonID=gibbonPerson.gibbonPersonID)" . $whereGroup;
        $result2=$connection2->prepare($sql2);
        $result2->execute($data);
    } catch (PDOException $e) {
    }

    $techs = $result2->fetchAll();
    print "<h3>";
        print "Technician Groups" ;
    print "</h3>";
    print "<div class='linkTop'>" ;
        print "<a style='position:relative; bottom:5px;float:right;' href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_createTechnicianGroup.php'><img style='margin-left: 2px' title=" . __($guid, 'Create ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_new.png'/></a>";
        print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_createTechnicianGroup.php'>" .  __($guid, 'Create') . "</a>";
    print "</div>" ;
    print "<table cellspacing='0' style='width: 100%'>" ;
        print "<tr class='head'>" ;
            print "<th>" ;
                print __($guid, "Group Name") ;
            print "</th>" ;
            print "<th>" ;
                print __($guid, "Technicians in group") ;
            print "</th>" ;
            print "<th>" ;
                print __($guid, "Actions") ;
            print "</th>" ;
        print "</tr>" ;

        if (!$result->rowcount() == 0) {
            $rowCount = 0;
            while ($row = $result->fetch()) {
                if ($rowCount % 2 == 0) {
                    $class = "even";
                } else {
                    $class = "odd";
                }
                print "<tr class='$class'>"
                    print "<td>" . $row['groupName'] . "</td>" ;
                    print "<td> ";
                        $techsIn = "";
                        foreach($techs as $row2){
                            if($row['groupID'] == $row2['groupID']) { $techsIn.= formatName($row2['title'],$row2['preferredName'],$row2['surname'], "Student", FALSE, FALSE) . ", "; }
                        }
                        $techsIn = substr($techsIn, 0, strlen($techsIn)-2);
                        if (strlen($techsIn) > 0) {
                            print $techsIn;
                        } else {
                            print "No one is currently in this group.";
                        }
                    print "</td>";
                    print "<td>";
                        print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_editTechnicianGroup.php&groupID=". $row['groupID'] ."'><img title=" . __($guid, 'Edit ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a>";
                        if($result->rowcount() > 1) { print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_technicianGroupDelete.php&groupID=". $row['groupID'] ."'><img title=" . __($guid, 'Delete Technician Group ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/garbage.png'/></a>"; }
                    print"</td>" ;
                print "</tr>" ;
                $rowCount++;
            }
        } else {
            print "<tr>";
                print "<td colspan= 3>";
                    print __($guid, "There are no records to display.");
                print "</td>";
            print "</tr>";
        }
    print "</table>" ;
}
?>
