<?php
// отдает JSON объект для селектора "топики"
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$lang = isset($_GET['lang']) ? substr($_GET['lang'],0,2) : 'ru';
$withoutid = isset($_GET['id']) ? 1 : 0;

$link = ConnectDB();

$query = "
SELECT id, title_ru, display_order AS title
FROM topicgroups
ORDER BY title_{$lang}
";

$result = mysql_query($query) or die($query);

$ref_numrows = @mysql_num_rows($result) ;

if ($ref_numrows>0)
{
    $data['state'] = 'ok';
    $data['error'] = 0;
    $data['count'] = $ref_numrows;
    $i = 1;
    $group = '';
    while ($row = mysql_fetch_assoc($result))
    {
        $data['data'][ $row['id'] ] = array(
            'type'      => 'option',
            'value'     => $row['id'],
            'text'      => $row['title']
        );
    }
} else {
    $data['state'] = "Справочник rooms пуст!";
    $data['error'] = 1;
    $data['count'] = 0;
}

CloseDB($link);

print(json_encode($data));
//print('<pre>'.print_r($data, true).'</pre>');
?>