<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.filestorage.php');

// а) удалить автора, если у него есть статьи НЕЛЬЗЯ
$author_id = $_GET["id"];

$table = 'authors';
$result = array();

$link = ConnectDB();

$qt = "SELECT COUNT(article) AS aha FROM cross_aa WHERE author={$author_id}";
if ($rt = mysql_query($qt)) {
    $aha = mysql_fetch_assoc($rt);
    if ($aha['aha'] > 0) {
        // у автора есть статьи, удалять низя
        $result["error"] = 4;
        $result['message'] = 'Нельзя удалять автора, если у него есть статьи!';
    } else {
        // статей нет, можно удалять автора
        // нужно получить информацию об авторе, в частности id его фотографии
        // заменено "фичей" - удаляем информацию из хранилища по relation-полю, в котором
        // лежит идентификатор автора и название коллекции.

        if (FileStorage::getCollectionByRel($author_id) === 'authors') {
            FileStorage::removeFileByRel($author_id, 'authors');
        } else {
            die('Нарушение целостности базы');
        }

        // удалить запись об авторе из таблицы AUTHORS
        $q = "DELETE FROM $table WHERE (id = $author_id)";
        if ($r = mysql_query($q)) {
            // запрос удаление успешен
            $result["error"] = 0;
            $result['message'] = 'Автор удален из базы данных.';

        } else {
            // DB error again
            $result["error"] = 1;
            $result['message'] = 'Ошибка удаления автора из базы данных!';
        }
    }
} else {
    // DB error
    $result["error"] = 2;
    $result['message'] = 'Ошибка доступа к базе данных!';
};


CloseDB($link);

if (isAjaxCall()) {
    print(json_encode($result));
} else {
    $override = array(
        'time' => 15,
        'target' => '/core/ref.authors.show.php',
        'buttonmessage' => 'Вернуться к списку авторов',
        'message' => $result['message']
    );
    $tpl = new kwt('../ref.all.timed.callback.tpl');
    $tpl->override($override);
    $tpl->out();
}
?>