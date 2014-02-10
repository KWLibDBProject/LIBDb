<?php
require_once('core.php');

$_SESSION['u_id'] = -1;
$_SESSION['u_permissions'] = -1;
setcookie('u_libdb_logged',null,-1);
unset($_COOKIE['u_libdb_logged']);
setcookie('u_libdb_permissions',null,-1);
unset($_COOKIE['u_libdb_permissions']);
Redirect('/core/');
?>