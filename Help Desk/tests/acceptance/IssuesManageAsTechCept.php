<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('create (on behalf of another), accept, discuss, reassign, resolve, reincarnate, and resolve an issue as a technician (admin, simple and complex categories)');
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

//Test from view
$I->reincarnateIssueFromView($issueID);
$I->resolveIssueFromView($issueID);


//check with simple categories
$I->amOnModulePage('Help Desk', 'helpDesk_settings.php');
$I->checkOption('simpleCategories');
$I->click('Submit');
$I->amOnModulePage('Help Desk', 'issues_view.php');

// Add ------------------------------------------------
$I->createIssueOnBehalfSimple();
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


$I->amOnModulePage('Help Desk', 'helpDesk_settings.php');
$I->uncheckOption('simpleCategories');
$I->click('Submit');
