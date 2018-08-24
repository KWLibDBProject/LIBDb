<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

const ROOT_PERMISSONS = 255;

$ref_prompt = IsSet($_GET["prompt"]) ? ($_GET["prompt"]) : 'Работа с пользователем';

$query = "SELECT * FROM `users` ";

$res = mysqli_query($mysqli_link, $query); // or die("Невозможно получить содержимое справочника! ".$ref_name);
$ref_numrows = @mysqli_num_rows($res) ;

$users_list = [];

if ($ref_numrows > 0) {
    while ($user_record = mysqli_fetch_assoc($res))
    {
        $user_record['is_root'] = $user_record['permissions'] == ROOT_PERMISSONS ? true : false;
        $users_list[ $user_record['id'] ] = $user_record;
    }
} else {
    $ref_message = 'Пока не ввели ни одного пользователя!';
}


$template_dir = '$/core/core.users';
$template_file = "_template.users.list.html";

$template_data = array(
    'users_list' =>  $users_list
);

echo websun_parse_template_path($template_data, $template_file, $template_dir);

