<?php
namespace OpenActu\IndexerBundle\Model\Indexer;

use OpenActu\IndexerBundle\Model\RequestHandler;
class HydratorIndexer
{
    private static function hydrateRequestHandler($string)
    {
        $rh = new RequestHandler();

        $blob = json_decode($string, true);
        if(!empty($blob['rh']['i']))
        {
            foreach($blob['rh'] as $key => $subdata)
            {
                switch((string)$key)
                {
                    case 'i':
                        foreach($subdata as $pos => $instance)
                        {
                            $rh->addField($instance['f'], $instance['c']);
                        }
                        $rh->generate();
                        break;
                    default:
                        $field  = $subdata['f'];
                        $data   = json_encode($subdata['d']);
                        $indexer= self::hydrate($data);
                        $rh->hydrateData($field, $indexer);
                }
            }
        }
        return $rh;
    }

    private static function hydrateIndexer($string, $depth=1)
    {
        if(1 === $depth)
            $arr = json_decode($string);
        else
            $arr = $string;

        if(null === $arr){ return null; }

        $pFields = array('c', 'p', 'i', 'n', 'd', 't','e');

        foreach($pFields as $pField){
            $$pField = $arr->$pField;
            unset($arr->$pField);
        }
        $indexer = new $c($p,$e);
        $indexer->forceCard($n);
        $indexer->forceContext($t);
        if(null !== $i){
            $indexer->forceIndex($i->v);
            $indexer->forceData($d->v);
        }
        foreach(get_object_vars($arr) as $key => $subblock){
            $indexer->set($key,HydratorIndexer::hydrateIndexer($subblock,$depth+1));
        }
        return $indexer;
    }

    public static function isValidRequestHandler($string)
    {
        $test = json_decode($string,true);
        return ( (1 === count($test)) && isset($test['rh']) );
    }
    public static function hydrate($string)
    {
        $check = self::isValidRequestHandler($string);
        if($check)
            return self::hydrateRequestHandler($string);
        else
            return self::hydrateIndexer($string);
    }
}
