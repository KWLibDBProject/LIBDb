<?php
require_once '../__required.php'; // $mysqli_link


$id = intval($_GET["id"]);

$table = 'news';
$result = array();


$q = "DELETE FROM {$table} WHERE (id={$id}) ";
if ($r = mysqli_query($mysqli_link, $q)) {
    // запрос удаление успешен
    $result["error"] = 0;
    $result['message'] = 'Новость удалена.';
    kwLogger::logEvent('Delete', 'news', $id, "News record deleted, id = {$id}");

} else {
    // DB error again
    $result["error"] = 1;
    $result['message'] = 'Ошибка удаления из базы данных!';
}


if (isAjaxCall()) {
    print(json_encode($result));
} else {
    $override = array(
        'time' => $CONFIG['callback_timeout'] ?? 15,
        'target' => '../ref.news.show.php',
        'buttonmessage' => 'Вернуться к списку новостей',
        'message' => $result['message']
    );
    $tpl = new kwt('../ref.all.timed.callback.tpl');
    $tpl->override($override);
    $tpl->out();
}