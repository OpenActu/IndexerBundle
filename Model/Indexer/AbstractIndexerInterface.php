<?php
namespace OpenActu\IndexerBundle\Model\Indexer;

use OpenActu\IndexerBundle\Model\Type\AbstractType;
interface AbstractIndexerInterface
{

    const CONTEXT_INIT        = 0;
    const CONTEXT_INSTANCIATE = 1;

    /**
     * check if the current instance is nillable
     *
     * @return bool
     */
    public function isNillable();

    /**
     * data attachment
     *
     * @param mixed $index
     * @param mixed $data
     */
    public function attach($index,$data);

    /**
     * data detachment
     *
     * @param mixed $index
     */
    public function detach($index);

    /**
     * execute data writing
     *
     * @param mixed $index
     * @param mixed $data
     */
    public function checkNotNillable($index,$data);

    /**
     * test if current instance is less or equals to the variable given
     *
     * @param mixed $index index to compare with
     * @return bool
     */
    public function isLessOrEqualsThan($index);

    /**
     * test if current instance is greater than the variable given
     *
     * @param mixed $index index to compare with
     * @return bool
     */
    public function isGreaterThan($index);

    /**
     * test if current instance is equals to the variable given
     *
     * @param mixed $index index to compare with
     * @return bool
     */
    public function isEquals($index);

    /**
     * test if current instance is equals or greater than the variable given
     *
     * @param mixed $index index to compare with
     * @return bool
     */
    public function isGreaterOrEqualsThan($index);
    public function exists($index);

    /**
     * return the count indexes in the current node
     *
     * @return int Count indexes
     */
    public function card();

    /**
     * return the highest index in the current node
     *
     * @return AbstractType|null $index
     */
    public function max();

    /**
     * return the smallest index in the current node
     *
     * @return AbstractType|null $index
     */
    public function min();

    public function score();

    public function __toString();

    public function get($pos=0,&$index=null);

    /**
     * convert current instance to database value
     *
     * @return string
     */
    public function convertToDatabaseValue();

    /**
     * return the position $pos from the mixed index $index
     *
     * @param AbstractType $index
     * @return int Position
     */
    public function cget(AbstractType $index);

    /**
     * check if data exists
     *
     * return an exception in case of detection, nothing else
     * @param mixed $data
     */
    public function __checkExistsOnData($data);
}
