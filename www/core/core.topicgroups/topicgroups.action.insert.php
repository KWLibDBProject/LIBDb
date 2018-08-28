<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

if (!IsSet($_POST['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
}

$dataset = array(
    'title_en'      => mysqli_real_escape_string($mysqli_link, $_POST['title_en']),
    'title_ru'      => mysqli_real_escape_string($mysqli_link, $_POST['title_ru']),
    'title_ua'      => mysqli_real_escape_string($mysqli_link, $_POST['title_ua']),
    'display_order' => mysqli_real_escape_string($mysqli_link, $_POST['display_order']),
);
$table = 'topicgroups';

$query = MakeInsert($dataset, $table);
$res = mysqli_query($mysqli_link, $query) or die("Unable to insert data to DB! ".$query);
$new_id = mysqli_insert_id($mysqli_link) or die("Unable to get last insert id!");

kwLogger::logEvent('Add', $table, $new_id, "Group of topics added, id = {$new_id}");

$result['message'] = $query;
$result['error'] = 0;

print(json_encode($result));