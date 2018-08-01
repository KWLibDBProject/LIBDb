<?php
require_once '../__required.php'; // $mysqli_link

$ref_name = 'news';

$year = isset($_GET['year'])
    ? intval($_GET['year'])
    : '';

$year = ($year != 0)
    ? "AND date_year= {$year} "
    : '';
// ну и зачем я это делал если год нигде не используется?
// кажется он использовался в запросе, но... где, когда?

$query = "SELECT id, title_ru, date_add FROM {$ref_name} WHERE 1=1 ";
$res = mysqli_query($mysqli_link, $query) or die($query);
$ref_numrows = @mysqli_num_rows($res) ;

if ($ref_numrows > 0) {
    while ($ref_record = mysqli_fetch_assoc($res)) {
        $ref_list[$ref_record['id']] = $ref_record;
    }
} else {
    $ref_message = 'Новостей не найдено!';
}

CloseDB($link);

$return = '';
$return .= <<<nal_table_start
<table border="1" width="100%">
nal_table_start;

$return .= <<<nal_table_th
    <tr>
        <th width="5%"> ID </th>
        <th>Заголовок</th>
        <th width="25%">Дата добавления</th>
        <th width="10%">&nbsp;</th>
    </tr>
nal_table_th;

if ($ref_numrows > 0) {
    foreach ($ref_list as $row) {
        $return .= <<<nal_table_onerow
    <tr>
        <td>{$row['id']}</td>
        <td>{$row['title_ru']}</td>
        <td>{$row['date_add']}</td>
        <td class="centred_cell"><button class="actor-edit button-edit" name="{$row['id']}">Edit</button></td>
    </tr>
nal_table_onerow;
    }
} else {
    $return .= <<<nal_table_norows
    <tr>
        <td colspan="4">$ref_message</td>
    </tr>
nal_table_norows;

}

$return .= <<<nal_table_end
</table>
nal_table_end;

print $return;