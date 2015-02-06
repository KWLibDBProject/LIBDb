<?php
// отдает JSON объект для селектора "топики"
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');


$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ru';

$lang = getAllowedValue($lang, array(
    'ru', 'en', 'uk', 'ua'
));

$withoutid = isset($_GET['withoutid']) ? 1 : 0;

$link = ConnectDB();

$query = "SELECT * FROM topics";
$result = mysql_query($query) or die($query);
$ref_numrows = @mysql_num_rows($result) ;

if ($ref_numrows>0)
{
    $data['error'] = 0;
    while ($row = mysql_fetch_assoc($result))
    {
        $data['data'][ $row['id'] ] = returnTopicsOptionString($row,$lang,$withoutid); // see CORE.PHP
    }
} else {
    $data['data'][1] = "Добавьте темы (топики) в базу!!!";
    $data['error'] = 1;
}

CloseDB($link);
print(json_encode($data));
?>