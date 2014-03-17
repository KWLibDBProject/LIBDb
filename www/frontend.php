<?php
require_once('core/core.kwt.php');

/*
Префикс DB_ имеют функции загрузки данных из базы
Префикс FE_ имеют функции оформления данных на вывод
Служебные функции префикса не имеют.
*/

/* функция загрузки статических страниц из БД */
function FE_GetStaticPage($alias, $lang)
{
    $return = '';

    $query = "SELECT content_{$lang} AS pagecontent FROM staticpages WHERE alias LIKE '{$alias}'";
    $res = mysql_query($query);
    $numrows = mysql_num_rows($res);

    if ($numrows == 1) {
        $a = mysql_fetch_assoc($res);
        $return = $a['pagecontent'];
    } else {
        $html404 = new kwt('tpl/page404.html');
        $html404->contentstart();
        $return = $html404->getcontent();
    }
    return $return;
}



/* загружает из базы рубрики, отдает ассоциативный массив вида [id -> title] */
function DB_LoadTopics($lang)
{
    global $MESSAGES;
    $q = "SELECT `id`, `title_{$lang}` AS title FROM topics WHERE `deleted`=0";
    $r = mysql_query($q);
    $ret = array();

    if (@mysql_num_rows($r) > 0)
    {
        while ($topic = mysql_fetch_assoc($r)) {
            $ret[ $topic['id']  ] = $topic['title'];
        }
    }
    return $ret;
}

/* Выводит рубрики согласно правилам в translations_.php
Принимает ассоциативный массив с данными и язык вывода */
function FE_PrintTopics($data, $lang)
{
    global $MESSAGES;
    $ret = '';
    $ret .= <<<FE_PrintTopics_Start
FE_PrintTopics_Start;

    foreach ($data as $id => $title )
    {
        $ret .= <<<FE_PrintTopics_Each
<li><a href="?fetch=articles&with=topic&id={$id}">{$title}</a></li>
FE_PrintTopics_Each;
    }

    $ret .= <<<FE_PrintTopics_End
FE_PrintTopics_End;
;
    return $ret;
}

/* загружает список сборников (книг) из базы, года в обратном порядке, сборники в прямом*/
function DB_LoadBooks($lang)
{
    $yq = "SELECT DISTINCT `year` FROM books WHERE `published`=1 AND `deleted`=0 ORDER BY `year` DESC";

    $yr = mysql_query($yq);
    $all_books = array();
    while ($ya = mysql_fetch_assoc($yr)) {
        $all_books[ $ya['year'] ] = array();
    }
    foreach ($all_books as $year => $value)
    {
        $bq = "SELECT books.title, books.year, books.id, COUNT(books.id) AS articles_count
FROM books, articles
WHERE
articles.book = books.id AND
books.year = $year AND
books.published = 1 AND
books.deleted = 0
GROUP BY books.title
ORDER BY books.title";

        $br = mysql_query($bq);
        while ($ba = mysql_fetch_assoc($br)) {
            $all_books[$year][ $ba['id'] ]['title'] = $ba['title'];
            $all_books[$year][ $ba['id'] ]['count'] = $ba['articles_count'];
        }

    }
    return $all_books;
}

/* отображает список сборников (книг) согласно инструкциям в translations.php
Принимает массив с данными и язык вывода */

function FE_PrintBooks($all_books, $lang='en')
{
    $ret = '';
    $ret .= <<<FE_PrintBooks_Start
FE_PrintBooks_Start;

    foreach ($all_books as $key => $year_books)
    {
        $ret .= <<<FE_PrintBooks_ItemStart
<h3 class="books-list-year">{$key}</h3>
<ul>
FE_PrintBooks_ItemStart;

        foreach ($year_books as $id => $book)
        {
            $ret .= <<<FE_PrintBooks_ItemEach
<li class="books-list-eachbook"><a href="?fetch=articles&with=book&id={$id}"> {$book['title']}</a>  ({$book['count']})</li>

FE_PrintBooks_ItemEach;
        }
        $ret .= <<<FE_PrintBooks_ItemEnd
</ul>
FE_PrintBooks_ItemEnd;
    }
    $ret .= <<<FE_PrintBooks_End
FE_PrintBooks_End;
    ;
    return $ret;
}


