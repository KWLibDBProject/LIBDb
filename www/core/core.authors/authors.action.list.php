<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$link = ConnectDB();

$ref_name = 'authors';

$sort_order = isset($_GET['order_by_name']) ? " ORDER BY name_ru " : '';

//function start (надо, наверное, эту функцию дергать из /frontend.php или из
//библиотечного модуля, относящегося к справочнику (например authors.lib.php), куда
// стоит отправить большинство имеющихся функций, описанных inline в коде.
if ( (!isset($_GET['letter'])) || ($_GET['letter'] != '0') ) {
    $like = " authors.name_ru LIKE '{$_GET['letter']}%'";
} else {
    $like = '';
}

$ref_list = array();

$query = "SELECT * FROM {$ref_name} WHERE {$like} {$sort_order}";
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
        $return .= <<<core_authors_action_list_each
    <tr>
        <td>
            {$row['id']}
        </td>
        <td>
            <div class="aal-author-name">
                {$row['name_ru']}
            </div>
            <div class="aal-author-info">
                {$row['workplace_ru']} , {$row['email']} , {$row['phone']}
            </div>
        </td>
        <td>
            <a href="getimage.php?id={$row['photo_id']}" target="_blank" class="lightbox {$is_link_disabled}"> &lt;Show&gt; </a>
        </td>
        <td class="centred_cell">
            <button class="action-edit button-edit" name="{$row['id']}">Edit</button>
        </td>

    </tr>
core_authors_action_list_each;
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

print $return;
?>