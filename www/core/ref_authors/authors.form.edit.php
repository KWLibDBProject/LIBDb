<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="../js/jquery-1.10.2.min.js"></script>
    <script src="../js/jquery-ui-1.10.3.custom.min.js"></script>
    <script src="../js/tinymce.min.js"></script>

    <link rel="stylesheet" type="text/css" href="authors.css">


    <script src="../js/core.js"></script>
    <script src="ref.authors.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            author_id = <?php echo $_GET['id'] ?>; /* !!!! */

            if (author_id != -1) {
                Authors_LoadRecord("#form_edit_author", author_id);
                $("#button-remove").show();
            }

            $("#button-exit").on('click',function(event){
                window.location.href = '../ref.authors.show.php';
            });
            $("#button-remove").on('click',function(event){
                window.location.href = 'authors.action.remove.php?id='+author_id;
            });

        });
    </script>
</head>
<body>

<form action="authors.action.update.php" method="post" enctype="multipart/form-data" id="form_edit_author">
    <button type="button" class="button-large" id="button-exit"><strong>ВЕРНУТЬСЯ К СПИСКУ АВТОРОВ</strong></button>
    <button type="button" class="button-large" id="button-remove"><strong>УДАЛИТЬ АВТОРА</strong></button>
    <button type="submit" class="button-large" ><strong>СОХРАНИТЬ ИЗМЕНЕНИЯ</strong></button>
    <hr>
    <input type="hidden" name="id" value="">

    <fieldset>
        <label for="name_rus">Ф.И.О. (русский)</label><br>
        <input type="text" name="name_rus" id="name_rus" size="40" value="">
        <br>

        <label for="name_eng">Ф.И.О. (английский)</label><br>
        <input type="text" name="name_eng" id="name_eng" size="40" value="">
        <br>

        <label for="name_ukr">Ф.И.О. (украинский)</label><br>
        <input type="text" name="name_ukr" id="name_ukr" size="40" value="">
    </fieldset>
    <fieldset>
        <label for="title_eng">Звание, ученая степень, должность (eng)</label><br>
        <input type="text" name="title_eng" id="title_eng" size="40" value="">
        <br>

        <label for="title_rus">Звание, ученая степень, должность</label><br>
        <input type="text" name="title_rus" id="title_rus" size="40" value="">
        <br>

        <label for="title_ukr">Званна, вчена ступiнь, посада</label><br>
        <input type="text" name="title_ukr" id="title_ukr" size="40" value="">

    </fieldset>
    <fieldset>
        <legend>Контактные данные:</legend>

        <label for="email">E-Mail</label><br>
        <input type="text" name="email" id="email" value="">

        <br>

        <label for="phone">Телефон для связи</label><br>
        <input type="text" name="phone" id="phone" value="">

        <br>

        <label for="workplace">Место работы</label><br>
        <textarea name="workplace" id="workplace" cols="90" rows="5"></textarea>
    </fieldset>
    <label>Участие в редакционной коллегии:<input type="checkbox" name="is_es" id="is_es"></label>
    <hr>
    Поля редколлегии
    Поле фотографии
    <hr>

    <button type="submit" class="button-large" id="button-submit"><strong>СОХРАНИТЬ ИЗМЕНЕНИЯ</strong></button>
</form>

</body>
</html>
