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

        $pFields = array('c', 'p', 'i', 'n', 'd', 't');
        foreach($pFields as $pField){
            $$pField = $arr->$pField;
            unset($arr->$pField);
        }
        $indexer = new $c($p);
        $indexer->forceCard($n);
        $indexer->forceContext($t);
        if(null !== $i){
            $indexer->forceIndex($i->v, $p, $d);
        }
        foreach(get_object_vars($arr) as $key => $subblock){
            $indexer->set($key,HydratorIndexer::hydrate($subblock,$depth+1));
        }
        return $indexer;
    }
}