/*
загружает информацию об авторе в ассциативный массив с учетом языка сайта
*/
function DB_LoadAuthorInformation_ById($id, $lang)
{
    global $MESSAGES_;
    $ret = '';
    $q = "SELECT * FROM `authors` WHERE id=$id";
    $r = mysql_query($q);
    if (@mysql_num_rows($r)>0) {
        $author = mysql_fetch_assoc($r);
        $ret['author_name'] = $author['name_'.$lang];
        $ret['author_title'] = $author['title_'.$lang];
        $ret['author_email'] = $author['email'];
        $ret['author_workplace'] = $author['workplace_'.$lang];
        $ret['author_bio'] = $author['bio_'.$lang];
        $ret['author_is_es'] = $author['is_es'];
        $ret['author_photo_id'] = $author['photo_id'];
    }
    return $ret;
}

/**
 * @param $id       - айди статьи
 * @param $lang     - язык
 * @return array    - возвращает базовую информацию о статье из БД как асс.массив
 */
function DB_LoadArticleInformation_ById($id, $lang)
{
    $q = "SELECT *, books.title AS btitle, books.year AS byear FROM articles, books  WHERE articles.id=$id AND books.id=articles.book";
    $ret = array();
    if ($r = mysql_query($q)) {
        if (mysql_num_rows($r)>0) {
            $ret = mysql_fetch_assoc($r);
        }
    }
    return $ret;
}

/* возвращает список авторов, участвовавших в создании статьи - как асс.массив
с учетом языка! */
function DB_LoadAuthors_ByArticle($id, $lang, $with_email='yes')
{
    $q_email = ($with_email == 'yes') ? ', email AS author_email' : ''; // insert email request to query
    $q = "SELECT authors.id AS author_id, name_{$lang} AS author_name, title_{$lang} AS author_title {$q_email} FROM authors, cross_aa WHERE cross_aa.author = authors.id AND cross_aa.article=$id ORDER BY name_{$lang}";
    $ret = array();
    if ($r = mysql_query($q)) {
        while ($row = @mysql_fetch_assoc($r)) {
            $ret[ $row['author_id'] ] = $row;
        }
    }
    return $ret;
}

/*
выводит список авторов по указанной статье на основе загруженных данных DB_LoadAuthors_ByArticle()
этот список нужен в /articles/[extended|book|topic]/ (поиск по критерию)
и в списке авторов по статье -- /articles/info/
*/
function FE_PrintAuthors_ByArticle($authors, $lang)
{
    $ret = '';
    foreach ($authors as $aid => $a_info)
    {
        // Иванов И.И., др.тех.наук
        if ($a_info['author_email'] != '') {
            $a_info['author_email'] = ' ('.$a_info['author_email'].')';
        }
        $ret .= <<<FE_PrintAuthorsInArticle
<li><a href="?fetch=authors&with=info&id={$a_info['author_id']}&lang=$lang">{$a_info['author_name']}</a>, {$a_info['author_title']} {$a_info['author_email']} </li>\r
FE_PrintAuthorsInArticle;
    }
    return $ret;
}

/* возвращает список статей, которые написал указанный автор, но только в опубликованных сборниках */
function DB_LoadArticles_ByAuthor($id, $lang)
{
    $ret = array();
    $q = "SELECT articles.id AS aid, articles.title_{$lang} AS atitle, articles.pdfid, books.title AS btitle, SUBSTRING(books.date,7,4) AS bdate
FROM articles, cross_aa, books
WHERE books.id=articles.book AND cross_aa.article = articles.id AND books.published=1 AND cross_aa.author = $id";
    $r = mysql_query($q);
    if (@mysql_num_rows($r) > 0) {
        while ($article = mysql_fetch_assoc($r)) {
            $ret [ $article['aid'] ] = $article;
        }
    }
    return $ret;
}

