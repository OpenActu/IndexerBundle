<?php
namespace OpenActu\IndexerBundle\Model\Indexer;

use OpenActu\IndexerBundle\Model\Type\AbstractTypeInterface;
class RequestIndexer
{
    /**
     * @var AbstractIndexerInterface $indexer
     */
    private $indexer;

    /**
     * @var int $maxPos
     */
    private $maxPos;

    /**
     * @var int $minPos
     */
    private $minPos;

    /**
     * @param AbstractIndexerInterface $indexer Current indexer
     */
    public function __construct(AbstractIndexerInterface $indexer)
    {
        $this->indexer = $indexer;
    }

    /**
     * Set Indexer to block result greater than or equals to current index value
     *
     * @param AbstractTypeInterface $index Current index to compare with
     */
    public function lt(AbstractTypeInterface $index)
    {
        $this->maxPos = $this->indexer->cget($index);
        /**
        $this->indexer->get($pos,$indexFound);
        if(null !== $indexFound){
            print_r($indexFound->getValue());
        }
        */
    }

    public function get($position)
    {
        if(($position >= $this->card()) || ($position < 0))
            return null;
        return $this->indexer->get($position + $this->minPos);

    }
    /**
     * Set Indexer to block result greater than current index value
     *
     * @param AbstractTypeInterface $index Current index to compare with
     */
    public function gt(AbstractTypeInterface $index)
    {
        $nindex = $index->succ();
        $this->minPos = $this->indexer->cget($nindex);
    }

    /**
     * return the highest index value
     *
     * @return AbstractType|null $index
     */
    public function max()
    {
        $card = $this->card();
        if(0 === $card){ return null; }
        return $this->get($card-1);
    }

    /**
     * return the smallest index value
     *
     * @return AbstractType|null $index
     */
    public function min()
    {
        $card = $this->card();
        if(0 === $card){ return null; }
        return $this->get(0);
    }

    /**
     * Get card from request
     *
     * @return int
     */
    public function card()
    {
        if( (null !== $this->maxPos) && (null !== $this->minPos) && ($this->maxPos <= $this->minPos) ){ return 0; }
        $min = ($this->minPos) ? $this->minPos : 0;
        $max = ($this->maxPos) ? $this->maxPos : $this->indexer->card();
        return $max-$min;
    }
}
