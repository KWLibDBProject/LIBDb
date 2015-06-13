<?php
// функции работы с базой (@todo: синглтон класс)
require_once('config/config.php');

function ConnectDB()
{
    global $CONFIG;
    $link = mysql_connect($CONFIG['hostname'], $CONFIG['username'], $CONFIG['password']);
    mysql_select_db($CONFIG['database'], $link) or die("Could not select db: " . mysql_error());
    mysql_query("SET NAMES utf8", $link);
    return $link;
}

function CloseDB($link) // useless
{
    mysql_close($link) or Die("Не удается закрыть соединение с базой данных.");
}

function DB_EscapeArray( $array )
{
    $result = array();
    foreach ($array as $key => $keyvalue) {
        switch (gettype( $keyvalue )) {
            case 'string': {
                $result [ $key ] = mysql_real_escape_string( $keyvalue );
                break;
            }
            case 'array': {
                $result [ $key ] = DB_EscapeArray( $keyvalue );
                break;
            }
            default: {
                $result [ $key ] = $keyvalue;
            }
        }
    }
    return $result;
}

function MakeInsertEscaped($array, $table, $where = "")
{
    $arr = DB_EscapeArray( $array );
    $query = "INSERT INTO $table ";

    $keys = "(";
    $vals = "(";
    foreach ($arr as $key => $val) {
        $keys .= "`" . $key . "`" . ",";
        $vals .= "'".$val."',";
    }
    $query .= trim($keys,",") . ") VALUES " . trim($vals,",") . ") ".$where;
    return $query;
}

function MakeInsert($arr, $table, $where="")
{
    $query = "INSERT INTO $table ";

    $keys = "(";
    $vals = "(";
    foreach ($arr as $key => $val) {
        $keys .= "`" . $key . "`" . ",";
        $vals .= "'".$val."',";
    }
    $query .= trim($keys,",") . ") VALUES " . trim($vals,",") . ") ".$where;
    return $query;
}

function MakeUpdateEscaped($array, $table, $where = "")
{
    $arr = DB_EscapeArray( $array );
    $query = "UPDATE $table SET ";
    foreach ($arr as $key=>$val)
    {
        $query.= "`".$key."` = '".$val."', ";
    };
    $query = rtrim( $query , ", ");
    $query.= " ".$where;
    return $query;
}

function MakeUpdate($arr, $table, $where="")
{
    $query = "UPDATE $table SET ";
    foreach ($arr as $key=>$val)
    {
        $query.= "`".$key."` = '".$val."', ";
    };
    $query = rtrim( $query , ", ");
    $query.= " ".$where;
    return $query;
}

function DBLoginCheck($login, $password)
{
    global $CONFIG;
    // возвращает массив с полями "error" и "message"
    $link = ConnectDB();
    // логин мы передали точно совершенно, мы это проверили в скрипте, а пароль может быть и пуст
    // а) логин не существует
    // б) логин существует, пароль неверен
    // в) логин существует, пароль верен
    $userlogin = mysql_real_escape_string(mb_strtolower($login));
    $q_login = "SELECT `md5password`,`permissions`,`id` FROM users WHERE login = '$userlogin'";
    if (!$r_login = mysql_query($q_login)) { /* error catch */ }

    if (mysql_num_rows($r_login)==1) {
        // логин существует
        $user = mysql_fetch_assoc($r_login);
        if ($password === $user['md5password']) {
            // пароль верен
            $return = array(
                'error'         => 0,
                'message'       => 'User credentials correct! ',
                'id'            => $user['id'],
                'permissions'   => $user['permissions'],
                'url'           => 'admin.php',
                'username'      => $userlogin
            );
            kwLogger::logEvent('login', 'userlist', $userlogin, 'User logged!');
        } else {
            // пароль неверен
            $return = array(
                'error'         => 1,
                'message'       => 'Пароль не указан или неверен! Проверьте раскладку клавиатуры! ',
            );
            kwLogger::logEvent('login', 'userlist', $userlogin, 'Error: password incorrect');
        }
    } else {
        // логин не существует
        $return = array(
            'error'         => 2,
            'message'       => 'Пользователь с логином '.$login.' в системе не обнаружен! '
        );
        kwLogger::logEvent('login', 'userlist', $userlogin, 'Error: unknown user!');
    }
    return $return;
}

function DBIsTableExists($table)
{
    return (mysql_query("SELECT 1 FROM $table WHERE 0")) ? true : false;
}



?>