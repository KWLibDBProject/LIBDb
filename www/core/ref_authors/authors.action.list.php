<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$link = ConnectDB();

$ref_name = 'authors';

// $sort_order = "ORDER BY name_ru";

if ( (!isset($_GET['letter'])) || ($_GET['letter'] != '0') ) {
    $like = " AND authors.name_ru LIKE '{$_GET['letter']}%'";
} else {
    $like = '';
}

$query = "SELECT * FROM {$ref_name} WHERE deleted=0 {$like} {$sort_order}";
$res = mysql_query($query) or die($query);
$ref_numrows = @mysql_num_rows($res) ;

if ($ref_numrows > 0) {
    while ($ref_record = mysql_fetch_assoc($res)) {
        $ref_list[$ref_record['id']] = $ref_record;
    }
}

CloseDB($link);
$return = <<<AAL_Start
<table border="1" width="100%">
AAL_Start;

$return .= <<<AAA_TH
    <tr>
        <th width="3%"> ID </th>
        <th width="30%" colspan="2"> Ф.И.О. </th>
        <th width="25%"> Звание, ученая степень, должность </th>
        <th width="15%" colspan="2">Контактные данные </th>
        <th width="22%"> Место работы </th>
        <th width="5%">&nbsp;</th>
    </tr>
AAA_TH;
if ($ref_numrows > 0)
{
    foreach ($ref_list as $row) {
        foreach ($row as $fid => $field) {
            if (empty($field)) $row[$fid] = '&nbsp;';
        }

        $return .= <<<AAA_EACH
    <tr>
        <td rowspan="3"> {$row['id']} </td>
        <td width="4%"><strong>Eng:</strong></td>
        <td> {$row['name_en']} </td>
        <td> {$row['title_en']} </td>
        <td width="5%">E-Mail: </td>
        <td> {$row['email']} </td>
        <td rowspan="3"> {$row['workplace_ru']} </td>
        <td rowspan="3"class="centred_cell"><button class="actor-edit button-edit" name="{$row['id']}">Edit</button></td>
    </tr>
    <tr>
        <td><strong>Рус:</strong></td>
        <td> {$row['name_ru']} </td>
        <td> {$row['title_ru']} </td>
        <td> Phone: </td>
        <td> {$row['phone']} </td>
    </tr>
    <tr>
        <td><strong>Укр:</strong></td>
        <td> {$row['name_uk']} </td>
        <td> {$row['title_uk']} </td>
        <td> Photo: </td>
        <td>
            <a href="getimage.php?id={$row['photo_id']}" target="_blank" class="lightbox"> &lt;Show&gt; </a>
        </td>
    </tr>
AAA_EACH;
    }
} else {
    $return .= <<<AAA_NO_AUTHORS
<tr><td colspan="11">Пока не добавили ни одного автора!</td></tr>
AAA_NO_AUTHORS;

}

$return .= <<<AAA_END
</table>
AAA_END;

print $return;
?>