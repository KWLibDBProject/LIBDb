<?php
define('__ACCESS_MODE__', 'admin');
require_once '__required.php'; // $mysqli_link

?>
<html>
<head>
    <title>Модуль: Статические страницы</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="_assets/jquery-1.10.2.min.js"></script>

    <link rel="stylesheet" type="text/css" href="_assets/core.admin.css">
    <link rel="stylesheet" type="text/css" href="core.pages/pages.css">

    <script src="../frontend.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({cache: false});
            $("#pages_list").load("core.pages/pages.action.list.php");

            $("#actor-exit").on('click',function(event){
                window.location.href = '/core/';
            });
            $("#actor-add").on('click',function(event){
                window.location.href = 'core.pages/pages.form.php';
            });
            $('#pages_list').on('click','.action-edit',function(){
                window.location.href = 'core.pages/pages.form.php?id='+$(this).attr('name');
            });
        });
    </script>
</head>
<body>
<button type="button" class="button-large" id="actor-exit"><strong><<< НАЗАД </strong></button>
<button type="button" class="button-large" id="actor-add">Добавить статическую страницу</button><br>
<hr>
Должны быть определены страницы с идентификаторами:<br />
<strong>pubethics</strong>, <strong>tom</strong>, <strong>format</strong>, <strong>forauthors</strong>,
<strong>estaff</strong>, <strong>details</strong>, <strong>imprint</strong>, <strong>default</strong>,
<strong>about</strong>. <br/> Страница about - стартовая страница сайта.
<hr>
<fieldset class="result-list table-hl-rows">
    <div id="pages_list">
    </div>
</fieldset>
</body>
</html>
