<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

if (!isAjaxCall()) Die('Некорректный вызов скрипта!');

if (!IsSet($_POST['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
}
$table = 'users';

$post = array(
    'name'          => mysqli_real_escape_string($mysqli_link, $_POST['name']),
    'email'         => mysqli_real_escape_string($mysqli_link, $_POST['email']),
    'permissions'   => mysqli_real_escape_string($mysqli_link, $_POST['permissions']),
    'login'         => mysqli_real_escape_string($mysqli_link, $_POST['login']),
    'phone'         => mysqli_real_escape_string($mysqli_link, $_POST['phone']),
    'md5password'   => md5(mysqli_real_escape_string($mysqli_link, $_POST['password'])),
);
// получить права админа (255) нельзя
$post['permissions'] = min($post['permissions'], 254);

$query_find_user = "SELECT `id` FROM users WHERE `login` LIKE '{$post['login']}'";
$r = mysqli_query($mysqli_link, $query_find_user);

if (mysqli_errno($mysqli_link)==0)
{
    if (mysqli_num_rows($r)==0) {
        // пользователь уникален
        $query_insert = MakeInsert($post, $table);
        $r = mysqli_query($mysqli_link, $query_insert) or Die("Unable to insert data to DB!".$query);
        $new_id = mysqli_insert_id($mysqli_link) or Die("Unable to get last insert id!");
        $result['query'] = $query_insert;
        $result['message'] = 'Adding complete: '.$query_insert;
        $result['error'] = 0;

        kwLogger::logEvent('Add', 'users', $new_id, "User added, id = {$new_id}");

    } else {
        $result['message'] = 'Trying to duplicate user! : '.$query_find_user;
        kwLogger::logEvent('Error', 'users', $post['login'], "Trying duplicate user");
        $result['error'] = 1;
    }
} else {
    // ошибка запроса
    $result['message'] = 'Trying to duplicate user! : '.$query_find_user;
    $result['error'] = 1;
}

print(json_encode($result));