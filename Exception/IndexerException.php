<?php
namespace OpenActu\IndexerBundle\Exception;

class IndexerException extends \Exception implements IndexerExceptionInterface
{
    public function __construct($message = '',$code = self::UNKNOWN_ERROR_ERRNO,$args = array())
    {
        if(count($args))
            foreach($args as $arg => $value)
                $message = str_replace("%".$arg."%",$value,$message);

        parent::__construct($message,$code);
    }
}
