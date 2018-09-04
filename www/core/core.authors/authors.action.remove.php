<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

// а) удалить автора, если у него есть статьи НЕЛЬЗЯ
$author_id = intval($_GET["id"]);

$table = 'authors';
$result = [];

$query_articles_count = "SELECT COUNT(article) AS articles_count FROM cross_aa WHERE author = {$author_id}";

$response_articles_count = mysqli_query($mysqli_link, $query_articles_count);

if (mysqli_num_rows($response_articles_count) > 0) {

    $result_articles_count = mysqli_fetch_assoc($response_articles_count);

    if ($result_articles_count['articles_count'] > 0) {
        // у автора есть статьи, удалять низя
        $result["error"] = 4;
        $result['message'] = 'Нельзя удалять автора, если у него есть статьи!';
    } else {
        // статей нет, можно удалять автора

        // нужно получить информацию об авторе, в частности id его фотографии
        $query_author_info = "SELECT `id`, `photo_id` FROM authors WHERE id = {$author_id}";
        $response_author_info = mysqli_query($mysqli_link, $query_author_info);
        $author_info = mysqli_fetch_assoc($response_author_info);
        $photo_id = $author_info['photo_id'];

        FileStorage::removeFileById($photo_id);

        // удалить запись об авторе из таблицы AUTHORS
        $query_author_delete = "DELETE FROM authors WHERE (id = {$author_id})";
        $responce_author_delete = mysqli_query($mysqli_link, $query_author_delete);

        if ($responce_author_delete) {
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
    echo websun_parse_template_path($template_data, $template_file, $template_dir);

}
