<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');


// а) удалить автора, если у него есть статьи НЕЛЬЗЯ
$id = $_GET["id"];

$table = 'authors';
$result = array();

$link = ConnectDB();

$qt = "SELECT COUNT(`article`) AS `aha` FROM cross_aa WHERE `author`=$id";
if ($rt = mysql_query($qt)) {
    $aha = mysql_fetch_assoc($rt);
    if ($aha['aha'] > 0) {
        // у автора есть статьи, удалять низя
        $result["error"] = 4;
        $result['message'] = 'Нельзя удалять автора, если у него есть статьи!';
    } else {
        // статей нет, можно удалять автора

        $q = "UPDATE $table SET deleted=1 WHERE (id=$id)";
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
        'target' => '../ref.authors.show.php',
        'buttonmessage' => 'Вернуться к списку авторов',
        'message' => $result['message']
    );
    $tpl = new kwt('../ref.all.timed.callback.tpl_');
    $tpl->override($override);
    $tpl->out();
}
?>