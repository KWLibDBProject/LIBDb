<?php
// @todo: переписать вывод под шаблон

$link = ConnectDB();

$ref_name = IsSet($_GET['ref']) ? $_GET['ref'] : 'authors';
$ref_prompt = IsSet($_GET["prompt"]) ? ($_GET["prompt"]) : 'Работа с автором';

$query = "SELECT * FROM $ref_name WHERE deleted=0";
$res = mysql_query($query) or die($query);
$ref_numrows = @mysql_num_rows($res) ;

if ($ref_numrows > 0) {
    while ($ref_record = mysql_fetch_assoc($res)) {
        $ref_list[$ref_record['id']] = $ref_record;
    }
} else {
    $ref_message = 'Пока не ввели ни одного автора!';
}

CloseDB($link);
?>

<table border="1" width="100%">
    <tr>
        <th width="3%"> ID </th>
        <th width="30%" colspan="2"> Ф.И.О. </th>
        <th width="25%"> Звание, ученая степень, должность </th>
        <th width="15%" colspan="2">Контактные данные </th>
        <th width="22%"> Место работы </th>
        <th width="5%">&nbsp;</th>
    </tr>
    <!-- single table row -->
<?php
    if ($ref_numrows>0) {
        foreach ($ref_list as $row) {
            //@todo: TEMPLATE ?
echo <<<REF_ONEROW
    <tr>
        <td rowspan="3"> {$row['id']} </td>
        <td width="4%"><strong>Eng:</strong></td>
        <td> {$row['name_en']} </td>
        <td> {$row['title_en']} </td>
        <td width="5%">E-Mail: </td>
        <td> {$row['email']} </td>
        <td rowspan="3"> {$row['workplace']} </td>
        <td rowspan="3"class="centred_cell"><button class="edit_button" name="{$row['id']}">Edit</button></td>
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
        <td> &nbsp; </td>
        <td> &nbsp; </td>
    </tr>
REF_ONEROW;
        } //foreach
        echo '</table>';
    } else {
echo <<<REF_NUMROWS_ZERO
<tr><td colspan="11">$ref_message</td></tr>
REF_NUMROWS_ZERO;
    } // else
?>