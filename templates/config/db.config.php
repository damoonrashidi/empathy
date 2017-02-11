<?php

$conf = Spyc::YAMLLoad(".empathy.yaml");
$GLOBALS['conf'] = $conf;
$env = "development";

$GLOBALS["MYSQL"] = mysqli_connect(
  $conf['db'][$env]["host"] . ":" . $conf['db'][$env]["port"],
  $conf['db'][$env]["username"],
  $conf['db'][$env]["password"],
  $conf['db'][$env]["database"]
) or die("Error establishing a connection to the database");

?>