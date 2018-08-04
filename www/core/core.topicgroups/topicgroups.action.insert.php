<?php
require_once '../__required.php'; // $mysqli_link

$SID = session_id();
if(empty($SID)) session_start();
if (!isLogged()) header('Location: /core/');

if (!IsSet($_POST['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
}

$q = array(
    'title_en'      => mysqli_real_escape_string($mysqli_link, $_POST['title_en']),
    'title_ru'      => mysqli_real_escape_string($mysqli_link, $_POST['title_ru']),
    'title_ua'      => mysqli_real_escape_string($mysqli_link, $_POST['title_ua']),
    'display_order' => mysqli_real_escape_string($mysqli_link, $_POST['display_order']),
);
$table = 'topicgroups';

$qstr = MakeInsert($q, $table);
$res = mysqli_query($mysqli_link, $qstr) or die("Unable to insert data to DB!".$qstr);
$new_id = mysqli_insert_id($mysqli_link) or die("Unable to get last insert id!");

kwLogger::logEvent('Add', $table, $new_id, "Group of topics added, id = {$new_id}");

$result['message'] = $qstr;
$result['error'] = 0;

print(json_encode($result));