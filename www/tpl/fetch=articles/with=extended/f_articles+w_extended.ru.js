var siteLanguage = '&lang=ru';
var topicsList = preloadOptionsList('ajax.php?actor=get_topics_as_optionlist'+siteLanguage);
var booksList = preloadOptionsList('ajax.php?actor=get_books_as_optionlist'+siteLanguage);
var lettersList = preloadOptionsList('ajax.php?actor=get_letters_as_optionlist'+siteLanguage);

BuildSelector('letters', lettersList, 0);
BuildSelector('books', booksList, 0);
BuildSelector('topics', topicsList, 0);

url = "ajax.php?actor=load_articles_by_query"+siteLanguage;

// показ всех статей сразу будет несколько накладным
// $("#articles_list").empty().load(url_a);

// если хэш установлен - нужно загрузить статьи согласно выбранным позициям
wlh = (window.location.hash).substr(1);
if (wlh !== '') {
    $("#articles_list").empty().load(url+'&'+wlh);
}

$("#button-show-withselection").on('click',function(){
    if ($('#button-toggle-search').attr('data-searchmode') == 'extended')
    {
        query = "";
        query+="&topic="+$('select[name="topics"]').val();
        query+="&book="+$('select[name="books"]').val();
        query+="&letter="+$('select[name="letters"]').val();
        $("#articles_list").empty().load(url+query);
    } else {
        alert('Expert search is not implemented!');
    }
});
$("#button-reset-selection").on('click',function(){
    $('select[name="letters"]').val(0);
    $('select[name="topics"]').val(0);
    $('select[name="books"]').val(0);
    setHashBySelectors(); // сброс хэша!
});
$("#button-show-all").on('click',function(){
    $("#articles_list").empty().load(url);
});

$('#articles_list').on('click','.more_info',function(){
    location.href = '?fetch=articles&with=info&id='+$(this).attr('name')+siteLanguage;
});

// onload
$('#expert_search').hide();
$('#button-show-extended').hide();

// extended/expert toggle
$("#button-toggle-search").on('click',function(){
    $(this).attr('data-searchmode', $(this).attr('data-searchmode') == 'expert' ? 'extended' : 'expert' );
    $(this).html( ($(this).attr('data-searchmode') == 'expert') ? '<<< LESS Search criteria' : 'MORE Search criteria >>>' );
    $('#extended_search').toggle();
    $('#expert_search').toggle();
});