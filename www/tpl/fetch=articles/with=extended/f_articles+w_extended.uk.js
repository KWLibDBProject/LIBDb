var topicsList = preloadOptionsList('ajax.php?actor=get_topics_as_optionlist&lang=uk');
var booksList = preloadOptionsList('ajax.php?actor=get_books_as_optionlist&lang=uk');
var lettersList = preloadOptionsList('ajax.php?actor=get_letters_as_optionlist&lang=uk');

BuildSelector('letter', lettersList, 0);
BuildSelector('book', booksList, 0);
BuildSelector('topic', topicsList, 0);

url = "ajax.php?actor=load_articles_by_query&lang=uk";

// показ всех статей сразу будет несколько накладным
// $("#articles_list").empty().load(url_a);

// если хэш установлен - нужно загрузить статьи согласно выбранным позициям
wlh = (window.location.hash).substr(1);
if (wlh !== '') {
    $("#articles_list").empty().load(url+'&'+wlh);
}

$("#button-show-withselection").on('click',function(){
    query = "";
    query+="&topic="+$('select[name="topic"]').val();
    query+="&book="+$('select[name="book"]').val();
    query+="&letter="+$('select[name="letter"]').val();
    $("#articles_list").empty().load(url+query);
});
$("#button-reset-selection").on('click',function(){
    $('select[name="letter"]').val(0);
    $('select[name="topic"]').val(0);
    $('select[name="book"]').val(0);
    setHashBySelectors();
    // сброс хэша!
});
$("#button-show-all").on('click',function(){
    $("#articles_list").empty().load(url);
});

$('#articles_list').on('click','.more_info',function(){
    location.href = '?fetch=articles&with=info&lang=uk&id='+$(this).attr('name');
});
