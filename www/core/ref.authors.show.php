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
    <title>Справочник: Список авторов</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="js/jquery.colorbox.js"></script>
    <link rel="stylesheet" type="text/css" href="css/colorbox.css" />

    <link rel="stylesheet" type="text/css" href="css/core.admin.css">
    <link rel="stylesheet" type="text/css" href="ref_authors/authors.css">

    <script src="js/core.js"></script>
    <script src="ref_authors/ref.authors.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({cache: false});
            $("#authors_list").load("ref_authors/authors.action.list.php?ref="+ref_name);

            $("#button_exit").on('click',function(event){
                window.location.href = '/core/';
            });
            $("#add_item").on('click',function(event){
                window.location.href = 'ref_authors/authors.form.php';
            });
            $('#authors_list')
                    .on('click','.actor-edit',function(){
                        window.location.href = 'ref_authors/authors.form.php?id='+$(this).attr('name');
                    })
                    .on('click','.lightbox',function(){
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
<button type="button" class="button-large" id="button_exit"><strong><<< НАЗАД </strong></button>
<button type="button" class="button-large" id="add_item">Добавить автора</button><br>
<hr>
<fieldset class="result-list table-hl-rows">
    <div id="authors_list">
    </div>
</fieldset>

</body>
</html>
