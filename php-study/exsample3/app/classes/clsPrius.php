<?php

class Prius
{
    private $mileage = 0; // 走行距離

    // 走行する
    public function drive($distance)
    {
        // 走った距離分、走行距離を加算
        $this->mileage += $distance;
    }

    // 走行距離を取得
    public function getMileage()
    {
        return $this->mileage;
    }
}
?>
