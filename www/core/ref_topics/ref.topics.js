var ref_name = "topics";
var button_id = 0;
function CallErrorMessage(message)
{
    alert(message);
}
function Topics_CallAddItem(source)
{
    var $form = $(source).find('form');
    url = $form.attr("action");
    var posting = $.post(url, {
        title: $form.find("input[name='add_title']").val(),
        ref_name: ref_name,
        shortname: $form.find("input[name='add_shortname']").val()
    } );
    posting.done(function(data){

        result = $.parseJSON(data);
        if (result['error']==0) { // update list
            $("#ref_list").empty().load("ref_topics/ref.topics.action.list.php?ref="+ref_name);
            $( source ).dialog( "close" );
        } else {
            // Some errors, show message!
            $( source ).dialog( "close" );
        }
    });
}
function Topics_CallLoadItem(destination, id) // номер записи, целевая форма
{
    url = 'ref_topics/ref.topics.action.getitem.php';
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
            $form.find("input[name='edit_shortname']").val( result ['data']['shortname'] );
        } else {
            // ошибка загрузки
        }
    });
}
function Topics_CallUpdateItem(source, id)
{
    var $form = $(source).find('form');
    url = $form.attr("action");
    var posting = $.post(url, {
        title: $form.find("input[name='edit_title']").val(),
        ref_name: ref_name,
        shortname: $form.find("input[name='edit_shortname']").val(),
        id: id
    } );
    posting.done(function(data){
        result = $.parseJSON(data);
        if (result['error']==0) { // update list
            $("#ref_list").empty().load("ref_topics/ref.topics.action.list.php?ref="+ref_name);
            $( source ).dialog( "close" );
        } else {
            // Some errors, show message!
            $( source ).dialog( "close" );
        }
    });
}
function Topics_CallRemoveItem(target, id)
{
    url = 'ref_topics/ref.topics.action.removeitem.php?ref='+ref_name;
    var getting = $.get(url, {
        ref_name: ref_name,
        id: id
    });
    getting.done(function(data){
        result = $.parseJSON(data);
        if (result['error'] == 0) {
            $('#ref_list').empty().load("ref_topics/ref.topics.action.list.php?ref="+ref_name);
            $( target ).dialog( "close" );
        } else {
            CallErrorMessage("Невозможно удалить топик, вероятно в нем есть статьи!");
            $( target ).dialog( "close" );
        }
    });

}
