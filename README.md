# IndexerBundle
List and BTree integration for custom indexation on variables PHP

## Basic usage

```php
  // implementation with BTree indexer strategy
  ...
  use OpenActu\IndexerBundle\Model\Indexer\BTreeIndexer;
  use OpenActu\IndexerBundle\Model\Type\NumericType;
  ...
  $indexer = new BTreeIndexer(NumericType::class,NumericType::class);
  # attachment based on key - value way
  $indexer->attach(5,8);
  $indexer->attach(3,24);
  $indexer->attach(7,4);
  echo $indexer;     # display the indexes representation
  // here we have "@node(@int:5,@node(@int:3,@empty,@empty),@node(@int:7,@empty,@empty))"
  # loop index
  for($i=0;$i<$indexer->card();$i++){
    $data = $indexer->get($i,$index);        # get the data
    echo $data->getValue();                  # display the data value
    echo $index->getValue();                 # display the index value
    $indexer->detach($index->getValue());    # remove the index value from the indexer
  }
```

To see more use cases, read directly the file /Tests/RoadmapTest.php
