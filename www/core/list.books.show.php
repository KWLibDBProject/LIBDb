<?php
define('__ACCESS_MODE__', 'admin');
require_once '__required.php'; // $mysqli_link

/*$SID = session_id();
if(empty($SID)) session_start();*/
// ifNotLoggedRedirect('/core/');

?>
<html>
<head>
    <title>Справочник: Сборники</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="js/jquery-1.10.2.min.js"></script>

    <link rel="stylesheet" type="text/css" href="_assets/core.admin.css">
    <link rel="stylesheet" type="text/css" href="core.books/books.css">

    <script type="text/javascript" src="../frontend.js"></script>

    <script type="text/javascript" src="_assets/jquery.colorbox.js"></script>
    <link rel="stylesheet" type="text/css" href="_assets/colorbox.css" />

    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({cache: false});
            $("#books_list")
                    .load("core.books/books.action.list.php")
                    .on('click','.lightbox-image',function(){
                        $.colorbox({
                            photo: true,
                            href: $(this).attr('href')
                        });
                        return false;
                    });

            $("#actor-exit").on('click',function(event){
                window.location.href = '/core/';
            });
            $("#actor-add").on('click',function(event){
                window.location.href = 'core.books/books.form.add.php';
            });
            $('#books_list')
                    .on('click','.action-edit',function(){
                        window.location.href = 'core.books/books.form.edit.php?id='+$(this).attr('name');
                    });
        });
    </script>
</head>
<body>
<button type="button" class="button-large" id="actor-exit"><strong><<< НАЗАД </strong></button>
<button type="button" class="button-large" id="actor-add">Добавить сборник</button><br>
<hr>
<fieldset class="result-list table-hl-rows">
    <div id="books_list" class="reference-list">
    </div>
</fieldset>

</body>
</html>
