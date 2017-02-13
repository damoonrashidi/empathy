<?php

/**
 * ArrayManipulor - Preprare Assoc Arrays for insertions, updates in db
 */
class ArrayManip {

  public static function chain($data, $delimiter = "AND") {
    $str = "";
    foreach ($data as $k => $v) {
      if (gettype($v) === "object") {
        $v = (int) $v->id;
      }
      $v = addslashes($v);
      $str .= "$k = '$v' " . $delimiter . " ";
    }
    return substr($str, 0, -(strlen($delimiter) + 2));
  }

  public static function listify($data, $enclose = "") {
    $str = "";
    foreach ($data as $value) {
      if (gettype($value) === 'object') {
        $value = (int) $value->id;
      }
      $value = addslashes($value);
      $str .= $enclose . $value . $enclose . ", ";
    }
    return substr($str, 0, -2);
  }

  public static function wildcard($data, $before = '', $after = '%', $delimiter = "AND") {
    $str = "";
    foreach ($data as $k => $v) {
      $str .= "$k LIKE '" . $before . $v . $after . "' " . $delimiter . " ";
    }
    return substr($str, 0, -(strlen($delimiter) + 2));
  }

  public static function without($data, $keys) {
    $result = array();
    foreach ($data as $k => $v) {
      if (gettype($v) === 'object') {
        $v = (int) $v->id;
      }
      if (!in_array($k, $keys)) {
        $result[$k] = $v;
      }

    }
    return $result;
  }
  public static function limit($number) {
    return array_slice($data, 0, $number - 1);
  }

}
