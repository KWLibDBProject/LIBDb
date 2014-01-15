<?php
require_once('core/core.kwt.php');
require_once('translations.php');

// функции, к которым обращается фронтэнд (сам сайт)

// вот нам и понадобился вложенный шаблон... причем не просто вложенный, а повторяемый, типа
// "повторить следующий блок N раз, заменяя какие-то переменные следующими:"
function DBLoadTopics($lang)
{
    global $MESSAGES;
    $q = "SELECT `id`, `title_{$lang}` AS title FROM topics WHERE `deleted`=0";
    $r = mysql_query($q);
    $ret = '';

    $ret .= $MESSAGES['LoadTopics_Start'][$lang];

    while ($topic = mysql_fetch_assoc($r))
    {
        $ret .= sprintf($MESSAGES['LoadTopics_Each'][$lang], $topic['id'], $topic['title']);
    }
    $ret .= $MESSAGES['LoadTopics_End'][$lang];
    return $ret;
}

function DBLoadBooks($lang)
{
    global $MESSAGES;
    $ret = '';
    $yq = "SELECT DISTINCT `year` FROM books WHERE `published`=1 AND `deleted`=0 ORDER BY `year` ";
    $yr = mysql_query($yq);
    $all_books = array();
    while ($ya = mysql_fetch_assoc($yr)) {
        $all_books[ $ya['year'] ] = array();
    }
    foreach ($all_books as $key => $value)
    {
        $bq = "SELECT id, title FROM books WHERE `year`=$key AND `published`=1 AND `deleted`=0 ORDER BY `title`";
        $br = mysql_query($bq);
        while ($ba = mysql_fetch_assoc($br)) {
            $all_books[$key][ $ba['id'] ] = $ba['title'];
        }

    }

    $ret .= $MESSAGES['LoadBooks_Start'][$lang];

    foreach ($all_books as $key => $year_books)
    {
        $ret .= sprintf($MESSAGES['LoadBooks_ItemStart'][$lang], $key);

        foreach ($year_books as $id => $book)
        {
            $ret .= sprintf($MESSAGES['LoadBooks_ItemEach'][$lang], $id, $book);
        }
        $ret .= $MESSAGES['LoadBooks_ItemEnd'][$lang];
    }
    $ret .= $MESSAGES['LoadBooks_End'][$lang];

    return $ret;
}

function DBLoadAuthorInformation($id, $lang) // see template
{
    global $MESSAGES;
    $ret = '';
    $q = "SELECT *  FROM authors WHERE id=$id";
    $r = mysql_query($q);
    if (@mysql_num_rows($r)>0) {
        $author = mysql_fetch_assoc($r);
        $ret['author_name'] = $author['name_'.$lang];
        $ret['author_title'] = $author['title_'.$lang];
        $ret['author_email'] = $author['email'];
        $ret['author_workplace'] = $author['workplace'];
    }
    return $ret;
}

function DBLoadArticleInfo($id, $lang) // see template
{
    $q = "SELECT *, books.title AS btitle, books.year AS byear FROM articles, books  WHERE articles.id=$id AND books.id=articles.book";
    if ($r = mysql_query($q)) {
        if (mysql_num_rows($r)>0) {
            $ret = mysql_fetch_assoc($r);
        }
    }
    return $ret;

}

function DBLoadArticleInfoAuthorsList($id, $lang)
{
    global $MESSAGES;
    // возвращает набор li-элементов с записями об авторах статьи:
    // Иванов И.И., др.тех.наук
    $q = "SELECT authors.id AS aid, name_{$lang}, title_{$lang}, email FROM AUTHORS, cross_aa WHERE cross_aa.author = authors.id AND cross_aa.article=$id";
    $ret = '';
    if ($r = mysql_query($q)) {
        while ($row = @mysql_fetch_assoc($r)) {
            $ret .= sprintf($MESSAGES['LoadArticleInfoAuthorsList'][$lang],$row['name_'.$lang],$row['title_'.$lang],$row['email'],$row['aid']);
        } // у статьи ОБЯЗАТЕЛЬНО есть авторы
    }
    return $ret;
}

