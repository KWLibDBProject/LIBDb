<?php
require_once '../__required.php'; // $mysqli_link
$ref_name = 'topics';

$query = "SELECT * FROM topics";
$res = mysqli_query($mysqli_link, $query) or die('$msg->say("errors/mysqli_query_error",$query)');

$ref_numrows = @mysqli_num_rows($res) ;

$topics_list = [];

if ($ref_numrows > 0) {
    while($topic_record = mysqli_fetch_assoc($res))
    {
        $topics_list[ $topic_record['id']  ] = $topic_record;
    }
}

$template_dir = '$/core/core.topics';
$template_file = "_template.topics.list.html";

$template_data = array(
    'topics_list' =>  $topics_list
);

echo \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);
