<?php
  /**
  * Model - Persitant Object Storage and Manipulation
  */
  abstract class Model{
    
    function __construct(){
      return $this;
    }

    function save() {
      $ar = new ArrayManip();
      $this->created_at = date("Y-m-d H:i:s");
      $this->modified_at = date("Y-m-d H:i:s");
      $rows = $ar->listify(array_keys($ar->without($this,['id'])));
      $values = $ar->listify(array_values($ar->without($this, ['id'])), "'");
      $q = "INSERT INTO ".static::$table." ($rows) VALUES($values)";
      $tstart = microtime(true);
      $create = $GLOBALS['MYSQL']->query($q);
      $time = round(microtime(true) - $tstart, 5)."s";
      error_log("QUERY: $q ($time)");
      if($create !== false){
        return $GLOBALS['MYSQL']->insert_id;
      }
      else return false;
    }
    /*
    * Function: where($data = array) -> array
    * PRE: every key in $data must match a column in the table for this model
    * POST: every object that matches $key = $value. if it's only one object (for instance when runnning where id = 1)
    *       it returns that object as an array, otherwise it returns an array of arrays
    * SIDE-EFFECTS: queries the database
    * EXAMPLE: $UserObject->where(array('email' => 'joe@example.net')) #=> array('id' => 1, 'email' => 'joe@example.net', 'firstname' => 'Joe', 'lastname' => 'Poe', 'age' => 20)
    * Example 2: $UserObject->where(array('age' => '20')); #=> array(array(user1, user2,...))
    */
    static function where($data){
      $set = [];
      $data = ArrayManip::chain($data, "AND");
      $q = "SELECT * FROM ".static::$table." WHERE ".$data;
      $tstart = microtime(true);
      $result = $GLOBALS['MYSQL']->query($q);
      $time = round(microtime(true) - $tstart, 5)."s";
      error_log("QUERY: $q ($time)");
      error_log("RESULT: ".$result->num_rows." rows");
      while($row = $result->fetch_assoc()){
        array_push($set, new static($row));
      }
      if(count($set) == 0){
        return null;
      }
      return new Resource($set);
    }
    /*
    * Function: find($id * int) -> array
    * PRE:
    * POST: corresponding object in the table for this model with the id = $id
    * SIDE-EFFECTS: queries the database
    * EXAMPLE: $user = $userObject->find(1) //find the first user
    */

    static function find($id){
      if(!is_numeric($id)) return null;
      $set = [];
      $q = "SELECT * FROM ".static::$table." WHERE id = $id";
      $tstart = microtime(true);
      $results = $GLOBALS['MYSQL']->query($q);
      $time = round(microtime(true) - $tstart, 5)."s";
      error_log("QUERY: $q ($time)");
      if($results->num_rows == 0){
         return null;
      }
      return new static($results->fetch_assoc());
    }

    /*
    * Function: wildcard($data = array) -> array
    * PRE: every key in $data must match a column in the table for this model
    * POST: every object that matches $key LIKE %$value%., An array of objects matching
    *       the query
    * SIDE-EFFECTS: queries the database
    * EXAMPLE: $find = Article::wildcard(['title' => 'Welc']) #=> ArticleObject->['title' => 'welcome to my blog', 'body' => 'stuff here..',...]
    */
    static function wildcard($data){
      $set = [];
      $data = ArrayManip::wildcard($data, '%', '%', 'OR');
      $q = "SELECT * FROM ".static::$table." WHERE ".$data;
      $tstart = microtime(true);
      $result = $GLOBALS['MYSQL']->query($q);
      $time = round(microtime(true) - $tstart, 5)."s";
      error_log("QUERY: $q ($time)");
      error_log("RESULT: ".$result->num_rows." rows");
      while($row = $result->fetch_assoc()){
        array_push($set, new static($row));
      }
      if(count($set) == 0){
        return null;
      }
      return new Resource($set);

    }

    /*
    * Function: order([predicate, direction limit]) -> array
    * PRE: every key in $parameters must match a column in the table for this model
    * POST: all objects of this model ordered by $fields in $direction (ascending|descending) order
    * SIDE-EFFECTS: queries the database
    * EXAMPLE: User::order(['predicate' => ['age'], 'direction' => 'ASC']) #=> [user1, user2, user3, ... ]
    * @params array $parameters
    */
    static function order($parameters = []){
      $limit = (isset($parameters['limit'])) ? "LIMIT ".$parameters['limit'] : '';
      $offset = (isset($parameters['offset'])) ? "OFFSET ".$parameters['offset'] : '';
      $parameters['predicate'] = ArrayManip::listify($parameters['predicate']);
      $parameters['direction'] = (isset($parameters['limit'])) ? $parameters['direction'] : 'ASC';
      $q = "SELECT * FROM ".static::$table." ORDER BY ".$parameters['predicate']." ".$parameters['direction']." $limit $offset";
      $tstart = microtime(true);
      $result = $GLOBALS['MYSQL']->query($q);
      $time = round(microtime(true) - $tstart, 5)."s";
      error_log("QUERY: $q ($time)");
      error_log("RESULT: ".$result->num_rows." rows");
      $set = [];
      while($row = $result->fetch_assoc()){
        array_push($set, new static($row));
      }
      return new Resource($set);
    }

    function update(){
      $this->modified_at = date("Y-m-d H:i:s");
      $data = ArrayManip::chain($this, ", ");
      $q = "UPDATE ".static::$table." SET ".$data." WHERE id = ".$this->id;
      $tstart = microtime(true);
      $update = $GLOBALS['MYSQL']->query($q);
      $time = round(microtime(true) - $tstart, 5)."s";
      error_log("QUERY: $q ($time)");
      error_log($GLOBALS['MYSQL']->info);
      return $update !== false;
    }

    static function all(){
      $set = [];
      $q = "SELECT * FROM ".static::$table;
      $tstart = microtime(true);
      $result = $GLOBALS['MYSQL']->query($q);
      while($row = $result->fetch_assoc()){
        array_push($set, new static($row));
      }
      $time = round(microtime(true) - $tstart, 5)."s";
      error_log("QUERY: $q ($time)");
      error_log("RESULT: ".$result->num_rows." rows");
      return new Resource($set);
    }

    function delete(){
      $delete = $GLOBALS['MYSQL']->query("DELETE FROM ".static::$table." WHERE id = ".$this->id);
      return $delete !== false;
    }

    function json() {
      return json_encode($this);
    }


  }

?>