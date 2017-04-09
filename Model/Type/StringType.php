<?php
namespace OpenActu\IndexerBundle\Model\Type;

use OpenActu\IndexerBundle\Exception\IndexerException;
class StringType extends AbstractType implements AbstractTypeInterface
{

    public function gt($value)
    {
        return (strcasecmp($this->getValue(),$value) > 0);
    }
    public function eq($value)
    {
        return ($this->getValue() === $value);
    }

    public function __toString()
    {
        return '@string:'.$this->getValue();
    }

    public static function cast($value)
    {
        if(is_object($value)){
            throw new IndexerException(
                IndexerException::INVALID_ORIGIN_DATA_ERRMSG,
                IndexerException::INVALID_ORIGIN_DATA_ERRNO,
                array('provenance' => 'object','type' => 'string')
            );
        }

        if(is_array($value)){
            throw new IndexerException(
                IndexerException::INVALID_ORIGIN_DATA_ERRMSG,
                IndexerException::INVALID_ORIGIN_DATA_ERRNO,
                array('provenance' => 'array','type' => 'string')
            );
        }

        return $value;
    }
}
