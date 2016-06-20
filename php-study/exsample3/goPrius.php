<?php

require('app/bootstrap.php');


// 2台のプリウスのインスタンスを生成
$prius1 = new clsPrius():
$prius2 = new clsPrius();


// プリウス1だけ走行してみる
$prius1->drive(5);


// 両方のプリウスの走行距離を取得する
$mileage1 = $prius1->getMileage();
$mileage2 = $prius2->getMileage();

print "プリウス1は" . $mileage1 . "km走りました<br>";
print "プリウス2は" . $mileage2 . "km走りました<br>";


