<?php
class Valid
{
    public $format = array();
    public $get = array();
    public function __set($key, $val)
    {
        $this->set($key, $val, $this->format[$key]);
    }

    public function __get($key)
    {
        return $this->get[$key];
    }

    public function set($key, $val, $format)
    {
        if ($this->_valid($val, $format)) {
            $this->get[$key] = $val;
            return true;
        } else {
            return false;
        }
    }

    public function setAll($arr)
    {
        foreach ($arr as list($key, $val, $format)) {
            $this->set($key, $val, $format);
        }
    }

    public function setFormat($array)
    {
        $this->format = $array;
    }

    public function _valid()
    {
        $this->validType();
        $this->validStringFormat();
        $this->validInarray();
    }

    public function validType($val, $type)
    {}
    public function validStringFormat($val, $format)
    {}
    public function validInarray($val, $haystack)
    {}

}


$this->valid($format);

FATAL
ERROR
WARNING


$valid = new Valid();
if ($valid->name = $_POST['name'])
$valid->set('name', $_POST['name'], $format);
