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

//Module includes
include "./modules/" . $_SESSION[$guid]["module"] . "/moduleFunctions.php" ;

if (isModuleAccessible($guid, $connection2)==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print "You do not have access to this action." ;
	print "</div>" ;
}
else {
	print "<h3>" ;
	print _("Filter") ;
	print "</h3>" ;

	$and="" ;
	$filter=NULL ;
	if (isset($_GET["filter"])) {
		$filter=$_GET["filter"] ;
	}
	else if (isset($_POST["filter"])) {
		$filter=$_POST["filter"] ;
	}
	
	$issueFilters = array("My Issues", "Resolved", "All");
	
	if ($filter=="") {
		$filter=$issueFilters[0];
		$and=" WHERE active='1'" ;
	}
	if ($filter=="Resolved") {
		$and=" WHERE active='0'" ;
	}

	print "<form method='post' action='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=" . $_GET["q"] . "'>" ;
		print"<table class='noIntBorder' cellspacing='0' style='width: 100%'>" ;	
			?>
			<tr>
				<td> 
					<b><?php print _('Issue Filter') ?></b><br/>
					<span style="font-size: 90%"><i></i></span>
				</td>
				<td class="right">
					<?php
					print "<select name='filter' id='filter' style='width:302px'>" ;
						
						foreach($issueFilters as $option) {
							$selected="" ;
							if ($option==$filter) {
								$selected="selected" ;
							}
							print "<option $selected value='" . $option . "'>". $option ."</option>" ;
						}
					print "</select>" ;
					?>
				</td>
			</tr>
			<?php
			print "<tr>" ;
				print "<td class='right' colspan=2>" ;
					print "<input type='submit' value='" . _('Go') . "'>" ;
				print "</td>" ;
			print "</tr>" ;
		print"</table>" ;
	print "</form>" ;	

  try {
    $dataIssue=array("issueID"=>$row["issueID"]);
    $sqlIssue="SELECT helpDeskIssue.* , surname , preferredName, gibbonPerson.title FROM helpDeskIssue JOIN gibbonPerson ON (helpDeskIssue.gibbonPersonID=gibbonPerson.gibbonPersonID)" . $and;
    $resultIssue=$connection2->prepare($sqlIssue);
    $resultIssue->execute($dataIssue);
  }
  catch(PDOException $e) {

  }
    print "<table class = 'smallIntBorder' cellspacing = '0' style = 'width: 100% !important'>";
    print "<tr> <th>Title</th> <th>Description</th> <th>Name</th> <th>Active</th> </tr>";
    foreach($resultIssue as $row){
      print "<tr>";
      printf("<td>" .$row['issueName']. "</td>");
      printf("<td>" .$row['desc']. "</td>");
      printf("<td>" .$row['title'].$row['surname'].", ".$row['preferredName']. "</td>");
      printf("<td>" .(($row['active'] == 1) ? "TRUE" : "FALSE"). "</td>");
      print "</tr>";
    }
    print "</table>";	

}
?>
