<?php
// отдает JSON объект для селектора "топики"
require_once '../__required.php'; // $mysqli_link

$lang = isset($_GET['lang']) ? substr($_GET['lang'],0,2) : 'ru';

$lang = getAllowedValue($lang, array(
    'ru', 'en', 'uk', 'ua'
));

$withoutid = isset($_GET['id']) ? 1 : 0;

$query = "
SELECT id, title_ru, display_order AS title
FROM topicgroups
ORDER BY title_{$lang}
";

$result = mysqli_query($mysqli_link, $query) or die($query);

$ref_numrows = @mysqli_num_rows($result) ;

if ($ref_numrows>0)
{
    $data['state'] = 'ok';
    $data['error'] = 0;
    $data['count'] = $ref_numrows;
    $i = 1;
    $group = '';
    while ($row = mysqli_fetch_assoc($result))
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


print(json_encode($data));