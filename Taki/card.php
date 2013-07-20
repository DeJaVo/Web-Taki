<?php
include_once('TakiModel.php');


class card {
    private $num;
    private $color;
    private $special_sign;
    private $pic;

    public function card($num, $col, $special_sign,$pic) {
        $this->num= $num;
        $this->color= $col;
        $this->special_sign = $special_sign;
        $this->pic = $pic;
    }
}