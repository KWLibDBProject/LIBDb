(function($) {
    // thanx to: http://drupalace.ru/lesson/skript-kotoryy-menyaet-cvet-pervogo-slova-v-stroke
    $.fn.paintFirstWord = function(selection_class) {
        var str = this.html();
        var splited = str.split(' ');
        var replaced = str.split(splited[0]).join('<span class = "' + selection_class + '">' + splited[0] + '</span>');
        this.html(replaced);
    };
})(jQuery);
$('span.authors-estufflist-name').each( function() {
    $(this).paintFirstWord('authors-estufflist-firstword');
});