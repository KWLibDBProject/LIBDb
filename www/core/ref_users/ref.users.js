var ref_name = "users";
var button_id = 0;
function Users_CallAddItem(source)
{
    bValid = true;
    var $form = $(source).find('form');
    url = $form.attr("action");
    //@todo: validate user information! - вопрос только, что именно нужно валидировать? Емейл?
    var posting = $.post(url, {
        name: $form.find("input[name='add_name']").val(),
        ref_name: ref_name,
        email: $form.find("input[name='add_email']").val(),
        permissions: $form.find("input[name='add_permissions']").val(),
        login: $form.find("input[name='add_login']").val(),
        password: $form.find("input[name='add_password']").val()
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
function Users_CallLoadItem(destination, id) // номер записи, целевая форма
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

function Users_CallUpdateItem(source, id)
{
    var $form = $(source).find('form');
    url = $form.attr("action");
    var posting = $.post(url, {
        name: $form.find("input[name='edit_name']").val(),
        ref_name: ref_name,
        email: $form.find("input[name='edit_email']").val(),
        permissions: $form.find("input[name='edit_permissions']").val(),
        login: $form.find("input[name='edit_login']").val(),
        password: $form.find("input[name='edit_password']").val(),
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
function Users_CallRemoveItem(target, id)
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
            // alert about error (try delete admin)
            $( target ).dialog( "close" );
        }
    });

}
