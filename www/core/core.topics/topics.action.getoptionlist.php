<?php
// отдает JSON объект для селектора "топики"
require_once '../__required.php'; // $mysqli_link

$lang = isset($_GET['lang']) ? substr($_GET['lang'],0,2) : 'ru';

$lang = getAllowedValue($lang, array(
    'ru', 'en', 'uk', 'ua'
));

$withoutid = isset($_GET['id']) ? 1 : 0;

$query = "
SELECT
topics.id,
topics.title_{$lang} AS title_topic,
topicgroups.title_{$lang}  AS title_group
FROM topics
LEFT JOIN topicgroups ON topicgroups.id = topics.rel_group
ORDER BY topicgroups.title_{$lang}, topics.title_{$lang}
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
        if ($group != $row['title_group']) {
            // send new optiongroup
            $group_id = 'g_'.$row['id'];
            $data['data'][ $i ] = array(
                'type'      => 'group',
                'value'     => $group_id,
                'text'      => $row['title_group']
            );
            $i++;
            $group = $row['title_group'];
        }
        // send option
        $data['data'][ $i ] = array(
            'type'      => 'option',
            'value'     => $row['id'],
            'text'      => $row['title_topic']
        );
        $i++;
    }
 } else {
    $data['state'] = "Справочник пуст!";
    $data['error'] = 1;
    $data['count'] = 0;
}

print(json_encode($data));