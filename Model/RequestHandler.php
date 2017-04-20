<?php
namespace OpenActu\IndexerBundle\Model;

use OpenActu\IndexerBundle\Model\Indexer\BTreeIndexer;
use OpenActu\IndexerBundle\Model\Type\AutoIncrementType;
use OpenActu\IndexerBundle\Model\Indexer\Invoker;
class RequestHandler

{
    /**
     * @var $isGenerated
     *
     */
    private $isGenerated = false;

    /**
     * @var $data
     *
     */
    private $data       = array();

    /**
     * @var $instance
     *
     */
    private $instance   = null;

    public function __construct()
    {
        $this->instance = new RequestInstanceHandler();
    }

    public function addField($field, $classname)
    {
        if(!$this->isGenerated)
        {
            $this->data[$field] = new BTreeIndexer($classname, AutoIncrementType::class);
            $this->instance->add($field,$classname);
        }
    }

    public function generate()
    {
        $this->isGenerated = true;
    }

    /**
     * Get new instance
     *
     * @return RequestInstanceHandler|null
     */
    public function newInstance()
    {
        if($this->isGenerated)
        {
            return clone $this->instance;
        }
        return null;
    }

    public function getRequest($field, array $parameters=array())
    {
        if(!empty($this->data[$field]))
            return Invoker::getRequest($this->data[$field], $parameters);
        return null;
    }

    public function save(RequestInstanceHandler $instance)
    {
        $increment = AutoIncrementType::increment();

        $values = $instance->getValues();
        foreach($values as $field => $value)
            Invoker::attach($this->data[$field],$value->getValue(),$increment);
    }
}
