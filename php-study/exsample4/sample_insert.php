<?php
require('app/bootstrap.php');



$sql=<<<SQL
  insert into auth_tbl
  (
  user_name,
  password,
  hp_passwd
  )
  values
  (
  :user_name,
  :password,
  :hp_passwd
  )
SQL;

$aryparam = array(
  ":user_name" =>"sugitani_test",
  ":password"  =>"ttujk3a8",
  ":hp_passwd" =>"hogehoge",
);

$objDatabase = clsCommonDatabase::getInstance();
$result = $objDatabase->addDbData($sql,$aryparam);

if ($result===ture){
  echo "success!";
} else {
  echo "error!!";
}

 ?>
