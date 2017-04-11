<?php
namespace OpenActu\IndexerBundle\Tests;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use OpenActu\IndexerBundle\Model\Indexer\BTreeIndexer;
use OpenActu\IndexerBundle\Model\Indexer\ListIndexer;
use OpenActu\IndexerBundle\Model\Indexer\HydratorIndexer;
use OpenActu\IndexerBundle\Model\Type\StringType;
use OpenActu\IndexerBundle\Model\Type\NumericType;
use OpenActu\IndexerBundle\Model\Type\DatetimeType;

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
}
