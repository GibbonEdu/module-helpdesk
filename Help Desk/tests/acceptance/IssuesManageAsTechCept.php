<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('create (on behalf of another), accept, discuss, reassign, resolve, reincarnate, and resolve an issue as a technician (admin)');
$I->loginAsAdmin();
$I->amOnModulePage('Help Desk', 'issues_view.php');

// Add ------------------------------------------------
$I->createIssueOnBehalf();
$issueID = $I->grabValueFromURL('issueID');

// discussView Accept ------------------------------------------------
$I->acceptIssue($issueID);

// discuss ------------------------------------------------
$I->discussIssue($issueID);


// discussView Assign ------------------------------------------------
$I->assignIssue($issueID);

//Resolve ------------------------------------------------
$I->resolveIssue($issueID);

//Reincarnate ------------------------------------------------
$I->reincarnateIssue($issueID);

//Resolve ------------------------------------------------
$I->resolveIssue($issueID);