/* печатает список статей, которые написал указанный автор - принимает массив статей DB_LoadArticles_ByAuthor() */
function FE_PrintArticles_ByAuthor($articles, $lang)
{
    $ret = '';
    if (count($articles) > 0) {
        $ret .= <<<FE_PrintArticles_ByAuthor_Start
<table class="articles_by-author-table">
FE_PrintArticles_ByAuthor_Start;
        foreach ($articles as $aid => $article)
        {
            $ret .= <<<FE_PrintArticles_ByAuthor_Each
    <tr>
        <td class="articles_by-author-table-book-title">{$article['btitle']}</td>
        <td rowspan="2" class="articles_by-author-table-pdficon">
            <a href="core/getfile.php?id={$article['pdfid']}"><img src="images/pdf32x32.png" width="32" height="32"></a>
        </td>
        <td rowspan="2" class="articles_by-author-table-title"><a href="?fetch=articles&with=info&id={$article['aid']}">{$article['atitle']}</a></td>
    </tr>
    <tr>
        <td class="articles_by-author-table-book-year">{$article['bdate']}</td>
    </tr>
FE_PrintArticles_ByAuthor_Each;
        }
        $ret .= <<<FE_PrintArticles_ByAuthor_End
</table>
FE_PrintArticles_ByAuthor_End;
    } else {
        $ret .= '';
    }
    return $ret;
}

/* смена и установка языка сайта -- НЕ ПАШЕТ */
function FE_GetSiteLanguage()
{
    $lang = 'en';
    if (isset($_COOKIE['libdb_sitelanguage']) && $_COOKIE['libdb_sitelanguage'] != '') {
        switch ($_COOKIE['libdb_sitelanguage']) {
            case 'en': { $lang = 'en'; break; }
            case 'ru': { $lang = 'ru'; break; }
            case 'uk': { $lang = 'uk'; break; }
            default: {$lang = 'en'; break;}
        }
    }
    // SetCookie('libdb_sitelanguage', $lang, 3600*24*366);
    return $lang;
}
function FE_SetSiteLanguage($lang)
{
    // setcookie('libdb_sitelanguage', '', -3600);
    // setcookie('libdb_sitelanguage', $lang, 3600*24*366);
    return $lang;
}

/* загрузка списка авторов с отбором по первой букве (в зависимости от языка)
значение буквы по умолчанию '0', что означает ВСЕ авторы
функция используется в аякс-ответах, в выгрузке полного списка авторов и выгрузке
списка авторов по первой букве
*/
function DB_LoadAuthors_ByLetter($letter, $lang, $is_es='no', $selfhood=-1)
{
    $authors = array();
    // check for letter, '0' is ANY first letter
    if ($letter == '') { $letter = '0'; }
    if ($letter != '0') {
        $where_like = " AND authors.name_{$lang} LIKE '{$letter}%'";
    } else {
        $where_like = '';
    }


    // check for 'is author in editorial stuff', default is 'no'
    if ($is_es != 'no') {
        $where_es = ' AND is_es=1 ';
    } else {
        $where_es = '';
    }
    // optional parameter selfhood (for extended estuff)
    if ($selfhood != -1 )
    {
        $where_selfhood = " AND selfhood=$selfhood";
    } else {
        $where_selfhood = '';
    }

    $order = "ORDER BY authors.name_{$lang}";

    //@todo: вообще-то тут можно вместо * перечислить поля в формате xx_lang AS yy и отвязаться от передачи $sitelanguage в функции FE_Print*()
    //но для этого придется править все функции, которые используют этот лоаред...
    $q = "SELECT * FROM `authors` WHERE `deleted`=0 {$where_es} {$where_selfhood} {$where_like} {$order}";

    $r = mysql_query($q) or Die(0);

    if ( @mysql_num_rows($r) > 0 ) {
        while ($i = mysql_fetch_assoc($r)) {
            $authors[ $i['id'] ] = $i;
        }
    }
    return $authors;
    // ФИО, научное звание/ученая степень

}

