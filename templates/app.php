<?php

  $res = substr($_SERVER['REQUEST_URI'],1);

  if(file_exists($res)){
    $mimes = [
      'css' => 'text/css',
      'js'  => 'application/javascript',
      'png' => 'image/png',
      'jpg' => 'image/jpeg',
      'gif' => 'image/gif',
      'webp' => 'image/webp',
      'webm' => 'image/webm',
      'pdf' => 'application/pdf'
    ];
    $ext = explode(".", $res)[1];
    header("Content-Type: ".$mimes[$ext]);
    echo file_get_contents($res);
    exit;
  }

  ob_start();
  ob_clean();
  
  include getenv("DOCUMENT_ROOT")."lib/loader.php";
  include getenv("DOCUMENT_ROOT")."config/routes.config.php";
?>
