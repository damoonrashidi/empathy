<?php
  $DB = [
    'dev' => [
      'host' => 'localhost',
      'user' => 'root',
      'password' => '',
      'database' => 'empathy'
    ],
    'test' => [
      'host' => 'localhost',
      'user' => 'root',
      'password' => '',
      'database' => 'empathy_test'
    ],
    'prod' => [
      'host' => 'localhost',
      'user' => 'root',
      'password' => 'supersecret',
      'database' => 'empathy_prod'
    ]
  ];
  $env = "dev";
  $GLOBALS["MYSQL"] = mysqli_connect(
    $DB[$env]["host"],
    $DB[$env]["user"],
    $DB[$env]["password"],
    $DB[$env]["database"]
  ) or die ("Error establishing a connection to the database");

?>