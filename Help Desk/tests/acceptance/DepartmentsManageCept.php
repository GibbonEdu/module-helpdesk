<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Create, add subcategory to, and delete a department, and then try after deleting the subcat');
$I->loginAsAdmin();
$I->amOnModulePage('Help Desk', 'helpDesk_manageDepartments.php');

// Add ------------------------------------------------
$I->createDepartment();
$departmentID = $I->grabValueFromURL('departmentID');

// Edit ------------------------------------------------
$I->addSubcategory($departmentID);

// Delete ------------------------------------------------
$I->deleteDepartment($departmentID);

// Testing if we can delete without a subcategory --------------------------------------------------------

$I->amOnModulePage('Help Desk', 'helpDesk_manageDepartments.php');

// Add ------------------------------------------------
$I->createDepartment();
$departmentID = $I->grabValueFromURL('departmentID');

$I->addSubcategory($departmentID);

$subcategoryID = $I->grabValueFromURL('subcategoryID');
$I->deleteSubcategory($departmentID, $subcategoryID);

// Delete ------------------------------------------------
$I->deleteDepartment($departmentID);
