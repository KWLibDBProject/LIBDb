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
    $template_dir = '$/core/_templates';
    $template_file = "ref.all_timed_callback.html";

    $template_data = array(
        'time'          => $CONFIG['callback_timeout'] ?? 15,
        'target'        => '/core/list.news.show.php',
        'button_text'   => 'Вернуться к списку новостей',
        'message'       => $result['message']
    );
    echo \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);
}