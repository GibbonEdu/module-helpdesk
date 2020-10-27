<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Create, add subcategory to, edit subcategory, and delete a department, and then try after deleting the subcat');
$I->loginAsAdmin();
$I->amOnModulePage('Help Desk', 'helpDesk_manageDepartments.php');


$I->createDepartment();
$departmentID = $I->grabValueFromURL('departmentID');


$I->addSubcategory($departmentID);
$subcategoryID = $I->grabValueFromURL('subcategoryID');

$I->editSubcategory($departmentID, $subcategoryID);


$I->deleteDepartment();

// Testing if we can delete without a subcategory --------------------------------------------------------

$I->amOnModulePage('Help Desk', 'helpDesk_manageDepartments.php');


$I->createDepartment();
$departmentID = $I->grabValueFromURL('departmentID');

$I->addSubcategory($departmentID);

$subcategoryID = $I->grabValueFromURL('subcategoryID');
$I->deleteSubcategory($departmentID, $subcategoryID);


$I->deleteDepartment();
