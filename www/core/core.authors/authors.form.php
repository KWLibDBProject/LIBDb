<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$SID = session_id();
if(empty($SID)) session_start();
if (!isLogged()) header('Location: /core/');

$tpl = new kwt('authors.form.tpl.html');

if (isset($_GET['id'])) {
    // EDIT
    $over = array(
        'author_id' => $_GET['id'],
        'form_call_script' => 'authors.action.update.php',
        'submit_button_text' => 'СОХРАНИТЬ ИЗМЕНЕНИЯ',
        'page_title' => 'Авторы -- редактирование',
        'author_selfhood' => 0,
        /* photo */
        'file_current_flag_show' => '', // эти поля тоже можно установить JS в клиенте
        'file_current_flag_delete' => '',
        // this values MUST be loaded by JS Authors_LoadRecord()
        // 'file_current_id' => $record['photo']['id'],
        // 'file_current_username' => $record['photo']['username']
    );
} else {
    // ADD
    $over = array(
        'author_id' => -1,
        'form_call_script' => 'authors.action.insert.php',
        'submit_button_text' => 'ДОБАВИТЬ АВТОРА',
        'page_title' => 'Авторы -- добавление',
        'author_selfhood' => 0,
        /* photo */
        'file_current_flag_show' => 'disabled',
        'file_current_flag_delete' => '',
        // this values MUST be loaded by JS Authors_LoadRecord()
        // 'file_current_id' => '',
        // 'file_current_username' => ''

    );
}
$tpl->override($over);
$tpl->out();
?>