/* печать загруженного списка авторов ($authors) в примитивной (простой строковой) форме */
function FE_PrintAuthors_PlainList($authors, $lang)
{
    $return = '';
    $return .= <<<PrintAuthorsSelectedByLetter_Start
<ul class="authors-list">
PrintAuthorsSelectedByLetter_Start;
    if (sizeof($authors) > 0 )
    {
        foreach ($authors as $i => $an_author)
        {
            $id = $an_author['id'];
            $name = $an_author['name_'.$lang];
            $title = $an_author['title_'.$lang];
            // $email = $an_author['email'];
            //@this: эктор-ссылка на /authors/info/(id) - работает, якорь для замены в модреврайт
            $return .= <<<PrintAuthorsSelectedByLetter_Each
<li class="authors-list-item">
<label>
<a href="?fetch=authors&with=info&id={$id}">{$name}</a>, {$title}
</label>
</li>
PrintAuthorsSelectedByLetter_Each;
        }
    } else {
        $return .= <<<PrintAuthorsSelectedByLetter_Nothing
Таких авторов нет!
PrintAuthorsSelectedByLetter_Nothing;
    }
    $return .= <<<PrintAuthorsSelectedByLetter_End
</ul>
PrintAuthorsSelectedByLetter_End;
    return $return;
}

/* печать нужных авторов ($authors) в расширенной форме для /authors/estuff */
/* функция НЕ оборачивает элементы списка в UL, поэтому её вывод надо вставлять
во внутрь списка в шаблоне */
function FE_PrintAuthors_EStuffList($authors, $lang)
{
    $return = '';
    $return .= <<<fe_printauthors_estuff_start
fe_printauthors_estuff_start;
    if ( sizeof($authors) > 0 ) {
        foreach ($authors as $i => $an_author ) {
            $name = $an_author['name_'.$lang];
            // первое слово в имени обернуть в <strong> ?
            // $name = preg_replace('/(?<=\>)\b(\w*)\b|^\w*\b/', '<strong>$0</strong>', $name); // see http://stackoverflow.com/questions/10833435/wrap-b-tag-around-first-word-of-string-with-preg-replace
            $name = preg_replace('/^([^\s]+)/','<strong>\1</strong>',$name); // спасибо Мендору

            $title = $an_author['title_'.$lang];
            $title = ($title != '') ? ",<br><div class=\"smaller\">{$title}</div>" : "";

            $email = ($an_author['email'] != '') ? "<strong>E-Mail: </strong>{$an_author['email']}" : '';
            $return .= <<<fe_printauthors_estuff_each
            <li><span class="authors-estufflist-name">{$name}</span>{$title}{$email}</li>\r\n
fe_printauthors_estuff_each;
        }
    }

    $return .= <<<fe_printauthors_estuff_end
fe_printauthors_estuff_end;

    return $return;
}



/* функции генерации селектов */
/*
возврат массива "первых букв" для списка авторов для указанного языка
используется для аякс-запроса
*/
function DB_LoadFirstLettersForSelector($lang)
{
    $ql = "SELECT DISTINCT SUBSTRING(name_{$lang},1,1) AS letter FROM authors WHERE deleted=0 ORDER BY name_{$lang}";
    if ($qr = mysql_query($ql))    // or Die("Death at request: ".$ql);
    {
        $qn = @mysql_num_rows($qr);

        if ($qn > 0) {
            $return['error'] = 0;
            while ($letter = mysql_fetch_assoc($qr)) {
                $return['data'][ "{$letter['letter']}" ] = "{$letter['letter']}";
            }

        } else {
            $return['error'] = 1;
            $return['data'] = 'No any letters found!';
        }
    } else {
        $return['error'] = 2;
        $return['data'] = $ql;
    }
    return $return;
}

