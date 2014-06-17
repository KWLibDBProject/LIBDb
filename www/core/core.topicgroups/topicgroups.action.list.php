<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$link = ConnectDB();
$ref_name = 'topicgroups';

$query = "SELECT * FROM $ref_name";
$res = mysql_query($query) or die('$msg->say("errors/mysql_query_error",$query)');

$ref_numrows = @mysql_num_rows($res) ;

$tgroups = array();

if ($ref_numrows > 0) {
    while($ref_record = mysql_fetch_assoc($res))
    {
        $tgroups[ $ref_record['id']  ] = $ref_record;
    }
}

CloseDB($link);
$return = <<<REF_TGAL_START
<table border="1" width="100%">
REF_TGAL_START;

$return .= <<<REF_TGAL_TH
    <tr>
        <th width="5%">№</th>
        <th>Английское название</th>
        <th>Русское название</th>
        <th>Украинское название</th>
        <th width="7%">Управление</th>
    </tr>
REF_TGAL_TH;

if ($ref_numrows > 0)
{
    foreach ($tgroups as $r_id => $row)
    {
        $return .= <<<REF_TAL_EACH
<tr>
<td class="centred_cell">{$row['id']}</td>
<td>{$row['title_en']}</td>
<td>{$row['title_ru']}</td>
<td>{$row['title_uk']}</td>
<td class="centred_cell"><button class="action-edit button-edit" name="{$row['id']}">Edit</button></td>
</tr>
REF_TAL_EACH;
    }
} else {
    $return .= '<tr><td colspan="5">Пока не добавили ни одну группу тематических разделов.</td></tr></table>';
}
$return .= <<<REF_TAL_END
</table>
REF_TAL_END;

print($return);
?>