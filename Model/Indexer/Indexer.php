<?php
namespace OpenActu\IndexerBundle\Model\Indexer;

use OpenActu\IndexerBundle\Model\Type\AbstractTypeInterface;
use OpenActu\IndexerBundle\Exception\IndexerException;

class Indexer
{

    /**
     *
     * @return AbstractIndexerInterface
     */
    public static function union(AbstractIndexerInterface $a, AbstractIndexerInterface $b)
    {

    }

    /**
     * Check if classname is interfaced with Indexer
     *
     * @param string $classname
     * @return bool
     */
    public static function check($classname)
    {
        $interfaces = @class_implements($classname);
        $isValidType= false;
        if($interfaces)
        {
            foreach($interfaces as $interface)
            {
                if($interface === AbstractTypeInterface::class)
                    $isValidType = true;
            }
        }

        if(!$isValidType)
            throw new IndexerException(
                IndexerException::INVALID_TYPE_FOUND_ERRMSG,
                IndexerException::INVALID_TYPE_FOUND_ERRNO,
                array('type' => $classname)
            );
        return true;
    }
}
