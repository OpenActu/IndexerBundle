<?php
namespace OpenActu\IndexerBundle\Model\Type;

interface AbstractTypeInterface
{
    /**
     * convert string to valid type
     *
     * @param string $string
     * @return mixed Formatted value
     */
    public static function strtotype($string);

    /**
     * get the successor of a  specific value
     *
     * @return AbstractTypeInterface
     */
    public function succ();

    /**
     * check if current value is greater than the data given
     *
     * @var mixed $value
     * @return bool
     */
    public function gt($value);

    /**
     * check if current value is equals to the data given
     *
     * @var mixed $value
     * @return bool
     */
    public function eq($value);

    public function __toString();

    /**
     * cast the value properly
     *
     * throw error if value can't be Indexed
     * @param mixed $value
     * @return mixed Formatted Index
     */
    public static function cast($value);

    /**
     * convert current instance to database value
     *
     * @return string
     */
    public function convertToDatabaseValue();
}
