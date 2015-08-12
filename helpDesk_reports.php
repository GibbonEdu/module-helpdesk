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
  print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Reports') . "</div>" ;
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
  /*
	$stats = array();
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
    print "Statistics" ;
  print "</h3>";
  print "<table cellspacing='0' style='width: 100%'>" ;
    print "<tr class='head'>" ;
      print "<th>" ;
        print _("Name") ;
      print "</th>" ;
      print "<th>" ;
        print _("Value") ;
      print "</th>" ;
  print "</tr>" ;

  if (! $result->rowcount() == 0){
  	$rowCount=0;
    while($row=$result->fetch()){
		$class = "odd";
		if($rowCount%2 == 0) {
			$class = "even";
		}
        print "<tr class='$class'>";
        	print "<td>";
        		
        	print "</td>";
        	print "<td>";
        	
        	print "</td>";
      	print "</tr>" ;
		$rowCount++;
    }
  } else {
    print "<tr>";
      print "<td colspan= 4>";
        print _("There are no records to display.");
      print "</td>";
    print "</tr>";
  }

  print "</table>" ;
	*/
}
?>
