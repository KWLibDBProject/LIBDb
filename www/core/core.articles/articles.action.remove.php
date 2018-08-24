<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

$result = [
    'message'   => '',
    'error'     => 0
];

$article_id = IsSet($_GET['id']) ? intval($_GET['id']) : Die("No id!");

// получить информацию о ПДФке, относящейся к статье
$q = "SELECT id, pdfid FROM articles WHERE id = {$article_id}";
$qr = mysqli_query($mysqli_link, $q);
$qf = mysqli_fetch_assoc($qr);
$pdfid = $qf['pdfid'];

// удалить пдфку из filestorage
FileStorage::removeFileById($pdfid);

// удалить связи СТАТЬЯ - АВТОРЫ из cross_aa
$q = "DELETE FROM cross_aa WHERE article = {$article_id}";
mysqli_query($mysqli_link, $q) or Die("Death at $q");

// только теперь удалить саму статью
$q = "DELETE FROM articles WHERE id = {$article_id}";
mysqli_query($mysqli_link, $q) or Die("Death at {$q}");

kwLogger::logEvent('Delete', 'articles', $article_id, "Article removed, id was: {$article_id}" );

$template_dir = '$/core/_templates';
$template_file = "ref.all_timed_callback.html";

$template_data = array(
    'time'          => Config::get('callback_timeout') ?? 15,
    'target'        => '../list.articles.show.php',
    'button_text'   => 'Вернуться к списку статей',
);

$template_data['message']
    = ($result['error'] == 0)
    ? ('Статья удалена из базы данных')
    : $result['message'];

echo websun_parse_template_path($template_data, $template_file, $template_dir);

