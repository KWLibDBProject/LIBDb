var ref_name = "books";
var button_id = 0;

function ShowErrorMessage(message)
{
    alert(message);
}

function Books_CallAddItem(source)
{
    var $form = $(source).find('form');
    url = $form.attr("action");
    var posting = $.post(url, {
        ref_name: ref_name,
        title: $form.find("input[name='add_title']").val(),
        date: $form.find("input[name='add_date']").val(),
        contentpages: $form.find("input[name='add_contentpages']").val(),
        published: $form.find("select[name='edit_is_book_ready'] :selected").val()
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
function Books_CallLoadItem(destination, id) // номер записи, целевая форма
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
            $form.find("input[name='edit_title']").val( result['data']['title'] );
            $form.find("input[name='edit_date']").val( result ['data']['date'] );
            $form.find("input[name='edit_contentpages']").val( result ['data']['contentpages'] );
            $form.find("select[name='edit_is_book_ready'] option[value='"+ result['data']['published'] +"']").prop("selected",true);
        } else {
            // ошибка загрузки
        }
    });
}

function Books_CallUpdateItem(source, id)
{
    var $form = $(source).find('form');
    url = $form.attr("action");

    var posting = $.post(url, {
        ref_name: ref_name,
        id: id,
        title: $form.find("input[name='edit_title']").val(),
        date: $form.find("input[name='edit_date']").val(),
        contentpages: $form.find("input[name='edit_contentpages']").val(),
        published: $form.find("select[name='edit_is_book_ready'] :selected").val()
    } );
    posting.done(function(data){
        result = $.parseJSON(data);
        if (result['error']==0) { // update list
            $("#ref_list").empty().load("ref_books/ref.books.action.list.php?ref="+ref_name);
            $( source ).dialog( "close" );
        } else {
            $( source ).dialog( "close" );
        }
    });
}
function Books_CallRemoveItem(target, id)
{
    url = 'ref_books/ref.books.action.removeitem.php';
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
            // удаление невозможно
            //@todo: JS message error
            ShowErrorMessage(result['message']);
            $( target ).dialog( "close" );
        }
    });

}
