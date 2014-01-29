<?php
require_once('core/core.kwt.php');

/*
Префикс DB_ имеют функции загрузки данных из базы
Префикс FE_ имеют функции оформления данных на вывод
Служебные функции префикса не имеют.
*/

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
//@todo: ВАЖНО: тут кроется неучтенка: если в одному году несколько сборников - колво статей в них считается неправильно
    // и вообще запрос составлен неверно (так, как будто в 1 году строго 1 сборник) .

    $ret = '';
    $yq = "SELECT DISTINCT `year` FROM books WHERE `published`=1 AND `deleted`=0 ORDER BY `year` DESC";
    $yr = mysql_query($yq);
    $all_books = array();
    while ($ya = mysql_fetch_assoc($yr)) {
        $all_books[ $ya['year'] ] = array();
    }
    foreach ($all_books as $year => $value)
    {
        // $bq = "SELECT id, title FROM books WHERE `year`=$key AND `published`=1 AND `deleted`=0 ORDER BY `title`";
        $bq = "SELECT books.id, books.title, COUNT(articles.id) AS acount
        FROM articles, books
        WHERE articles.book = books.id AND
        books.year = $year AND
        books.published=1 AND
        books.deleted=0 ORDER BY books.title";

        $br = mysql_query($bq);
        while ($ba = mysql_fetch_assoc($br)) {
            $all_books[$year][ $ba['id'] ]['title'] = $ba['title'];
            $all_books[$year][ $ba['id'] ]['count'] = $ba['acount'];
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
<li class="books-list-year"><div>{$key}</div><ul>
FE_PrintBooks_ItemStart;

        foreach ($year_books as $id => $book)
        {
            $ret .= <<<FE_PrintBooks_ItemEach
<li class="books-list-eachbook"><a href="?fetch=articles&with=book&id={$id}"> {$book['title']} <em>({$book['count']})</em></a></li>
FE_PrintBooks_ItemEach;
        }
        $ret .= <<<FE_PrintBooks_ItemEnd
</ul></li>
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
        $ret['author_workplace'] = $author['workplace'];
        $ret['author_bio'] = $author['bio'];
        $ret['author_is_es'] = $author['is_es'];
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
function DB_LoadAuthors_ByArticle($id, $lang)
{
    $q = "SELECT authors.id AS aid, name_{$lang}, title_{$lang}, email FROM authors, cross_aa WHERE cross_aa.author = authors.id AND cross_aa.article=$id";
    $ret = '';
    if ($r = mysql_query($q)) {
        while ($row = @mysql_fetch_assoc($r)) {
            $ret[ $row['aid'] ] = $row;
        } // у статьи ОБЯЗАТЕЛЬНО есть авторы
    }
    return $ret;
}

/* выводит список авторов по указанной статье на основе загруженных данных DB_LoadAuthors_ByArticle() */
function FE_PrintAuthors_ByArticle($authors, $lang)
{
//     global $MESSAGES;
    $ret = '';
    foreach ($authors as $aid => $a_info)
    {
        // Иванов И.И., др.тех.наук
        if ($a_info['email'] != '') {
            $a_info['email'] = '('.$a_info['email'].')';
        }
        $ret .= <<<FE_PrintAuthorsInArticle
<li><a href="?fetch=authors&with=info&id={$a_info['aid']}">{$a_info['name_'.$lang]}, {$a_info['title_'.$lang]}</a> {$a_info['email']} </li>
FE_PrintAuthorsInArticle;
    }
    return $ret;
}

/* возвращает список статей, которые написал указанный автор, но только в опубликованных сборниках */
function DB_LoadArticles_ByAuthor($id, $lang)
{
    $ret = array();
    $q = "SELECT articles.id AS aid, articles.title_en AS atitle, articles.pdfid, books.title AS btitle, SUBSTRING(books.date,7,4) AS bdate
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
            <a href="core/getpdf.php?id={$article['pdfid']}"><img src="images/pdf32x32.png" width="32" height="32"></a>
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
функция используется в аякс-ответах и в выгрузке полного списка авторов
*/
function DB_LoadAuthors_ByLetter($letter, $lang, $is_es='no')
{
    global $MESSAGES_;
    $return = '';
    // check for letter, '0' is ANY first letter
    if ($letter != '0') {
        $like = " AND authors.name_{$lang} LIKE "."'".strtolower($letter)."%'";
    } else {
        $like = '';
    }
    // check for 'is author in editorial stuff', default is 'no'
    if ($is_es != 'no') {
        $where_es = ' AND is_es=1 ';
    } else {
        $where_es = '';
    }

    $q = "SELECT * FROM authors WHERE `deleted`=0 ".$where_es.$like;
    $r = mysql_query($q) or Die(0);
    // ФИО, научное звание/ученая степень

    $return .= <<<LoadAuthorsSelectedByLetter_Start
<ul class="authors-list">
LoadAuthorsSelectedByLetter_Start;

    if ( @mysql_num_rows($r) > 0) {
        while ($i = mysql_fetch_assoc($r)){
            //@this: эктор-ссылка на /authors/info/(id) - работает, якорь для замены в модреврайт
            $id = $i['id'];
            $name = $i['name_'.$lang];
            $title = $i['title_'.$lang];
            $email = $i['email'];
            $return .= <<<LoadAuthorsSelectedByLetter_Each
<li class="authors-list-item">
<label>
<a href="?fetch=authors&with=info&id={$id}">{$name} , {$title}, {$email}</a>
</label>
</li>
LoadAuthorsSelectedByLetter_Each;
        } // while
    } else {
        $return .= <<<LoadAuthorsSelectedByLetter_Nothing
Таких авторов нет!
LoadAuthorsSelectedByLetter_Nothing;
;
    }

    $return .= <<<LoadAuthorsSelectedByLetter_End
</ul>
LoadAuthorsSelectedByLetter_End;
;
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
    $q = "SELECT DISTINCT
articles.id
, articles.title_{$lang} AS article_title
, articles.book
, articles.topic
, books.title AS books_title
, topics.title_en AS topics_title
, books.year AS books_year
FROM
articles
, books, topics
, cross_aa
, authors
WHERE
authors.id = cross_aa.author AND
    articles.id = cross_aa.article AND
        books.id = articles.book AND
            topics.id = articles.topic AND
                articles.deleted = 0 AND books.published=1 AND topics.deleted=0
";

    $q .= (IsSet($get['book']) && ($get['book'] != 0))          ? " AND articles.book = {$get['book']} " : "";
    $q .= (IsSet($get['topic']) && ($get['topic'] != 0))        ? " AND articles.topic = {$get['topic']}" : "";
    $q .= (IsSet($get['letter']) && ($get['letter'] != '0'))    ? " AND authors.name_{$lang} LIKE '{$get['letter']}%' " : "";
    $q .= (IsSet($get['aid']) && ($get['aid'] != 0))            ? " AND authors.id = {$get['aid']} " : "";
    $q .= (IsSet($get['year']) && ($get['year'] != 0))          ? " AND books.year = {$get['year']} " : "";
    $q .= " GROUP BY articles.title_{$lang} ORDER BY articles.id ";
    return $q;
}

/* универсальная функция загрузки списка статей по сложному запросу */
function DB_LoadArticlesByQuery($get, $lang, $loadmode = 'search')
{
    $return = '';
    $query = DB_BuildQuery($get, $lang);

    $res = mysql_query($query) or die("ОШИБКА: Доступ к базе данных ограничен, запрос: ".$query);
    $articles_count = @mysql_num_rows($res);

    $all_articles = array();

    if ($articles_count > 0) {
        while ($an_article = mysql_fetch_assoc($res))
        {
            $id = $an_article['id']; // айди статьи
            $all_articles[$id] = $an_article;

            $q_authors = "SELECT authors.name_{$lang},authors.title_{$lang},authors.id FROM authors,cross_aa WHERE authors.id=cross_aa.author AND cross_aa.article={$id} ORDER BY cross_aa.id";
            $r_authors = mysql_query($q_authors) or die("ОШИБКА: не получается извлечь авторов по статье: ".$q_authors);
            $r_authors_count = @mysql_num_rows($r_authors);

            if ($r_authors_count>0)
            {
                while ($an_author = mysql_fetch_assoc($r_authors))
                {
                    $all_articles[$id]['authors'] .= <<<LoadArticlesByQuery_AuthorsTemplate
 {$an_author['name_'.$lang]}, {$an_author['title_'.$lang]};
LoadArticlesByQuery_AuthorsTemplate;
                }
                if (strpos($all_articles[$id]['authors'], '<br>')>0)
                    $all_articles[$id]['authors'] = substr($all_articles[$id]['authors'],0,-4); //удаляет последний <br> если он есть
                    $all_articles[$id]['authors'] = substr($all_articles[$id]['authors'],0,-1); // удалить последний ";"
            } // if authors
        } // while each article record
    } // if
    $return .= <<<LoadArticlesList_Start
<table class="articles-list-by-query" border="1" width="100%">
LoadArticlesList_Start;
;

    // название, авторы, сборник, в котором она опубликована
    // atitle, $all_articles[$id]['authors'], btitle
    if ($articles_count>0) {
        foreach ($all_articles as $a_id => $an_article) {
            $return .= <<<LoadArticlesList_Each
<tr>
<td>{$an_article['books_year']}</td>
<td>«{$an_article['article_title']}»</td>
<td>{$an_article['authors']}</td>
<td><nobr>{$an_article['books_title']}</nobr></td>
<td><button class="more_info" name="{$an_article['id']}" data-text="More"> >>> </button></td>
</tr>
LoadArticlesList_Each;
        };
    } else {
        // статей по заданному критерию нет
        //@MESSAGE: "нет статей по заданному критерию"
        switch ($loadmode) {
            case 'search' : {
                $return .= <<<LoadArticlesList_SearchNoArticles
<br><strong>No articles found within this search criteria!</strong>
LoadArticlesList_SearchNoArticles;
;
                break;
            }
            case 'onload' : {
                $return .= <<<LoadArticlesList_OnloadNoArticles
Articles not found!
LoadArticlesList_OnloadNoArticles;
                break;
            }
        } // case loadmode
    } // else
    $return .= <<<LoadArticlesList_End
</table>
LoadArticlesList_End;
;
    return $return;
}


?>