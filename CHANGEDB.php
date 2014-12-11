<?php
//USE ;end TO SEPERATE SQL STATEMENTS. DON'T USE ;end IN ANY OTHER PLACES!

$sql=array() ;
$count=0 ;

//v0.0.01
$sql[$count][0]="0.0.01" ;
$sql[$count][1]="-- First version, nothing to update" ;

//v0.0.02
$count++;
$sql[$count][0]="0.0.02" ;
$sql[$count][1]="" ;

//v0.1.00
$count++;
$sql[$count][0]="0.1.00" ;
$sql[$count][1]="UPDATE gibbonAction SET name='Create Issue', URLList='issues_createIssue.php', entryURL='issues_createIssue.php' WHERE name='Submit Issue' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end" ;


?>