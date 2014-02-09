<?php
require_once('core.php');
require_once('core.db.php');
require_once('core.kwt.php');
?>
<html>
<head>
    <title>Справочник: Сборники</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="js/jquery-1.10.2.min.js"></script>

    <link rel="stylesheet" type="text/css" href="ref_books/books.css">

    <script type="text/javascript" src="js/core.js"></script>
    <script type="text/javascript" src="ref_books/books.js"></script>

    <script type="text/javascript" src="js/jquery.colorbox.js"></script>
    <link rel="stylesheet" href="css/colorbox.css" />

    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({cache: false});
            $("#books_list").load("ref_books/books.action.list.php");

            $("#button_exit").on('click',function(event){
                window.location.href = 'admin.html';
            });
            $("#add_item").on('click',function(event){
                window.location.href = 'ref_books/books.form.add.php';
            });
            $('#books_list')
                    .on('click','.edit_button',function(){
                        window.location.href = 'ref_books/books.form.edit.php?id='+$(this).attr('name');
                    });
            $("#books_list").on('click','.lightbox-image',function(){
                $.colorbox({
                    photo: true,
                    href: $(this).attr('href')
                });
                return false;
            });

        });
    </script>
</head>
<body>
<button type="button" class="button-large" id="button_exit"><strong>ВЕРНУТЬСЯ В АДМИНКУ</strong></button>
<button type="button" class="button-large" id="add_item">Добавить сборник</button><br>
<hr>
<div id="books_list" class="reference-list">
</div>

</body>
</html>