function DBLoadAuthorPublications($id, $lang) // @todo: темплейт заменен на переводной файл
{
    // публикации (название, номер сборника, год выпуска)
    global $MESSAGES;
    $ret = '';
    $q = "SELECT articles.*, books.title AS btitle , SUBSTRING(books.date,7,4) AS bdate  FROM articles, cross_aa, books WHERE books.id=articles.book AND cross_aa.article = articles.id AND cross_aa.author=$id";
    $r = mysql_query($q);
    if (@mysql_num_rows($r)>0) {

        $ret .= sprintf($MESSAGES['LoadAuthorPublications_Start'][$lang]);

        while ($i = @mysql_fetch_assoc($r))
        {
            $ret .= sprintf($MESSAGES['LoadAuthorPublications_EachRecord'][$lang], $i['id'], $i['title_'.$lang], $i['btitle'], $i['bdate']);
        }

        $ret .= sprintf($MESSAGES['LoadAuthorPublications_End'][$lang]);

    } else {
        $ret .= $MESSAGES['LoadAuthorPublications_NoArticles'][$lang];
    }

    return $ret;
}

// функция работает аналогично core/articles.action.list.php , но иной формат вывода данных
// вызывается функция из ajax.php @ load_articles_selected_by_query

function DBLoadArticlesList($getarray, $lang, $loadmode='search') // $loadmode = search or onload
{
    global $MESSAGES;

    $return = '';
    // название, авторы, сборник, в котором она опубликована
    $query = "
SELECT DISTINCT articles.id, articles.title_{$lang} AS atitle,
topics.title_{$lang} AS ttitle,
books.title AS btitle,
`add_date`
from articles, cross_aa, topics, books
WHERE
cross_aa.article=articles.id
AND
articles.deleted=0
AND
topics.id=articles.topic
AND
books.id=articles.book
AND
books.published=1"; // только из опубликованных сборников

    $query .= (IsSet($getarray['author'])   && $getarray['author']!=0)  ? " AND cross_aa.author = $getarray[author] "   : "";
    $query .= (IsSet($getarray['book'])     && $getarray['book']!=0 )   ? " AND articles.book = $getarray[book] "       : "";
    $query .= (IsSet($getarray['topic'])    && $getarray['topic'] !=0 ) ? " AND articles.topic = $getarray[topic] "     : "";

    $res = mysql_query($query) or die("ОШИБКА: Доступ к базе данных ограничен, запрос: ".$query);
    $articles_count = @mysql_num_rows($res);

    $all_articles = array();

    if ($articles_count>0) {
        while ($an_article = mysql_fetch_assoc($res))
        {
            $id = $an_article['id']; // айди статьи
            $all_articles[$id] = $an_article; // ВСЯ статья

            $q_auths = "SELECT authors.name_{$lang},authors.title_{$lang},authors.id FROM authors,cross_aa WHERE authors.id=cross_aa.author AND cross_aa.article={$id} ORDER BY cross_aa.id";
            $r_auths = mysql_query($q_auths) or die($q_auths);
            $r_auths_count = @mysql_num_rows($r_auths);

            if ($r_auths_count>0)
            {
                while ($an_author = mysql_fetch_assoc($r_auths))
                {
                    //@todo: MESSAGE+this: формат вывода строки авторов
                    $all_articles[$id]['authors'] .= $an_author['name_'.$lang]." (".$an_author['title_'.$lang].")<br>";
                } // while authors
                $all_articles[$id]['authors'] = substr($all_articles[$id]['authors'],0,-4); //удаляет последний <br>
            } // if authors
        } // while each article record
    } // if
    //@todo: MESSAGE+this: формат вывода одной статьи в списке статей
    /* $return .= <<<LA_START
<table border="1" width="100%" class="articles_list">
LA_START; */
    $return .= $MESSAGES['LoadArticlesList_Start'][$lang];

    // название, авторы, сборник, в котором она опубликована
    // atitle, $all_articles[$id]['authors'], btitle
    if ($articles_count>0) {
        foreach ($all_articles as $a_id => $an_article) {
            $return .= sprintf($MESSAGES['LoadArticlesList_Each'][$lang],
                $an_article['add_date'], $an_article['atitle'], $an_article['authors'],
                $an_article['btitle'], $an_article['id']);
        };
    } else {
        // статей по заданному критерию нет
        //@MESSAGE: "нет статей по заданному критерию"
        switch ($loadmode) {
            case 'search' : {
                $return .= $MESSAGES['LoadArticlesList_SearchNoArticles'][$lang];
                break;
            }
            case 'onload' : {
                $return .= $MESSAGES['LoadArticlesList_OnloadNoArticles'][$lang];
                break;
            }
        } // case loadmode
    } // else
    $return .= $MESSAGES['LoadArticlesList_End'][$lang];
    return $return;
} // function

