<?php
// @todo: работа над ошибками и не только

$id = $_POST['id'];
$ref_name = 'authors';

$link = ConnectDB();

$q = array(
    'name_rus' => mysql_escape_string($_POST['name_rus']),
    'name_eng' => mysql_escape_string($_POST['name_eng']),
    'name_ukr' => mysql_escape_string($_POST['name_ukr']),
    'title_rus' => mysql_escape_string($_POST['title_rus']),
    'title_eng' => mysql_escape_string($_POST['title_eng']),
    'title_ukr' => mysql_escape_string($_POST['title_ukr']),
    'email' => mysql_escape_string($_POST['email']),
    'workplace' => mysql_escape_string($_POST['workplace']),
    'is_es' => (strtolower(mysql_escape_string($_POST['is_es']))=='on' ? 1 : 0),
    'phone' => mysql_escape_string($_POST['phone'])
);

$qstr = MakeUpdate($q, $ref_name, "WHERE id=$id");
$res = mysql_query($qstr, $link) or Die("Unable update data : ".$qstr);
CloseDB($link);

$result['message'] = $qstr;
$result['error'] = 0;

if (isAjaxCall()) {
    print(json_encode($result));
} else {
    if ($result['error'] == 0) {
        // use template
        $override = array(
            'time' => 10,
            'target' => '../ref.authors.show.php',
            'buttonmessage' => 'Вернуться к списку авторов',
            'message' => 'Информация об авторе обновлена'
        );
        $tpl = new kwt('ref.authors.callback.tpl');
        $tpl->override($override);
        $tpl->out();
    }
}
?>