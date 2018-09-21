<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

$year = $_GET['year'] ?? '';

$query = "
SELECT 
    id, title_ru, DATE_FORMAT(publish_date, '%d.%m.%Y') as publish_date 
FROM 
    news 
WHERE 
    1=1
ORDER BY news.publish_date DESC  
";

$news_list = [];

$res = mysqli_query($mysqli_link, $query) or die($query);

if (mysqli_num_rows($res) > 0) {
    while ($news_record = mysqli_fetch_assoc($res)) {
        $news_list[$news_record['id']] = $news_record;
    }
}

$template_dir = '$/core/core.news';
$template_file = "_template.news.list.html";

$template_data = array(
    'news_list' =>  $news_list
);

echo websun_parse_template_path($template_data, $template_file, $template_dir);