// аналог core/articles.action.list.php , только вместо ID автора используется первая буква имени автора
// вызывается функция из ajax.php @ load_articles_selected_by_query_with_letter
function DBLoadArticlesListWithLetter($getarray, $lang, $loadmode='search') // $loadmode = search or onload
{
    global $MESSAGES;

    $return = '';
    // сложный запрос.
    $query = "SELECT DISTINCT
articles.title_{$lang} AS article_title,
articles.id AS article_id,
articles.add_date,
topics.title_{$lang} AS topic_title ,
topics.id AS topic_id,
books.title AS book_title,
books.id AS books_id,
authors.name_{$lang}
FROM
articles, cross_aa, books, topics, authors
WHERE
articles.book = books.id
AND cross_aa.article = articles.id
AND cross_aa.author = authors.id
AND topics.id = articles.topic
AND articles.deleted=0 AND topics.deleted=0 AND books.published=1 ";

    $query .= (IsSet($getarray['book'])) && ($getarray['book']!=0 )     ? " AND books.id = $getarray[book]"     : "";
    $query .= (IsSet($getarray['topic'])) && ($getarray['topic']!=0 )   ? " AND topics.id = $getarray[topic]"     : "";
    $query .= (IsSet($getarray['letter'])) && ($getarray['letter']!='0')  ? " AND authors.name_{$lang} LIKE '{$getarray['letter']}%' " : "";

    $query .= " GROUP BY articles.title_en
    ORDER BY articles.id ";

    $res = mysql_query($query) or die("ОШИБКА: Доступ к базе данных ограничен, запрос: ".$query);
    $articles_count = @mysql_num_rows($res);

    $all_articles = array();

    if ($articles_count>0) {
        while ($an_article = mysql_fetch_assoc($res))
        {
            $id = $an_article['article_id']; // айди статьи
            $all_articles[$id] = $an_article; // ВСЯ статья

            $q_auths = "SELECT authors.name_{$lang},authors.title_{$lang},authors.id FROM authors,cross_aa WHERE authors.id=cross_aa.author AND cross_aa.article={$id} ORDER BY cross_aa.id";
            $r_auths = mysql_query($q_auths) or die($q_auths);
            $r_auths_count = @mysql_num_rows($r_auths);

            if ($r_auths_count>0)
            {
                while ($an_author = mysql_fetch_assoc($r_auths))
                {
                    //@todo: MESSAGE+this: формат вывода строки авторов
                    $all_articles[$id]['authors'] .= $an_author['name_'.$lang]." (".$an_author['title_'.$lang].")<br>";

                } // while
                $all_articles[$id]['authors'] = substr($all_articles[$id]['authors'],0,-4); //удаляет последний <br>
            } // if authors
        } // while each article record
    } // if
    //@todo: MESSAGE+this: формат вывода одной статьи в списке статей
    $return .= $MESSAGES['LoadArticlesList_Start'][$lang];

    // название, авторы, сборник, в котором она опубликована
    // atitle, $all_articles[$id]['authors'], btitle
    if ($articles_count>0) {
        foreach ($all_articles as $a_id => $an_article) {
            $return .= sprintf($MESSAGES['LoadArticlesList_Each'][$lang],
                $an_article['add_date'], $an_article['article_title'], $an_article['authors'],
                $an_article['book_title'], $an_article['article_id']); // so, topic_title unused, contains topic title
        };
    } else {
        // статей по заданному критерию нет
        //@MESSAGE: "нет статей по заданному критерию"
        switch ($loadmode) {
            case 'search' : {
                $return .= $MESSAGES['LoadArticlesList_SearchNoArticles'][$lang];
                break;
            }
            case 'onload' : {
                $return .= $MESSAGES['LoadArticlesList_OnloadNoArticles'][$lang];
                break;
            }
        } // case loadmode
    } // else
    $return .= $MESSAGES['LoadArticlesList_End'][$lang];
    return $return;
} // function

