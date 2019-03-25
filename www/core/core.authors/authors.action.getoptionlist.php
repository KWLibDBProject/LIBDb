<?php
define('__ACCESS_MODE__', 'frontend');
require_once '../__required.php'; // $mysqli_link

// отдает JSON объект для селектора "авторы"

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ru';
$lang = getAllowedValue($lang, array(
    'en', 'ru', 'ua'
), 'en');

$flag_without_id = isset($_GET['without_id']) ? 1 : 0;

$flag_legacy_format = isset($_GET['legacyformat']) ? 1 : 0;

$flag_with_orcid = isset($_GET['with_orcid']) ? 1 : 0;

$flag_with_title = isset($_GET['with_title']) ? 1 : 0;

$query = "
SELECT
    id, 
    name_{$lang} as name,
    title_{$lang} as title,
    orcid 
FROM 
    authors
COLLATE utf8_unicode_ci
";

if ($result = mysqli_query($mysqli_link, $query)) {
    $ref_numrows = @mysqli_num_rows($result) ;

    if ($ref_numrows>0)
    {
        $data['error'] = 0;
        $data['state'] = 'ok';
        $data['count'] = $ref_numrows;

        while ($row = mysqli_fetch_assoc($result))
        {
            $prefix = (!$flag_without_id ? "[{$row['id']}]" : '');

            $option_value = "{$prefix} {$row['name']}";

            if ($flag_with_title && ($row['title'] != '')) {
                $option_value .= ", {$row['title']}";
            }

            if ($flag_with_orcid && ($row['orcid'] != '')) {
                $option_value .= " ({$row['orcid']})";
            }

            if ($flag_legacy_format) {
                $row_result = $option_value;
            } else {
                $row_result = [
                    'type'      => 'option',
                    'value'     => $row['id'],
                    'text'      => $option_value
                ];
            }

            $data['data'][ $row['id'] ] = $row_result;
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

