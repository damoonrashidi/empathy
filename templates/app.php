<?php
  ob_start();
  ob_clean();
  
  include getenv("DOCUMENT_ROOT")."lib/loader.php";
  include getenv("DOCUMENT_ROOT")."config/routes.config.php";
?>
