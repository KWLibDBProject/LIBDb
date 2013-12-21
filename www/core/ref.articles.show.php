<?php
// отображение списка статей, вызывает в ajax load ref.articles.action.list.php by author
// если author not defined - показхывает всех
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Список статей</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="js/jquery-1.10.2.min.js"></script>
    <link rel="stylesheet" type="text/css" href="ref_articles/articles.css">
    <style>
        .button-large {
            height: 60px;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#button-addnewarticle").on('click',function(){
                location.href = 'ref_articles/articles.form.add.php';
            });
            $("#button-exittoadminpanel").on('click',function(){
                location.href = 'admin.html';
            });
            $('#articles_list')
                    .on('click','.download-pdf',function(){
                        window.location.href="getpdf.php?id="+$(this).attr('name')
                    })
                    .on('click','.edit_button',function(){
                        location.href = 'ref_articles/articles.form.edit.php?id='+$(this).attr('name');
                    });

            $("#button-showarticles-byauthor").on('click',function(){
                author = -1;
                $("#articles_list").load("ref_articles/ref.articles.action.list.php?author="+author);
            });
            $("#button-showarticles-all").on('click',function(){
                $("#articles_list").load("ref_articles/ref.articles.action.list.php");

            });

        });
    </script>
</head>

<body>
<button id="button-exittoadminpanel" class="button-large">Выход в админку </button>
<button id="button-addnewarticle" class="button-large">Добавить новую статью </button>
<hr>
<button type="button" id="button-showarticles-all">Показать все статьи</button>
Автор: <select><option valie="0">Выбрать автора</option></select><button id="button-showbyauthor" disabled>Показать статьи по автору</button>

<div id="articles_list">
</div>

</body>
</html>