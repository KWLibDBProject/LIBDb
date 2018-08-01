<?php
require_once '../__required.php'; // $mysqli_link
$ref_name = 'topics';

$query = "SELECT * FROM topics";
$res = mysqli_query($mysqli_link, $query) or die('$msg->say("errors/mysqli_query_error",$query)');

$ref_numrows = @mysqli_num_rows($res) ;

if ($ref_numrows > 0) {
    while($ref_record = mysqli_fetch_assoc($res))
    {
        $ref_list[ $ref_record['id']  ] = $ref_record;
    }
}

$return = <<<REF_TAL_START
<table border="1" width="100%">
REF_TAL_START;

$return .= <<<REF_TAL_TH
    <tr>
        <th width="5%">№</th>
        <th>Thematic section</th>
        <th>Тематический раздел</th>
        <th>Тематичний розділ</th>
        <th>Group</th>
        <th width="7%">Управление</th>
    </tr>
REF_TAL_TH;

if ($ref_numrows > 0)
{
    foreach ($ref_list as $r_id => $row)
    {
        $return .= <<<REF_TAL_EACH
<tr>
<td class="centred_cell">{$row['id']}</td>
<td>{$row['title_en']}</td>
<td>{$row['title_ru']}</td>
<td>{$row['title_uk']}</td>
<td class="centred_cell">{$row['rel_group']}</td>
<td class="centred_cell"><button class="actor-edit button-edit" name="{$row['id']}">Edit</button></td>
</tr>
REF_TAL_EACH;
    }
} else {
    $return .= '<tr><td colspan="6">Пока не добавили ни один тематический раздел.</td></tr></table>';
}
$return .= <<<REF_TAL_END
</table>
REF_TAL_END;

print($return);