var topicsList = preloadOptionsList('ajax.php?actor=get_topics_as_optionslist&lang=en');
var booksList = preloadOptionsList('ajax.php?actor=get_books_as_optionslist&lang=en');
var lettersList = preloadOptionsList('ajax.php?actor=get_letters_as_optionlist&lang=en');

BuildSelector('select_with_letter', lettersList, 0);
BuildSelector('select_with_book', booksList, 0);
BuildSelector('select_with_topic', topicsList, 0);

url_q = "ajax.php?actor=load_articles_selected_by_query_with_letter&lang=en";
url_a = "ajax.php?actor=load_articles_all&lang=en";

// возможно, что показ всех статей сразу будет несколько накладным
$("#articles_list").empty().load(url_a);

$("#button-show-withselection").on('click',function(){
    query = "";
    query+="&topic="+$('select[name="select_with_topic"]').val();
    query+="&book="+$('select[name="select_with_book"]').val();
    query+="&letter="+$('select[name="select_with_letter"]').val();
    $("#articles_list").empty().load(url_q+query);
});
$("#button-reset-selection").on('click',function(){
    $('select[name="select_with_letter"]').val(0);
    $('select[name="select_with_topic"]').val(0);
    $('select[name="select_with_book"]').val(0);
});
$("#button-show-all").on('click',function(){
    $("#articles_list").empty().load(url_a);
});

$('#articles_list').on('click','.more_info',function(){
    location.href = '?fetch=articles&with=info&lang=en&id='+$(this).attr('name');

});
