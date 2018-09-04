<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

$query = "SELECT * FROM `staticpages`";
$res = mysqli_query($mysqli_link, $query) or die($query);
$ref_numrows = @mysqli_num_rows($res);

$pages_list = [];

if (mysqli_num_rows($res) > 0) {
    while ($page_record = mysqli_fetch_assoc($res)) {
        $pages_list[$page_record['id']] = $page_record;
    }
} else {
    $ref_message = 'Страниц не найдено!';
}

$template_dir = '$/core/core.pages';
$template_file = "_template.pages.list.html";

$template_data = array(
    'pages_list' =>  $pages_list
);

echo websun_parse_template_path($template_data, $template_file, $template_dir);