// Загружает список авторов с отбором по первой букве, буква и язык передаются параметрами
// вызывает нас ajax.php @ load_authors_selected_by_letter
function DBLoadAuthorsSelectedByLetter($letter, $lang)
{
    global $MESSAGES;
    $return = '';
    if ($letter != '0') {
        $like = " AND authors.name_{$lang} LIKE "."'".strtolower($letter)."%'";
    } else {
        $like = '';
    }

    $q = "SELECT * FROM authors WHERE `deleted`=0 ".$like;
    $r = mysql_query($q) or Die(0);
    // ФИО, научное звание/ученая степень
    $return .= $MESSAGES['LoadAuthorsSelectedByLetter_Start'][$lang];

    if ( @mysql_num_rows($r) > 0) {
        while ($i = mysql_fetch_assoc($r)){
            //@this: эктор-ссылка на /authors/info/(id) - работает, якорь для замены в модреврайт
            $id = $i['id'];
            $name = $i['name_'.$lang];
            $title = $i['title_'.$lang];
            $email = $i['email'];
            $return .= sprintf($MESSAGES['LoadAuthorsSelectedByLetter_Start'][$lang], $id, $name, $title, $email);
        } // while
    } else {
        $return .= $MESSAGES['LoadAuthorsSelectedByLetter_Nothing'][$lang];
    }

    $return .= $MESSAGES['LoadAuthorsSelectedByLetter_End'][$lang];
    return $return;
}

/*
//
возврат массива "первых букв" для списка авторов
генерируем массив "на лету" на основе первых букв авторов в зависимости от ЯЗЫКА

вызывает нас ajax.php @  load_letters_optionlist
*/
function DBLoadFirstLettersForSelector($lang)
{
    $ql = "SELECT DISTINCT SUBSTRING(name_{$lang},1,1) AS letter FROM authors WHERE deleted=0";
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

/* Три функции возврата данных в option соотв. селекта */
function returnBooksOptionString_noid($row, $lang, $withoutid)
{
    // @todo: ВАЖНО: ТУТ ЗАДАЕТСЯ ФОРМАТ ВЫВОДА ДАННЫХ В СЕЛЕКТ (оформить функцией на основе шаблона? )
    // по идее можно и с шаблоном, но ну нафиг
    /*     switch ($lang) {
            case 'en': {
                $name = $row['name_en'];
                $title = $row['title_en'];
                break;
            }
            case 'ru': {
                $name = $row['name_ru'];
                $title = $row['title_ru'];
                break;
            }
            case 'uk': {
                $name = $row['name_uk'];
                $title = $row['title_uk'];
                break;
            }
        } */
    $id = ($withoutid==1) ? '' : "[{$row['id']}] " ;

    $title = ($row['title'] != '') ? $row['title'] : 'Unnamed';

    return $id."\"$title\"";
}

function returnAuthorsOptionString_noid($row, $lang, $withoutid)
{
    // @todo: ВАЖНО: ТУТ ЗАДАЕТСЯ ФОРМАТ ВЫВОДА ДАННЫХ В СЕЛЕКТ (оформить функцией на основе шаблона? )
    // по идее можно и с шаблоном, но ну нафиг
    $id = ($withoutid==1) ? '' : "[{$row['id']}] " ;
    switch ($lang) {
        case 'en': {
            $name = $row['name_en'];
            $title = $row['title_en'];
            break;
        }
        case 'ru': {
            $name = $row['name_ru'];
            $title = $row['title_ru'];
            break;
        }
        case 'uk': {
            $name = $row['name_uk'];
            $title = $row['title_uk'];
            break;
        }
    }
    return $id."$name $title";

}

function returnTopicsOptionString_noid($row, $lang, $withoutid)
{
    // @todo: ВАЖНО: ТУТ ЗАДАЕТСЯ ФОРМАТ ВЫВОДА ДАННЫХ В СЕЛЕКТ (оформить функцией на основе шаблона? )
    // по идее можно и с шаблоном, но ну нафиг
    switch ($lang) {
        case 'en': {
            // $name = $row['name_en'];
            $title = $row['title_en'];
            break;
        }
        case 'ru': {
            // $name = $row['name_ru'];
            $title = $row['title_ru'];
            break;
        }
        case 'uk': {
            // $name = $row['name_uk'];
            $title = $row['title_uk'];
            break;
        }
    }
    $id = ($withoutid==1) ? '' : "[{$row['id']}] " ;
    $title = ($title != '') ? $title : '<NONAME>';

    return $id.$title;
}

?>