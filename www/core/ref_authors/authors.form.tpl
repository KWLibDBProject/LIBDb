<html>
<head>
    <title>{%page_title%}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="../js/jquery-1.10.2.min.js"></script>
    <script src="../js/tinymce.min.js"></script>

    <link rel="stylesheet" type="text/css" href="/core/css/core.admin.css">
    <link rel="stylesheet" type="text/css" href="authors.css">
    <style type="text/css"></style>

    <script src="../js/core.js"></script>
    <script src="ref.authors.js"></script>

    <script type="text/javascript">
        // tinyMCE inits
        tinymce.init({
            selector:'textarea#bio',forced_root_block : "",
            force_br_newlines : true,
            force_p_newlines : false
        });

        $(document).ready(function () {
            author_id = {%author_id%};

            if (author_id != -1) {
                Authors_LoadRecord("#form_edit_author", author_id, 'bio');
                $("#button-remove").show().on('click',function(event){
                    window.location.href = 'authors.action.remove.php?id='+author_id;
                });
            }

            $("#button-exit").on('click',function(event){
                window.location.href = '/core/ref.authors.show.php';
            });
            $("#is_es").on('change',function(event){
                $("#es_fieldset").toggle();
            });

        });
    </script>
</head>
<body>

<form action="{%form_call_script%}" method="post" enctype="multipart/form-data" id="form_edit_author">
    <button type="button" class="button-large" id="button-exit"><strong>ВЕРНУТЬСЯ К СПИСКУ АВТОРОВ</strong></button>
    <button type="button" class="button-large" id="button-remove"><strong>УДАЛИТЬ АВТОРА</strong></button>
    <button type="submit" class="button-large" ><strong>{%submit_button_text%}</strong></button>
    <hr>
    <input type="hidden" name="id">
    <fieldset>
        <label for="name_en">Name, surname</label><br>
        <input type="text" name="name_en" id="name_en" size="40" value="">
        <br>

        <label for="name_ru">Ф.И.О. (русский)</label><br>
        <input type="text" name="name_ru" id="name_ru" size="40" value="">
        <br>

        <label for="name_uk">Ф.И.О. (украинский)</label><br>
        <input type="text" name="name_uk" id="name_uk" size="40" value="">
    </fieldset>
    <fieldset>
        <label for="title_en">Звание, ученая степень, должность (на английском)</label><br>
        <input type="text" name="title_en" id="title_en" size="40" value="">
        <br>

        <label for="title_ru">Звание, ученая степень, должность</label><br>
        <input type="text" name="title_ru" id="title_ru" size="40" value="">
        <br>

        <label for="title_uk">Званна, вчена ступiнь, посада</label><br>
        <input type="text" name="title_uk" id="title_uk" size="40" value="">

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
    <hr>

        <fieldset id="es_fieldset_">
        <legend>Автор как участник редколлегии</legend>
    </fieldset>

    <fieldset>
        <legend><label>Участие в редакционной коллегии:<input type="checkbox" name="is_es" id="is_es"></label>  </legend>
        <div id="es_fieldset">
            <label for="bio">Биография и публикации в других изданиях:</label><br>
            <textarea name="bio" id="bio" cols="90" rows="7"></textarea>

            Поля редколлегии
            Поле фотографии
        </div>
    </fieldset>

    <hr>

    <button type="submit" class="button-large" id="button-submit"><strong>СОХРАНИТЬ ИЗМЕНЕНИЯ</strong></button>
</form>

</body>
</html>
