var ref_name = "authors";
var button_id = 0;

function Authors_CallAddItem(source)
{
    var $form = $(source).find('form');
    url = $form.attr("action");
    var posting = $.post(url, {
        name_rus: $form.find("input[name='add_name_rus']").val(),
        name_eng: $form.find("input[name='add_name_eng']").val(),
        name_ukr: $form.find("input[name='add_name_ukr']").val(),
        title_eng : $form.find("input[name='add_title_eng']").val(),
        title_rus : $form.find("input[name='add_title_rus']").val(),
        title_ukr : $form.find("input[name='add_title_ukr']").val(),
        workplace: $form.find("textarea[name='add_workplace']").val(),
        ref_name: ref_name,
        email: $form.find("input[name='add_email']").val()
    } );
    posting.done(function(data){

        result = $.parseJSON(data);
        if (result['error']==0) { // update list
            $("#ref_list").empty().load("ref_authors/ref.authors.action.list.php?ref="+ref_name);
            $( source ).dialog( "close" );
        } else {
            // Some errors, show message!
            $( source ).dialog( "close" );
        }
    });
}
function Authors_CallLoadItem(destination, id) // номер записи, целевая форма
{
    url = 'ref_authors/ref.authors.action.getitem.php';
    var getting = $.get(url, {
        id: id,
        ref: ref_name
    });

    var $form = $(destination).find('form');

    getting.done(function(data){
        result = $.parseJSON(data);
        if (result['error'] == 0) {
            // загружаем данные в поля формы
            $form.find("input[name='edit_name_rus']").val( result['data']['name_rus'] );
            $form.find("input[name='edit_name_eng']").val( result['data']['name_eng'] );
            $form.find("input[name='edit_name_ukr']").val( result['data']['name_ukr'] );
            $form.find("input[name='edit_title_rus']").val( result['data']['title_rus'] );
            $form.find("input[name='edit_title_eng']").val( result['data']['title_eng'] );
            $form.find("input[name='edit_title_ukr']").val( result['data']['title_ukr'] );
            $form.find("input[name='edit_email']").val( result['data']['email'] );
            $form.find("textarea[name='edit_workplace']").val(result['data']['workplace']);
        } else {
            // ошибка загрузки
        }
    });
}

function Authors_CallUpdateItem(source, id)
{
    var $form = $(source).find('form');
    url = $form.attr("action");
    var posting = $.post(url, {
        name_rus: $form.find("input[name='edit_name_rus']").val(),
        name_eng: $form.find("input[name='edit_name_eng']").val(),
        name_ukr: $form.find("input[name='edit_name_ukr']").val(),
        title_eng : $form.find("input[name='edit_title_eng']").val(),
        title_rus : $form.find("input[name='edit_title_rus']").val(),
        title_ukr : $form.find("input[name='edit_title_ukr']").val(),
        workplace: $form.find("textarea[name='edit_workplace']").val(),
        ref_name: ref_name,
        email: $form.find("input[name='edit_email']").val(),
        id: id
    } );
    posting.done(function(data){
        result = $.parseJSON(data);
        if (result['error']==0) { // update list
            $("#ref_list").empty().load("ref_authors/ref.authors.action.list.php?ref="+ref_name);
            $( source ).dialog( "close" );
        } else {
            // Some errors, show message!
            $( source ).dialog( "close" );
        }
    });
}
function Authors_CallRemoveItem(target, id)
{
    url = 'ref_authors/ref.authors.action.removeitem.php?ref='+ref_name;
    var getting = $.get(url, {
        ref_name: ref_name,
        id: id
    });
    getting.done(function(data){
        result = $.parseJSON(data);
        if (result['error'] == 0) {
            $('#ref_list').empty().load("ref_authors/ref.authors.action.list.php?ref="+ref_name);
            $( target ).dialog( "close" );
        } else {
            $( target ).dialog( "close" );
        }
    });

}
