<html>
<head>
    <title>Справочник: Список пользователей</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/jquery-ui-1.10.3.custom.min.js"></script>

    <link rel="stylesheet" type="text/css" href="css/ref.main.css">
    <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.10.3.custom.min.css">
    <link rel="stylesheet" href="css/ref.ui.css">

    <script type="text/javascript">
        var ref_name = "users";
        var button_id = 0;
        $(document).ready(function () {
            function CallAddItem(source)
            {
                bValid = true;
                var $form = $(source).find('form');
                url = $form.attr("action");
                //@todo: validate!
                f_name = $form.find("input[name='add_name']").val();
                f_email = $form.find("input[name='add_email']").val();
                f_permissions = $form.find("input[name='add_permissions']").val();
	        f_login = $form.find("input[name='add_login']").val();
        	f_password = $form.find("input[name='add_password']").val();
                var posting = $.post(url, {
                    name: f_name,
                    ref_name: ref_name,
                    email: f_email,
                    permissions: f_permissions,
	            login: f_login,
	            password: f_password
                } );
                posting.done(function(data){

                    result = $.parseJSON(data);
                    if (result['error']==0) { // update list
                        $("#ref_list").empty().load("ref_users/ref.users.action.list.php?ref="+ref_name);
                        $( source ).dialog( "close" );
                    } else {
                        // Some errors, show message!
                        $( source ).dialog( "close" );
                    }
                });
            }
            function CallLoadItem(destination, id) // номер записи, целевая форма
            {
                url = 'ref_users/ref.users.action.getitem.php';
                var getting = $.get(url, {
                    id: id,
                    ref: ref_name
                });

                var $form = $(destination).find('form');

                getting.done(function(data){
                    result = $.parseJSON(data);
                    if (result['error'] == 0) {
                        // загружаем данные в поля формы
                        $form.find("input[name='edit_name']").val( result['data']['name'] );
                        $form.find("input[name='edit_email']").val( result['data']['email'] );
                        $form.find("input[name='edit_permissions']").val( result['data']['permissions'] );
    	        	$form.find("input[name='edit_login']").val( result ['data']['login'] );
            		$form.find("input[name='edit_password']").val( result ['data']['password'] );
                    } else {
                        // ошибка загрузки
                    }
                });
            }

            function CallUpdateItem(source, id)
            {
                var $form = $(source).find('form');
                url = $form.attr("action");
                f_name = $form.find("input[name='edit_name']").val();
                f_email = $form.find("input[name='edit_email']").val();
                f_permissions = $form.find("input[name='edit_permissions']").val();
	        f_login = $form.find("input[name='edit_login']").val();
	       	f_password = $form.find("input[name='edit_password']").val();
                var posting = $.post(url, {
                    name: f_name,
                    ref_name: ref_name,
                    email: f_email,
                    permissions: f_permissions,
	            login: f_login,
	            password: f_password,
                    id: id
                } );
                posting.done(function(data){
                    result = $.parseJSON(data);
                    if (result['error']==0) { // update list
                        $("#ref_list").empty().load("ref_users/ref.users.action.list.php?ref="+ref_name);
                        $( source ).dialog( "close" );
                    } else {
                        // Some errors, show message!
                        $( source ).dialog( "close" );
                    }
                });
            }
            function CallRemoveItem(target, id)
            {
                url = 'ref_users/ref.users.action.removeitem.php?ref='+ref_name;
                var getting = $.get(url, {
                    ref_name: ref_name,
                    id: id
                });
                getting.done(function(data){
                    result = $.parseJSON(data);
                    if (result['error'] == 0) {
                        $('#ref_list').empty().load("ref_users/ref.users.action.list.php?ref="+ref_name);
                        $( target ).dialog( "close" );
                    } else {
                        $( target ).dialog( "close" );
                    }
                });

            }

            $("#ref_list").load("ref_users/ref.users.action.list.php?ref="+ref_name);

            /* вызов и обработчик диалога ADD-ITEM */
            $("#add_item").on('click',function() {
                $('#add-form').dialog('open');
            });
            $( "#add-form" ).dialog({
                autoOpen: false,
                height: 500,
                width: 500,
                y: 100,
                modal: true,
                buttons:[
                    {
                        text: "Добавить пользователя",
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
                        text: "Удалить пользователя из базы",
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
            $("#button-exit").on('click',function(event){
                window.location.href = 'admin.html';
                return false;
            });

        });
    </script>
</head>
<body>
<button type="button" class="button-large" id="button-exit"><strong>ВЕРНУТЬСЯ В АДМИНКУ</strong></button>
<div id="add-form" title="Добавить пользователя">
    <form action="ref_users/ref.users.action.add.php">
        <fieldset>
            <label for="add_name">Ф.И.О. (полностью)</label>
            <input type="text" name="add_name" id="add_name" class="text ui-widget-content ui-corner-all">
            <label for="add_email">Email</label>
            <input type="text" name="add_email" id="add_email" value="" class="text ui-widget-content ui-corner-all">
            <label for="add_permissions">Права</label>
            <input type="text" name="add_permissions" id="add_permissions" value="" class="text ui-widget-content ui-corner-all">
	        <label for="add_login">Имя пользователя</label>
	        <input type="text" name="add_login" id="add_login" value ="" class="text ui-widget-content ui-corner-all">
	        <label for="add_password">Пароль</label>
	        <input type="text" name="add_password" id="add_password" value ="" class="text ui-widget-content ui-corner-all">
        </fieldset>
    </form>
</div>
<div id="edit_form" title="Изменить пользователя">
    <form action="ref_users/ref.users.action.update.php">
        <fieldset>
            <label for="edit_name">Ф.И.О. (полностью)</label>
            <input type="text" name="edit_name" id="edit_name" class="text ui-widget-content ui-corner-all">
            <label for="edit_email">Email</label>
            <input type="text" name="edit_email" id="edit_email" value="" class="text ui-widget-content ui-corner-all">
            <label for="edit_permissions">Права</label>
            <input type="text" name="edit_permissions" id="edit_permissions" value="" class="text ui-widget-content ui-corner-all">
	    <label for="edit_login">Имя пользователя</label>
	    <input type="text" name="edit_login" id="edit_login" value="" class="text ui-widget-content ui-corner-all">
	    <label for="edit_password">Пароль</label>
	    <input type="text" name="edit_password" id="edit_password" value="" class="text ui-widget-content ui-corner-all">
        </fieldset>
    </form>
</div>



<button id="add_item" class="button-large">Добавить пользователя</button><br>

<hr>
<div id="ref_list">
</div>

</body>
</html>