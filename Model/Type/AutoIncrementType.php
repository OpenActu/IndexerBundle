<?php
namespace OpenActu\IndexerBundle\Model\Type;

use OpenActu\IndexerBundle\Exception\IndexerException;
/**
 * auto-increment is an numeric extended
 */
class AutoIncrementType extends NumericType
{

    public static $_max = 1;

    public function __toString()
    {
        return '@auto:'.$this->getValue();
    }

    public static function increment()
    {
        $max = self::$_max;
        self::$_max++;
        return $max;
    }


}
