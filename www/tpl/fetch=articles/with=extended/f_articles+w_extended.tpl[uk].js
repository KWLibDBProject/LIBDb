var authorsList = preloadOptionsList('core/ref_authors/ref.authors.action.getoptionlist.php?lang=uk&withoutid');
var booksList = preloadOptionsList('core/ref_books/ref.books.action.getoptionlist.php?lang=uk&withoutid');
var topicsList = preloadOptionsList('core/ref_topics/ref.topics.action.getoptionlist.php?lang=uk&withoutid');

BuildSelector('select_with_author', authorsList, 0);
BuildSelector('select_with_book', booksList, 0);
BuildSelector('select_with_topic', topicsList, 0);

url = "core/ajax.frontend.php?actor=load_articles_selected_by_query&lang=uk";

// возможно, что показ всех статей сразу будет несколько накладным
$("#articles_list").empty().load(url);

$("#button-show-withselection").on('click',function(){
    query = "&";
    query+="author="+$('select[name="select_with_author"]').val();
    query+="&topic="+$('select[name="select_with_topic"]').val();
    query+="&book="+$('select[name="select_with_book"]').val();
    $("#articles_list").empty().load(url+query);
});
$("#button-reset-selection").on('click',function(){
    $('select[name="select_with_author"]').val(0);
    $('select[name="select_with_topic"]').val(0);
    $('select[name="select_with_book"]').val(0);
});
$("#button-show-all").on('click',function(){
    $("#articles_list").empty().load(url);
});

$('#articles_list').on('click','.more_info',function(){
    location.href = '?fetch=articles&with=info&lang=uk&id='+$(this).attr('name');

});
