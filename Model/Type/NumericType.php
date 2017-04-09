<?php
namespace OpenActu\IndexerBundle\Model\Type;

use OpenActu\IndexerBundle\Exception\IndexerException;
class NumericType extends AbstractType implements AbstractTypeInterface
{
    public function gt($value)
    {
        return ($this->getValue() > $value);
    }
    public function eq($value)
    {
        return ($this->getValue() === $value);
    }
    public function __toString()
    {
        return '@int:'.(string)$this->getValue();
    }
    public static function cast($value)
    {
        if(is_object($value)){
            throw new IndexerException(
                IndexerException::INVALID_ORIGIN_DATA_ERRMSG,
                IndexerException::INVALID_ORIGIN_DATA_ERRNO,
                array('provenance' => 'object','type' => 'numeric')
            );
        }

        if(is_array($value)){
            throw new IndexerException(
                IndexerException::INVALID_ORIGIN_DATA_ERRMSG,
                IndexerException::INVALID_ORIGIN_DATA_ERRNO,
                array('provenance' => 'array','type' => 'numeric')
            );
        }
        $expr = (string)$value;
        if(!preg_match("/^[0-9]+$/",$expr)){
            throw new IndexerException(
                IndexerException::INVALID_TYPE_DATA_FOUND_ERRMSG,
                IndexerException::INVALID_TYPE_DATA_FOUND_ERRNO,
                array('data' => $expr,'target' => '[0-9]+')
            );
        }

        return $value;
    }
}
