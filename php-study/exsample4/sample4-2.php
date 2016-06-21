<?php 
require('app/bootstrap.php');



$sql=<<<SQL
  select    *
  from      valid_users
  where     user_name = :user_name
SQL;

$aryparam = array(
  ":user_name"=>"kix00001",
);

$objDatabase = clsCommonDatabase::getInstance();
$rec = $objDatabase->pullDbData($sql,$aryparam);

var_dump($rec[0]);

 ?>