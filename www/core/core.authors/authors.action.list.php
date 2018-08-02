<?php
require_once '../__required.php'; // $mysqli_link

$ref_name = 'authors';

$sort_order = isset($_GET['order_by_name']) ? " ORDER BY name_ru " : '';

if ( (!isset($_GET['letter'])) || ($_GET['letter'] != '0') ) {
    $letter = substr($_GET['letter'], 0, 6);
    $like = " authors.name_ru LIKE '{$letter}%'";
    $sort_order = " ORDER BY name_ru ";
} else {
    $like = '';
}

$where = ($like != '') ? " WHERE {$like}" : "";

$authors_list = [];

$query = "SELECT * FROM authors {$where} {$sort_order}";
$res = mysqli_query($mysqli_link, $query) or die($query);
$authors_count = @mysqli_num_rows($res) ;

if ($authors_count > 0) {
    while ($author_record = mysqli_fetch_assoc($res)) {
        $author_record['is_photo_present'] = ($author_record['photo_id'] == -1) ? false : true;

        $authors_list[$author_record['id']] = $author_record;
    }
}

$template_dir = '$/core/core.authors';
$template_file = "_template.authors.list.html";

$template_data = array(
    'authors_list' =>  $authors_list
);

echo \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);

