<?php
require_once '__required.php'; // $mysqli_link
$SID = session_id();
if(empty($SID)) session_start();

kwLogger::logEvent('login', 'userlist', $_SESSION['u_username'], 'User logged out');

$_SESSION['u_id'] = -1;
$_SESSION['u_permissions'] = -1;

setcookie('u_libdb_logged',null,-1);
unset($_COOKIE['u_libdb_logged']);

setcookie('u_libdb_permissions',null,-1);
unset($_COOKIE['u_libdb_permissions']);

Redirect('/core/');