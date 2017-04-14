<?php
namespace OpenActu\IndexerBundle\Tests;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use OpenActu\IndexerBundle\Model\Indexer\BTreeIndexer;
use OpenActu\IndexerBundle\Model\Indexer\ListIndexer;
use OpenActu\IndexerBundle\Model\Indexer\HydratorIndexer;
use OpenActu\IndexerBundle\Model\Type\StringType;
use OpenActu\IndexerBundle\Model\Type\NumericType;
use OpenActu\IndexerBundle\Model\Type\DatetimeType;
use OpenActu\IndexerBundle\Model\Indexer\RequestIndexer;
use OpenActu\IndexerBundle\Exception\IndexerException;
/**
 *   -------------------
 *   |                 |
 *   |  Configuration  |
 *   |                 |
 *   -------------------
 *
 *    write here the namespace URL you have declared
 *
 */
//use <YourBundle>\Entity\<YourUrlEntity>;
class RoadmapTest extends KernelTestCase
{
    private $container;

    public function __construct()
    {
        self::bootKernel();
        $this->container = self::$kernel->getContainer();
    }

    public function testIndex()
    {
        $this->validateBTreeStringType();
        $this->validateBTreeNumericType();
        $this->validateBTreeDatetimeType();

        $this->validateListStringType();
        $this->validateListNumericType();
        $this->validateListDatetimeType();

        $this->validateBTreeStringReduce();
        $this->validateBTreeNumericReduce();
        $this->validateBTreeDatetimeReduce();

        $this->validateBTreeComplexReduce();
    }

    public function validateIndexer($classname, $indexes, $noindexes, $type=BTreeIndexer::class)
    {
        $indexer = new $type($classname, NumericType::class);
        foreach($indexes as $index){
            $value = rand(0,1000);
            $indexer->attach($index,$value);
        }

        /**
         * build a db response to store it
         */
        $string = $indexer->convertToDatabaseValue();
        $rindexer = HydratorIndexer::hydrate($string);
        unset($indexer);
        $indexer = $rindexer;

        /**
         * try to inject false index
         */
        $testNoIndex = true;
        foreach($noindexes as $noindex)
        {
            $unitTest = false;
            try{
                $value = rand(0,1000);
                $indexer->attach($noindex,$value);
            }
            catch(\Exception $e)
            {
                $unitTest = true;
            }
            $testNoIndex = $testNoIndex && $unitTest;
        }

        /**
         * check that all indexes are in the indexer
         */
        $this->assertEquals($indexer->card(), count($indexes));

        $testIndexExist = true;

        foreach($indexes as $index){
            $testIndexExist = $testIndexExist && $indexer->exists($index);
        }
        $this->assertEquals($testIndexExist, true);

        /**
         * check that all indexes are ordonned
         */
        $testIndexOrdonned = true;
        for($i = 0; ( $i< $indexer->card() ); $i++){

            $indexer->get($i,$index);

            /**
             * cget validation
             */
            $this->assertTrue($indexer->cget($index) === $i);


            if($i > 0)
                $testIndexOrdonned = $testIndexOrdonned && $index->gte($pred);
            $pred = $index->getValue();
        }
        $this->assertEquals($testIndexOrdonned, true);

        /**
         * optimization
         */
        $nindexer = $indexer->optimize();
        $this->assertTrue( ($indexer->score() <= 1) && ($nindexer->score() == 1) );

        /**
         * detachment
         */
        foreach($indexes as $index){ $indexer->detach($index); }

        $this->assertEquals($indexer->card(), 0);
    }

    public function validateListStringType()
    {
        $noindexes = array(
            array(true)
        );

        $indexes = array(
            "sods",
            "fqofao",
            "atajso",
            "4633Ses",
            "QTEeqo",
            "4334SQ",
            "pqohq",
            "436DQ",
            12,
            "ies",
        );

        $this->validateIndexer(StringType::class, $indexes, $noindexes, ListIndexer::class);
    }

    public function validateBTreeStringType()
    {
        $noindexes = array(
            array(true)
        );

        $indexes = array(
            "sods",
            "fqofao",
            "atajso",
            "4633Ses",
            "QTEeqo",
            "4334SQ",
            "pqohq",
            "436DQ",
            12,
            "ies",
        );

        $this->validateIndexer(StringType::class, $indexes, $noindexes);
    }

    public function validateBTreeDatetimeType()
    {
        $noindexes = array(
            "x",
            1,
            array()
        );

        $indexes = array(
            new \DateTime("2015-01-01 00:00:00"),
            new \DateTime("2015-06-01 00:00:00"),
            new \DateTime("2016-06-01 00:00:00"),
        );

        $this->validateIndexer(DatetimeType::class, $indexes, $noindexes);
    }

    public function validateListDatetimeType()
    {
        $noindexes = array(
            "x",
            1,
            array()
        );

        $indexes = array(
            new \DateTime("2015-01-01 00:00:00"),
            new \DateTime("2015-06-01 00:00:00"),
            new \DateTime("2016-06-01 00:00:00"),
        );

        $this->validateIndexer(DatetimeType::class, $indexes, $noindexes, ListIndexer::class);
    }


