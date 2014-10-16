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

	try {
		$dataIssue=array("issueID"=>$row["issueID"]); 
		$sqlIssue="SELECT * FROM helpDeskIssue" ;
		$resultIssue=$connection2->prepare($sqlIssue);
		$resultIssue->execute($dataIssue);
		
		print "<table class = 'smallIntBorder' cellspacing = '0' style = 'width: 100% !important'>";
		print "<tr> <th>Title</th> <th>Description</th> </tr>";

		foreach($resultIssue as $row){
			print "<tr>";
			printf("<td>" .$row['title']. "</td>");
			printf("<td>" .$row['desc']. "</td>");
			print "</tr>";
		}
		print "</table>";
	}
	catch(PDOException $e) { 
	}

	

}
?>
