<?php
require_once('config/config.php');

/**
 *
 * @return mysqli
 */
function ConnectDB()
{
    global $CONFIG;
    $link = mysqli_connect($CONFIG['hostname'], $CONFIG['username'], $CONFIG['password'], $CONFIG['database'])
            or die("Can't establish connection to '{$CONFIG['hostname']}' for user '{$CONFIG['username']}' at '{$CONFIG['database']}' database");
    mysqli_query($link, "SET NAMES utf8");
    return $link;
}

/**
 * @param $link
 */
function CloseDB($link)
{
    mysqli_close($link);
}


/**
 *
 * @param $array
 * @return array
 */
function DB_EscapeArray( $array )
{
    global $mysqli_link;
    $result = array();
    foreach ($array as $key => $keyvalue) {
        switch (gettype( $keyvalue )) {
            case 'string': {
                $result [ $key ] = mysqli_real_escape_string($mysqli_link, $keyvalue );
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
    global $mysqli_link;
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
    global $mysqli_link;
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
    global $mysqli_link;
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
    global $mysqli_link;
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
    global $mysqli_link;
    // возвращает массив с полями "error" и "message"
    $link = ConnectDB();
    // логин мы передали точно совершенно, мы это проверили в скрипте, а пароль может быть и пуст
    // а) логин не существует
    // б) логин существует, пароль неверен
    // в) логин существует, пароль верен
    $userlogin = mysqli_real_escape_string($mysqli_link, mb_strtolower($login));
    $q_login = "SELECT `md5password`,`permissions`,`id` FROM users WHERE login = '$userlogin'";

    $r_login = mysqli_query($mysqli_link, $q_login);

    if (!$r_login) { /* error catch */ }

    if (mysqli_num_rows($r_login)==1) {
        // логин существует
        $user = mysqli_fetch_assoc($r_login);
        if ($password === $user['md5password']) {
            // пароль верен
            $return = array(
                'error'         => 0,
                'message'       => 'User credentials correct! ',
                'id'            => $user['id'],
                'permissions'   => $user['permissions'],
                'url'           => '_admin.php',
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

/**
 * @param $table
 * @return bool
 */
function DBIsTableExists($table)
{
    global $mysqli_link;
    return (mysqli_query($mysqli_link,"SELECT 1 FROM $table WHERE 0")) ? true : false;
}

/**
 * @param $field
 * @param $table
 * @param string $condition
 * @return null
 */
function DBGetCount($field, $table, $condition = "")
{
    global $mysqli_link;
    $cond  = ($condition !== "")
        ? " WHERE {$condition}"
        : "";
    $query = "SELECT COUNT({$field}) AS rowcount FROM {$table} {$cond}";
    $result = mysqli_query($mysqli_link, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $ret = $row['rowcount'];
    } else {
        $ret = NULL;
    }
    return $ret;
}