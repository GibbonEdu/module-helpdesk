<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('create (on behalf of another), accept, discuss, reassign, resolve, reincarnate, and resolve an issue as a technician (admin)');
$I->loginAsAdmin();
$I->amOnModulePage('Help Desk', 'issues_view.php');

// Add ------------------------------------------------
$I->clickNavigation('Create');
$I->seeBreadcrumb('Create Issue');
$I->fillField('issueName', 'Test Issue');
$I->fillField('description', '<p>Test Description</p>');
$I->selectFromDropdown('category', 2);
$I->selectFromDropdown('createFor', -1); 
$I->selectFromDropdown('priority', -1);
$I->click('Submit');

//Check if table view is correct (and that we've been redirected to issues_view.php)
$issueID = $I->grabValueFromURL('issueID');
$I->seeSuccessMessage();
$I->seeBreadcrumb('Issues');
$I->see('Test Issue');
$I->see('Test Description');


// discussView Accept ------------------------------------------------
$I->amOnModulePage('Help Desk', 'issues_discussView.php', ['issueID' => $issueID]);
$I->seeBreadcrumb('Discuss Issue');

$I->see('Test Issue');
$I->see('Test Description');

$I->click('Accept');
$I->seeSuccessMessage();
$I->seeBreadcrumb('Discuss Issue');

// discuss ------------------------------------------------
$I->amOnModulePage('Help Desk', 'issues_discussPost.php', ['issueID' => $issueID]);
$I->seeBreadcrumb('Post Discuss');
$I->fillField('comment', '<p>Discuss Test</p>');
$I->click('Submit');
$I->seeSuccessMessage();


// discussView Assign ------------------------------------------------
$I->amOnModulePage('Help Desk', 'issues_assign.php', ['issueID' => $issueID]);
$I->seeBreadcrumb('Reassign Issue');

$I->selectFromDropdown('technician', 2);
$I->click('Submit');
$I->seeSuccessMessage();
$I->seeBreadcrumb('Discuss Issue');

//Resolve ------------------------------------------------
$I->amOnModulePage('Help Desk', 'issues_discussView.php', ['issueID' => $issueID]);

$I->click('Resolve');
$I->seeSuccessMessage();
$I->seeBreadcrumb('Issues');

//Reincarnate ------------------------------------------------
$I->amOnModulePage('Help Desk', 'issues_discussView.php', ['issueID' => $issueID]);

$I->click('Reincarnate');
$I->seeSuccessMessage();
$I->seeBreadcrumb('Discuss Issue');

//Resolve ------------------------------------------------
$I->amOnModulePage('Help Desk', 'issues_discussView.php', ['issueID' => $issueID]);

$I->click('Resolve');
$I->seeSuccessMessage();
$I->seeBreadcrumb('Issues');
