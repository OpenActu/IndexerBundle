<?php
namespace OpenActu\IndexerBundle\Model\Indexer;

use OpenActu\IndexerBundle\Model\Type\AbstractTypeInterface;
use OpenActu\IndexerBundle\Exception\IndexerException;
abstract class AbstractIndexer implements AbstractIndexerInterface
{
    /**
     * @var $type type
     */
    private $classname;
    private $index;
    private $context = self::CONTEXT_INIT;
    private $card = 0;
    private $data;

    public function forceCard($card)
    {
        $this->card=$card;
    }
    public function forceContext($context)
    {
        $this->context=$context;
    }
    public function forceIndex($index,$type,$data)
    {
        $vindex = $type::strtotype($index);
        $this->index    = new $type($vindex);
        $this->data     = $data;
    }

    public function convertToDatabaseValue()
    {
        $output = array(
            'n' => $this->card(),
            't' => $this->getContext(),
            'c' => get_class($this),
            'p' => $this->getClassname(),
            'i' => null,
            'd' => null,
        );
        if(!$this->isNillable()){
            $output['i'] = $this->getIndex()->convertToDatabaseValue();
            $output['d']  = $this->getData();
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
        $classname      = $this->classname;
        return $classname::cast($value);
    }

    public function clear()
    {
        $this->index    = null;
        $this->data     = null;
        $this->card     = 0;
        $this->context  = self::CONTEXT_INIT;
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
        $this->data     = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setIndex($index)
    {
        $classname = $this->classname;
        $this->index = new $classname($index);
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function getClassname()
    {
        return $this->classname;
    }

    /**
     * execute data writing
     *
     * @var mixed $index
     * @var mixed $data
     */
    public function checkNotNillable($index,$data)
    {
      $classname     = $this->classname;
      $this->context = self::CONTEXT_INSTANCIATE;
      $this->index   = new $classname($index);
      $this->data    = $data;
    }

    public function __construct($classname)
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

        $this->classname = $classname;
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
        return !$this->index->gt($index);
    }

    /**
     * test if current instance is greater than the variable given
     *
     * @param mixed $index index to compare with
     * @return bool
     */
    public function isGreaterThan($index)
    {
        return $this->index->gt($index);
    }

    /**
     * test if current instance is equals to the variable given
     *
     * @param mixed $index index to compare with
     * @return bool
     */
    public function isEquals($index)
    {
        return $this->index->eq($index);
    }

    /**
     * test if current instance is greater or equals to the variable given
     *
     * @param mixed $index index to compare with
     * @return bool
     */
    public function isGreaterOrEqualsThan($index)
    {
        return ($this->index->gt($index) || $this->index->eq($index));
    }

    public function card()
    {
        return $this->card;
    }

}
