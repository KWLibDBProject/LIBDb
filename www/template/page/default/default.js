// var siteLanguage = '&lang={*site_language*}';

// attach lightbox event to image above lightbox area
$(".books-extended-info").on('click','.lightbox-image',function(){
    $.colorbox({
        photo: true,
        href: $(this).attr('href')
    });
    return false;
});