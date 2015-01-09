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
$sql[$count][1]="UPDATE gibbonAction SET name='Create Issue', URLList='issues_create.php', entryURL='issues_create.php' WHERE name='Submit Issue' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
INSERT INTO gibbonAction SET name='Create Issue_forOther', precedence='1', category='', description='Submits an IT related issue to be resolved by the help desk staff with an optional feature to create on the behalf of others.', URLList='issues_create.php', entryURL='issues_create.php', defaultPermissionAdmin='Y', defaultPermissionTeacher='Y', defaultPermissionStudent='Y', defaultPermissionParent='N', defaultPermissionSupport='Y', categoryPermissionStaff='Y', categoryPermissionStudent='Y', categoryPermissionParent='Y', categoryPermissionOther='N' WHERE name='Submit Issue' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Help Desk');end
ALTER TABLE helpDeskIssue ADD createdByID int(12) unsigned zerofill NOT NULL;end" ;


?>