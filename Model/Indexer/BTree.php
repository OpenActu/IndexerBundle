<?php
namespace OpenActu\IndexerBundle\Model\Indexer;

class BTree extends AbstractIndexer
{
    /**
     * data attachment
     *
     * @var mixed $index
     * @var mixed $data
     */
    public function attach($index,$data)
    {
        /**
         * @todo cast index
         */
        $index = $this->checkIndex($index);

        $this->increaseCard();

        if($this->isNillable()){ $this->checkNotNillable($index,$data); }
        elseif($this->isLessOrEqualsThan($index)){ $this->l2->attach($index,$data); }
        else{ $this->l1->attach($index,$data); }

    }

    /**
     * data detachment
     *
     * @var mixed $index
     */
    public function detach($index)
    {
        $check = $this->exists($index);
        if($check){
            if($this->isGreaterThan($index)){ $this->decreaseCard(); $this->l1->detach($index); }
            elseif($this->isEquals($index)){
                $node = clone $this;
                $this->l1     = null;
                $this->l2     = null;
                $this->clear();

                for($i=0;$i<$node->card();$i++){
                    $tdata = $node->get($i,$tvalue);
                    if( $tvalue->getValue() !== $node->getIndex()->getValue() )
                        $this->attach($tvalue->getValue(),$tdata);
            }


            }
            else{ $this->decreaseCard(); $this->l2->detach($index); }
        }
        return $check;
    }

    public function __toString()
    {
        if($this->isNillable()){ return '@empty'; }
        else{ return '@node('.$this->getIndex().','.(string)$this->l1.','.(string)$this->l2.')'; }
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

      $classname     = $this->getClassname();
      $this->l1      = new BTree($classname);
      $this->l2      = new BTree($classname);
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

        $card = $this->l1->card();
        if($card < $pos){ return $this->l2->get($pos-$card-1,$index); }
        elseif($card === $pos){ $index=$this->getIndex();return $this->getData(); }
        else{ return $this->l1->get($pos,$index); }

    }

    /**
     * check if the index exists
     *
     * @param mixed $index index to compare with
     * @return bool
     */
    public function exists($index)
    {
        if($this->isNillable()){ return false; }
        elseif($this->isGreaterThan($index)){ return $this->l1->exists($index); }
        elseif($this->isEquals($index)){ return true; }
        else{ return $this->l2->exists($index); }
    }

    /**
     * return the higher index value
     *
     * @return mixed|null $index
     */
    public function max()
    {
        if($this->isNillable()){ return null; }
        elseif($this->l2->isNillable()){ return $this->getIndex()->getValue(); }
        else{ return $this->l2->max(); }
    }

    /**
     * return the smallest index value
     *
     * @return mixed|null $index
     */
    public function min()
    {
        if($this->isNillable()){ return null; }
        elseif($this->l1->isNillable()){ return $this->getIndex()->getValue(); }
        else{ return $this->l1->min(); }
    }

    /**
     * check the optimized level of optimization for the current indexer
     * The score at 1 is optimal
     *
     * @return float Level optimization
     */
    public function score()
    {
        $adepth = ceil(log($this->card(),2)+1);
        $tdepth = $this->__max_depth();
        return $adepth/$tdepth;
    }

    private function __max_depth($depth=1){

        if($this->isNillable()){ return $depth; }
        $l_depth = $this->l1->__max_depth($depth+1);
        $r_depth = $this->l2->__max_depth($depth+1);
        if($r_depth > $l_depth)
          return $r_depth;
        return $l_depth;
    }

    /**
     * Build a new optimized BTree
     *
     * @return BTree
     */
    public function optimize()
    {
        $dichotomies  = $this->__extract_dichotomies();
        $classname     = $this->getClassname();
        $new_item     = new BTree($classname);
        foreach($dichotomies as $depth){
          foreach($depth as $pos){
              $data = $this->get((int)$pos,$index);
              $new_item->attach($index->getValue(),$data);
          }
        }
        return $new_item;
    }

    private function __extract_dichotomies($min=0,$max=0, $depth=1,&$arr = array())
    {
        if($this->isNillable()){ return; }

        if(0 === $max){
            $max = $this->card();
            $min = 0;
        }

        if($max-$min > 1){
            $median   = floor(($max+$min)/2);
            $arr[$depth][] = $median;
            $this->__extract_dichotomies($min,$median,$depth+1, $arr);
            $this->__extract_dichotomies($median+1,$max, $depth+1, $arr);
        }
        elseif($max-$min == 1){ $arr[$depth][] = $min; }
        return $arr;

    }

}