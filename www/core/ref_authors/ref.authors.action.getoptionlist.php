<?php
// отдает JSON объект для селектора

$link = ConnectDB();

$query = "SELECT * FROM authors WHERE deleted=0";
if ($result = mysql_query($query)) {
    $ref_numrows = @mysql_num_rows($result) ;

    if ($ref_numrows>0)
    {
        $data['error'] = 0;
        while ($row = mysql_fetch_assoc($result))
        {
            $data['data'][ $row['id'] ] = "[$row[id]] $row[name_rus]  ($row[title_rus])";
            // @todo: ВАЖНО: ТУТ ЗАДАЕТСЯ ФОРМАТ ВЫВОДА ДАННЫХ В СЕЛЕКТ
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