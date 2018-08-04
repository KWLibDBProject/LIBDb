var siteLanguage = '&lang=ua';
var topicsList = preloadOptionsList('ajax.php?actor=get_topics_as_optgroup_list'+siteLanguage);

BuildSelectorExtended('topics', topicsList, '&nbsp;&nbsp;&nbsp;ANY&nbsp;&nbsp;&nbsp;', 0);

url = "ajax.php?actor=load_articles_by_query&book="/*plus_book_id*/+siteLanguage;

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

// attach lightbox event to image above search area
$(".books-extended-info").on('click','.lightbox-image',function(){
    $.colorbox({
        photo: true,
        href: $(this).attr('href')
    });
    return false;
});