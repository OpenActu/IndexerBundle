<?php
namespace OpenActu\IndexerBundle\Model\Indexer;

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
     * @var mixed $index
     * @var mixed $data
     */
    public function attach($index,$data);

    /**
     * data detachment
     *
     * @var mixed $index
     */
    public function detach($index);

    /**
     * execute data writing
     *
     * @var mixed $index
     * @var mixed $data
     */
    public function checkNotNillable($index,$data);

    /**
     * test if current instance is less or equals to the variable given
     *
     * @var mixed $index index to compare with
     * @return bool
     */
    public function isLessOrEqualsThan($index);

    /**
     * test if current instance is greater than the variable given
     *
     * @var mixed $index index to compare with
     * @return bool
     */
    public function isGreaterThan($index);
    public function isEquals($index);
    public function isGreaterOrEqualsThan($index);
    public function exists($index);
    public function card();
    public function max();
    public function min();
    public function score();
    public function __toString();
    public function get($pos=0,&$index=null);
}
