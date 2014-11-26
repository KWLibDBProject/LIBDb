<?php
require_once('core.php');
require_once('core.db.php');
require_once('core.kwlogger.php');

$SID = session_id();
if(empty($SID)) session_start();

ConnectDB();

kwLogger::logEvent('login', 'userlist', $_SESSION['u_username'], 'User logged out');

$_SESSION['u_id'] = -1;
$_SESSION['u_permissions'] = -1;

setcookie('u_libdb_logged',null,-1);
unset($_COOKIE['u_libdb_logged']);

setcookie('u_libdb_permissions',null,-1);
unset($_COOKIE['u_libdb_permissions']);

Redirect('/core/');
?>