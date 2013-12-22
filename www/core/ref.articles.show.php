<!DOCTYPE HTML>
<html>
<head>
    <title>Список статей</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/core.js"></script>

    <link rel="stylesheet" type="text/css" href="ref_articles/articles.css">
    <style>
        .button-large {
            height: 60px;
        }
    </style>
    <script type="text/javascript">
        var authorsList = preloadOptionsList('ref_authors/ref.authors.action.getoptionlist.php');
        var booksList = preloadOptionsList('ref_books/ref.books.action.getoptionlist.php');
        var topicsList = preloadOptionsList('ref_topics/ref.topics.action.getoptionlist.php');

        $(document).ready(function () {
            BuildSelector('with_author',authorsList,0);
            BuildSelector('with_book',booksList,0);
            BuildSelector('with_topic',topicsList,0);

            $("#button-newarticle").on('click',function(){
                location.href = 'ref_articles/articles.form.add.php';
            });
            $("#button-exit").on('click',function(){
                location.href = 'admin.html';
            });
            $('#articles_list')
                    .on('click','.download-pdf',function(){
                        window.location.href="getpdf.php?id="+$(this).attr('name')
                    })
                    .on('click','.edit_button',function(){
                        location.href = 'ref_articles/articles.form.edit.php?id='+$(this).attr('name');
                    });

            $("#button-show-all").on('click',function(){
                $("#articles_list").load("ref_articles/articles.action.list.php");

            });
            $("#button-show-withauthor").on('click',function(){
                author = -1;
                $("#articles_list").load("ref_articles/articles.action.list.php?author="+author);
            });
            $("#button-show-withbook").on('click',function(){
                author = -1;
                $("#articles_list").load("ref_articles/articles.action.list.php?book="+author);
            });
            $("#button-show-withtopic").on('click',function(){
                author = -1;
                $("#articles_list").load("ref_articles/articles.action.list.php?topic="+author);
            });

        });
    </script>
</head>

<body>
<button id="button-exit" class="button-large">Выход в админку </button>
<button id="button-newarticle" class="button-large">Добавить новую статью </button>
<hr>
<fieldset>
    <legend>Критерии отбора</legend>
    Автор: <select name="with_author"><option value="0">ЛЮБОЙ</option></option></select>
    Топик: <select name="with_topic"><option value="0">ЛЮБОЙ</option></select>
    Сборник: <select name="with_book"><option value="0">ЛЮБОЙ</option></select>
    <button id="button-show-withselection">Показать выбранное</button>
    <button id="button-reset-selection">Сбросить критерии</button>
    <button type="button" id="button-show-all">Показать ВСЕ статьи</button>
</fieldset>

<div id="articles_list">
</div>

</body>
<!--
// отображение списка статей, вызывает в ajax load articles.action.list.php by author
// если author not defined - показхывает всех
-->
</html>