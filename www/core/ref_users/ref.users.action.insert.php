<?php

if (!isAjaxCall()) Die('Некорректный вызов скрипта!');

if (!IsSet($_POST['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
}
$table = $_POST['ref_name'];

$link = ConnectDB();

$post = array(
    'name' => mysql_escape_string($_POST['name']),
    'email' => mysql_escape_string($_POST['email']),
    'permissions' => mysql_escape_string($_POST['permissions']),
    'login' => mysql_escape_string($_POST['login']),
    'password' => mysql_escape_string($_POST['password']),
    'phone' => mysql_escape_string($_POST['phone']),
    'md5password' => md5(mysql_escape_string($_POST['password']))
);

$q = "SELECT `id` FROM $table WHERE `login` LIKE '$post[login]'";
$r = mysql_query($q,$link);

if (mysql_errno($link)==0)
{
    // что-то нашли т.е. mysql_num_rows()>1
    if (mysql_num_rows($r)==0) {
        // пользователь уникален
        $q = MakeInsert($post,$table);
        $r = mysql_query($q,$link) or Die("Unable to insert data to DB!".$qstr);
        $new_id = mysql_insert_id() or Die("Unable to get last insert id!");
        $result['query'] = $q;
        $result['message'] = 'Adding complete: '.$q;
        $result['error'] = 0;
    } else {
        $result['message'] = 'Trying to duplicate user! : '.$q;
        $result['error'] = 1;
    }
} else {
    // ошибка запроса
    $result['message'] = 'Trying to duplicate user! : '.$q;
    $result['error'] = 1;
}

print(json_encode($result));
CloseDB($link);
?>