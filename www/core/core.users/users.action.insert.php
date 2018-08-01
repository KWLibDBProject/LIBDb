<?php
require_once '../__required.php'; // $mysqli_link

if (!isAjaxCall()) Die('Некорректный вызов скрипта!');

if (!IsSet($_POST['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
}
$table = 'users';

$now = ConvertTimestampToDate();
$post = array(
    'name'          => mysqli_real_escape_string($mysqli_link, $_POST['name']),
    'email'         => mysqli_real_escape_string($mysqli_link, $_POST['email']),
    'permissions'   => mysqli_real_escape_string($mysqli_link, $_POST['permissions']),
    'login'         => mysqli_real_escape_string($mysqli_link, $_POST['login']),
    'password'      => mysqli_real_escape_string($mysqli_link, $_POST['password']), //@todo: REMOVE IT
    'phone'         => mysqli_real_escape_string($mysqli_link, $_POST['phone']),
    'md5password'   => md5(mysqli_real_escape_string($mysqli_link, $_POST['password'])),
    'stat_date_insert'  =>  $now,
    'stat_date_update'  =>  $now
);
// повысить до админа нельзя, по крайней мере отсюда
$post['permissions'] =
    ($post['permissions'] > 254)
        ? 254
        : $post['permissions'];

$q = "SELECT `id` FROM $table WHERE `login` LIKE '$post[login]'";
$r = mysqli_query($mysqli_link, $q);

if (mysqli_errno($link)==0)
{
    // что-то нашли т.е. mysqli_num_rows()>1
    if (mysqli_num_rows($r)==0) {
        // пользователь уникален
        $q = MakeInsert($post,$table);
        $r = mysqli_query($mysqli_link, $q) or Die("Unable to insert data to DB!".$qstr);
        $new_id = mysqli_insert_id($mysqli_link) or Die("Unable to get last insert id!");
        $result['query'] = $q;
        $result['message'] = 'Adding complete: '.$q;
        $result['error'] = 0;

        kwLogger::logEvent('Add', 'users', $new_id, "User added, id = {$new_id}");

    } else {
        $result['message'] = 'Trying to duplicate user! : '.$q;
        kwLogger::logEvent('Error', 'users', $post['login'], "Trying duplicate user");
        $result['error'] = 1;
    }
} else {
    // ошибка запроса
    $result['message'] = 'Trying to duplicate user! : '.$q;
    $result['error'] = 1;
}

print(json_encode($result));