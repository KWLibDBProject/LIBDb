<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

if (!isAjaxCall()) Die('Некорректный вызов скрипта!');

if (!IsSet($_POST['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
}

$id = intval($_POST['id']);
$table = 'users';

$post = array(
    'name'          => mysqli_real_escape_string($mysqli_link, trim($_POST['name']) ),
    'email'         => mysqli_real_escape_string($mysqli_link, trim($_POST['email']) ),
    'permissions'   => mysqli_real_escape_string($mysqli_link, $_POST['permissions'] ),
    'login'         => mysqli_real_escape_string($mysqli_link, trim($_POST['login']) ),
    'phone'         => mysqli_real_escape_string($mysqli_link, trim($_POST['phone']) ),
    'md5password'   => md5(mysqli_real_escape_string($mysqli_link, trim($_POST['password']))),
);

// обновить права доступа до админа тоже нельзя
$post['permissions'] = min($post['permissions'], 254);

$q = "SELECT `id` FROM {$table} WHERE `login` LIKE '$post[login]'";
$r = mysqli_query($mysqli_link, $q);

if (mysqli_errno($mysqli_link)==0)
{
    if (mysqli_num_rows($r)==0) {
        // новое имя не совпадает с уже существующими
        $qstr = MakeUpdate($post, $table, "WHERE id=$id");
        $res = mysqli_query($mysqli_link, $qstr) or Die("Unable update data : ".$qstr);

        $result['message'] = "User update successful!";
        $result['error'] = 0;

        kwLogger::logEvent('Update', $table, $id, "User updated, id = {$id}");

    } else {
        $result['message'] = 'Trying to duplicate user! : '.$q;
        kwLogger::logEvent('Error', $table, $post['login'], "Trying duplicate user");
        $result['error'] = 1;
    }
} else {
    // ошибка запроса
    $result['message'] = 'Trying to duplicate user! : '.$q;
    $result['error'] = 1;
}

print(json_encode($result));