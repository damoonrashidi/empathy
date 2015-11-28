<?php 
	/**
	* Model - Persitant Object Storage and Manipulation
	*/
	abstract class Model {	
		
		function __construct(){
			return $this;
		}

		function save(){
			$ar = new ArrayManip();
			$this->created_at = date("Y-m-d H:i:s");
			$this->modified_at = date("Y-m-d H:i:s");
			$rows = $ar->listify(array_keys($ar->without($this,['id'])));
			$values = $ar->listify(array_values($ar->without($this, ['id'])), "'");
			$create = $GLOBALS['MYSQL']->query("INSERT INTO ".static::$table." ($rows) VALUES($values)");
			if($create !== false)
				return $GLOBALS['MYSQL']->insert_id;
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
			$result = $GLOBALS['MYSQL']->query("SELECT * FROM ".static::$table." WHERE ".$data);
			while($row = $result->fetch_assoc()){
				array_push($set, new static($row));
			}
			if(count($set) == 0) return null;
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
			$results = $GLOBALS['MYSQL']->query("SELECT * FROM ".static::$table." WHERE id = $id");
			if($results->num_rows == 0) return null;
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
			$result = $GLOBALS['MYSQL']->query("SELECT * FROM ".static::$table." WHERE ".$data);
			while($row = $result->fetch_assoc()){
				array_push($set, new static($row));
			}
			if(count($set) == 0) return null;
			return new Resource($set);

		}

		/*
		* Function: order_by(array($fields * string, $direction * string)) -> array
		* PRE: every key in $parameters must match a column in the table for this model
		* POST: all objects of this model ordered by $fields in $direction (ascending|descending) order
		* SIDE-EFFECTS: queries the database
		* EXAMPLE: User::order_by(array('data' => 'age', 'direction' => 'ASC')) #=> [user1, user2, user3, ... ]
		*/
		static function order($parameters = []){
			$limit = (isset($parameters['limit'])) ? "LIMIT ".$parameters['limit'] : '';
			$offset = (isset($parameters['offset'])) ? "OFFSET ".$parameters['offset'] : '';
			$parameters['data'] = ArrayManip::listify($parameters['data']);
			$parameters['direction'] = (isset($parameters['limit'])) ? $parameters['direction'] : 'ASC';
			$result = $GLOBALS['MYSQL']->query("SELECT * FROM ".static::$table." ORDER BY ".$parameters['data']." ".$parameters['direction']." $limit $offset");
			$set = [];
			while($row = $result->fetch_assoc()){
				array_push($set, new static($row));
			}
			return new Resource($set);
		}

		function update(){
			$this->modified_at = date("Y-m-d H:i:s");
			$data = ArrayManip::chain($this, ", ");
			$update = $GLOBALS['MYSQL']->query("UPDATE ".static::$table." SET ".$data." WHERE id = ".$this->id);
			return $update !== false;
		}

		static function all(){
			$set = [];
			$result = $GLOBALS['MYSQL']->query("SELECT * FROM ".static::$table);
			while($row = $result->fetch_assoc()){
				array_push($set, new static($row));
			}
			return new Resource($set);
		}

		function delete(){
			$delete = $GLOBALS['MYSQL']->query("DELETE FROM ".static::$table." WHERE id = ".$this->id);
			return $delete !== false;
		}

		function json(){
			return json_encode($this);
		}

	}

?>