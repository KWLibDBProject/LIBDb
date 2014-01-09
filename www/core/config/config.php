<?php
// здесь задаются ключевые переменные конфигурации (в частности, доступ к базе)

$CFG = array(
    'hostname' => array(
        'local' => 'localhost',
        'remote' => 'localhost'
    ),
    'username' => array(
        'local' => 'root',
        'remote' => 'root'
    ),
    'password' => array(
        'local' => '',
        'remote' => 'JTofv6iB'
    ),
    'database' => array(
        'local' => 'libdb',
        'remote' => 'libdb'
    )
);

$CONFIG['hostname'] = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CFG['hostname']['local']     : $CFG['hostname']['remote'];
$CONFIG['username'] = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CFG['username']['local']     : $CFG['username']['remote'];
$CONFIG['password'] = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CFG['password']['local']     : $CFG['password']['remote'];
$CONFIG['database'] = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CFG['database']['local']     : $CFG['database']['remote'];


$CONFIG['flag_dbconnected'] = false;
global $CONFIG;
?>