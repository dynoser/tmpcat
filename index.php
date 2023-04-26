<?php
namespace dynoser\tmpcat;

// ****** EXAMPLE ******

require_once 'src/CatGrouper.php';

$cgObj = new CatGrouper();

//Uncomment this to use the database source:
//$cgObj->dbConnect("localhost", "username", "password", "myDB");

//Use data from array:
$cgObj->categories_arr = (include 'testdata.php');

// Calculate groups
$result = $cgObj->getCategoriesGrouped();

// view results
print_r($result);
