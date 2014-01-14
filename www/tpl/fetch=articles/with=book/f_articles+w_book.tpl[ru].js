// var book_id = {%book_id%} ;
/* Загрузка статей по определенному сборнику И топику (topic) из селекта */

var booksList = preloadOptionsList('core/ref_topics/ref.topics.action.getoptionlist.php?lang=ru&withoutid');

BuildSelector('select_with_topic', booksList, 0);

url = "core/ajax.frontend.php?actor=load_articles_selected_by_query&lang=ru&book="/*plus_book_id*/;

// возможно, что показ всех статей сразу будет несколько накладным
$("#articles_list").empty().load(url);

$("#button-show-withselection").on('click',function(){
    query = "&";
    query+="topic="+$('select[name="select_with_topic"]').val();
    $("#articles_list").empty().load(url+query);
});

$("#button-reset-selection").on('click',function(){
    $('select[name="select_with_topic"]').val(0);
});

$("#button-show-all").on('click',function(){
    $("#articles_list").empty().load(url);
});

$('#articles_list').on('click','.more_info',function(){
    location.href = '?fetch=articles&with=info&id='+$(this).attr('name');

});