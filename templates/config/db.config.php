<?php
  $DB = [
    'dev' => [
      'host' => 'localhost',
      'user' => 'root',
      'password' => '',
      'database' => 'v2'
    ],
    'test' => [
      'host' => 'localhost',
      'user' => 'root',
      'password' => '',
      'database' => 'v2test'
    ],
    'prod' => [
      'host' => 'localhost',
      'user' => 'root',
      'password' => '',
      'database' => 'v2prod'
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