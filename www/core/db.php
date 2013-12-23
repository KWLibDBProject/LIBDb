<?php
// функции работы с базой (@todo: синглтон класс)
require_once('config.php');

function ConnectDB()
{
    global $CONFIG;
    $hostname = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CONFIG['host_local']     : $CONFIG['host_remote'];
    $username = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CONFIG['username_local'] : $CONFIG['username_remote'];
    $password = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CONFIG['password_local'] : $CONFIG['password_remote'];
    $database = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? $CONFIG['database_local'] : $CONFIG['database_remote'];
    $link = mysql_connect($hostname,$username,$password);
    mysql_select_db($database, $link) or die("Could not select db: " . mysql_error());
    mysql_query("SET NAMES utf8", $link);
    return $link;
}

function CloseDB($link)
{
    mysql_close($link) or Die("Не удается закрыть соединение с базой данных.");
}

function MakeInsert($arr,$table,$where="")
{
    $str = "INSERT INTO $table ";

    $keys = "(";
    $vals = "(";
    foreach ($arr as $key => $val) {
        $keys .= $key . ",";
        $vals .= "'".$val."',";
    }
    $str .= trim($keys,",") . ") VALUES " . trim($vals,",") . ") ".$where;
    return $str;
}

function MakeUpdate($arr,$table,$where="")
{
    $str = "UPDATE $table SET ";
    foreach ($arr as $key=>$val)
    {
        $str.= $key."='".$val."', ";
    };
    $str = substr($str,0,(strlen($str)-2)); // обрезаем последнюю ","
    $str.= " ".$where;
    return $str;
}



?>