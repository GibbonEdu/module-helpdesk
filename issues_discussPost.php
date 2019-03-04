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
include "./modules/Help Desk/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Help Desk/issues_view.php") == FALSE || !relatedToIssue($connection2, $_GET["issueID"], $_SESSION[$guid]["gibbonPersonID"])) {
    //Acess denied
    print "<div class='error'>" ;
        print "You do not have access to this action." ;
    print "</div>" ;
} else {
    print "<div class='trail'>" ;
        print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . __("Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . __(getModuleName($_GET["q"])) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/issues_discussView.php&issueID=". $_GET["issueID"] . "'>" . __("Discuss Issue") . "</a> > </div><div class='trailEnd'>" . __('Post Discuss') . "</div>" ;
    print "</div>" ;
    ?>
    <form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] . "/issues_discussPostProccess.php?issueID="  . $_GET["issueID"]?>">
        <table class='smallIntBorder' cellspacing='0' style="width: 100%">
            <tr>
                <td colspan=2>
                    <b>
                        <?php print __('Comment') ?>
                    </b><br/>
                    <?php print getEditor($guid, TRUE, "comment", "", 5, true, true, false); ?>
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
