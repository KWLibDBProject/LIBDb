<?php
// @todo: переписать под вывод сообщений + шаблон?
$id = $_GET["id"];

$table = 'authors';

$link = ConnectDB();

$q = "UPDATE $table SET deleted=1 WHERE (id=$id)";
$r = mysql_query($q) or Die(print_r($q));

$result["error"] = 0;
$result['message'] = 'Record marked as "deleted"';

CloseDB($link);

if (isAjaxCall()) {
    print(json_encode($result));
} else {
    if ($result['error'] == 0) {
        // use template
        $override = array(
            'time' => 10,
            'target' => '../ref.authors.show.php',
            'buttonmessage' => 'Вернуться к списку авторов',
            'message' => 'Автор удален из базы данных'
        );
        $tpl = new kwt('../ref.all.timed.callback.tpl');
        $tpl->override($override);
        $tpl->out();
    }
}
?>