/* построение универсального запроса */
function DB_BuildQuery($get, $lang)
{
    $q = '';
    $q_select = " SELECT DISTINCT
articles.id
, articles.udc AS article_udc
, articles.add_date AS article_add_date
, articles.title_{$lang} AS article_title
, articles.book
, articles.topic
, books.title AS book_title
, topics.title_{$lang} AS topic_title
, books.year AS book_year
, articles.pages AS article_pages
, pdfid ";
    // $q_select_expert = ", articles.keywords_{$lang}";

    $q_from = " FROM
articles
, books, topics
, cross_aa
, authors ";

    $q_base_where = " WHERE
authors.id = cross_aa.author AND
    articles.id = cross_aa.article AND
        books.id = articles.book AND
            topics.id = articles.topic AND
                articles.deleted = 0 AND books.published=1 AND topics.deleted=0 ";

    $q_final = " GROUP BY articles.title_{$lang} ORDER BY articles.id ";

    /* Extended search conditions */
    $q_extended = '';
    $q_extended .= (IsSet($get['book']) && ($get['book'] != 0))          ? " AND articles.book = {$get['book']} " : "";
    $q_extended .= (IsSet($get['topic']) && ($get['topic'] != 0))        ? " AND articles.topic = {$get['topic']}" : "";
    $q_extended .= (IsSet($get['letter']) && ($get['letter'] != '0'))    ? " AND authors.name_{$lang} LIKE '{$get['letter']}%' " : "";
    $q_extended .= (IsSet($get['aid']) && ($get['aid'] != 0))            ? " AND authors.id = {$get['aid']} " : "";
    $q_extended .= (IsSet($get['year']) && ($get['year'] != 0))          ? " AND books.year = {$get['year']} " : "";

    /* Expert search conditions */
    $q_expert = '';
    if ($get['actor'] == 'load_articles_expert_search') {
        /* AND authors.name_en LIKE 'Mak%' */
        /* AND articles.udc LIKE '%621%' */
        /* AND articles.add_date LIKE '%2013' */
        /* AND (articles.keywords_en LIKE '%robot%' OR ... OR ... )*/
        $q_expert .= ($get['expert_name'] != '')             ? " AND authors.name_{$lang} LIKE '{$get['expert_name']}%' " : "";
        $q_expert .= ($get['expert_udc'] != '')              ? " AND articles.udc LIKE '%{$get['expert_udc']}%' " : "";
        $q_expert .= ($get['expert_add_date'] != '')    ? " AND articles.add_date LIKE '%{$get['expert_add_date']}' " : "";

        /* это оптимизированная достраивалка запроса на основе множественных keywords */
        $keywords = explode(' ', $get['expert_keywords']);
        $q_expert .= " AND ( ";
        foreach ($keywords as $keyword) {
            $q_expert .= " articles.keywords_{$lang} LIKE '%{$keyword}%' OR ";
        }
        $q_expert = substr($q_expert , 0 , (strlen($q_expert)-4));
        $q_expert .= " ) ";
    }

    $q = $q_select . $q_from . $q_base_where . $q_extended . $q_expert . $q_final;
    return $q;
}

/*
Загрузка статей по сложному запросу
$with_email - передается в DB_LoadAuthorsByArticles, который отдает МАССИВ авторов по статье)
этот массив обрабатывается в FE_Print-функции, использующей результаты этой функции.
*/
function DB_LoadArticlesByQuery($get, $lang, $with_email = 'yes')
{
    $query = DB_BuildQuery($get, $lang);
    $res = mysql_query($query) or die("ОШИБКА: Доступ к базе данных ограничен, запрос: ".$query);
    $articles_count = @mysql_num_rows($res);
    $all_articles = array();

    if ($articles_count > 0) {
        while ($an_article = mysql_fetch_assoc($res))
        {
            $id = $an_article['id'];
            $all_articles[$id] = $an_article;
            $all_articles[$id]['authors'] = DB_LoadAuthors_ByArticle($id, $lang, $with_email);
        } //end while
    } // end if
    return $all_articles;
}


/*
возвращает ОДИН элемент из списка статей в сокращенной нотации (для "полного списка статей")
а именно - "<li> Название. авторы; автор; автор; сборник, в котором она опубликована"
*/

function FE_PrintArticleItem_Simple($an_article, $lang)
{
    $return = '';
    $authors_string = '';
    foreach ($an_article['authors'] as $id => $an_author)
    {
        $authors_string .= ' '.$an_author['author_name'].';';
    }
    // $authors_string = substr($authors_string,0,-1); // удалить последний ";"

    $return .= <<<FE_PAI_Simple
<li>
    <a href="?fetch=articles&with=info&id={$an_article['id']}">{$an_article['article_title']}</a>
    {$authors_string} {$an_article['book_year']}
</li>
FE_PAI_Simple;

    return $return;
}

/*
возвращает ОДИН элемент из списка статей в расширенной табличной нотации (для отбора статей)
*/

