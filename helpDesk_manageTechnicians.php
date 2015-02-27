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

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicians.php")==FALSE) {
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
  
  if (isset($_GET["addReturn"])) { $addReturn=$_GET["addReturn"] ; } else { $addReturn="" ; }
  $addReturnMessage="" ;
  $class="error" ;
  if (!($addReturn=="")) {
    if ($addReturn=="fail0") {
      $addReturnMessage=_("Your request failed because you do not have access to this action.") ;
    }
    else if ($addReturn=="fail2") {
      $addReturnMessage=_("Your request failed due to a database error.") ;
    }
    else if ($addReturn=="fail3") {
      $addReturnMessage=_("Your request failed because your inputs were invalid.") ;
    }
    else if ($addReturn=="fail4") {
      $addReturnMessage="Your request failed because your inputs were invalid." ;
    }
    else if ($addReturn=="fail5") {
      $addReturnMessage="Your request was successful, but some data was not properly saved." ;
    }
    else if ($addReturn=="success0") {
      $addReturnMessage=_("Your request was completed successfully. You can now add another record if you wish.") ;
      $class="success" ;
    }
    print "<div class='$class'>" ;
    print $addReturnMessage;
    print "</div>" ;
  }
  

  try {
    $data=array();
    $sql="SELECT helpDeskTechnicians.* , surname, preferredName, title, groupName FROM helpDeskTechnicians JOIN gibbonPerson ON (helpDeskTechnicians.gibbonPersonID=gibbonPerson.gibbonPersonID) JOIN helpDeskTechGroups ON (helpDeskTechnicians.groupID=helpDeskTechGroups.groupID) ORDER BY helpDeskTechnicians.technicianID ASC";
    $result=$connection2->prepare($sql);
    $result->execute($data);

    $sql2="SELECT helpDeskIssue.technicianID , issueName , issueID FROM helpDeskIssue JOIN helpDeskTechnicians ON (helpDeskTechnicians.technicianID=helpDeskIssue.technicianID) ORDER BY helpDeskIssue.issueID ASC";
    $result2=$connection2->prepare($sql2);
    $result2->execute($data);
  } catch(PDOException $e) {
    print $e;
  }
  
  print "<h3>";
    print "Technicians" ;
  print "</h3>";
  print "<table cellspacing='0' style='width: 100%'>" ;
    print "<tr class='head'>" ;
      print "<th>" ;
        print _("Name") ;
      print "</th>" ;
      print "<th>" ;
        print _("Working On") ;
      print "</th>" ;
      print "<th>" ;
        print _("Group") ;
      print "</th>" ;
      print "<th>" ;
        print _("Action") ;
      print "</th>" ;
  print "</tr>" ;

print "<div class='linkTop'>" ;
    print "<a style='position:relative; bottom:5px;float:right;' href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_createTechnician.php'><img title=" . _('Create ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_new.png'/></a>";
  	print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_createTechnician.php'>" .  _('Create') . "</a>";
  print "</div>" ;
  if (! $result->rowcount() == 0){
    while($row=$result->fetch()){
      print "<tr>" ;
        print "<td>". formatName($row['title'],$row['preferredName'],$row['surname'], "Student", FALSE, FALSE) ."</td>" ;
        print "<td> ";
        $issues = "";
          while($row2=$result2->fetch()){
            $issues.= "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_discussView.php&issueID=". $row2["issueID"] . "'>". $row2["issueName"] . "</a>, ";
          }
        $issue = substr($issues, 0, strlen($issues)-2);
        if (strlen($issue) > 0) {
          print $issue;
        } else {
          print "Nothing";
        }
        print "</td>";
        print "<td>";
        	print $row["groupName"];
        print "</td>";
        print "<td>";
        print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_technicianDeleteProcess.php?technicianID=". $row['technicianID'] ."'><img title=" . _('Delete  ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/iconCross.png'/></a>";
        print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_setTechGroup.php&technicianID=". $row['technicianID'] ."'><img title=" . _('Edit ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a>";
		print "</td>";
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
