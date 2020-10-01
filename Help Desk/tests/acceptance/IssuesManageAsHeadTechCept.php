<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Create and manage an issue as a Head Technician using simple and complex categories)');
$I->loginAsHeadTech();
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
$I->changetoSimpleCategory();
$I->loginAsHeadTech();
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

$I->changetoComplexCategory();
