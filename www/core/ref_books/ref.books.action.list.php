<?php
// выводит в виде таблицы содержимое справочника 'books'

$link = ConnectDB();
$ref_name = 'books';

$ref_prompt = IsSet($_GET["prompt"]) ? ($_GET["prompt"]) : 'Работа со сборником';

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
    $ref_message = 'Пока не ввели ни один сборник!';
}

CloseDB($link);
?>
<table border="1" width="100%">
<tr>
    <th width="5%">№</th>
    <th>Название или номер сборника</th>
    <th>Дата(год) выпуска</th>
    <th>Страницы со статьями</th>
    <th>Сборник готов? </th>
    <th width="7%">Управление</th>
</tr>
    <?php
    if ($ref_numrows > 0) {
    foreach ($ref_list as $r_id => $r_value)
    {
        //@todo: TEMPLATE
        $row = $r_value;
        $book_ready = ($row['published']!=0) ? "Да<br><small>(опубликован)</small>" : "Нет<br><small>(в работе)</small>";
        echo <<<REF_ANYROW
<tr>
<td class="centred_cell">{$row['id']}</td>
<td>{$row['title']}</td>
<td class="centred_cell">{$row['date']}</td>
<td>{$row['contentpages']}</td>
<td class="centred_cell">{$book_ready}</td>
<td class="centred_cell"><button class="edit_button" name="{$row['id']}">Edit</button></td>
</tr>
REF_ANYROW;
        }
        echo "</table>";
    } else {
        echo <<<REF_NUMROWS_ZERO
<tr><td colspan="5">$ref_message</td></tr>
REF_NUMROWS_ZERO;
    }

    ?>

</table>
