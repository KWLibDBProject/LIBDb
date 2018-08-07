/* */
var topic_id = +$("#articles__topic__topic_id").val();
var siteLanguage = '&lang=' + $("#articles__topic__site_language").val();

var booksList = preloadOptionsList('ajax.php?actor=get_books_as_optionlist_extended'+siteLanguage);
var url = "ajax.php?actor=load_articles_by_query&topic=" + topic_id + siteLanguage;

BuildSelectorExtended('books', booksList, 'Choose... ', 0);

// если хэш установлен - можно? загрузить статьи согласно выбранным позициям
// мы на старте загружаем все статьи
wlh = (window.location.hash).substr(1);
$("#articles_list").empty().load(url+'&'+wlh); // передача лишнего & запросу не вредит.

$("#button-show-withselection").on('click',function(){
    query = "";
    query+="&book="+$('select[name="books"]').val();
    $("#articles_list").empty().load(url+query);
});
$("#button-reset-selection").on('click',function(){
    $('select[name="books"]').val(0);
    setHashBySelectors(); // сброс хэша!
});
$("#button-show-all").on('click',function(){
    $("#articles_list").empty().load(url);
});

$('#articles_list').on('click','.more_info',function(){
    location.href = '?fetch=articles&with=info&id='+$(this).attr('name')+siteLanguage;
});