    public function validateListNumericType()
    {
        $noindexes = array(
            1.2,
            -1,
            "a",
            array(),
        );

        $indexes = array(
            1,
            3,
            4,
            5,
            6,
            12,
            2,
            100,
            10,
        );
        $this->validateIndexer(NumericType::class, $indexes, $noindexes,ListIndexer::class);

    }

    public function validateBTreeNumericType()
    {
        $noindexes = array(
            1.2,
            -1,
            "a",
            array(),
        );

        $indexes = array(
            1,
            3,
            4,
            5,
            6,
            7,

        );
        $this->validateIndexer(NumericType::class, $indexes, $noindexes);

    }

    public function validateBTreeStringReduce()
    {
        $data = ['b','c','a','d','g','e','f'];
        $this->validateReduce($data,BTreeIndexer::class,StringType::class,'c','f','b','g');
    }

    public function validateBTreeNumericReduce()
    {
        $data = [5,10,1,2,4,6,9,7,8];
        $this->validateReduce($data,BTreeIndexer::class,NumericType::class,4,8,2,9);
    }

    public function validateBTreeDatetimeReduce()
    {
        $data = array(
            new \DateTime("2015-01-01 00:00:00"),
            new \DateTime("2015-06-01 00:00:00"),
            new \DateTime("2016-06-01 00:00:00"),
            new \DateTime("2017-01-01 00:00:00"),
            new \DateTime("2017-06-01 00:00:00"),
            new \DateTime("2017-06-01 00:00:01"),
        );
        $this->validateReduce($data,BTreeIndexer::class,DatetimeType::class,
            new \DateTime("2015-06-01 00:00:00"),
            new \DateTime("2017-06-01 00:00:00"),
            new \DateTime("2015-01-01 00:00:00"),
            new \DateTime("2017-06-01 00:00:01")
        );
    }

    public function validateBTreeComplexReduce()
    {
        $indexer = new BTreeIndexer(NumericType::class,NumericType::class);

        for($i=0;$i<100;$i++)
            $indexer->attach($i,$i);

        $request = new RequestIndexer($indexer);
        $request
            ->lt(new NumericType(95))
            ->gt(new NumericType(92))
            ->notIn(array(90,43,93,93,34))
            ->execute();

        $this->assertEquals($request->card(),1);
        $this->assertEquals($request->get(0),new NumericType(94));

        unset($request);

        /**
         * assert that the clonage goodly works
         */
        $this->assertTrue($indexer->exists(93));

        /**
         * check that the limit and offset works
         */
        $request = new RequestIndexer($indexer);
        $request
            ->gt(new NumericType(10))
            ->lt(new NumericType(90))
            ->offset(10)
            ->limit(10)
            ->execute();
        $test = true;
        for($i=0;$i<$request->card();$i++){
            if($i < 10)
                $test = $test && (null !== $request->get($i));
            else
                $test = $test && (null === $request->get($i));
        }

        $this->assertEquals($request->get(0), new NumericType(21));
        $this->assertTrue($test);

        /**
         * check the in method of request
         */
        $request
            ->reload()
            ->in(array(1,3,4,7,7,4))
            ->offset(0)
            ->limit(1)
            ->execute();

        $this->assertEquals($request->get(0), new NumericType(1));

        $request->reload()
          ->offset(1)
          ->limit(2)
          ->execute();

        $this->assertEquals($request->get(0), new NumericType(3));
        $this->assertEquals($request->get(1), new NumericType(4));

        /**
         * @todo block the "in" methods (call only one time)
         */
        $msg = '';
        try{ $request->reload()->in(array(2))->execute(); }
        catch(IndexerException $e){ $msg = $e->getMessage(); }
        $this->assertEquals($msg, IndexerException::NO_DOUBLE_CALL_ON_IN_ACCEPTED_ERRMSG);

    }

    public function validateReduce(array $data,$classIndexer,$classType,$min,$max,$lt,$gt)
    {
        $indexer = new $classIndexer($classType, $classType);
        foreach($data as $i)
            $indexer->attach($i,$i);

        $request = new RequestIndexer($indexer);
        $request
            ->lt(new $classType($gt))
            ->gt(new $classType($lt))
            ->execute();

        /**
         * check that the good bounds are found
         *
         */
        $this->assertEquals($request->min()->getValue(), $min);
        $this->assertEquals($request->max()->getValue(), $max);

        $mem = $request->min();
        $testOrderred = true;
        for($i=1;$i<$request->card();$i++){
            $testOrderred = $testOrderred && $request->get($i)->gt($mem->getValue());
            $mem = $request->get($i);
        }

        /**
         * check that the request indexer is orderred
         *
         */
        $this->assertEquals($testOrderred, true);

        /**
         * check the reload of request indexer
         */
        $request->reload()->execute();

        $this->assertEquals($request->max(), $indexer->max());
        $this->assertEquals($request->min(), $indexer->min());
        $this->assertEquals($request->card(), $indexer->card());
    }
}
