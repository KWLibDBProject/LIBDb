/* Загрузка статей из определенного топика с выбором сборника из селекта */

var booksList = preloadOptionsList('ajax.php?actor=get_books_as_optionslist&lang=en');

BuildSelector('book', booksList, 0);

url = "ajax.php?actor=load_articles_selected_by_query&lang=en&topic="/*plus_topic_id*/;

// возможно, что показ всех статей сразу будет несколько накладным
$("#articles_list").empty().load(url);

$("#button-show-withselection").on('click',function(){
    query = "&";
    query+="book="+$('select[name="book"]').val();
    $("#articles_list").empty().load(url+query);
});

$("#button-reset-selection").on('click',function(){
    $('select[name="book"]').val(0);
});


$("#button-show-all").on('click',function(){
    $("#articles_list").empty().load(url);
});


$('#articles_list').on('click','.more_info',function(){
    location.href = '?fetch=articles&with=info&id='+$(this).attr('name');

});