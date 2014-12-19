<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.filestorage.php');

$id = isset($_POST['id']) ? $_POST['id'] : Die('Unknown ID. ');
$ref_name = 'authors';

$link = ConnectDB();

$q = array(
    'name_ru'       => trim($_POST['name_ru'], ' '),
    'name_en'       => trim($_POST['name_en'], ' '),
    'name_uk'       => trim($_POST['name_uk'], ' '),
    'title_ru'      => $_POST['title_ru'],
    'title_en'      => $_POST['title_en'],
    'title_uk'      => $_POST['title_uk'],
    'email'         => $_POST['email'],
    'workplace_en'  => strip_tags($_POST['workplace_en']),
    'workplace_ru'  => strip_tags($_POST['workplace_ru']),
    'workplace_uk'  => strip_tags($_POST['workplace_uk']),
    'is_es'         => strtolower(($_POST['is_es']))=='on' ? 1 : 0,
    'phone'         => $_POST['phone'],
    'bio_en'        => $_POST['bio_en'],
    'bio_ru'        => $_POST['bio_ru'],
    'bio_uk'        => $_POST['bio_uk'],
    'selfhood'      => $_POST['selfhood'],
    'stat_date_update' => ConvertTimestampToDate()
);
$qstr = MakeUpdateEscaped($q, $ref_name, "WHERE id=$id");

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
    die("Unable to insert data to DB!  ".$qstr);
}

kwLogger::logEvent('Update', 'authors', $new_author_id, "Author updated, id is {$new_author_id}" );

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