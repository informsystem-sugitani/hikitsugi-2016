<?php 
require('app/bootstrap.php');



clsTest::getTestEcho('static !');


$objTest = new clsTest;
$objTest->getTestEcho('public call !');


 ?>