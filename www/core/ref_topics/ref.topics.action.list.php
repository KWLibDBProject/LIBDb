<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$link = ConnectDB();
$ref_name = 'topics';

$query = "SELECT * FROM $ref_name WHERE deleted=0";
$res = mysql_query($query) or die("Невозможно получить содержимое справочника! ".$query);

$ref_numrows = @mysql_num_rows($res) ;

if ($ref_numrows > 0) {
    for ($i=0; $i < $ref_numrows; $i++)
    {
        $ref_record = mysql_fetch_assoc($res);
        $ref_list[$ref_record['id']] = $ref_record;

    }
} else {
    $ref_message = 'Пока не ввели ни один топик!';
}

CloseDB($link);
?>
<table border="1" width="100%">
    <tr>
        <th width="5%">№</th>
        <th>Thematic section</th>
        <th>Тематический раздел</th>
        <th>Тематичний розділ</th>
        <th width="7%">Управление</th>
    </tr>
<?php
    if ($ref_numrows > 0) {
        foreach ($ref_list as $r_id => $r_value)
        {
            $row = $r_value;
            echo <<<REF_ANYROW
<tr>
<td>{$row['id']}</td>
<td>{$row['title_en']}</td>
<td>{$row['title_ru']}</td>
<td>{$row['title_uk']}</td>
<td class="centred_cell"><button class="edit_button" name="{$row['id']}">Edit</button></td>
</tr>
REF_ANYROW;
        }
    } else {
        echo <<<REF_NUMROWS_ZERO
<tr><td colspan="4">$ref_message</td></tr>
REF_NUMROWS_ZERO;
    }
?>
</table>