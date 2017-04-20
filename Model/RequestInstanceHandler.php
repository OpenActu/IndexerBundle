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

    public function convertToDatabaseValue($json_encode = true)
    {
        $output = array();
        foreach($this->classnames as $field => $classname)
        {
            $output[] = array('f' => $field, 'c' => $classname);
        }
        if(true === $json_encode)
            return json_encode($output);
        return $output;
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
