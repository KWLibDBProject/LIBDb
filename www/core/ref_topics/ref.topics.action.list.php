<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
// require_once('../core.messages.php');

// $msg = TheMessenger::getIt(); // Messenger singleton class

$link = ConnectDB();
$ref_name = 'topics';

$query = "SELECT * FROM $ref_name WHERE deleted=0";
$res = mysql_query($query) or die('$msg->say("errors/mysql_query_error",$query)');

$ref_numrows = @mysql_num_rows($res) ;

if ($ref_numrows > 0) {
    while($ref_record = mysql_fetch_assoc($res))
    {
        $ref_list[ $ref_record['id']  ] = $ref_record;
    }
}

CloseDB($link);
$return = <<<REF_TAL_START
<table border="1" width="100%">
REF_TAL_START;

$return .= <<<REF_TAL_TH
    <tr>
        <th width="5%">№</th>
        <th>Thematic section</th>
        <th>Тематический раздел</th>
        <th>Тематичний розділ</th>
        <th width="7%">Управление</th>
    </tr>
REF_TAL_TH;

if ($ref_numrows > 0)
{
    foreach ($ref_list as $r_id => $row)
    {
        $return .= <<<REF_TAL_EACH
<tr>
<td>{$row['id']}</td>
<td>{$row['title_en']}</td>
<td>{$row['title_ru']}</td>
<td>{$row['title_uk']}</td>
<td class="centred_cell"><button class="edit_button" name="{$row['id']}">Edit</button></td>
</tr>
REF_TAL_EACH;
    }
} else {
    $return .= '<tr><td colspan="5">Пока не добавили ни один тематический раздел.</td></tr></table>';
}
$return .= <<<REF_TAL_END
</table>
REF_TAL_END;

print($return);
?>