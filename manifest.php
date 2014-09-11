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
$name="Help Desk" ; //The name of the variable as it appears to users. Needs to be unique to installation. Also the name of the folder that holds the unit.
$description="Gibbon Help Desk Module"; //Short text description
$entryURL="index.php" ; //The landing page for the unit, used in the main menu
$type="Additional" ; //Do not change.
$category="Other" ; //The main menu area to place the module in
$version="0.0.01" ; //Verson number
$author="Ray Clark & Adrien Tremblay" ; //Your name
$url="https://github.com/adrientremblay/helpdesk" ; //Your URL

//Module tables & gibbonSettings entries
$moduleTables[0]="CREATE TABLE `gibbonTechnicians` (
  `technicianID` int(4) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `gibbonPersonID` int(10) unsigned zerofill NOT NULL,
  `permission` enum('Head','Normal','Junior') NOT NULL DEFAULT 'Junior',
  PRIMARY KEY (`technicianID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;" ;

$moduleTables[1]="CREATE TABLE `gibbonIssue` (
  `issueID` int(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `technicianID` int(4) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `gibbonPersonID` int(10) unsigned zerofill NOT NULL,
  `title` varchar(55) NOT NULL,
  `desc` text NOT NULL,
  `active` enun('Y','N') DEFAULT 'Y',
  PRIMARY KEY (`issueID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;" ;

$moduleTables[2]="CREATE TABLE `gibbonIssueDiscuss` (
  `issueDiscussID` int(12) unsigned zerofill NOT NULL,
  `issueID` int(12) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `gibbonPersonID` int(10) unsigned zerofill NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`issueDiscussID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;" ;

//Action rows 
//One array per action
$actionRows[0]["name"]="" ; //The name of the action (appears to user in the right hand side module menu)
$actionRows[0]["precedence"]="0"; //If it is a grouped action, the precedence controls which is highest action in group
$actionRows[0]["category"]="" ; //Optional: subgroups for the right hand side module menu
$actionRows[0]["description"]="" ; //Text description
$actionRows[0]["URLList"]="" ; //List of pages included in this action
$actionRows[0]["entryURL"]="" ; //The landing action for the page.
$actionRows[0]["defaultPermissionAdmin"]="Y" ; //Default permission for built in role Admin
$actionRows[0]["defaultPermissionTeacher"]="Y" ; //Default permission for built in role Teacher
$actionRows[0]["defaultPermissionStudent"]="N" ; //Default permission for built in role Student
$actionRows[0]["defaultPermissionParent"]="N" ; //Default permission for built in role Parent
$actionRows[0]["defaultPermissionSupport"]="N" ; //Default permission for built in role Support
$actionRows[0]["categoryPermissionStaff"]="Y" ; //Should this action be available to user roles in the Staff category?
$actionRows[0]["categoryPermissionStudent"]="N" ; //Should this action be available to user roles in the Student category?
$actionRows[0]["categoryPermissionParent"]="N" ; //Should this action be available to user roles in the Parent category?
$actionRows[0]["categoryPermissionOther"]="N" ; //Should this action be available to user roles in the Other category?

?>