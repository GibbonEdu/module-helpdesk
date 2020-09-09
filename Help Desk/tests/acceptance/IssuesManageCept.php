<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('create (on behalf of another), accept, reassign, resolve, reincarnate, and resolve an issues');
$I->loginAsAdmin();
$I->amOnModulePage('Help Desk', 'issues_view.php');

// Add ------------------------------------------------
$I->clickNavigation('Create');
$I->seeBreadcrumb('Create Issue');
$I->fillField('issueName', 'Test Issue');
$I->fillField('description', '<p>Test Description</p>');
$I->selectFromDropdown('category', 2);
$I->selectFromDropdown('createFor', -1); 
//TODO: priorities, they don't exist by default so
$I->click('Submit');
$I->seeSuccessMessage();


$issueID = $I->grabValueFromURL('issueID');

// discussView Accept ------------------------------------------------
$I->amOnModulePage('Help Desk', 'issues_discussView.php', ['issueID' => $issueID]);
$I->seeBreadcrumb('Discuss Issue');

$I->click('Accept');
$I->seeSuccessMessage();


// discussView Assign ------------------------------------------------
$I->amOnModulePage('Help Desk', 'issues_assign.php', ['issueID' => $issueID]);
$I->seeBreadcrumb('Reassign Issue');

$I->selectFromDropdown('technician', 1);
$I->click('Submit');
$I->seeSuccessMessage();

//Resolve ------------------------------------------------
$I->amOnModulePage('Help Desk', 'issues_discussView.php', ['issueID' => $issueID]);

$I->click('Resolve');
$I->seeSuccessMessage();

//Resolve ------------------------------------------------
$I->amOnModulePage('Help Desk', 'issues_discussView.php', ['issueID' => $issueID]);

$I->click('Reincarnate');
$I->seeSuccessMessage();

//Resolve ------------------------------------------------
$I->amOnModulePage('Help Desk', 'issues_discussView.php', ['issueID' => $issueID]);

$I->click('Resolve');
$I->seeSuccessMessage();
