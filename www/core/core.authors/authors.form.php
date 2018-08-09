<?php
require_once '../__required.php'; // $mysqli_link

$SID = session_id();
if(empty($SID)) session_start();
ifNotLoggedRedirect('/core/');

$template_dir = '$/core/core.authors/';
$template_file = "_template.authors.form.html";

if (isset($_GET['id'])) {
    // EDIT
    $template_data = [
        'author_id'             => intval($_GET['id']),
        'form_call_script'      => 'authors.action.update.php',
        'submit_button_text'    => 'СОХРАНИТЬ ИЗМЕНЕНИЯ',
        'page_title'            => 'Авторы -- редактирование',
        'author_selfhood'       => 0,
        'file_current_flag_show' => '',
        'file_current_flag_delete' => '',
    ];
} else {
    // ADD
    $template_data = [
        'author_id'             => -1,
        'form_call_script'      => 'authors.action.insert.php',
        'submit_button_text'    => 'ДОБАВИТЬ АВТОРА',
        'page_title'            => 'Авторы -- добавление',
        'author_selfhood'       => 0,
        'file_current_flag_show' => 'disabled',
        'file_current_flag_delete' => '',
    ];
}

echo \Websun\websun::websun_parse_template_path($template_data, $template_file, $template_dir);


