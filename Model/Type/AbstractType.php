<?php
namespace OpenActu\IndexerBundle\Model\Type;

abstract class AbstractType
{
    private $value = null;

    public function getValue()
    {
        return $this->value;
    }
    public function __construct($value)
    {
        $classname =get_class($this);
        $value = $classname::cast($value);

        $this->value = $value;
    }
    public function lt($value)
    {
        return !($this->gt($value) || $this->eq($value));
    }
    public function gte($value)
    {
        return ($this->gt($value) || $this->eq($value));
    }
    public function lte($value)
    {
        return !$this->gt($value);
    }


    /**
     * convert current instance to database value
     *
     * @return string
     */
    public function convertToDatabaseValue()
    {
        $arr = array(
                'type' => get_class($this),
                'value' => $this->getValue(),
        );
        return $arr;
    }
}
