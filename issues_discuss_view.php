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

@session_start() ;

include "./modules/Help Desk/moduleFunctions.php" ;

if (isModuleAccessible($guid, $connection2)==FALSE || !isTechnician($_SESSION[$guid]["gibbonPersonID"], $connection2)) {
  //Acess denied
  print "<div class='error'>" ;
    print "You do not have access to this action." ;
  print "</div>" ;
  exit();
}
else {
  //New PDO DB connection.
  //Gibbon uses PDO to connect to databases, rather than the PHP mysql classes, as they provide paramaterised connections, which are more secure.
  try {
    $connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword);
    $connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  }
  catch(PDOException $e) {
    echo $e->getMessage();
  }

  $issueID=$_GET["issueID"] ;
  $data=array("issueID"=>$issueID) ;

  try {
    $sql="SELECT helpDeskIssue.* , surname , preferredName , title FROM helpDeskIssue JOIN gibbonPerson ON (helpDeskIssue.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE issueID=:issueID " ;
    $result=$connection2->prepare($sql);
    $result->execute($data);
    $array = $result->fetchall();

    $sql2="SELECT helpDeskTechnicians.*, surname , title, preferredName FROM helpDeskIssue JOIN helpDeskTechnicians ON (helpDeskIssue.technicianID=helpDeskTechnicians.technicianID) JOIN gibbonPerson ON (helpDeskTechnicians.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE issueID=:issueID " ;
    $result2=$connection2->prepare($sql2);	
    $result2->execute($data);
    $array2 = $result2->fetchall();

    $sql3="SELECT * FROM helpDeskIssueDiscuss WHERE issueID=:issueID ORDER BY timestamp ASC" ;
    $result3=$connection2->prepare($sql3);
    $result3->execute($data);
    //$array3 = $result3->fetchall();

    $studentName = formatName($array[0]["title"] , $array[0]["preferredName"] , $array[0]["surname"] , "Student", FALSE, FALSE);

    if (!isset($array2[0]["gibbonPersonID"])) {
      $technicianName = "UNASSIGNED" ;
    } else {
      $technicianName = formatName($array2[0]["title"] , $array2[0]["preferredName"] , $array2[0]["surname"] , "Student", FALSE, FALSE);
    }

  }
  catch(PDOException $e) {
    print $e ;
  }

  print "<div class='trail'>" ;
  print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . _("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . _(getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . _('Discuss Issue') . "</div>" ;
  print "</div>" ;

  if(!isset($array2[0]["technicianID"])) {
    $array2[0]["technicianID"] = null;
  }

  if(technicianExists($connection2, $array2[0]["technicianID"]))
  {
    if(!($array2[0]["technicianID"]==getTechnicianID($_SESSION[$guid]["gibbonPersonID"], $connection2))) {
	  print "<div class='error'>" ;
	    print "You do not have access to this action." ;
	  print "</div>" ;
	  exit();
    }
  }
  else if(!($array2[0]["technicianID"]==null)){
    print "<div class='error'>" ;
	  print "You do not have access to this action." ;
	print "</div>" ;
	exit();
  }

  print "<h1>" . $array[0]["issueName"] . "</h1>" ;

  print "<table class='smallIntBorder' cellspacing='0' style='width: 100%;'>" ;
  print "<tr>" ;
  print "<td style='width: 33%; vertical-align: top'>" ;
  print "<span style='font-size: 115%; font-weight: bold'>" . _('Student') . "</span><br/>" ;
  print $studentName ;
  print "</td>" ;
  print "<td style='width: 33%; vertical-align: top'>" ;
  print "<span style='font-size: 115%; font-weight: bold'>" . _('Technician') . "</span><br/>" ;
  print $technicianName;
  print "</td>" ;
  print "<td style='width: 33%; vertical-align: top'>" ;
  print "<span style='font-size: 115%; font-weight: bold'>" . _('Date') . "</span><br/>" ;
  print dateConvertBack($guid, $array[0]["date"]) ;
  print "</td>" ;
  print "</tr>" ;
  print "</table>" ;

  print "<h2 style='padding-top: 30px'>" . _('Description') . "</h2>" ;
  print "<table class='smallIntBorder' cellspacing='0' style='width: 100%;'>" ;
    print "<tr>" ;
      print "<td>". $array[0]["description"] ."</td>" ;
    print "</tr>" ;
    if($array2[0]["technicianID"]==null) {
      print "<tr>";
        print "<td class='right'>";
          print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/issues_acceptProcess.php?issueID=". $issueID . "'><img title=" . _('Accept ') . "' src='" . $_SESSION[$guid]["absoluteURL"] . "/themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/plus.png'/></a>";
        print "</td>";
      print "</tr>";
    }
  print "</table>" ;

	if(!($array2[0]["technicianID"]==null)) {
	  print "<a name='discuss'></a>" ;
	  print "<h2 style='padding-top: 30px'>" . _('Discuss') . "</h2>" ;
	  print "<table class='smallIntBorder' cellspacing='0' style='width: 100%;'>" ;
		print "<tr>" ;
		  print "<td style='text-align: justify; padding-top: 5px; width: 33%; vertical-align: top; max-width: 752px!important;' colspan=3>" ;

		  print "<div style='margin: 0px' class='linkTop'>" ;
		  print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/issues_discuss_view.php&issueID=" . $_GET["issueID"] . "'>" . _('Refresh') . "<img style='margin-left: 5px' title='" . _('Refresh') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/refresh.png'/></a> <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_discuss_view_post.php&issueID=" . $_GET["issueID"] . "'>" .  _('Add') . "<img style='margin-left: 5px' title='" . _('Add') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_new.png'/></a> " ;
		  print "</div>" ;

		  print "<div style='margin-bottom: 0px' class='success'>" ;
		  printf(_('Items in %1$sred%2$s are new since your last login. Items in green are older.'), "<span style='color: #c00'>", "</span>") ;
		  print "</div>" ;

		  if ($result3->rowCount()==0){
			print "<div class = 'error'>" ;
				print _("There are no records to display.");
			print "</div>";
		  } else {
			$level = 0 ;
			while ($row3=$result3->fetch()){
			  print "<table class='noIntBorder' cellspacing='0' style='width: 500px ; padding: 1px 3px; margin-bottom: -2px; margin-top: 50; margin-left: 100px; border:  ; background-color: #f9f9f9'>" ;
				print "<tr>" ;
				  if ($row3["technicianPosted"] == 0) {
					print "<td style='color: #777'><i>". $studentName . " " . _('said') . "</i>:</td>" ;
				  } else {
					print "<td style='color: #777'><i>". $technicianName . " " . _('said') . "</i>:</td>" ;
				  }
				  print "<td>" . $row3["comment"] . "</td>" ;
				  print "<td style='color: #777; text-align: right'><i>" . _('Posted at') . " <b>" . substr($row3["timestamp"],11,5) . "</b> on <b>" . dateConvertBack($guid, $row3["timestamp"]) . "</b></i></td>" ;
				print "</tr>" ;
			  print "</table>" ;
			}
		  }

		  print "</td>" ;
		print "</tr>" ;
	  print "</table>" ;
}

}
?>
