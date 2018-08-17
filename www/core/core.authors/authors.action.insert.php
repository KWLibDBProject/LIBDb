<?php
require_once '../__required.php'; // $mysqli_link

$table = 'authors';

$dataset = array(
    'name_ru'       => trim($_POST['name_ru'] ?? '', ' '),
    'name_en'       => trim($_POST['name_en'] ?? '', ' '),
    'name_ua'       => trim($_POST['name_ua'] ?? '', ' '),

    'title_ru'      => trim($_POST['title_ru'] ?? '', ' '),
    'title_en'      => trim($_POST['title_en'] ?? '',' '),
    'title_ua'      => trim($_POST['title_ua'] ?? '', ' '),

    'email'         => trim($_POST['email'] ?? '', ' '),
    'orcid'         => trim($_POST['orcid'] ?? '', ' '),
    'phone'         => trim($_POST['phone'] ?? '', ' '),

    'workplace_en'  => strip_tags($_POST['workplace_en'] ?? ''),
    'workplace_ru'  => strip_tags($_POST['workplace_ru'] ?? ''),
    'workplace_ua'  => strip_tags($_POST['workplace_ua'] ?? ''),

    'bio_en'        => $_POST['bio_en'] ?? '',
    'bio_ru'        => $_POST['bio_ru'] ?? '',
    'bio_ua'        => $_POST['bio_ua'] ?? '',

    /* Участие в редколлегии */
    'is_es'         => (($_POST['is_es'] ?? 'off') == 'on') ? 1 : 0,

    /* Роль в редколлегии */
    'estaff_role'      => $_POST['estaff_role'] ?? 0,
);

$dataset['firstletter_name_en'] = mb_substr( $dataset['name_en'], 0, 1 );
$dataset['firstletter_name_ru'] = mb_substr( $dataset['name_ru'], 0, 1 );
$dataset['firstletter_name_ua'] = mb_substr( $dataset['name_ua'], 0, 1 );

$qstr = MakeInsertEscaped( $dataset, $table );

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

        $template_dir = '$/core/_templates';
        $template_file = "ref.all_timed_callback.html";

        $template_data = array(
            'time'          => Config::get('callback_timeout') ?? 15,
            'target'        => '/core/list.authors.show.php',
            'button_text'   => 'Вернуться к списку авторов',
            'message'       => "Автор добавлен в базу данных, его внутренний идентификатор = {$new_author_id}"
        );
        echo \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);

    }
}

