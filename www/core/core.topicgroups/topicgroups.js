var ref_name = "topicgroups";
var button_id = 0;

function ShowErrorMessage(message)
{
    alert(message);
}

function Topicgroups_CallAddItem(source, result_area)
{
    var $form = $(source).find('form');
    url = $form.attr("action");
    var posting = $.post(url, {
        title_en: $form.find("input[name='add_title_en']").val(),
        title_ru: $form.find("input[name='add_title_ru']").val(),
        title_ua: $form.find("input[name='add_title_ua']").val(),
        display_order: $form.find("input[name='add_display_order']").val(),
        ref_name: ref_name
    } );
    posting.done(function(data){
        result = $.parseJSON(data);
        if (result['error']==0) { // update list
            $(result_area).empty().load("core.topicgroups/topicgroups.action.list.php?ref="+ref_name);
            $( source ).dialog( "close" );
        } else {
            $( source ).dialog( "close" );
        }
    });
}

function Topicgroups_CallLoadItem(destination, id) // номер записи, целевая форма
{
    url = 'core.topicgroups/topicgroups.action.getitem.php';
    var getting = $.get(url, {
        id: id,
        ref: ref_name
    });

    var $form = $(destination).find('form');

    getting.done(function(data){
        result = $.parseJSON(data);
        if (result['error'] == 0) {
            // загружаем данные в поля формы
            $form.find("input[name='edit_title_en']").val( result['data']['title_en'] );
            $form.find("input[name='edit_title_ru']").val( result['data']['title_ru'] );
            $form.find("input[name='edit_title_ua']").val( result['data']['title_ua'] );
            $form.find("input[name='edit_display_order']").val( result['data']['display_order'] );
        } else {
            // ошибка загрузки
        }
    });
}

function Topicgroups_CallUpdateItem(source, id, result_area)
{
    var $form = $(source).find('form');
    url = $form.attr("action");
    var posting = $.post(url, {
        title_en: $form.find("input[name='edit_title_en']").val(),
        title_ru: $form.find("input[name='edit_title_ru']").val(),
        title_ua: $form.find("input[name='edit_title_ua']").val(),
        display_order: $form.find("input[name='edit_display_order']").val(),
        ref_name: ref_name,
        id: id
    } );
    posting.done(function(data){
        result = $.parseJSON(data);
        if (result['error']==0) { // update list
            $(result_area).empty().load("core.topicgroups/topicgroups.action.list.php?ref="+ref_name);
            $( source ).dialog( "close" );
        } else {
            // Some errors, show message!
            $( source ).dialog( "close" );
        }
    });
}

function Topicgroups_CallRemoveItem(target, id, result_area)
{
    url = 'core.topicgroups/topicgroups.action.removeitem.php';
    var getting = $.get(url, {
        id: id,
        ref_name: ref_name
    });
    getting.done(function(data){
        result = $.parseJSON(data);
        if (result['error'] == 0) {
            $(result_area).empty().load("core.topicgroups/topicgroups.action.list.php");
            $( target ).dialog( "close" );
        } else {
            // удаление невозможно
            ShowErrorMessage(result['message']); // Невозможно удалить группу топиков, вероятно в нем есть статьи!
            $( target ).dialog( "close" );
        }
    });

}
