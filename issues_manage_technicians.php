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
include "./modules/" . $_SESSION[$guid]["module"] . "/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/issues_manage_technicians.php")==FALSE) {
  //Acess denied
  print "<div class='error'>" ;
    print _("You do not have access to this action.") ;
  print "</div>" ;
}
else {
  //Proceed!
  print "<div class='trail'>" ;
  print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Assign Issue') . "</div>" ;
  print "</div>" ;

  try {
    $data=array();
    $sql="SELECT helpDeskTechnicians.* , surname, preferredName, title FROM helpDeskTechnicians JOIN gibbonPerson ON (helpDeskTechnicians.gibbonPersonID=gibbonPerson.gibbonPersonID)";
    $result=$connection2->prepare($sql);
    $result->execute($data);

    $sql2="SELECT helpDeskIssue.technicianID , issueName , issueID FROM helpDeskIssue JOIN helpDeskTechnicians ON (helpDeskTechnicians.technicianID=helpDeskIssue.technicianID)";
    $result2=$connection2->prepare($sql2);
    $result2->execute($data);
  } catch(PDOException $e) {
    print $e;
  }

  print "Technicians" ;

  print "<table cellspacing='0' style='width: 100%'>" ;
    print "<tr class='head'>" ;
      print "<th>" ;
        print _("Name") ;
      print "</th>" ;
      print "<th>" ;
        print _("Working On") ;
      print "</th>" ;
      print "<th>" ;
        print _("Action") ;
      print "</th>" ;
  print "</tr>" ;

  print "<a style = 'position:relative; bottom:5px; float: right' href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_createTechnician.php"."'><img title=" . _('Create Technician ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_new.png'/></a>";

  if (! $result->rowcount() == 0){
    while($row=$result->fetch()){
      print "<tr>" ;
        print "<td>". formatName($row['title'],$row['preferredName'],$row['surname'], "Student", FALSE, FALSE) ."</td>" ;
        if (! $result2->rowcount() == 0) {
          print "<td>";
          $issues = "";
          while($row2=$result2->fetch()){
            $issues.=$row2["issueName"] . ", ";
          }
          print substr($issues, 0, strlen($issues)-2);
          print "</td>";
        } else {
          print "<td> UNASSIGNED </td>" ;
        }
        print "<td>". "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/issues_technicianDeleteProcess.php?technicianID=". $row['technicianID'] ."'><img title=" . _('Delete Technician ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/iconCross.png'/></a>" ."</td>" ;

      print "</tr>" ;

    }
  } else {
    print "<tr>";
      print "<td colspan= 3>";
        print _("There are no records to display.");
      print "</td>";
    print "</tr>";
  }

  print "</table>" ;

}
?>
