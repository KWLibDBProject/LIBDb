<?php
// calls with parameter 'authors'
require_once('core.php');
require_once('core.db.php');

$ref_prompt = 'Добавление автора';

//@todo: отбор списка авторов, валидация данных:
// по хорошему, отбор листинга нужен по каким-то критериям из выпадающего списка
// в AddItem и UpdateItem ввести валидацию данных (как минимум емейла) как в http://jqueryui.com/dialog/#modal-form

//@todo: отбор по полю "члены редколлегии" - имеется ли смысл? вообще по каким полям мы отбираем авторов
?>
<html>
<head>
    <title>Справочник: Список авторов</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/jquery-ui-1.10.3.custom.min.js"></script>

    <link rel="stylesheet" type="text/css" href="css/ref.main.css">
    <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.10.3.custom.min.css">
    <link rel="stylesheet" type="text/css" href="css/ref.ui.css">

    <script src="js/core.js"></script>
    <script src="ref_authors/ref.authors.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({cache: false});
            $("#ref_list").load("ref_authors/ref.authors.action.list.php?ref="+ref_name);

            /* вызов и обработчик диалога ADD-ITEM */
            $("#add_item").on('click',function() {
                $('#add_form').dialog('open');
            });
            $( "#add_form" ).dialog({
                autoOpen: false,
                height: 700,
                width: 500,
                y: 100,
                modal: true,
                buttons:[
                    {
                        text: "Добавить автора",
                        click: function() {
                            Authors_CallAddItem(this);
                            $(this).find('form').trigger('reset');
                            $(this).dialog( "close" );
                        }
                    },
                    {
                        text: "Отмена",
                        click: function() {
                            $(this).find('form').trigger('reset');
                            // просто отмена
                            $(this).dialog( "close" );
                        }

                    }
                ],
                close: function() {
                    $(this).find('form').trigger('reset');
                }
            });

            /* вызов и обработчик диалога редактирования */
            $('#ref_list').on('click', '.edit_button', function() {
                button_id = $(this).attr('name');
                Authors_CallLoadItem("#edit_form",button_id);
                $('#edit_form').dialog('open');
            });

            $( "#edit_form" ).dialog({
                autoOpen: false,
                height: 700,
                width: 500,
                y: 100,
                modal: true,
                buttons:[
                    {
                        text: "Принять и обновить данные",
                        click: function() {
                            Authors_CallUpdateItem(this, button_id);
                            $(this).find('form').trigger('reset');
                            $(this).dialog("close");
                        }
                    },
                    {
                        text: "Удалить автора из базы",
                        click: function() {
<?php //@todo: логика УДАЛЕНИЯ с конфирмом ?>
                            Authors_CallRemoveItem(this, button_id);
                            $(this).find('form').trigger('reset');
                            $(this).dialog("close");
                        }
                    }
                ],
                close: function() {
                    $(this).find('form').trigger('reset');
                }
            });
            $("#button-exit").on('click',function(event){
                window.location.href = 'admin.html';
            });

        });
    </script>
</head>
<body>
<button type="button" class="button-large" id="button-exit"><strong>ВЕРНУТЬСЯ В АДМИНКУ</strong></button>
<button id="add_item"  class="button-large">Добавить автора</button><br>

<div id="add_form" title="Добавить автора">
    <form action="ref_authors/ref.authors.action.insert.php">
        <fieldset>
            <label for="add_name_rus">Ф.И.О. (русский)</label>
            <input type="text" name="add_name_rus" id="add_name_rus" class="text ui-widget-content ui-corner-all">

            <label for="add_name_eng">Ф.И.О. (английский)</label>
            <input type="text" name="add_name_eng" id="add_name_eng" class="text ui-widget-content ui-corner-all">

            <label for="add_name_ukr">Ф.И.О. (украинский)</label>
            <input type="text" name="add_name_ukr" id="add_name_ukr" class="text ui-widget-content ui-corner-all">

            <label for="add_email">E-Mail</label>
            <input type="text" name="add_email" id="add_email" value="" class="text ui-widget-content ui-corner-all">

            <label for="add_phone">Телефон для связи</label>
            <input type="text" name="add_phone" id="add_phone" value="" class="text ui-widget-content ui-corner-all">

            <label for="add_title_eng">Звание, ученая степень, должность (eng)</label>
            <input type="text" name="add_title_eng" id="add_title_eng" value="" class="text ui-widget-content ui-corner-all">

            <label for="add_title_rus">Звание, ученая степень, должность</label>
            <input type="text" name="add_title_rus" id="add_title_rus" value="" class="text ui-widget-content ui-corner-all">

            <label for="add_title_ukr">Званна, вчена ступiнь, посада</label>
            <input type="text" name="add_title_ukr" id="add_title_ukr" value="" class="text ui-widget-content ui-corner-all">

            <label for="add_workplace">Место работы</label>
            <textarea name="add_workplace" id="add_workplace" class="text ui-widget-content ui-corner-all" cols="50" rows="5"></textarea>

            <label>Участие в редакционной коллегии:<input type="checkbox" name="add_is_es" id="add_is_es">
            <!-- <select name="add_is_es_selector"><option value="0">Нет</option><option value="1">Да</option></select> -->
            </label>

        </fieldset>
    </form>
</div>
<div id="edit_form" title="Добавить автора">
    <form action="ref_authors/ref.authors.action.update.php">
        <fieldset>
            <label for="edit_name_rus">Ф.И.О. (русский)</label>
            <input type="text" name="edit_name_rus" id="edit_name_rus" class="text ui-widget-content ui-corner-all">

            <label for="edit_name_eng">Ф.И.О. (английский)</label>
            <input type="text" name="edit_name_eng" id="edit_name_eng" class="text ui-widget-content ui-corner-all">

            <label for="edit_name_ukr">Ф.И.О. (украинский)</label>
            <input type="text" name="edit_name_ukr" id="edit_name_ukr" class="text ui-widget-content ui-corner-all">

            <label for="edit_email">Email</label>
            <input type="text" name="edit_email" id="edit_email" value="" class="text ui-widget-content ui-corner-all">

            <label for="edit_phone">Телефон для связи</label>
            <input type="text" name="edit_phone" id="edit_phone" value="" class="text ui-widget-content ui-corner-all">

            <label for="edit_title_eng">Звание, ученая степень, должность (eng)</label>
            <input type="text" name="edit_title_eng" id="edit_title_eng" value="" class="text ui-widget-content ui-corner-all">

            <label for="edit_title_rus">Звание, ученая степень, должность</label>
            <input type="text" name="edit_title_rus" id="edit_title_rus" value="" class="text ui-widget-content ui-corner-all">

            <label for="edit_title_ukr">Званна, вчена ступiнь, посада</label>
            <input type="text" name="edit_title_ukr" id="edit_title_ukr" value="" class="text ui-widget-content ui-corner-all">

            <label for="edit_workplace">Место работы</label>
            <textarea name="edit_workplace" id="edit_workplace" class="text ui-widget-content ui-corner-all" cols="50" rows="5"></textarea>

            <label>Участие в редакционной коллегии:<input type="checkbox" name="edit_is_es" id="edit_is_es">
                <!-- <select name="edit_is_es_selector"><option value="0">Нет</option><option value="1">Да</option></select> -->

            </label>
        </fieldset>
    </form>
</div>

<hr>
<div id="ref_list">
</div>

</body>
</html>
