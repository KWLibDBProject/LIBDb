<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

// отдает JSON объект для селектора "авторы"
// данный файл 'duplicated' и почти эквивалентен файлу
// authors.action.getoptionlist.php , только чуть-чуть другой формат
// возвращаемых данных для функции buildSelector()
//@Todo: переделать в версии 1.9R

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ru';
$lang = getAllowedValue( $lang, array(
    'ru', 'en', 'ua', 'uk'
));

$withoutid = isset($_GET['withoutid']) ? 1 : 0;


$link = ConnectDB();

$query = "SELECT * FROM authors";
if ($result = mysql_query($query)) {
    $ref_numrows = @mysql_num_rows($result) ;

    if ($ref_numrows>0)
    {
        $data['error'] = 0;
        while ($row = mysql_fetch_assoc($result))
        {
            $data['data'][$row['id']] = returnAuthorsOptionString($row, $lang, $withoutid); // see CORE.PHP
        }
    } else {
        $data['data']['1'] = 'Добавьте авторов в базу!!!';
        $data['error'] = 1;
    }
} else {
    $data['data']['2'] = "Ошибка работы с базой! [$query]";
    $data['error'] = 2;
}
print(json_encode($data));
?>