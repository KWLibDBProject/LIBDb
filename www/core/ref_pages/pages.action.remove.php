<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');


$id = $_GET["id"];

$table = 'staticpages';
$result = array();

$link = ConnectDB();


$q = "UPDATE $table SET deleted=1 WHERE (id=$id)";
if ($r = mysql_query($q)) {
    // запрос удаление успешен
    $result["error"] = 0;
    $result['message'] = 'Страница помечена на удаление.';

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
        'buttonmessage' => 'Вернуться к списку статических страниц',
        'message' => $result['message']
    );
    $tpl = new kwt('../ref.all.timed.callback.tpl');
    $tpl->override($override);
    $tpl->out();
}
?>