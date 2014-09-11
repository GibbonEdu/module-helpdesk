<?php
//USE ;end TO SEPERATE SQL STATEMENTS. DON'T USE ;end IN ANY OTHER PLACES!

$sql=array() ;
$count=0 ;

//v0.0.00
$sql[$count][0]="0.0.00" ;
$sql[$count][1]="-- First version, nothing to update" ;


//v0.0.01
$count++
$sql[$count][0]="0.0.01" ;
$sql[$count][1]="
CREATE TABLE `gibbonTechnicians` (`technicianID` int(4) unsigned zerofill NOT NULL AUTO_INCREMENT,`gibbonPersonID` int(10) unsigned zerofill NOT NULL,`permission` enum('Head','Normal','Junior') NOT NULL DEFAULT 'Junior',PRIMARY KEY (`technicianID`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;end
CREATE TABLE `gibbonIssue` (`issueID` int(12) unsigned zerofill NOT NULL AUTO_INCREMENT,`technicianID` int(4) unsigned zerofill NOT NULL AUTO_INCREMENT,`gibbonPersonID` int(10) unsigned zerofill NOT NULL,`title` varchar(55) NOT NULL,`desc` text NOT NULL,`active` boolean DEFAULT TRUE,PRIMARY KEY (`issueID`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;end
CREATE TABLE `gibbonIssueDiscuss` (`issueDiscussID` int(12) unsigned zerofill NOT NULL,`issueID` int(12) unsigned zerofill NOT NULL AUTO_INCREMENT,`gibbonPersonID` int(10) unsigned zerofill NOT NULL,`comment` text NOT NULL,PRIMARY KEY (`issueDiscussID`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;end
" ;

?>