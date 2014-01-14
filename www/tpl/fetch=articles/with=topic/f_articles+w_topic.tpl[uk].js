// var topic_id = {%topic_id%};
/* Загрузка статей по определенному топику И сборнику (book) из селекта */
var booksList = preloadOptionsList('core/ref_books/ref.books.action.getoptionlist.php?lang=en&withoutid');

BuildSelector('select_with_book', booksList, 0);

url = "core/ajax.frontend.php?actor=load_articles_selected_by_query&lang=en&topic="/*plus_topic_id*/;

// возможно, что показ всех статей сразу будет несколько накладным
$("#articles_list").empty().load(url);

$("#button-show-withselection").on('click',function(){
    query = "&";
    query+="book="+$('select[name="select_with_book"]').val();
    $("#articles_list").empty().load(url+query);
});

$("#button-reset-selection").on('click',function(){
    $('select[name="select_with_book"]').val(0);
});


$("#button-show-all").on('click',function(){
    $("#articles_list").empty().load(url);
});


$('#articles_list').on('click','.more_info',function(){
    location.href = '?fetch=articles&with=info&id='+$(this).attr('name');

});