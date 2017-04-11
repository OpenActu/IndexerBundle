<?php
namespace OpenActu\IndexerBundle\Model\Type;

use OpenActu\IndexerBundle\Exception\IndexerException;
class DatetimeType extends AbstractType implements AbstractTypeInterface
{
    public static function strtotype($string)
    {
        return $string;
    }

    public function gt($value)
    {
        return ($this->getValue() > $value);
    }

    public function eq($value)
    {

        $result = ($this->getValue() == $value);
        return $result;
    }

    public function __toString()
    {
        return '@datetime:'.$this->getValue()->format("YmdHis");
    }

    public static function cast($value)
    {
        if(!is_object($value)){
            throw new IndexerException(
                IndexerException::INVALID_ORIGIN_DATA_ERRMSG,
                IndexerException::INVALID_ORIGIN_DATA_ERRNO,
                array('provenance' => 'scalar','type' => 'datetime')
            );
        }
        elseif(get_class($value) === 'stdClass'){
            $time       = $value->date;
            $timezone   = new \DateTimeZone($value->timezone);
            $value      = new \DateTime($time,$timezone);
        }
        elseif(get_class($value) !== 'DateTime'){
            throw new IndexerException(
                IndexerException::INVALID_ORIGIN_DATA_ERRMSG,
                IndexerException::INVALID_ORIGIN_DATA_ERRNO,
                array('provenance' => 'object('.get_class($value).')','type' => 'datetime')
            );
        }
        if(is_array($value)){
            throw new IndexerException(
                IndexerException::INVALID_ORIGIN_DATA_ERRMSG,
                IndexerException::INVALID_ORIGIN_DATA_ERRNO,
                array('provenance' => 'array','type' => 'datetime')
            );
        }

        return $value;
    }

}
