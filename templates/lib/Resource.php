<?php
  class Resource {

    private $bucket;

    function __construct($bucket){
      $this->bucket = $bucket;
    }

    //return the last $n items in $bucket as a new bucket unless $n is 1 then return that item
    function last($n = 1) {
      if($n == 1)
        return $this->bucket[count($this->bucket)-1];
      if($n >= count($this->bucket))
        return new Resource($this->bucket);
      else
        return new Resource(array_slice($this->bucket), -$n, count($this->bucket)-1);
    }

    //return the first $n items in $bucket as a new bucket unless $n is 1 then return that item
    function first($n = 1){
      if($n == 1)
        return $this->bucket[0];
      if($n >= count($this->bucket))
        return new Resource($this->bucket);
      else
        return new Resource(array_slice($this->bucket), 0, $n);
    }


    //FUNCTION: map: fn -> Resource
    //DESCRIPTION
    //PRE: none
    //POST: Resoruce with all items from this bucket but with $fn applied to them
    function map($fn) {
      $len = count($this->bucket);
      for($i = 0; $i < $len; $i++) {
        $this->bucket[$i] = $fn($this->bucket[$i]);
      }
      return $this;
    }

    function reduce($fn) {
      $this->bucket = call_user_func($fn,$this->bucket);
      return $this;
    }

    //iterates over all elements in the bucket and calls a function on each item
    function each($fn) {
      for ($i=0; $i < count($this->bucket); $i++) { 
        $fn($this->bucket[$i]);
      }
    }

    //convert the current bucket to a json string
    function json() {
      return json_encode($this->bucket);
    }

    //sort this bucket according to the predicate
    function order($predicate) {
      $res = [];
      for($i = 0, $count = count($this->bucket); $i < $count; $i++) {
        $min = $i;
        for($j = 0; $j < $count; $j++) {
          if($this->bucket[$j]->$predicate < $this->bucket[$min]->$predicate) {
            $min = $j;
          }
        }
        $res[] = $this->bucket[$min];
      }
      return new Resource($res);
    }

    //reverse the current bucket
    function reverse() {
      return new Resource(array_reverse($this->bucket));
    }

    function to_array() {
      return $this->bucket;
    }

  }

?>