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

	if (!relatedToIssue($connection2, $_GET["issueID"], $_SESSION[$guid]["gibbonPersonID"])) {
	  //Fail 0
	  print "<div class='error'>" ;
  print "You do not have access to this action." ;
  print "</div>" ;
	  exit();
	}

  ?>

  <form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/issues_discuss_view_postProccess.php?issueID="  . $_GET["issueID"]?>">
    <table class='smallIntBorder' cellspacing='0' style="width: 100%">
      <tr>
        <td>
          <b><?php print _('Comment') ?></b><br/>
        </td>
        <td class="right">
          <textarea name='comment' id='comment' maxlength=1000 rows=5 style='width: 300px'></textarea>
        </td>
      </tr>
      <tr>
        <td>
          <span style="font-size: 90%"><i>* <?php print _("denotes a required field") ; ?></i></span>
        </td>
        <td class="right">
          <input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
          <input type="submit" value="<?php print _("Submit") ; ?>">
        </td>
      </tr>
    </table>
  </form>

  <?php

}
?>
