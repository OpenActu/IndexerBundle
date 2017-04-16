<?php
namespace OpenActu\IndexerBundle\Model\Indexer;

use OpenActu\IndexerBundle\Model\Indexer\RequestIndexer;
class Invoker
{
    const AUTO_OPTIMIZE     = 100;

    public static function refreshRequest($request, array $parameters=array())
    {
        $request->reload();
        return self::__callRequest($request, $parameters);
    }

    public static function getRequest($indexer, array $parameters=array())
    {
        $request = new RequestIndexer($indexer);
        return self::__callRequest($request, $parameters);
    }

    private static function __callRequest($request, array $parameters=array())
    {
        if(count($parameters)>0)
        {
            $type = $request->getIndexer()->getClassnameIndex();
            foreach($parameters as $rule => $parameter)
            {
                switch($rule)
                {
                    case 'lt':
                    case 'gt':
                        $parameter = new $type($parameter);
                    default:
                        $request->$rule($parameter);
                }
            }
            $request->execute();
        }
        return $request;
    }
    public static function attach(&$indexer,$index, $data)
    {
        $i = $indexer->card();
        if(($i%self::AUTO_OPTIMIZE===0) && $i>0){ $indexer = $indexer->optimize(); }
        $indexer->attach($index, $data);
    }

    public static function detach(&$indexer, $index)
    {
        $indexer->detach($index);
    }
}
