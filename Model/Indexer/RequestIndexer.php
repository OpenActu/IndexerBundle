<?php
namespace OpenActu\IndexerBundle\Model\Indexer;

use OpenActu\IndexerBundle\Model\Type\AbstractTypeInterface;
use OpenActu\IndexerBundle\Exception\IndexerException;
class RequestIndexer
{
    const MAX_LINE_RETURNED = 1000;

    const AUTO_OPTIMIZE     = 100;

    /**
     * @var bool $executeDone
     */
    private $executeDone=false;

    /**
     * @var AbstractIndexerInterface $memIndexer
     */
    private $memIndexer;

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
     * @var int $offset
     */
    private $offset=null;

    /**
     * @var int $limit
     */
    private $limit=null;

    /**
     * @var array $inIndexValues
     */
    private $inIndexValues = array();

    /**
     * @var bool $blockNotIn
     */
    private $blockNotIn = false;

    /**
     * @param AbstractIndexerInterface $indexer Current indexer
     */
    public function __construct(AbstractIndexerInterface $indexer)
    {
        $this->indexer = clone $indexer;
    }

    public function __destruct()
    {
        unset($this->indexer);
    }

    public function getIndexer()
    {
        return $this->indexer;
    }

    /**
     * Set Indexer to block result greater than or equals to current index value
     *
     * @param AbstractTypeInterface $index Current index to compare with
     */
    public function lt(AbstractTypeInterface $index)
    {
        $this->maxPos = $this->indexer->cget($index);
        return $this;
    }

    public function execute()
    {
        $this->memIndexer = $this->indexer;

        /**
         * in method management
         */
        $this->executeDone  = true;
        $classnameIndexer   = get_class($this->indexer);
        $indexer            = new $classnameIndexer(
            $this->indexer->getClassnameIndex(),
            $this->indexer->getClassnameData()
        );

        if( $this->inIndexValues )
        {
            if(null === $this->limit){
                $this->limit = self::MAX_LINE_RETURNED;
            }
            $max = $this->offset+$this->limit;
            for($i=0, $found=0;($i < $this->indexer->card()) && ($found<$max);$i++){
                $data = $this->indexer->get($i,$index);
                if(in_array($index,$this->inIndexValues)){
                    $indexer->attach($index->getValue(),$data->getValue());
                    if(($i%self::AUTO_OPTIMIZE===0) && ($i>0))
                    {
                        $indexer = $indexer->optimize();
                    }
                    $found++;
                }
            }
            $this->indexer = $indexer;
        }
    }
    /**
     * Set the offset
     *
     * If null is given, the offset is the first instance found
     * @param int|null $offset
     */
    public function offset($offset=null)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Set the limit
     *
     * If null is given, there are no bounds to the result
     * @param int|null $limit
     */
    public function limit($limit=null)
    {
        $this->limit = $limit;

        return $this;
    }

    private function _getOffsetPosition($position)
    {
        if( (null !== $this->limit) && ($position >= $this->limit) )
            return null;
        if(null !== $this->offset)
            $position+=$this->offset;
        return $position;
    }

    public function get($position,&$index=null)
    {
        $position = $this->_getOffsetPosition($position);

        if((null === $position) || ($position >= $this->card()) || ($position < 0))
            return null;
        return $this->indexer->get($position + $this->minPos, $index);
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

        return $this;
    }

    /**
     * Restrict the Resultset to the index values given as parameters
     *
     * Remark: the restrict can't be reverted with reload() method
     * @param array $indexes Array of index values
     */
    public function in(array $indexes)
    {
        if(count($this->inIndexValues))
        {
            throw new IndexerException(
                IndexerException::NO_DOUBLE_CALL_ON_IN_ACCEPTED_ERRMSG,
                IndexerException::NO_DOUBLE_CALL_ON_IN_ACCEPTED_ERRNO,
                array()
            );
        }

        $indexes = array_unique($indexes);
        $classnameType = $this->indexer->getClassnameIndex();

        $this->inIndexValues = array();
        foreach($indexes as $index)
            $this->inIndexValues[] = new $classnameType($index);

        return $this;
    }

