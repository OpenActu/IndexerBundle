<?php
namespace OpenActu\IndexerBundle\Model;

class RequestInstanceHandler
{
    private $classnames = array();
    private $values     = array();

    public function __construct()
    {

    }

    public function getValues()
    {
        return $this->values;
    }

    public function set($field, $value)
    {
        if(in_array($field, array_keys($this->classnames)))
        {
            $classname = $this->classnames[$field];
            $this->values[$field] = new $classname($value);
        }
    }

    public function add($field, $classname)
    {
        $this->classnames[$field] = $classname;
    }
}
