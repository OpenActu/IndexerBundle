<?php
namespace OpenActu\IndexerBundle\Model\Indexer;

use OpenActu\IndexerBundle\Model\Type\AbstractType;
class ListIndexer extends AbstractIndexer
{
    /**
     * @var l next node
     */
    protected $l = null;

    /**
     * data attachment
     *
     * @param mixed $index
     * @param mixed $data
     */
    public function attach($index,$data)
    {
        /**
         * @todo cast index
         */
        $index = $this->checkIndex($index);

        $this->increaseCard();

        if($this->isNillable()){ $this->checkNotNillable($index,$data); }
        elseif($this->isGreaterOrEqualsThan($index)){
            $mem = clone $this;
            $this->setIndex($index);
            $this->setData($data);
            $this->l = $mem;
        }
        else{ $this->l->attach($index,$data); }
    }

    /**
     * return the position $pos from the mixed index $index
     * @param AbstractType $index
     */
    public function cget(AbstractType $index)
    {
        if( get_class($this->getIndex()) != get_class($index) ){
            throw new IndexerException(
                IndexerException::INVALID_TYPE_INDEX_EXPECTED_ERRMSG,
                IndexerException::INVALID_TYPE_INDEX_EXPECTED_ERRNO,
                array(
                    'type' => get_class($index),
                    'type_expected' => get_class($this->getIndex())
                )
            );
        }

        if($this->isNillable()){ return 0; }
        elseif($this->isEquals($index->getValue())){ return 0; }
        else{ return 1+$this->l->cget($index); }
    }

    /**
     * data detachment
     *
     * @var mixed $index
     */
    public function detach($index,$bypassExist = false)
    {
        $check = true;
        if($bypassExist === false){
            $check = $this->exists($index);
            $bypassExist = true;
        }

        if($check){
            if($this->isEquals($index)){
                $mem = clone $this->l;
                if(null !== $mem->getIndex()){
                    $this->setIndex($mem->getIndex()->getValue());
                    $this->setData($mem->getData()->getValue());
                    $this->l = $mem->l;
                }
                else{
                    $this->clear();
                }
                return true;
            }
            else{
                $this->decreaseCard();
                return $this->l->detach($index,$bypassExist);
            }
        }
        else{
            return false;
        }
    }

    public function __toString()
    {

        if($this->isNillable()){ return '@nil'; }
        else{ return '@cons('.$this->getIndex().','.(string)$this->l.')'; }

    }

    /**
     * execute data writing
     *
     * @var mixed $index
     * @var mixed $data
     */
    public function checkNotNillable($index,$data)
    {
      parent::checkNotNillable($index,$data);

      $classnameIndex= $this->getClassnameIndex();
      $classnameData = $this->getClassnameData();
      $this->l       = new ListIndexer($classnameIndex, $classnameData);
    }

    /**
     * return the data stored at position $pos (between 0 and card length)
     *
     * @param int $pos
     * @param mixed $index
     * @return mixed|null Data
     */
    public function get($pos=0,&$index=null)
    {
        if($this->isNillable()){ return null; }
        elseif(0 === $pos){ $index=$this->getIndex(); return $this->getData(); }
        else{ return $this->l->get($pos-1, $index); }
    }

    /**
     * check if the index exists
     *
     * @param mixed $index index to compare with
     * @return bool
     */
    public function exists($index)
    {
        if( ($this->isNillable()) || ($this->isGreaterThan($index)) ){ return false; }
        elseif($this->isEquals($index)){ return true; }
        else{ return $this->l->exists($index); }
    }

    /**
     * return the higher index value
     *
     * @return mixed|null $index
     */
    public function max()
    {
        if($this->isNillable()){ return null; }
        elseif($this->l->isNillable()){ return $this->getIndex(); }
        else{ return $this->l->max(); }
    }

    /**
     * return the smallest index value
     *
     * @return mixed|null $index
     */
    public function min()
    {
        if($this->isNillable()){ return null; }
        return $this->getIndex();
    }

    /**
     * check the optimized level of optimization for the current indexer
     * The score at 1 is optimal
     *
     * @return float Level optimization
     */
    public function score()
    {
        return 1;
    }

    /**
     * Build a new optimized ListIndexer
     *
     * @return ListIndexer
     */
    public function optimize()
    {
        return clone $this;
    }

    /**
     * convert current instance to database value
     *
     * @return string
     */
    public function convertToDatabaseValue($master=true)
    {
        $output = parent::convertToDatabaseValue();

        $output['l'] = null;

        if(!$this->isNillable()){
            $output['l']    = $this->l->convertToDatabaseValue(false);
        }

        return (true === $master) ? json_encode($output) : $output;
    }
}
