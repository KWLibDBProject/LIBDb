var siteLanguage = '&lang=ru';
var topicsList = preloadOptionsList('ajax.php?actor=get_topics_as_optionlist'+siteLanguage);

BuildSelector('topics', topicsList, 0);

url = "ajax.php?actor=load_articles_by_query&lang=en&book="/*plus_book_id*/;

// если хэш установлен - нужно загрузить статьи согласно выбранным позициям
// тут нам лишний if не нужен, мы на старте загружаем все статьи
wlh = (window.location.hash).substr(1);
$("#articles_list").empty().load(url+'&'+wlh); // передача лишнего & запросу не вредит.

$("#button-show-withselection").on('click',function(){
    query = "";
    query+="&topic="+$('select[name="topics"]').val();
    $("#articles_list").empty().load(url+query);
});
$("#button-reset-selection").on('click',function(){
    $('select[name="topics"]').val(0);
    setHashBySelectors(); // сброс хэша!
});
$("#button-show-all").on('click',function(){
    $("#articles_list").empty().load(url);
});

$('#articles_list').on('click','.more_info',function(){
    location.href = '?fetch=articles&with=info&id='+$(this).attr('name')+siteLanguage;
});
