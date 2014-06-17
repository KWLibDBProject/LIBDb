<?php
// отдает JSON объект для селектора "топики"
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$lang = isset($_GET['lang']) ? substr($_GET['lang'],0,2) : 'ru';
$withoutid = isset($_GET['id']) ? 1 : 0;

$link = ConnectDB();

$query = "
SELECT
topics.id,
topics.title_{$lang} AS title_topic,
topicgroups.title_{$lang}  AS title_group
FROM topics
LEFT JOIN topicgroups ON topicgroups.id = topics.rel_group
ORDER BY topicgroups.title_{$lang}, topics.title_{$lang}
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

CloseDB($link);

print(json_encode($data));
//print('<pre>'.print_r($data, true).'</pre>');
?>