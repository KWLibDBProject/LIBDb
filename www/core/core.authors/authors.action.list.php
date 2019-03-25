<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

$select_letter = mb_substr($_GET['letter'] ?? '0', 0, 1);

$where_like = '';
$sort_order = '';

if ($select_letter != '0') {
    $where_like = " WHERE authors.firstletter_name_ru = '{$select_letter}'";
    $sort_order = " ORDER BY name_ru ";
} else {
    $where_like = ' ';
    $sort_order = isset($_GET['order_by_name']) ? " ORDER BY name_ru COLLATE utf8_unicode_ci " : '';
}

$authors_list = [];

$query = "SELECT * FROM authors {$where_like} {$sort_order}  ";
$res = mysqli_query($mysqli_link, $query) or die($query);
$authors_count = @mysqli_num_rows($res) ;

if ($authors_count > 0) {
    while ($author_record = mysqli_fetch_assoc($res)) {
        $author_record['is_photo_present'] = ($author_record['photo_id'] == -1) ? false : true;
        $authors_list[$author_record['id']] = $author_record;
    }
}

$template_dir = '$/core/core.authors';

$template_file = ($authors_count > 0) ? "_template.authors.list.html" : "_template.authors.not-found.html";

$template_data = array(
    'authors_count' =>  count($authors_list),
    'authors_list'  =>  $authors_list
);

echo websun_parse_template_path($template_data, $template_file, $template_dir);

