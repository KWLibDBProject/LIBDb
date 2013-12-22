var ref_name = "books";
var button_id = 0;

function Books_CallAddItem(source)
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
        } else {
            // ошибка загрузки
        }
    });
}

function Books_CallUpdateItem(source, id)
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
            $( source ).dialog( "close" );
        }
    });
}
function Books_CallRemoveItem(target, id)
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
