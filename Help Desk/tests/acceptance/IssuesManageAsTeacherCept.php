<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Create, discuss, resolve, reincarnate, and resolve an issue as a teacher');
$I->loginAsTeacher();
$I->amOnModulePage('Help Desk', 'issues_view.php');

// Add ------------------------------------------------
$I->createIssueForMyself();
$issueID = $I->grabValueFromURL('issueID');

// discussView Accept ------------------------------------------------
$I->click('Logout');
$I->loginAsAdmin();
$I->acceptIssue($issueID);


// discuss ------------------------------------------------
$I->click('Logout');
$I->loginAsTeacher();
$I->discussIssue($issueID);


//Resolve ------------------------------------------------
$I->resolveIssue($issueID);

//Reincarnate ------------------------------------------------
$I->reincarnateIssue($issueID);

//Resolve ------------------------------------------------
$I->resolveIssue($issueID);
