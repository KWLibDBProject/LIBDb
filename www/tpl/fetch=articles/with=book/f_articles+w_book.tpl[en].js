/* Загрузка статей из заданного сборнику с выбором топика из селекта  */
var topicsList = preloadOptionsList('ajax.php?actor=get_topics_as_optionslist&lang=en');

BuildSelector('select_with_topic', topicsList, 0);

url_q = "ajax.php?actor=load_articles_selected_by_query&lang=en&book="/*plus_book_id*/;
url_a = "ajax.php?actor=load_articles_all&lang=en&book="/*plus_book_id*/;

// возможно, что показ всех статей сразу будет несколько накладным
$("#articles_list").empty().load(url_a);

$("#button-show-withselection").on('click',function(){
    query = "&";
    query+="topic="+$('select[name="select_with_topic"]').val();
    $("#articles_list").empty().load(url_q+query);
});

$("#button-reset-selection").on('click',function(){
    $('select[name="select_with_topic"]').val(0);
});

$("#button-show-all").on('click',function(){
    $("#articles_list").empty().load(url_a);
});

$('#articles_list').on('click','.more_info',function(){
    location.href = '?fetch=articles&with=info&id='+$(this).attr('name');

});