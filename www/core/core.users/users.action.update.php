<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.kwlogger.php');


if (!isAjaxCall()) Die('Некорректный вызов скрипта!');

if (!IsSet($_POST['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
}

$id = intval($_POST['id']);
$table = 'users';

$link = ConnectDB();

$post = array(
    'name'          => mysql_real_escape_string($_POST['name']),
    'email'         => mysql_real_escape_string($_POST['email']),
    'permissions'   => mysql_real_escape_string($_POST['permissions']),
    'login'         => trim(mysql_real_escape_string($_POST['login'])),
    'password'      => trim(mysql_real_escape_string($_POST['password'])),
    'phone'         => mysql_real_escape_string($_POST['phone']),
    'md5password'   => md5(trim(mysql_real_escape_string($_POST['password']))),
    'stat_date_update' => ConvertTimestampToDate()
);
// нельзя создать админа
$post['permissions'] =
    ($post['permissions'] > 254)
    ? 254
    : $post['permissions'];

$q = "SELECT `id` FROM {$table} WHERE `login` LIKE '$post[login]'";
$r = mysql_query($q, $link);

if (mysql_errno($link)==0)
{
    if (mysql_num_rows($r)==0) {
        // новое имя не совпадает с уже существующими
        $qstr = MakeUpdate($post, $table, "WHERE id=$id");
        $res = mysql_query($qstr, $link) or Die("Unable update data : ".$qstr);

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
CloseDB($link);

?>