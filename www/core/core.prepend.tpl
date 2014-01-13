<?php
require_once('core.php');
require_once('core.db.php');
require_once('core.kwt.php');
session_start();

if (isLogged() == 0) {
    if (strpos($_SERVER['SCRIPT_NAME'],'admin.login.php')==0)
        Redirect('admin.login.php');
} else {  }

?>