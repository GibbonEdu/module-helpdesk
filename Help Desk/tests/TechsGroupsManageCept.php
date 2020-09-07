<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('add, edit and delete technicians');
$I->loginAsAdmin();
$I->amOnModulePage('Help Desk', 'helpDesk_manageTechnicianGroup.php');

// Add ------------------------------------------------
$I->clickNavigation('Add');
$I->seeBreadcrumb('Create Technician Group');


$addFormValues = array(
    'groupName'             => 'Test Group'
);

$I->submitForm('#content form', $addFormValues, 'Submit');
$I->seeSuccessMessage();


$groupID = $I->grabEditIDFromURL();

// Edit ------------------------------------------------
$I->amOnModulePage('Help Desk', 'helpDesk_editTechnicianGroup.php', array('groupID' => $groupID));
$I->seeBreadcrumb('Edit Technician Group');

$I->seeInFormFields('#content form', $addFormValues);
$editFormValues = array(
    'groupName' => 'Test Group',
    'viewIssue' => 'Y',
    'assignIssue' => 'N',
    'acceptIssue' => 'N',
    'resolveIssue' => 'N',
    'createIssueForOther' => 'N',
    'reassignIssue' => 'N',
    'reincarnateIssue' => 'N'
    'fullAccess' => 'N'
    
);
$I->selectFromDropdown('viewIssueStatus', 1);
$I->submitForm('#content form', $addFormValues, 'Submit');
$I->seeSuccessMessage();

// Delete ------------------------------------------------
$I->amOnModulePage('Help Desk', 'helpDesk_technicianGroupDelete.php', array('groupID' => $groupID));

$I->click('Yes');
$I->seeSuccessMessage();
