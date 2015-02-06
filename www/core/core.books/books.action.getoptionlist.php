<?php
require_once('../core.php');
require_once('../core.db.php');

// отдает JSON объект для селектора 'books'

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ru';

$lang = getAllowedValue( $lang, array(
    'ru', 'en', 'ua', 'uk'
));

$withoutid = isset($_GET['withoutid']) ? 1 : 0;


$link = ConnectDB();

$query = "SELECT * FROM books ";
$result = mysql_query($query) or die($query);
$ref_numrows = @mysql_num_rows($result) ;

if ($ref_numrows>0)
{
    $data['error']  = 0;
    $data['state']  = 'ok';
    while ($row = mysql_fetch_assoc($result))
    {
        $data['data'][ $row['id'] ] = array(
            'type'      => 'option',
            'value'     => $row['id'],
            'text'      => returnBooksOptionString($row,$lang,$withoutid)
        );
    }
} else {
    $data['data'][1] = "Добавьте книги (сборники) в базу!!!";
    $data['error'] = 1;
}

CloseDB($link);
print(json_encode($data));
?>