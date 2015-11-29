<?php
  $DB = [
    'dev' => [
      'host' => 'localhost',
      'user' => 'root',
      'password' => '',
      'database' => 'dev'
    ],
    'test' => [
      'host' => 'localhost',
      'user' => 'root',
      'password' => '',
      'database' => 'test'
    ],
    'prod' => [
      'host' => 'localhost',
      'user' => 'root',
      'password' => 'supersecret',
      'database' => 'prod'
    ]
  ];
  $env = "dev";
  $MYSQL = mysqli_connect(
    $GLOBALS["DB"][$env]["host"],
    $GLOBALS["DB"][$env]["user"],
    $GLOBALS["DB"][$env]["password"],
    $GLOBALS["DB"][$env]["database"]
  ) or die ("Error establishing a connection to the database");

?>