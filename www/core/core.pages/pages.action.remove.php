<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

$id = intval($_GET["id"]);

$result = [];

$query = " DELETE FROM `staticpages` WHERE (id = {$id}) ";
if ($r = mysqli_query($mysqli_link, $query)) {
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
    $template_dir = '$/core/_templates';
    $template_file = "ref.all_timed_callback.html";

    $template_data = array(
        'time'          => Config::get('callback_timeout') ?? 15,
        'target'        => '../list.pages.show.php',
        'button_text'   => 'Вернуться к списку страниц',
        'message'       => $result['message']
    );
    echo websun_parse_template_path($template_data, $template_file, $template_dir);
}