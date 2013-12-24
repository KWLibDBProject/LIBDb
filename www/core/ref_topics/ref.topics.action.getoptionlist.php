<?php
// отдает JSON объект для селектора
require_once('../core.php');
require_once('../core.db.php');

$link = ConnectDB();

$query = "SELECT * FROM topics WHERE deleted=0";
$result = mysql_query($query) or die($query);
$ref_numrows = @mysql_num_rows($result) ;

if ($ref_numrows>0)
{
    $data['error'] = 0;
    while ($row = mysql_fetch_assoc($result))
    {
        $title = ($row['title'] != '') ? $row['title'] : '<NONAME>';
        $data['data'][ $row['id'] ] = "[$row[id]] $title ($row[date])";
    }
} else {
    $data['data'][1] = "Добавьте темы (топики) в базу!!!";
    $data['error'] = 1;
}

CloseDB($link);
print(json_encode($data));
?>