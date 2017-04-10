<?php
namespace OpenActu\IndexerBundle\Model\Indexer;

class HydratorIndexer
{
    public static function hydrate($string,$depth=1)
    {
        if(1 === $depth)
            $arr = json_decode($string);
        else
            $arr = $string;

        if(null === $arr){ return null; }

        $pFields = array('classname', 'classnameType', 'index', 'card', 'data', 'context');
        foreach($pFields as $pField){
            $$pField = $arr->$pField;
            unset($arr->$pField);
        }
        $indexer = new $classname($classnameType);
        $indexer->forceCard($card);
        $indexer->forceContext($context);
        if(null !== $index)
            $indexer->forceIndex($index->value, $classnameType,$data);
        /**
         * @todo rest l1 and l2
         */
        foreach(get_object_vars($arr) as $key => $subblock){
            $indexer->set($key,HydratorIndexer::hydrate($subblock,$depth+1));
        }
        return $indexer;
    }
}
