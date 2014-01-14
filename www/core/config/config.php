<?php
// здесь задаются ключевые переменные конфигурации (в частности, доступ к базе)

$CFG = array(
    'hostname' => array(
        'local' => 'localhost',
        'sweb' => 'localhost',
        'pony' => 'localhost'
    ),
    'username' => array(
        'local' => 'root',
        'sweb' => 'opuru',
        'pony' => 'root'
    ),
    'password' => array(
        'local' => '',
        'sweb' => 'opurumysqnopassword',
        'pony' => 'JTofv6iB'
    ),
    'database' => array(
        'local' => 'libdb',
        'sweb' => 'opuru',
        'pony' => 'libdb'
    )
);
$remote_hosting_keyname = 'sweb';

$CONFIG['hostname'] = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CFG['hostname']['local']     : $CFG['hostname'][$remote_hosting_keyname];
$CONFIG['username'] = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CFG['username']['local']     : $CFG['username'][$remote_hosting_keyname];
$CONFIG['password'] = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CFG['password']['local']     : $CFG['password'][$remote_hosting_keyname];
$CONFIG['database'] = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CFG['database']['local']     : $CFG['database'][$remote_hosting_keyname];


$CONFIG['flag_dbconnected'] = false;
global $CONFIG;
?>