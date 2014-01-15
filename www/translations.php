<?php

$MESSAGES = array(
    'LoadAuthorPublications_NoArticles' => array(
        'ru' => '<div class="articles-list">У автора нет статей</div>',
        'en' => '<div class="articles-list">У автора нет статей</div>',
        'uk' => '<div class="articles-list">У автора нет статей</div>'
    ),
    'LoadArticlesList_SearchNoArticles' => array(
        'ru' => '<br><strong>Статей по заданным критериям поиска не найдено</strong>',
        'en' => '<br><strong>No articles found within this search criteria!</strong>',
        'uk' => '<br><strong>Статей за заданими критеріями пошуку не знайдено</strong>',
    ),
    'LoadArticlesList_OnloadNoArticles' => array(
    'ru' => 'Статей в сборнике нет',
    'en' => 'Articles not found!',
    'uk' => 'Статей в сборнике нет',
    ),
    'LoadAuthorPublications_EachRecord' => array(
        'ru' => '<li><a href="?fetch=articles&with=info&id=%1$s">%2$s</a> "%3$s", %4$s г.</li>',
        'en' => '<li><a href="?fetch=articles&with=info&id=%1$s">%2$s</a> "%3$s", %4$s</li>',
        'uk' => '<li><a href="?fetch=articles&with=info&id=%1$s">%2$s</a></li>',
    ),
    'LoadAuthorPublications_Start' => array(
        'ru' => '<ul>',
        'en' => '<ul>',
        'uk' => '<ul>',
    ),
    'LoadAuthorPublications_End' => array(
        'ru' => '</ul>',
        'en' => '</ul>',
        'uk' => '</ul>',
    ),
    'LoadTopics_Start' => array(
        'ru' => '<ul>',
        'en' => '<ul>',
        'uk' => '<ul>',
    ),
    'LoadTopics_Each' => array(
        'ru' => '<li><a href="?fetch=articles&with=topic&id=%1$s">%2$s</a></li>',
        'en' => '<li><a href="?fetch=articles&with=topic&id=%1$s">%2$s</a></li>',
        'uk' => '<li><a href="?fetch=articles&with=topic&id=%1$s">%2$s</a></li>',
    ),
    'LoadTopics_End' => array(
        'ru' => '</ul>',
        'en' => '</ul>',
        'uk' => '</ul>',
    ),
    'LoadBooks_Start' => array(
        'ru' => '<ul>',
        'en' => '<ul>',
        'uk' => '<ul>',
    ),
    'LoadBooks_ItemStart' => array(
        'ru' => '<li>
        <div>%1$s</div>
        <ul>',
        'en' => '<li>
        <div>%1$s</div>
        <ul>',
        'uk' => '<li>
        <div>%1$s</div>
        <ul>',
    ),
    'LoadBooks_ItemEach' => array(
        'ru' => '<li><a href="?fetch=articles&with=book&id=%1$s">%2$s</a></li>',
        'en' => '<li><a href="?fetch=articles&with=book&id=%1$s">%2$s</a></li>',
        'uk' => '<li><a href="?fetch=articles&with=book&id=%1$s">%2$s</a></li>',
    ),
    'LoadBooks_ItemEnd' => array(
        'ru' => '</ul></li>',
        'en' => '</ul></li>',
        'uk' => '</ul></li>',
    ),
    'LoadBooks_End' => array(
        'ru' => '</ul>',
        'en' => '</ul>',
        'uk' => '</ul>',
    ),
    'LoadArticleInfoAuthorsList' => array(
        'ru' => '<li><a href="?fetch=authors&with=info&id=%4$s">%1$s, %2$s</a> (%3$s) </li>',
        'en' => '<li><a href="?fetch=authors&with=info&id=%4$s">%1$s, %2$s</a> (%3$s) </li>',
        'uk' => '<li><a href="?fetch=authors&with=info&id=%4$s">%1$s, %2$s</a> (%3$s) </li>',
    ),
    'LoadArticlesList_Start' => array(
        'ru' => '<table border="1" width="100%">',
        'en' => '<table border="1" width="100%">',
        'uk' => '<table border="1" width="100%">',
    ),
    'LoadArticlesList_Each' => array(
        'ru' => '<tr>
                <td>%1$s</td>
                <td>%2$s</td>
                <td>%3$s</td>
                <td>%4$s</td>
                <td><button class="more_info" name="%5$s" data-text="More"> Подробнее >>> </button></td>
            </tr>',
        'en' => '<tr>
                <td>%1$s</td>
                <td>%2$s</td>
                <td>%3$s</td>
                <td>%4$s</td>
                <td><button class="more_info" name="%5$s" data-text="More"> Details >>> </button></td>
            </tr>',
        'uk' => '<tr>
                <td>%1$s</td>
                <td>%2$s</td>
                <td>%3$s</td>
                <td>%4$s</td>
                <td><button class="more_info" name="%5$s" data-text="More"> ???? >>> </button></td>
            </tr>',
    ),
    'LoadArticlesList_End' => array(
        'ru' => '</table>',
        'en' => '</table>',
        'uk' => '</table>',
    ),
    'LoadAuthorsSelectedByLetter_Start' => array(
        'ru' => '<ul>',
        'en' => '<ul>',
        'uk' => '<ul>',
    ),
    'LoadAuthorsSelectedByLetter_Each' => array(
        'ru' => '<li>
<label>
<a href="?fetch=authors&with=info&id=%1$s">%2$s , %3$s, %4$s</a>
</label>
</li>
',
        'en' => '<li>
<label>
<a href="?fetch=authors&with=info&id=%1$s">%2$s , %3$s, %4$s</a>
</label>
</li>
',
        'uk' => '<li>
<label>
<a href="?fetch=authors&with=info&id=%1$s">%2$s , %3$s, %4$s</a>
</label>
</li>
',
    ),
    'LoadAuthorsSelectedByLetter_End' => array(
        'ru' => '</ul>',
        'en' => '</ul>',
        'uk' => '</ul>',
    ),
    'LoadAuthorsSelectedByLetter_Nothing' => array(
        'ru' => 'Таких авторов нет',
        'en' => 'Таких авторов нет',
        'uk' => 'Таких авторов нет',
    ),
    '...' => array(
        'ru' => '',
        'en' => '',
        'uk' => '',
    ),

);



?>