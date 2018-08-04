<?php
require_once '../__required.php'; // $mysqli_link

// отдает JSON объект для селектора "авторы"

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ru';
$lang = getAllowedValue($lang, array(
    'en', 'ru', 'ua'
));

$withoutid = isset($_GET['withoutid']) ? 1 : 0;


$query = "SELECT * FROM authors";
if ($result = mysqli_query($mysqli_link, $query)) {
    $ref_numrows = @mysqli_num_rows($result) ;

    if ($ref_numrows>0)
    {
        $data['error'] = 0;
        $data['state'] = 'ok';
        while ($row = mysqli_fetch_assoc($result))
        {
            $data['data'][ $row['id'] ] = array(
                'type'      => 'option',
                'value'     => $row['id'],
                'text'      => returnAuthorsOptionString($row, $lang, $withoutid)
            );
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

