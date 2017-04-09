<?php
namespace OpenActu\IndexerBundle\Tests;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use OpenActu\IndexerBundle\Model\Indexer\BTree;
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
    }

    public function validateBTree($classname, $indexes, $noindexes)
    {
        $indexer = new BTree($classname);
        foreach($indexes as $index){
            $value = rand(0,1000);
            $indexer->attach($index,$value);
        }

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

        foreach($indexes as $index)
            $testIndexExist = $testIndexExist && $indexer->exists($index);
        $this->assertEquals($testIndexExist, true);

        /**
         * check that all indexes are ordonned
         */
        $testIndexOrdonned = true;
        for($i = 0; ( $i< $indexer->card() ); $i++){
            $indexer->get($i,$index);
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

        $this->validateBTree(StringType::class, $indexes, $noindexes);
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

        $this->validateBTree(DatetimeType::class, $indexes, $noindexes);
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
            5,

        );
        $this->validateBTree(NumericType::class, $indexes, $noindexes);

    }
}
