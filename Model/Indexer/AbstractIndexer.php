<?php
namespace OpenActu\IndexerBundle\Model\Indexer;

use OpenActu\IndexerBundle\Model\Type\AbstractTypeInterface;
use OpenActu\IndexerBundle\Exception\IndexerException;
abstract class AbstractIndexer implements AbstractIndexerInterface
{
    /**
     * @var $classnameIndex
     */
    private $classnameIndex;

    /**
     * @var $objectIndex
     */
    private $objectIndex;

    /**
     * @var $classnameData
     */
    private $classnameData;

    /**
     * @var $objectData
     */
    private $objectData;

    /**
     * @var $context
     */
    private $context = self::CONTEXT_INIT;

    /**
     * @var $card
     */
    private $card = 0;

    /**
     * @var $data (to delete)
     */
    private $data;

    public function __clone()
    {
        if(!$this->isNillable()){
            $this->objectIndex      = clone $this->objectIndex;
            $this->objectData       = clone $this->objectData;
        }
    }

    public function forceCard($card)
    {
        $this->card=$card;
    }
    public function forceContext($context)
    {
        $this->context=$context;
    }
    public function forceIndex($index)
    {
        $type = $this->classnameIndex;
        $vindex = $type::strtotype($index);
        $this->objectIndex    = new $type($vindex);
    }
    public function forceData($data)
    {
        $type = $this->classnameData;
        $vindex = $type::strtotype($data);
        $this->objectData    = new $type($vindex);
    }

    public function convertToDatabaseValue()
    {
        $output = array(
            // length
            'n' => $this->card,
            // context (init or instanciated)
            't' => $this->context,
            // current class
            'c' => get_class($this),
            // current class type
            'p' => $this->classnameIndex,
            // index
            'i' => null,
            // data
            'd' => null,
            // classname data
            'e' => $this->classnameData,
        );
        if(!$this->isNillable()){
            $output['i'] = $this->getIndex()->convertToDatabaseValue();
            $output['d'] = $this->getData()->convertToDatabaseValue();
        }
        return $output;
    }

    public function increaseCard()
    {
        $this->card++;
    }

    public function set($attribute, $data)
    {
        if(property_exists($this, $attribute)){
            $this->$attribute = $data;
        }
    }
    public function checkIndex($value)
    {
        $classname      = $this->classnameIndex;
        return $classname::cast($value);
    }

    public function clear()
    {
        $this->objectIndex  = null;
        $this->objectData   = null;
        $this->card         = 0;
        $this->context      = self::CONTEXT_INIT;
        $this->shorcutGt    = null;
        $this->shorcutLt    = null;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function decreaseCard()
    {
        $this->card--;
    }

    public function setData($data)
    {
        $classnameData = $this->classnameData;
        $this->objectData = new $classnameData($data);
    }

    public function setShorcutGt($position)
    {
        $this->shorcutGt = $position;
    }

    public function getData()
    {
        return $this->objectData;
    }

    public function setIndex($index)
    {
        $classnameIndex = $this->classnameIndex;
        $this->objectIndex = new $classnameIndex($index);
    }

    public function getIndex()
    {
        return $this->objectIndex;
    }

    public function getClassnameIndex()
    {
        return $this->classnameIndex;
    }

    public function getClassnameData()
    {
        return $this->classnameData;
    }
    /**
     * execute data writing
     *
     * @var mixed $index
     * @var mixed $data
     */
    public function checkNotNillable($index,$data)
    {
      $classnameIndex= $this->classnameIndex;
      $classnameData = $this->classnameData;
      $this->context = self::CONTEXT_INSTANCIATE;
      $this->objectIndex   = new $classnameIndex($index);
      $this->objectData    = new $classnameData($data);
    }

    public function __construct($classnameIndex, $classnameData)
    {
        Indexer::check($classnameIndex);
        Indexer::check($classnameData);

        $this->classnameIndex = $classnameIndex;
        $this->classnameData  = $classnameData;
    }

    /**
     * Check if the current item is empty
     *
     */
    public function isNillable()
    {
        return ($this->context === self::CONTEXT_INIT);
    }

    /**
     * test if current instance is less or equals to the variable given
     *
     * @var mixed $index index to compare with
     * @return bool
     */
    public function isLessOrEqualsThan($index)
    {
        return !$this->objectIndex->gt($index);
    }

    /**
     * test if current instance is greater than the variable given
     *
     * @param mixed $index index to compare with
     * @return bool
     */
    public function isGreaterThan($index)
    {
        return $this->objectIndex->gt($index);
    }

    /**
     * test if current instance is equals to the variable given
     *
     * @param mixed $index index to compare with
     * @return bool
     */
    public function isEquals($index)
    {
        return $this->objectIndex->eq($index);
    }

    /**
     * test if current instance is greater or equals to the variable given
     *
     * @param mixed $index index to compare with
     * @return bool
     */
    public function isGreaterOrEqualsThan($index)
    {
        return ($this->objectIndex->gt($index) || $this->objectIndex->eq($index));
    }

    public function card()
    {
        return $this->card;
    }

}
