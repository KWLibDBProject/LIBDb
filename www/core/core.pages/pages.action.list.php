<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$link = ConnectDB();

$ref_name = IsSet($_GET['ref']) ? $_GET['ref'] : 'staticpages';

$query = "SELECT * FROM {$ref_name}";
$res = mysql_query($query) or die($query);
$ref_numrows = @mysql_num_rows($res) ;

if ($ref_numrows > 0) {
    while ($ref_record = mysql_fetch_assoc($res)) {
        $ref_list[$ref_record['id']] = $ref_record;
    }
} else {
    $ref_message = 'Страниц не найдено!';
}

CloseDB($link);
?>

<table border="1" width="100%">
    <tr>
        <th width="5%"> ID </th>
        <th width="15%">Alias <br><small>click to view in another window</small></th>
        <th>Comment</th>
        <th width="10%">&nbsp;</th>
    </tr>
    <!-- single table row -->
<?php
    if ($ref_numrows>0) {
        foreach ($ref_list as $row) {
            echo <<<REF_ONEROW
    <tr>
        <td>{$row['id']}</td>
        <td><a href="/?fetch=page&with={$row['alias']}" target="_blank">{$row['alias']}</a></td>
        <td>{$row['comment']}</td>
        <td class="centred_cell"><button class="action-edit button-edit" name="{$row['id']}">Edit</button></td>
    </tr>
REF_ONEROW;
        } //foreach
        echo '</table>';
    } else {
        echo <<<REF_NUMROWS_ZERO
<tr><td colspan="4">$ref_message</td></tr>
REF_NUMROWS_ZERO;
    } // else
?>