<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');
require_once('../core.kwlogger.php');


$id = intval($_GET["id"]);

$table = 'staticpages';
$result = array();

$link = ConnectDB();

$q = " DELETE FROM {$table} WHERE (id = {$id}) ";
if ($r = mysql_query($q)) {
    // запрос удаление успешен
    $result["error"] = 0;
    $result['message'] = 'Статичная страница удалена!';
    kwLogger::logEvent('Delete', 'pages', $id, "Static page updated, id = {$id}");

} else {
    // DB error again
    $result["error"] = 1;
    $result['message'] = 'Ошибка удаления страницы из базы данных!';
}


CloseDB($link);

if (isAjaxCall()) {
    print(json_encode($result));
} else {
    $override = array(
        'time' => 15,
        'target' => '../ref.pages.show.php',
        'buttonmessage' => 'Вернуться к списку статичных страниц',
        'message' => $result['message']
    );
    $tpl = new kwt('../ref.all.timed.callback.tpl');
    $tpl->override($override);
    $tpl->out();
}
?>