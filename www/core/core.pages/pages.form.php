<?php
require_once '../__required.php'; // $mysqli_link

$SID = session_id();
if(empty($SID)) session_start();
ifNotLoggedRedirect('/core/');

// это эмулирует http-запрос к соотв. странице и отдает нам результат.
// $data = json_decode(file_get_contents("http://".$_SERVER['HTTP_HOST'].'/core/core.pages/pages.action.getitem.php?id='.$_GET['id']), true);

$page_id = isset($_GET['id']) ? intval($_GET['id']) : -1;

if ($page_id != -1) {
    $query = "SELECT * FROM staticpages WHERE id=$page_id";
    $res = mysqli_query($mysqli_link, $query) or die("Unable to execute mysqli request: ".$query);

    if (mysqli_num_rows($res) > 0) {
        $data = mysqli_fetch_assoc($res);
    }
}

$tpl = new kwt('pages.form.tpl.html');

$tpl -> config('/**','**/');

$over = array(
    'page_id'           => $page_id,
    'form_call_script'  => ($page_id != -1) ? 'pages.action.update.php' : 'pages.action.insert.php',
    'submit_button_text'=> ($page_id != -1) ? 'СОХРАНИТЬ ИЗМЕНЕНИЯ' : 'СОХРАНИТЬ СТРАНИЦУ',
    'page_title'        => ($page_id != -1) ? 'Страницы -- редактирование' : 'Страницы -- добавление',
    'title_en'          => $data['title_en'] ?? '',
    'title_ru'          => $data['title_ru'] ?? '',
    'title_uk'          => $data['title_uk'] ?? '',
    'content_en'        => $data['content_en'] ?? '',
    'content_ru'        => $data['content_ru'] ?? '',
    'content_uk'        => $data['content_uk'] ?? '',
    'comment'           => $data['comment'] ?? '',
    'alias'             => $data['alias'] ?? '',
);
$tpl->override($over);
$tpl->out();