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

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/helpDesk_manageTechnicianGroup.php") == FALSE) {
    //Acess denied
    print "<div class='error'>" ;
    print __($guid, "You do not have access to this action.") ;
    print "</div>" ;
} else {
    //Proceed!
    print "<div class='trail'>" ;
       print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . __($guid, "Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . __($guid, getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/helpDesk_manageTechnicianGroup.php'>" . __($guid, "Manage Technician Groups") . "</a> > </div><div class='trailEnd'>" . __($guid, 'Create Technician Group') . "</div>" ;
    print "</div>" ;

    if (isset($_GET['return'])) {
        $editLink = null;
        if (isset($_GET['groupID'])) {
            $groupID = $_GET['groupID'];
            $editLink = $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Help Desk/helpDesk_editTechnicianGroup.php&groupID=$groupID";
        }
        returnProcess($guid, $_GET['return'], $editLink, null);
    }

?>

    <form method = "post" action = "<?php print $_SESSION[$guid]['absoluteURL'] . '/modules/Help Desk/helpDesk_createTechnicianGroupProcess.php' ?>">
        <table class = 'smallIntBorder' cellspacing = '0' style = "width: 100%">
            <tr>
                <td style = 'width: 275px'>
                    <b><?php print __($guid, 'Group Name') ?> *</b><br/>
                </td>
                <td class = "right">
                    <input name = "groupName" id = "groupName" maxlength = 55 value = "" type = "text" style = "width: 300px">
                    <script type = "text/javascript">
                        var name=new LiveValidation('groupName');
                        name.add(Validate.Presence);
                    </script>
                </td>
            </tr>
            <tr>
                <td>
                    <span style = "font-size: 90%"><i>* <?php print __($guid, "denotes a required field") ; ?></i></span>
                </td>
                <td class="right">
                    <input type = "submit" value  = "<?php print __($guid, "Submit") ; ?>">
                </td>
            </tr>
        </table>
    </form>
<?php
}
?>