function FE_PrintArticleItem_Extended($an_article, $lang)
{
    $return = '';
    $authors_string = '';
    // построить список авторов
    $authors_string = FE_PrintAuthors_ByArticle($an_article['authors'], $lang);

    switch ($lang) {
        case 'en': { $lal_e_bi = 'Pp. '; break; }
        case 'ru': { $lal_e_bi = 'C. '; break; }
        case 'uk': { $lal_e_bi = 'C. '; break; }
    }

    $book_info = <<<LoadArticlesList_Each_BookInfo
<nobr>{$an_article['books_title']}</nobr><br>
<nobr>{$lal_e_bi} {$an_article['article_pages']}</nobr>
LoadArticlesList_Each_BookInfo;


    $return .= <<<LoadArticlesList_Each
<tr>
    <td class="articles-list-table-book-title">
        {$book_info}
    </td>
    <td class="articles-list-table-pdficon">
        <a href="core/getfile.php?id={$an_article['pdfid']}"><img src="images/pdf32x32.png" width="32" height="32"></a>
    </td>
    <td class="articles-list-table-title">
        <a href="?fetch=articles&with=info&id={$an_article['id']}">{$an_article['article_title']}</a>
    </td>
    <td>
        <ul class="articles-list-table-authors-list">
            {$authors_string}
        </ul>
    </td>
</tr>
LoadArticlesList_Each;
    return $return;
}

/* выводит СПИСОК статей в СОКРАЩЕННОЙ нотации */
function FE_PrintArticlesList_Simple($articles, $lang)
{
    $return = '';
    if (count($articles)>0)
    {
        $return .= <<<PAL_S_Start
<ul class="articles-list-full">
PAL_S_Start;
        foreach ($articles as $an_article_id => $an_article_data)
        {
            $return .= FE_PrintArticleItem_Simple($an_article_data, $lang);
        }
        $return .= <<<PAL_S_End
</ul>
PAL_S_End;
    }
    return $return;
}

/* выводит СПИСОК статей в ПОЛНОЙ четырехколоночной нотации (таблицу) (для отборочных скриптов)*/
function FE_PrintArticlesList_Extended($articles, $lang)
{
    $return = '';

    if (count($articles) > 0)
    {
        $return .= <<<LoadArticlesList_Start
<table class="articles-list-by-query" border="1" width="100%">
LoadArticlesList_Start;

        foreach ($articles as $an_article_id => $an_article_data)
        {
            $return .= FE_PrintArticleItem_Extended($an_article_data, $lang);
        }

        $return .= <<<LoadArticlesList_End
</table>
LoadArticlesList_End;
    } else {
        switch ($lang)
        {
            case 'en': {
                $return .= <<<LoadArticlesList_SearchNoArticles_EN
<br><strong>No articles found within this search criteria!</strong>
LoadArticlesList_SearchNoArticles_EN;
                break;
            }
            case 'ru': {
                $return .= <<<LoadArticlesList_SearchNoArticlesRU
<br><strong>No articles found within this search criteria!</strong>
LoadArticlesList_SearchNoArticlesRU;
                break;
            }
            case 'uk': {
                $return .= <<<LoadArticlesList_SearchNoArticlesUA
<br><strong>No articles found within this search criteria!</strong>
LoadArticlesList_SearchNoArticlesUA;
                break;
            }
        }
        // empty array
    }
    return $return;
}

/* выводит СПИСОК статей в ПОЛНОЙ двухколоночной нотации (таблицу) (для отборочных скриптов)*/
function  FE_PrintArticlesList_Expert($articles, $lang)
{

}

/* загружает в асс.массив новость с указанным id,
usable: используется для pure-вставки в шаблон
*/
function DB_LoadNewsItem($id, $lang)
{
    $query = "SELECT id, title_{$lang} AS title, text_{$lang} AS text, date_add FROM news where id={$id}";
    if ($r = mysql_query($query)) {
        if (@mysql_num_rows($r) > 0) {
            $ret = mysql_fetch_assoc($r);
        }
    }
    return $ret;
}
/* загружает список новостей в краткой форме (асс.массив)
[id] => [id => '', title => '', date => '']
используется в шаблоне */
function DB_LoadNewsListTOC($lang)
{
    $query = "SELECT id, title_{$lang} AS title, date_add AS date FROM news";
    if ($r = mysql_query($query)) {
        while ($row = mysql_fetch_assoc($r)) {
            $ret[ $row['id'] ] = $row;
        }
    }
    return $ret;
}


?>