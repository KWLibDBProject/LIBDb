<?php
$tpl = new kwt('authors.form.tpl');

$over = array(
    'author_id' => isset($_GET['id']) ? $_GET['id'] : -1,
    'form_call_script' => isset($_GET['id']) ? 'authors.action.update.php' : 'authors.action.insert.php',
    'submit_button_text' => isset($_GET['id']) ? 'СОХРАНИТЬ ИЗМЕНЕНИЯ' : 'ДОБАВИТЬ АВТОРА',
);
$tpl->override($over);
$tpl->out();
?>
