<?php
require_once('core.php');
require_once('core.db.php');
require_once('core.kwt.php');

$SID = session_id();
if(empty($SID)) session_start();
ifNotLoggedRedirect('/core/');
?>
<html>
<head>
    <title>Модуль: новости</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="js/jquery-1.10.2.min.js"></script>

    <link rel="stylesheet" type="text/css" href="css/core.admin.css">
    <link rel="stylesheet" type="text/css" href="core.news/news.css">

    <script src="js/core.js"></script>
    <script src="core.news/news.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({cache: false});
            $("#news_list").load("core.news/news.action.list.php");

            $("#actor-exit").on('click',function(event){
                window.location.href = '/core/';
            });
            $("#actor-add").on('click',function(event){
                window.location.href = 'core.news/news.form.php';
            });
            $('#news_list')
                    .on('click','.actor-edit',function(){
                        window.location.href = 'core.news/news.form.php?id='+$(this).attr('name');
                    });
        });
    </script>
</head>
<body>
<button type="button" class="button-large" id="actor-exit"><strong><<< НАЗАД </strong></button>
<button type="button" class="button-large" id="actor-add">Добавить новость</button><br>
<hr>
<fieldset class="result-list table-hl-rows">
    <div id="news_list">
    </div>
</fieldset>
</body>
</html>