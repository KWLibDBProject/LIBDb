<?php
define('__ACCESS_MODE__', 'admin');
require_once '__required.php'; // $mysqli_link

?>
<html>
<head>
    <title>Справочник: Тематические разделы</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="_assets/jquery-1.10.2.min.js"></script>
    <script src="_assets/jquery-ui-1.10.3.custom.min.js"></script>
    <link rel="stylesheet" type="text/css" href="_assets/jquery-ui-1.10.3.custom.min.css">
    <link rel="stylesheet" type="text/css" href="_assets/core.ui.css">

    <link rel="stylesheet" type="text/css" href="_assets/core.admin.css">

    <script src="core.topics/topics.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({cache: false});
            $("#topics_list").load("core.topics/topics.action.list.php?ref="+ref_name);

            /* вызов и обработчик диалога ADD-ITEM */
            $("#actor-add").on('click',function() {
                $('#add_form').dialog('open');
            });

            $( "#add_form" ).dialog({
                autoOpen: false,
                height: 500,
                width: 600,
                y: 100,
                modal: true,
                buttons:[
                    {
                        text: "Добавить",
                        click: function() {
                            Topics_CallAddItem(this, "#topics_list");
                            $(this).find('form').trigger('reset');
                            // логика добавления
                            $( this ).dialog( "close" );
                        }
                    },
                    {
                        text: "Сброс",
                        click: function() {
                            $(this).find('form').trigger('reset');
                        }
                    },
                    {
                        text: "Отмена",
                        click: function() {
                            $(this).find('form').trigger('reset');
                            // просто отмена
                            $( this ).dialog( "close" );
                        }

                    }
                ],
                close: function() {
                    $(this).find('form').trigger('reset');
                }
            });

            /* вызов и обработчик диалога редактирования */

            $('#topics_list').on('click', '.actor-edit', function() {
                button_id = $(this).attr('name');
                Topics_CallLoadItem("#edit_form",button_id);
                $('#edit_form').dialog('open');
            });

            $( "#edit_form" ).dialog({
                autoOpen: false,
                height: 500,
                width: 600,
                y: 100,
                modal: true,
                buttons:[
                    {
                        text: "Принять и обновить данные",
                        click: function() {
                            Topics_CallUpdateItem(this, button_id, "#topics_list");
                            $(this).find('form').trigger('reset');
                            $( this ).dialog("close");
                        }
                    },
                    {
                        text: "Удалить тематический раздел",
                        click: function() {
                            Topics_CallRemoveItem(this, button_id, "#topics_list");
                            $(this).find('form').trigger('reset');
                            $( this ).dialog("close");
                        }
                    }
                ],
                close: function() {
                    $(this).find('form').trigger('reset');
                }
            });

            $("#actor-exit").on('click',function(event){
                window.location.href = '/core/';
            });

        });
    </script>
</head>
<body>
<button type="button" id="actor-exit" class="button-large"><strong><<< НАЗАД </strong></button>
<button type="button" id="actor-add"  class="button-large">Добавить тематический раздел</button><br>

<div id="add_form" title="Добавить тематический раздел">
    <form action="core.topics/topics.action.insert.php">
        <fieldset>
            <label for="add_title_en">Topic: </label>
            <input type="text" name="add_title_en" id="add_title_en" class="text ui-widget-content ui-corner-all">
            <label for="add_title_ru">Тематический раздел:</label>
            <input type="text" name="add_title_ru" id="add_title_ru" class="text ui-widget-content ui-corner-all">
            <label for="add_title_ua">Тематичний розділ:</label>
            <input type="text" name="add_title_ua" id="add_title_ua" class="text ui-widget-content ui-corner-all">
            <label for="add_group">Относится к группе №</label>
            <input type="text" name="add_group" id="add_group" class="text ui-widget-content ui-corner-all">
        </fieldset>
        Пожалуйста, вводите тематические разделы маленькими (строчными) буквами. Помните, на сайте они все равно выведутся большими (прописными)!
    </form>
</div>
<div id="edit_form" title="Изменить тематический раздел">
    <form action="core.topics/topics.action.update.php">
        <fieldset>
            <label for="edit_title_en">Topic: </label>
            <input type="text" name="edit_title_en" id="edit_title_en" class="text ui-widget-content ui-corner-all">
            <label for="edit_title_ru">Тематический раздел:</label>
            <input type="text" name="edit_title_ru" id="edit_title_ru" class="text ui-widget-content ui-corner-all">
            <label for="edit_title_ua">Тематичний розділ:</label>
            <input type="text" name="edit_title_ua" id="edit_title_ua" class="text ui-widget-content ui-corner-all">
            <label for="edit_group">Относится к группе №</label>
            <input type="text" name="edit_group" id="edit_group" class="text ui-widget-content ui-corner-all">
        </fieldset>
        Пожалуйста, вводите тематические разделы маленькими (строчными) буквами. Помните, на сайте они все равно выведутся большими (прописными)!
     </form>
</div>

<hr>
<fieldset class="result-list table-hl-rows">
    <div id="topics_list" class="list-limited-height">
    </div>
</fieldset>

</body>
</html>