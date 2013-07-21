<?php
include_once('TakiModel.php');


class card {
    const path = "\\www\\Taki\\TakiImages\\";
    private $sign;
    private $color;


    public function card($sign, $color) {
        $this->sign= $sign;
        $this->color= $color;
        $this->pic = self::path.$this->color."\\".$sign;
    }

    //getter
    public function __get($property)
    {
        if(property_exists($this, $property)){
             return $this->property;
        }
    }

    //setter
    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }
}