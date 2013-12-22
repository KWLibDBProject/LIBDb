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

    <script type="text/javascript">
        var ref_name = "books";
        var button_id = 0;

        $(document).ready(function () {
            function CallAddItem(source)
            {
                var $form = $(source).find('form');
                url = $form.attr("action");
                f_title = $form.find("input[name='add_title']").val();
                f_date = $form.find("input[name='add_date']").val();
                 var posting = $.post(url, {
                     title: f_title,
                     ref_name: ref_name,
                     date: f_date
                } );
                posting.done(function(data){

                    result = $.parseJSON(data);
                    if (result['error']==0) { // update list
                        $("#ref_list").empty().load("ref_books/ref.books.action.list.php?ref="+ref_name);
                        $( source ).dialog( "close" );
                    } else {
                        // Some errors, show message!
                        $( source ).dialog( "close" );
                    }
                });
            }
            function CallLoadItem(destination, id) // номер записи, целевая форма
            {
                url = 'ref_books/ref.books.action.getitem.php';
                var getting = $.get(url, {
                    id: id,
                    ref: ref_name
                });

                var $form = $(destination).find('form');

                getting.done(function(data){
                    result = $.parseJSON(data);
                    if (result['error'] == 0) {
                        // загружаем данные в поля формы
                    $form.find("input[name='edit_title']").val( result['data']['title'] );
             		$form.find("input[name='edit_date']").val( result ['data']['date'] );
                    } else {
                        // ошибка загрузки
                    }
                });
            }

            function CallUpdateItem(source, id)
            {
                var $form = $(source).find('form');
                url = $form.attr("action");
                f_title = $form.find("input[name='edit_title']").val();
                f_date = $form.find("input[name='edit_date']").val();
                var posting = $.post(url, {
                    title: f_title,
                    ref_name: ref_name,
                    date: f_date,
                    id: id
                } );
                posting.done(function(data){
                    result = $.parseJSON(data);
                    if (result['error']==0) { // update list
                        $("#ref_list").empty().load("ref_books/ref.books.action.list.php?ref="+ref_name);
                        $( source ).dialog( "close" );
                    } else {
                        // Some errors, show message!
                        $( source ).dialog( "close" );
                    }
                });
            }
            function CallRemoveItem(target, id)
            {
                url = 'ref_books/ref.books.action.removeitem.php?ref='+ref_name;
                var getting = $.get(url, {
                    ref_name: ref_name,
                    id: id
                });
                getting.done(function(data){
                    result = $.parseJSON(data);
                    if (result['error'] == 0) {
                        $('#ref_list').empty().load("ref_books/ref.books.action.list.php?ref="+ref_name);
                        $( target ).dialog( "close" );
                    } else {
                        $( target ).dialog( "close" );
                    }
                });

            }

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
                            CallAddItem(this);
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
                CallLoadItem("#edit_form",button_id);
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
                            CallUpdateItem(this, button_id);
                            $(this).find('form').trigger('reset');
                            $( this ).dialog("close");
                        }
                    },
                    {
                        text: "Удалить сборник из базы",
                        click: function() {
                            //@todo: логика УДАЛЕНИЯ с конфирмом

                            CallRemoveItem(this, button_id);
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
                buttonImage: "css/images/calendar.gif"
            });
            $("#button-exit").on('click',function(event){
                window.location.href = 'admin.html';
            });

        });
    </script>
</head>
<body>
<button type="button" class="button-large" id="button-exit"><strong>ВЕРНУТЬСЯ В АДМИНКУ</strong></button>

<div id="add_form" title="Добавить cборник">
    <form action="ref_books/ref.books.action.add.php">
        <fieldset>
            <label for="add_title">Название:</label>
            <input type="text" name="add_title" id="add_title" class="text ui-widget-content ui-corner-all">
            <label for="add_datepicker">Дата:</label>
            <input type="text" class="datepicker" id="add_datepicker" name="add_date">
        </fieldset>
    </form>
</div>
<div id="edit_form" title="Изменить сборник">
    <form action="ref_books/ref.books.action.update.php">
        <fieldset>
            <label for="edit_title">Название:</label>
            <input type="text" name="edit_title" id="edit_title" class="text ui-widget-content ui-corner-all">
            <label for="edit_datepicker">Дата:</label>
            <input type="text" class="datepicker ui-widget-content ui-corner-all" id="edit_datepicker" name="edit_date">
        </fieldset>
     </form>
</div>

<button id="add_item"  class="button-large">Добавить сборник</button><br>

<hr>
<div id="ref_list">
</div>

</body>
</html>
