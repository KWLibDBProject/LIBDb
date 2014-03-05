<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');


$id = $_GET["id"];

$table = 'news';
$result = array();

$link = ConnectDB();


$q = "DELETE FROM $table WHERE (id=$id)";
if ($r = mysql_query($q)) {
    // запрос удаление успешен
    $result["error"] = 0;
    $result['message'] = 'Новость удалена.';

} else {
    // DB error again
    $result["error"] = 1;
    $result['message'] = 'Ошибка удаления из базы данных!';
}


CloseDB($link);

if (isAjaxCall()) {
    print(json_encode($result));
} else {
    $override = array(
        'time' => 15,
        'target' => '../ref.news.show.php',
        'buttonmessage' => 'Вернуться к списку новостей',
        'message' => $result['message']
    );
    $tpl = new kwt('../ref.all.timed.callback.tpl');
    $tpl->override($override);
    $tpl->out();
}
?>