    /**
     * Remove indexes value given as parameters
     *
     * Remark: the removing can't be reverted with reload() method
     * @param array $indexes Array of index values
     */
    public function notIn(array $indexes)
    {
        $indexes = array_unique($indexes);
        foreach($indexes as $index)
        {
            $classnameType = $this->indexer->getClassnameIndex();
            $oindex = new $classnameType($index);
            if( $this->indexer->exists($index) )
            {
                $pos = $this->indexer->cget($oindex);

                if( (0 < $pos) && ($this->indexer->card() > $pos) )
                {
                    if($this->minPos > $pos)
                        $this->minPos--;
                    if($this->maxPos > $pos)
                        $this->maxPos--;
                    $this->indexer->detach($index);
                }
            }
        }

        return $this;
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
        if(false === $this->executeDone)
        {
            throw new IndexerException(
                IndexerException::CALL_RESPONSE_BEFORE_EXECUTE_ERRMSG,
                IndexerException::CALL_RESPONSE_BEFORE_EXECUTE_ERRNO,
                array()
            );
        }
        if( (null !== $this->maxPos) && (null !== $this->minPos) && ($this->maxPos <= $this->minPos) ){ return 0; }
        $min = ($this->minPos) ? $this->minPos : 0;
        $max = ($this->maxPos) ? $this->maxPos : $this->indexer->card();
        return $max-$min;
    }

    /**
     * reload the current request
     *
     */
    public function reload()
    {
        $this->minPos       = null;
        $this->maxPos       = null;
        $this->offset       = null;
        $this->limit        = null;
        $this->executeDone  = false;

        if(null !== $this->memIndexer)
            $this->indexer      = $this->memIndexer;

        return $this;
    }

    /**
     * Export the datas in current Request index
     *
     * @return array AbstractTypeInterface[]
     */
    public function exportDatas()
    {
        $output = array();
        for($i=0;( $i<$this->card() ) && ( null !== ( $data = $this->get($i,$index) ) );$i++)
        {
            $output[] = $data;
        }
        return $output;
    }

    /**
     * Make a difference between datas from a and b with preservation of indexes from a
     *
     * @return AbstractTypeInterface
     */
    public static function diff(RequestIndexer $a, RequestIndexer $b)
    {
        $datas_a = $a->exportDatas();
        $datas_b = $b->exportDatas();

        $datas = array_diff($datas_a, $datas_b);

        $cdata   = $a->indexer->getClassnameData();
        $cindex  = $a->indexer->getClassnameIndex();
        $indexer = get_class($a->indexer);

        $output = new $indexer($cdata,$cindex);

        for($i=0;( $i<$a->card() ) && ( null !== ( $data = $a->get($i,$index) ) );$i++)
            if(in_array($data, $datas))
                Invoker::attach($output, $index->getValue(), $data->getValue());

        return Invoker::getRequest($output,array());
    }

    /**
     * convert current instance to database value
     *
     * @param bool $main
     * @return string
     */
    public function convertToDatabaseValue()
    {
        return $this->indexer->convertToDatabaseValue();
    }

    /**
     * Make an intersection between datas from a and b with preservation of indexes from a
     *
     * @return AbstractIndexerInterface
     */
    public static function intersect(RequestIndexer $a, RequestIndexer $b)
    {
        $datas_a = $a->exportDatas();
        $datas_b = $b->exportDatas();

        $datas = array_intersect($datas_a, $datas_b);

        $cdata   = $a->indexer->getClassnameData();
        $cindex  = $a->indexer->getClassnameIndex();
        $indexer = get_class($a->indexer);

        $output = new $indexer($cindex,$cdata);

        for($i=0;( $i<$a->card() ) && ( null !== ( $data = $a->get($i,$index) ) );$i++)
            if(in_array($data, $datas))
                Invoker::attach($output, $index->getValue(), $data->getValue());

        return Invoker::getRequest($output,array());
    }
}
