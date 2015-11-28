<?php 
  date_default_timezone_set('UTC');
  include __DIR__.'/../config/db.config.php';

  //Load the library - make sure Resource, ArrayManip and Model are there first though
  require __DIR__.'/Resource.php';
  require __DIR__.'/ArrayManip.php';
  require __DIR__.'/Model.php';
  require __DIR__.'/Router.php';
  require __DIR__.'/Controller.php';

  $dh = opendir(__DIR__."/../models/");
  while(false != ($file = readdir($dh))) {
    if(substr($file, 0, 1) != ".")
      require __DIR__."/../models/$file";
  }


?>