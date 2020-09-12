<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Create, add subcategory to, and delete a department');
$I->loginAsAdmin();
$I->amOnModulePage('Help Desk', 'helpDesk_manageDepartments.php');

// Add ------------------------------------------------
$I->clickNavigation('Add');
$I->seeBreadcrumb('Create Department');

$I->fillField('departmentName', 'Test Department');
$I->fillField('departmentDesc', 'Test Department Description');
$I->click('Submit');

$I->seeSuccessMessage();

$departmentID = $I->grabValueFromURL('departmentID');

// Edit ------------------------------------------------
$I->amOnModulePage('Help Desk', 'helpDesk_createSubcategory.php', array('departmentID' => $departmentID));

$I->fillField('subcategoryName', 'Test Subcategory');
$I->click('Submit');
$I->seeSuccessMessage();

// Delete ------------------------------------------------
$I->amOnModulePage('Help Desk', 'helpDesk_deleteDepartment.php', array('departmentID' => $departmentID));

$I->click('Yes');
$I->seeSuccessMessage();
