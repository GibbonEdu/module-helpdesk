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

//This file describes the module, including database tables

//Basic variables
$name="Help Desk" ;
$description="A virtual Help Desk Moudle for Gibbon.";
$entryURL="issues_view.php" ;
$type="Additional" ;
$category="Other" ;
$version="0.4.20" ;
$author="Adrien Tremblay & Ray Clark" ;
$url="https://github.com/raynichc/helpdesk" ;

//Module tables & gibbonSettings entries
$moduleTables[0]="CREATE TABLE `helpDeskTechnicians` (
  `technicianID` int(4) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `gibbonPersonID` int(10) unsigned zerofill NOT NULL,
  `groupID` int(4) unsigned zerofill NOT NULL,
  PRIMARY KEY (`technicianID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;" ;

$moduleTables[1]="CREATE TABLE `helpDeskIssue` (
  `issueID` int(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `technicianID` int(4) unsigned zerofill DEFAULT NULL,
  `gibbonPersonID` int(10) unsigned zerofill NOT NULL,
  `issueName` varchar(55) NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `status` ENUM('Unassigned','Pending','Resolved') DEFAULT 'Unassigned',
  `category` varchar(100) DEFAULT NULL,
  `priority` varchar(100) DEFAULT NULL,
  `gibbonSchoolYearID` int(3) unsigned zerofill NOT NULL,
  `createdByID` int(12) unsigned zerofill NOT NULL,
  `privacySetting` ENUM('Everyone', 'Related', 'Owner', 'No one') DEFAULT 'Everyone',
  PRIMARY KEY (`issueID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;" ;

$moduleTables[2]="CREATE TABLE `helpDeskIssueDiscuss` (
  `issueDiscussID` int(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `issueID` int(12) unsigned zerofill NOT NULL,
  `comment` text NOT NULL,
  `timestamp` timestamp DEFAULT CURRENT_TIMESTAMP,
  `gibbonPersonID` int(10) unsigned zerofill NOT NULL,
  PRIMARY KEY (`issueDiscussID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;" ;

$moduleTables[3]="INSERT INTO `gibbonSetting` (`gibbonSystemSettingsID`, `scope`, `name`, `nameDisplay`, `description`, `value`)
VALUES
(NULL, 'Help Desk', 'issuePriority', 'Issue Priority', 'Different priority levels for the issues.', ''),
(NULL, 'Help Desk', 'issuePriorityName', 'Issue Priority Name', 'Different name for the Issue Priority', 'Priority'),
(NULL, 'Help Desk', 'issueCategory', 'Issue Category', 'Different categories for the issues.', 'Network,Hardware,Software,Application'),
(NULL, 'Help Desk', 'resolvedIssuePrivacy', 'Default Resolved Issue Privacy', 'Default privacy setting for resolved issues.', 'Related')";

$moduleTables[4]="CREATE TABLE `helpDeskTechGroups` (
  `groupID` int(4) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `groupName` varchar(55) NOT NULL,
  `viewIssue` boolean DEFAULT 1,
  `viewIssueStatus` ENUM('All', 'UP', 'PR', 'Pending') DEFAULT 'All',
  `assignIssue` boolean DEFAULT 0,
  `acceptIssue` boolean DEFAULT 1,
  `resolveIssue` boolean DEFAULT 1,
  `createIssueForOther` boolean DEFAULT 1,
  `fullAccess` boolean DEFAULT 0,
  `reassignIssue` boolean DEFAULT 0,
  `reincarnateIssue` boolean DEFAULT 1,
   PRIMARY KEY (`groupID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;" ;
//Action rows
//One array per action
$actionCount = 0 ;

$actionRows[$actionCount]["name"]="Create Issue" ; //The name of the action (appears to user in the right hand side module menu)
$actionRows[$actionCount]["precedence"]="0" ; //If it is a grouped action, the precedence controls which is highest action in group
$actionRows[$actionCount]["category"]="Issues" ; //Optional: subgroups for the right hand side module menu
$actionRows[$actionCount]["description"]="Allows the user to submit an issue to be resolved by the help desk staff." ; //Text description
$actionRows[$actionCount]["URLList"]="issues_create.php" ;
$actionRows[$actionCount]["entryURL"]="issues_create.php" ;
$actionRows[$actionCount]["defaultPermissionAdmin"]="Y" ; //Default permission for built in role Admin
$actionRows[$actionCount]["defaultPermissionTeacher"]="Y" ; //Default permission for built in role Teacher
$actionRows[$actionCount]["defaultPermissionStudent"]="Y" ; //Default permission for built in role Student
$actionRows[$actionCount]["defaultPermissionParent"]="N" ; //Default permission for built in role Parent
$actionRows[$actionCount]["defaultPermissionSupport"]="Y" ; //Default permission for built in role Support
$actionRows[$actionCount]["categoryPermissionStaff"]="Y" ; //Should this action be available to user roles in the Staff category?
$actionRows[$actionCount]["categoryPermissionStudent"]="Y" ; //Should this action be available to user roles in the Student category?
$actionRows[$actionCount]["categoryPermissionParent"]="Y" ; //Should this action be available to user roles in the Parent category?
$actionRows[$actionCount]["categoryPermissionOther"]="Y" ; //Should this action be available to user roles in the Other category?

$actionCount++ ;
$actionRows[$actionCount]["name"]="Issues" ;
$actionRows[$actionCount]["precedence"]="0" ;
$actionRows[$actionCount]["category"]="Issues" ;
$actionRows[$actionCount]["description"]= "Gives the user access to the Issues section." ;
$actionRows[$actionCount]["URLList"]="issues_view.php" ;
$actionRows[$actionCount]["entryURL"]="issues_view.php" ;
$actionRows[$actionCount]["entrySidebar"]="N" ;
$actionRows[$actionCount]["defaultPermissionAdmin"]="Y" ;
$actionRows[$actionCount]["defaultPermissionTeacher"]="Y" ;
$actionRows[$actionCount]["defaultPermissionStudent"]="Y" ;
$actionRows[$actionCount]["defaultPermissionParent"]="N" ;
$actionRows[$actionCount]["defaultPermissionSupport"]="Y" ;
$actionRows[$actionCount]["categoryPermissionStaff"]="Y" ;
$actionRows[$actionCount]["categoryPermissionStudent"]="Y" ;
$actionRows[$actionCount]["categoryPermissionParent"]="Y" ;
$actionRows[$actionCount]["categoryPermissionOther"]="Y" ;

$actionCount++ ;
$actionRows[$actionCount]["name"]="Help Desk Settings" ;
$actionRows[$actionCount]["precedence"]="0" ;
$actionRows[$actionCount]["category"]="Admin" ;
$actionRows[$actionCount]["description"]="Allows the user to edit the settings for the module." ;
$actionRows[$actionCount]["URLList"]="helpDesk_settings.php" ;
$actionRows[$actionCount]["entryURL"]="helpDesk_settings.php" ;
$actionRows[$actionCount]["defaultPermissionAdmin"]="Y" ;
$actionRows[$actionCount]["defaultPermissionTeacher"]="N" ;
$actionRows[$actionCount]["defaultPermissionStudent"]="N" ;
$actionRows[$actionCount]["defaultPermissionParent"]="N" ;
$actionRows[$actionCount]["defaultPermissionSupport"]="N" ;
$actionRows[$actionCount]["categoryPermissionStaff"]="Y" ;
$actionRows[$actionCount]["categoryPermissionStudent"]="N" ;
$actionRows[$actionCount]["categoryPermissionParent"]="N" ;
$actionRows[$actionCount]["categoryPermissionOther"]="Y" ;

$actionCount++ ;
$actionRows[$actionCount]["name"]="Manage Technicians" ;
$actionRows[$actionCount]["precedence"]="0" ;
$actionRows[$actionCount]["category"]="Technician" ;
$actionRows[$actionCount]["description"]="Allows the user to manage the Technicians." ;
$actionRows[$actionCount]["URLList"]="helpDesk_manageTechnicians.php" ;
$actionRows[$actionCount]["entryURL"]="helpDesk_manageTechnicians.php" ;
$actionRows[$actionCount]["defaultPermissionAdmin"]="Y" ;
$actionRows[$actionCount]["defaultPermissionTeacher"]="N" ;
$actionRows[$actionCount]["defaultPermissionStudent"]="N" ;
$actionRows[$actionCount]["defaultPermissionParent"]="N" ;
$actionRows[$actionCount]["defaultPermissionSupport"]="N" ;
$actionRows[$actionCount]["categoryPermissionStaff"]="Y" ;
$actionRows[$actionCount]["categoryPermissionStudent"]="N" ;
$actionRows[$actionCount]["categoryPermissionParent"]="N" ;
$actionRows[$actionCount]["categoryPermissionOther"]="Y" ;

$actionCount++ ;
$actionRows[$actionCount]["name"]="Manage Technician Groups" ;
$actionRows[$actionCount]["precedence"]="0" ;
$actionRows[$actionCount]["category"]="Technician" ;
$actionRows[$actionCount]["description"]="Allows the user to manage the Technicians Groups." ;
$actionRows[$actionCount]["URLList"]="helpDesk_manageTechnicianGroup.php" ;
$actionRows[$actionCount]["entryURL"]="helpDesk_manageTechnicianGroup.php" ;
$actionRows[$actionCount]["defaultPermissionAdmin"]="Y" ;
$actionRows[$actionCount]["defaultPermissionTeacher"]="N" ;
$actionRows[$actionCount]["defaultPermissionStudent"]="N" ;
$actionRows[$actionCount]["defaultPermissionParent"]="N" ;
$actionRows[$actionCount]["defaultPermissionSupport"]="N" ;
$actionRows[$actionCount]["categoryPermissionStaff"]="Y" ;
$actionRows[$actionCount]["categoryPermissionStudent"]="N" ;
$actionRows[$actionCount]["categoryPermissionParent"]="N" ;
$actionRows[$actionCount]["categoryPermissionOther"]="Y" ;

$actionCount++ ;
$actionRows[$actionCount]["name"]="Technician Report" ;
$actionRows[$actionCount]["precedence"]="0" ;
$actionRows[$actionCount]["category"]="Technician" ;
$actionRows[$actionCount]["description"]="Technician reports." ;
$actionRows[$actionCount]["URLList"]="helpDesk_reports.php" ;
$actionRows[$actionCount]["entryURL"]="helpDesk_reports.php" ;
$actionRows[$actionCount]["defaultPermissionAdmin"]="Y" ;
$actionRows[$actionCount]["defaultPermissionTeacher"]="N" ;
$actionRows[$actionCount]["defaultPermissionStudent"]="N" ;
$actionRows[$actionCount]["defaultPermissionParent"]="N" ;
$actionRows[$actionCount]["defaultPermissionSupport"]="N" ;
$actionRows[$actionCount]["categoryPermissionStaff"]="Y" ;
$actionRows[$actionCount]["categoryPermissionStudent"]="N" ;
$actionRows[$actionCount]["categoryPermissionParent"]="N" ;
$actionRows[$actionCount]["categoryPermissionOther"]="Y" ;

$actionCount++ ;
$actionRows[$actionCount]["name"]="Statistics" ;
$actionRows[$actionCount]["precedence"]="0" ;
$actionRows[$actionCount]["category"]="Admin" ;
$actionRows[$actionCount]["description"]="Statistics for the Help Desk." ;
$actionRows[$actionCount]["URLList"]="helpDesk_statistics.php" ;
$actionRows[$actionCount]["entryURL"]="helpDesk_statistics.php" ;
$actionRows[$actionCount]["defaultPermissionAdmin"]="Y" ;
$actionRows[$actionCount]["defaultPermissionTeacher"]="N" ;
$actionRows[$actionCount]["defaultPermissionStudent"]="N" ;
$actionRows[$actionCount]["defaultPermissionParent"]="N" ;
$actionRows[$actionCount]["defaultPermissionSupport"]="N" ;
$actionRows[$actionCount]["categoryPermissionStaff"]="Y" ;
$actionRows[$actionCount]["categoryPermissionStudent"]="N" ;
$actionRows[$actionCount]["categoryPermissionParent"]="N" ;
$actionRows[$actionCount]["categoryPermissionOther"]="Y" ;

?>
