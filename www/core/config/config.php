<?php
// здесь задаются ключевые переменные конфигурации (в частности, доступ к базе)
$GLOBALS['debugmode'] = true; // установить в true для возможности вызова аякс-вызываемых экторов в обычном режиме

$CONFIG['username_local'] 	= 'root';
$CONFIG['username_remote']	= 'root';

$CONFIG['host_local']    	= 'localhost';
$CONFIG['host_remote']   	= 'localhost';

$CONFIG['password_local']	= '';
$CONFIG['password_remote']	= 'JTofv6iB';

$CONFIG['database_local']       = 'libdb';
$CONFIG['database_remote']       = 'libdb';

$CONFIG['table'] = array(
    'users' => 'users'
); // смысл в этой таблице?
$CONFIG['flag_dbconnected'] = false;

?>