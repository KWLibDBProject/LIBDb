<?php
require_once('core.php');
require_once('core.db.php');

// отдает JSON объект для построения selector/options list на основе абстрактного справочника
$ref = $_GET['ref'];

if (!empty($ref))
{
    $ref = $_GET['ref'];
    $link = ConnectDB();

    $query = "SELECT * FROM $ref";
    $result = mysql_query($query) or die($query);
    $ref_numrows = @mysql_num_rows($result) ;

    if ($ref_numrows>0)
    {
        $data['error'] = 0;
        while ($row = mysql_fetch_assoc($result))
        {
            $data['data'][ $row['id'] ] = "[{$row['id']}] {$row['data_str']}";
        }
        $data['count'] = $ref_numrows;
    } else {
        $data['data'][1] = "Справочник $ref пуст!";
        $data['error'] = 1;
        $data['count'] = 0;
    }

    CloseDB($link);

} else {
    $data['data'][1] = "Справочник $ref не существует!";
    $data['error'] = 2;
    $data['count'] = 0;
}

print(json_encode($data));
?>