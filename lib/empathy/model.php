<?php
  /**
   * %NAME% - Description 
   * extended description
   **/
  class %NAME% extends Model {

    //database table for this model
    static $table = %TABLE%;
    %THIS%

    function __construct($data = []) {
      foreach($data as $attribute => $override) {
        $this->$attribute = $override;
      }

      return $this;
    }

  }

?>