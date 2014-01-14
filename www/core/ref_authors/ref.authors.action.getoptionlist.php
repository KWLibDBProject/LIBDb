<?php
// отдает JSON объект для селектора "авторы"


$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ru';
$withoutid = isset($_GET['withoutid']) ? 1 : 0;


$link = ConnectDB();

$query = "SELECT * FROM authors WHERE deleted=0";
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
        $data['data']['-1'] = 'Добавьте авторов в базу!!!';
        $data['error'] = -1;
    }
} else {
    $data['data']['-1'] = 'Ошибка работы с базой!';
    $data['error'] = -1;
}
print(json_encode($data));
?>