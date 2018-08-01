<?php
require_once '../__required.php'; // $mysqli_link


$id = intval($_GET["id"]);

$table = 'staticpages';
$result = array();

$q = " DELETE FROM {$table} WHERE (id = {$id}) ";
if ($r = mysqli_query($mysqli_link, $q)) {
    // запрос удаление успешен
    $result["error"] = 0;
    $result['message'] = 'Статичная страница удалена!';
    kwLogger::logEvent('Delete', 'pages', $id, "Static page updated, id = {$id}");

} else {
    // DB error again
    $result["error"] = 1;
    $result['message'] = 'Ошибка удаления страницы из базы данных!';
}


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