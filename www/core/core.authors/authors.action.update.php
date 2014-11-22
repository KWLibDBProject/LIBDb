<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.filestorage.php');

$id = isset($_POST['id']) ? $_POST['id'] : Die('Unknown ID. ');
$ref_name = 'authors';

$link = ConnectDB();

$q = array(
    'name_ru' => mysql_escape_string(trim($_POST['name_ru'], ' ')),
    'name_en' => mysql_escape_string(trim($_POST['name_en'], ' ')),
    'name_uk' => mysql_escape_string(trim($_POST['name_uk'], ' ')),
    'title_ru' => mysql_escape_string($_POST['title_ru']),
    'title_en' => mysql_escape_string($_POST['title_en']),
    'title_uk' => mysql_escape_string($_POST['title_uk']),
    'email' => mysql_escape_string($_POST['email']),
    'workplace_en' => mysql_escape_string($_POST['workplace_en']),
    'workplace_ru' => mysql_escape_string($_POST['workplace_ru']),
    'workplace_uk' => mysql_escape_string($_POST['workplace_uk']),
    'is_es' => (strtolower(mysql_escape_string($_POST['is_es']))=='on' ? 1 : 0),
    'phone' => mysql_escape_string($_POST['phone']),
    'bio_en' => mysql_escape_string($_POST['bio_en']),
    'bio_ru' => mysql_escape_string($_POST['bio_ru']),
    'bio_uk' => mysql_escape_string($_POST['bio_uk']),
    /* самость */
    'selfhood' => mysql_escape_string($_POST['selfhood']),
    /* stat */
    'stat_date_update'      => ConvertTimestampToDate()
);

$qstr = MakeUpdate($q, $ref_name, "WHERE id=$id");
$res = mysql_query($qstr, $link);

if (!empty($res)) {
    $new_author_id = $id; // айди автора в базе, он нужен для вставки фото
    if ($_POST['file_current_changed'] == 1)
    {
        if (!empty($_FILES))
        {
            FileStorage::addFile($_FILES['file_new_input'], $new_author_id, 'authors', 'photo_id');
        }
    }
    $result['message'] = $qstr;
    $result['error'] = 0;
}
else {
    Die("Unable to insert data to DB!  ".$qstr);
}

CloseDB($link);

if (isAjaxCall()) {
    print(json_encode($result));
} else {
    if ($result['error'] == 0) {
        // use template
        $override = array(
            'time' => 10,
            'target' => '/core/ref.authors.show.php',
            'buttonmessage' => 'Вернуться к списку авторов',
            'message' => "Информация об авторе c внутренним идентификатором $id обновлена"
        );
        $tpl = new kwt('../ref.all.timed.callback.tpl');
        $tpl->override($override);
        $tpl->out();
    }
}
?>