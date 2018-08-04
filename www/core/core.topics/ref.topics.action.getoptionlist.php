<?php
// отдает JSON объект для селектора "топики"
require_once '../__required.php'; // $mysqli_link


$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ru';

$lang = getAllowedValue($lang, array(
    'ru', 'en', 'ua'
));

$withoutid = isset($_GET['withoutid']) ? 1 : 0;

$query = "SELECT * FROM topics";
$result = mysqli_query($mysqli_link, $query) or die($query);
$ref_numrows = @mysqli_num_rows($result) ;

if ($ref_numrows>0)
{
    $data['error'] = 0;
    while ($row = mysqli_fetch_assoc($result))
    {
        $data['data'][ $row['id'] ] = returnTopicsOptionString($row,$lang,$withoutid); // see CORE.PHP
    }
} else {
    $data['data'][1] = "Добавьте темы (топики) в базу!!!";
    $data['error'] = 1;
}

print(json_encode($data));