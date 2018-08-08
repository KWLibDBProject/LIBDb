<?php
// отдает JSON объект для селектора "топики"
require_once '../__required.php'; // $mysqli_link

$flag_lang = isset($_GET['lang']) ? substr($_GET['lang'],0,2) : 'ru';

$flag_lang = getAllowedValue($flag_lang, array(
    'ru', 'en', 'ua'
));

/**
 Новое поведение: теперь можно задать в GET флаги:
 - id - наличие добавляет к строчкам селекта [id]
 - nogroup - наличие отключает группировку по супергруппам

 Задавать значения ключей не обязательно!
 */

$flag_with_id = isset($_GET['id']) ? 1 : 0;
$flag_nogroup = isset($_GET['nogroup']) ? 1 : 0;

$query_with_groups = "
SELECT
topics.id,
topics.title_{$flag_lang} AS title_topic,
topicgroups.title_{$flag_lang}  AS title_group
FROM topics
LEFT JOIN topicgroups ON topicgroups.id = topics.rel_group
ORDER BY topicgroups.title_{$flag_lang}, topics.title_{$flag_lang}
";

$query_no_groups = "
SELECT 
topics.id,
topics.title_{$flag_lang} AS title_topic,
'' AS title_group
FROM topics
";

$query = $flag_nogroup ? $query_no_groups : $query_with_groups;

$result = mysqli_query($mysqli_link, $query) or die($query);

$ref_numrows = @mysqli_num_rows($result);

if ($ref_numrows>0)
{
    $data['state'] = 'ok';
    $data['error'] = 0;
    $data['count'] = $ref_numrows;
    $i = 1;
    $group = ''; // no optiongroup

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
            'text'      =>  ($flag_with_id ? "[{$row['id']}] " : '') . $row['title_topic']
        );
        $i++;
    }
 } else {
    $data['state'] = "Справочник пуст!";
    $data['error'] = 1;
    $data['count'] = 0;
}

print(json_encode($data));