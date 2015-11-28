<?php
  /**
   * %NAME% - Description 
   * extended description
   **/
  class %NAME% extends Model {

    //database table for this model
    static $table = %TABLE%;

    function __construct($data = []) {
      $options = [%FIELDS%];
      foreach($options as $key) {
        $data[$key] = isset($data[$key]) ? $data[$key] : "";
      }

%THIS%
      return $this;
    }

  }

?>