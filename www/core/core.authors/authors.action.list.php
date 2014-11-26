<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$link = ConnectDB();

$ref_name = 'authors';

$sort_order = isset($_GET['order_by_name']) ? " ORDER BY name_ru " : '';

if ( (!isset($_GET['letter'])) || ($_GET['letter'] != '0') ) {
    $like = " authors.name_ru LIKE '{$_GET['letter']}%'";
    $sort_order = " ORDER BY name_ru ";
} else {
    $like = '';
}

if ($like != '') $where = " WHERE {$like}";

$ref_list = array();

$query = "SELECT * FROM {$ref_name} {$where} {$sort_order}";
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

$return .= <<<core_authors_action_list_th
    <tr>
        <th width="5%">#</th>
        <th width="85%">Информация об авторе</th>
        <th width="5%"><img src="/core/css/jpeg48x48.png" width="32" height="32"></th>
        <th width="5%">Edit</th>
    </tr>
core_authors_action_list_th;

if ($ref_numrows > 0)
{
    foreach ($ref_list as $row) {
        foreach ($row as $fid => $field) {
            if (empty($field)) $row[$fid] = '';
        }
        $is_link_disabled = ($row['photo_id'] == -1) ? 'action-aal-no-photo' : '';

        $tpl = new kwt('authors.action.list.onerow.html');
        $tpl->override(array(
            'id'            =>  $row['id'],
            'name_ru'       =>  $row['name_ru'],
            'workplace_ru'  =>  $row['workplace_ru'],
            'email'         =>  $row['email'],
            'phone'         =>  $row['phone'],
            'photo_id'      =>  $row['photo_id'],
            'is_link_disabled' => ($row['photo_id'] == -1) ? 'action-aal-no-photo' : ''
        ));
        $tpl->contentstart();
        $return .= $tpl->getcontent();
    }
} else {
    $return .= <<<core_authors_action_list_noauthors
    <tr>
        <td colspan="4">Пока не добавили ни одного автора!</td>
    </tr>
core_authors_action_list_noauthors;

}

$return .= <<<AAA_END
</table>
AAA_END;
// $tpl->out();
print $return;
?>