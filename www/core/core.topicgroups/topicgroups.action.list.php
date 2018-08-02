<?php
require_once '../__required.php'; // $mysqli_link

$ref_name = 'topicgroups';

$query = "SELECT * FROM $ref_name";
$res = mysqli_query($mysqli_link, $query) or die('$msg->say("errors/mysqli_query_error",$query)');

$topicgroups_list = array();

if (mysqli_num_rows($res) > 0) {
    while($ref_record = mysqli_fetch_assoc($res))
    {
        $topicgroups_list[ $ref_record['id']  ] = $ref_record;
    }
}

$template_dir = '$/core/core.topicgroups';
$template_file = "_template.topicgroups.list.html";

$template_data = array(
    'topicgroups_list' =>  $topicgroups_list
);

echo \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);
