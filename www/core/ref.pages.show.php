<?php
require_once('core.php');
require_once('core.db.php');
require_once('core.kwt.php');

$SID = session_id();
if(empty($SID)) session_start();
if (!isLogged()) header('Location: /core/');
?>
<html>
<head>
    <title>Модуль: Статические страницы</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="js/jquery-1.10.2.min.js"></script>

    <link rel="stylesheet" type="text/css" href="css/core.admin.css">
    <link rel="stylesheet" type="text/css" href="core.pages/pages.css">

    <script src="js/core.js"></script>

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
            $('#pages_list')
                    .on('click','.action-edit',function(){
                        window.location.href = 'core.pages/pages.form.php?id='+$(this).attr('name');
                    });
        });
    </script>
</head>
<body>
<button type="button" class="button-large" id="actor-exit"><strong><<< НАЗАД </strong></button>
<button type="button" class="button-large" id="actor-add">Добавить статическую страницу</button><br>
<hr>
<fieldset class="result-list table-hl-rows">
    <div id="pages_list">
    </div>
</fieldset>
</body>
</html>
