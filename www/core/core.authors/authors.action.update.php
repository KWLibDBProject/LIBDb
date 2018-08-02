<?php
require_once '../__required.php'; // $mysqli_link

$id = isset($_POST['id']) ? $_POST['id'] : Die('Unknown ID. ');
$ref_name = 'authors';

$q = array(
    'name_ru'       => trim($_POST['name_ru'] ?? '', ' '),
    'name_en'       => trim($_POST['name_en']  ?? '', ' '),
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

    /* Роль в редколлегии: @todo: доп-проверка, если он не in_es - то 0 ? */
    'selfhood'      => $_POST['selfhood'] ?? 0,
);
$qstr = MakeUpdateEscaped($q, $ref_name, "WHERE id=$id");

$res = mysqli_query($mysqli_link, $qstr);

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


if (isAjaxCall()) {
    print(json_encode($result));
} else {
    if ($result['error'] == 0) {

        $template_dir = '$/core/_templates';
        $template_file = "ref.all_timed_callback.html";

        $template_data = array(
            'time'          => $CONFIG['callback_timeout'] ?? 15,
            'target'        => '/core/ref.authors.show.php',
            'button_text'   => 'Вернуться к списку авторов',
            'message'       => "Информация об авторе c внутренним идентификатором {$id} обновлена"
        );
        echo \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);

    }
}