<?php
require_once '../__required.php'; // $mysqli_link

// а) удалить автора, если у него есть статьи НЕЛЬЗЯ
$author_id = intval($_GET["id"]);

$table = 'authors';
$result = array();

$qt = "SELECT COUNT(article) AS aha FROM cross_aa WHERE author = {$author_id}";

if ($rt = mysqli_query($mysqli_link, $qt)) {
    $aha = mysqli_fetch_assoc($rt);
    if ($aha['aha'] > 0) {
        // у автора есть статьи, удалять низя
        $result["error"] = 4;
        $result['message'] = 'Нельзя удалять автора, если у него есть статьи!';
    } else {
        // статей нет, можно удалять автора
        // нужно получить информацию об авторе, в частности id его фотографии

        $q = "SELECT `id`, `photo_id` FROM {$table} WHERE id = {$author_id}";
        $qr = mysqli_query($mysqli_link, $q);
        $qf = mysqli_fetch_assoc($qr);
        $photo_id = $qf['photo_id'];

        FileStorage::removeFileById($photo_id);

        // удалить запись об авторе из таблицы AUTHORS
        $q = "DELETE FROM {$table} WHERE (id = {$author_id})";
        if ($r = mysqli_query($mysqli_link, $q)) {
            // запрос удаление успешен
            $result["error"] = 0;
            $result['message'] = 'Автор удален из базы данных.';

        } else {
            // DB error again
            $result["error"] = 1;
            $result['message'] = 'Ошибка удаления автора из базы данных!';
        }
        kwLogger::logEvent('Delete', 'authors', $author_id, "Author deleted, id is {$author_id}" );
    }
} else {
    // DB error
    $result["error"] = 2;
    $result['message'] = 'Ошибка доступа к базе данных!';
};



if (isAjaxCall()) {
    print(json_encode($result));
} else {
    $template_dir = '$/core/_templates';
    $template_file = "ref.all_timed_callback.html";

    $template_data = array(
        'time'          => Config::get('callback_timeout') ?? 15,
        'target'        => '/core/list.authors.show.php',
        'button_text'   => 'Вернуться к списку авторов',
        'message'       => $result['message']
    );
    echo \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);

}
