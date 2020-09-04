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

use Gibbon\Module\HelpDesk\Domain\IssueGateway;

include "../../functions.php" ;
include "../../config.php" ;

include "./moduleFunctions.php" ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

$URL = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/" ;

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/issues_view.php") == false) {
    //Fail 0
      $URL = $URL . "issues_view.php&return=error0" ;
    header("Location: {$URL}");
    exit();
} else {

    if (isset($_GET["issueID"])) {
        $issueID = $_GET["issueID"];
        $URL = $URL . "issues_discussView.php&issueID=" . $issueID ;
    } else {
        $URL = $URL . "issues_view.php&return=error1" ;
        header("Location: {$URL}");
        exit();
    }

    if (!isPersonsIssue($connection2, $issueID, $_SESSION[$guid]["gibbonPersonID"])) {
        $URL = $URL . "issues_view.php&return=error0" ;
        header("Location: {$URL}");
        exit();
    }

    if (isset($_POST["privacySetting"])) {
        $privacySetting = $_POST["privacySetting"];
    } else {
        $URL = $URL . "&return=error1" ;
        header("Location: {$URL}");
          exit();
    }

    try {
        $data = array("privacySetting" => $privacySetting);

        $issueGateway = $container->get(IssueGateway::class);
        $issueGateway->update($issueID, $data);
    } catch (PDOException $e) {
        $URL = $URL . "&return=error2" ;
        header("Location: {$URL}");
        exit();
    }

      $URL = $URL . "&return=success0" ;
    header("Location: {$URL}");
}
?>
