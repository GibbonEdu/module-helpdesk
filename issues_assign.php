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

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/issues_view.php") == false) {
    //Acess denied
    print "<div class='error'>" ;
        print __("You do not have access to this action.") ;
    print "</div>" ;
} else {
    $issueID = null;
    if (isset($_GET["issueID"])) {
        $issueID = $_GET["issueID"];
    } else {
        print "<div class='error'>" ;
            print __("No issue selected.") ;
        print "</div>" ;
        exit();
    }

    $isReassign = false;
    if (hasTechnicianAssigned($connection2, $issueID)) {
        $isReassign = true;
    }

    $permission = "assignIssue";

    if ($isReassign) {
        $permission = "reassignIssue";
    }

    if (!getPermissionValue($connection2, $_SESSION[$guid]["gibbonPersonID"], $permission)) {
        print "<div class='error'>" ;
            print __("You do not have access to this action.") ;
        print "</div>" ;
        exit();
    }

    //Proceed!
    $title = $isReassign ? __('Reassign Issue') : __('Assign Issue');
    $page->breadcrumbs->add($title);

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    $technicians = getAllTechnicians($connection2);

    ?>

    <form method="post" action="<?php print $_SESSION[$guid]['absoluteURL'] . '/modules/Help Desk/issues_assignProcess.php?issueID=' . $issueID . '&permission=' . $permission ?>">
        <table class='smallIntBorder' cellspacing='0' style="width: 100%">
            <tr>
                <td>
                    <b>
                        <?php print __('Technicians') ." *"; ?>
                    </b><br/>
                </td>
                <td class=\"right\">
                    <select name='technician' id='technician' style='width:302px'>
                    <?php
                        foreach ($technicians as $option) {
                            if (!isPersonsIssue($connection2, $issueID, $option["gibbonPersonID"])) {
                                if ($isReassign) {
                                    if (getTechWorkingOnIssue($connection2, $issueID)["personID"] != $option["gibbonPersonID"]) {
                                        print "<option value='" . $option["technicianID"] . "'>". $option["surname"]. ", ". $option["preferredName"] ."</option>" ;
                                    }
                                } else {
                                    print "<option value='" . $option["technicianID"] . "'>". $option["surname"]. ", ". $option["preferredName"] ."</option>" ;
                                }
                            }
                        }
                    ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <span style="font-size: 90%"><i>* <?php print __("denotes a required field") ; ?></i></span>
                </td>
                <td class="right">
                    <input type="submit" value="<?php print __("Submit") ; ?>">
                </td>
            </tr>
        </table>
    </form>
<?php
}
?>
