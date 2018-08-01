<?php
require_once '../__required.php'; // $mysqli_link

$table = 'authors';

printr($_POST);


$now = ConvertTimestampToDate();
$q = array(
    'name_ru'       => trim($_POST['name_ru'] ?? '', ' '),
    'name_en'       => trim($_POST['name_en'] ?? '', ' '),
    'name_uk'       => trim($_POST['name_uk'] ?? '', ' '),
    'title_ru'      => $_POST['title_ru'] ?? '',
    'title_en'      => $_POST['title_en'] ?? '',
    'title_uk'      => $_POST['title_uk'] ?? '',
    'email'         => $_POST['email'] ?? '',
    'workplace_en'  => strip_tags($_POST['workplace_en'] ?? ''),
    'workplace_ru'  => strip_tags($_POST['workplace_ru'] ?? ''),
    'workplace_uk'  => strip_tags($_POST['workplace_uk'] ?? ''),
    'phone'         => $_POST['phone'] ?? '',
    'bio_en'        => $_POST['bio_en'] ?? '',
    'bio_ru'        => $_POST['bio_ru'] ?? '',
    'bio_uk'        => $_POST['bio_uk'] ?? '',
    /* Участие в редколлегии */
    'is_es'         => (($_POST['is_es'] ?? 'off') == 'on') ? 1 : 0,

    /* Роль в редколлегии */
    'selfhood'      => $_POST['selfhood'] ?? 0,

    /* stats */
    'stat_date_insert'  =>  $now,
    'stat_date_update'  =>  $now
);
$qstr = MakeInsertEscaped( $q, $table );

$res = mysqli_query($mysqli_link, $qstr) or die("Error at $qstr");

if (!empty($res)) {
    $new_author_id = mysqli_insert_id($mysqli_link); // айди автора в базе, он нужен для вставки фото

    if ($_POST['file_current_changed'] == 1)
    {
        if (isset($_FILES))
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

kwLogger::logEvent('Add', 'authors', $new_author_id, "Author added, new id is {$new_author_id}" );

if (isAjaxCall()) {
    print(json_encode($result));
} else {
    if ($result['error'] == 0) {
        // use template
        $override = array(
            'time' => $CONFIG['callback_timeout'] ?? 15,
            'target' => '/core/ref.authors.show.php',
            'buttonmessage' => 'Вернуться к списку авторов',
            'message' => "Автор добавлен в базу данных, его внутренний идентификатор = $new_author_id"
        );
        $tpl = new kwt('../ref.all.timed.callback.tpl');
        $tpl->override($override);
        $tpl->out();
    }
}

