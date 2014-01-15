<?php
require_once('core.php');
require_once('core.db.php');
require_once('core.kwt.php');

?>
<html>
<head>
    <title>Справочник: Список книг (сбоников)</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/jquery-ui-1.10.3.custom.min.js"></script>
    <script src="js/jquery.ui.datepicker.rus.js"></script>

    <link rel="stylesheet" type="text/css" href="css/ref.main.css">
    <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.10.3.custom.min.css">
    <link rel="stylesheet" href="css/ref.ui.css">

    <script src="js/core.js"></script>
    <script src="ref_books/ref.books.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({cache: false});
            $("#ref_list").load("ref_books/ref.books.action.list.php?ref="+ref_name);

            /* вызов и обработчик диалога ADD-ITEM */
            $("#add_item").on('click',function() {
                $('#add_form').dialog('open');
            });

            $( "#add_form" ).dialog({
                autoOpen: false,
                height: 500,
                width: 500,
                y: 100,
                modal: true,
                buttons:[
                    {
                        text: "Добавить сборник",
                        click: function() {
                            Books_CallAddItem(this);
                            $(this).find('form').trigger('reset');
                            // логика добавления
                            $( this ).dialog( "close" );
                        }
                    },
                    {
                        text: "Отмена",
                        click: function() {
                            $(this).find('form').trigger('reset');
                            // просто отмена
                            $( this ).dialog( "close" );
                        }

                    },
                    {
                        text: "Сброс",
                        click: function() {
                            $(this).find('form').trigger('reset');
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
                Books_CallLoadItem("#edit_form",button_id);
                $('#edit_form').dialog('open');
            });
            $( "#edit_form" ).dialog({
                autoOpen: false,
                height: 500,
                width: 500,
                y: 100,
                modal: true,
                buttons:[
                    {
                        text: "Принять и обновить данные",
                        click: function() {
                            Books_CallUpdateItem(this, button_id);
                            $(this).find('form').trigger('reset');
                            $( this ).dialog("close");
                        }
                    },
                    {
                        text: "Удалить сборник из базы",
                        click: function() {
                            //@todo: логика УДАЛЕНИЯ с конфирмом
                            Books_CallRemoveItem(this, button_id);
                            $(this).find('form').trigger('reset');
                            $( this ).dialog("close");
                        }
                    }
                ],
                close: function() {
                    $(this).find('form').trigger('reset');
                }
            });

            $('.datepicker').datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'dd/mm/yy',
                minDate: '01/01/2003',
                maxDate: '01/01/2020',
                showButtonPanel: true,
                showOn: "both",
                buttonImageOnly: true,
                buttonImage: "css/images/calendar.gif",
                duration: ''
            });
            $("#button-exit").on('click',function(event){
                window.location.href = 'admin.html';
            });

        });
    </script>
</head>
<body>
<button type="button" class="button-large" id="button-exit"><strong>ВЕРНУТЬСЯ В АДМИНКУ</strong></button>
<button id="add_item"  class="button-large">Добавить сборник</button><br>

<div id="add_form" title="Добавить cборник">
    <form action="ref_books/ref.books.action.insert.php">
        <fieldset>
            <label for="add_title">Название:</label>
            <input type="text" name="add_title" id="add_title" class="text ui-widget-content ui-corner-all">

            <label for="add_datepicker">Дата (год) выпуска:</label>
            <input type="text" class="datepicker" id="add_datepicker" name="add_date">

            <label for="add_contentpages">Страницы со статьями:</label>
            <input type="text" name="add_contentpages" id="add_contentpages" class="text ui-widget-content ui-corner-all">

            <label>
                Выпущен ли сборник:
                <select name="add_is_book_ready"><option value="0">Нет (в работе)</option><option value="1">Да (опубликован)</option></select>
            </label>

        </fieldset>
    </form>
</div>
<div id="edit_form" title="Изменить сборник">
    <form action="ref_books/ref.books.action.update.php">
        <fieldset>
            <label for="edit_title">Название:</label>
            <input type="text" name="edit_title" id="edit_title" class="text ui-widget-content ui-corner-all">

            <label for="edit_datepicker">Дата (год) выпуска:</label>
            <input type="text" class="datepicker ui-widget-content ui-corner-all" id="edit_datepicker" name="edit_date">

            <label for="edit_contentpages">Страницы со статьями:</label>
            <input type="text" name="edit_contentpages" id="edit_contentpages" class="text ui-widget-content ui-corner-all">

            <label>
                Выпущен ли сборник:
                <select name="edit_is_book_ready"><option value="0">Нет (в работе)</option><option value="1">Да (опубликован)</option></select>
            </label>
        </fieldset>
     </form>
</div>

<hr>
<div id="ref_list">
</div>

</body>
</html>
