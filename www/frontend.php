<?php

// функции, к которым обращается фронтэнд (сам сайт)

// вот нам и понадобился вложенный шаблон... причем не просто вложенный, а повторяемый, типа
// "повторить следующий блок N раз, заменяя какие-то переменные следующими:"
function DBLoadTopics($lang)
{
    $q = "SELECT `id`, `title_{$lang}` AS title FROM topics WHERE `deleted`=0";
    $r = mysql_query($q);
    $ret = '';
    $ret .= <<<DBLoadTopicsStart
<ul>
DBLoadTopicsStart;
    while ($topic = mysql_fetch_assoc($r)) {
        $ret.= <<<DBLoadTopicsItem
        <li><a href="?fetch=articles&with=topic&id={$topic['id']}">{$topic['title']}</a></li>
DBLoadTopicsItem;
    }
   $ret .= <<<DBLoadTopicsEnd
</ul>
DBLoadTopicsEnd;
    return $ret;
}

function DBLoadBooks()
{
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
    $ret = <<<LB_Start
    <ul>
LB_Start;
    foreach ($all_books as $key => $year_books)
    {
        $ret.= <<<LB_Item_Start
    <li>
        <div>$key</div>
        <ul>
LB_Item_Start;
        foreach ($year_books as $id => $book)
        {
            $ret .= <<<LB_Item_Data
            <li><a href="?fetch=articles&with=book&id=$id">$book</a></li>
LB_Item_Data;
        }
$ret.= <<<LB_Item_End
        </ul>
    </li>
LB_Item_End;
    }

    $ret .= <<<LB_End
    </ul>
LB_End;
    return $ret;
}

function DBLoadAuthorInformation($id, $lang) // @todo: this + language!!! + template?
{
    $ret = "Базовая информация об авторе с айди = ".$id;
    return $ret;
}

function DBLoadAuthorPublications($id, $lang) // @todo: this+ language
{
    global $MESSAGES;
    $ret = '';
    $q = "SELECT articles.* FROM articles, cross_aa WHERE cross_aa.article = articles.id AND cross_aa.author=$id";
    $r = mysql_query($q);
    if (@mysql_num_rows($r)>0) {
        $ret .= <<<LAP_START
<ul>
LAP_START;

        while ($i = @mysql_fetch_assoc($r)) {
            $ret .= <<<LAP_ITEM
<li><a href="?fetch=articles&with=info&id={$i['id']}">{$i['title_'.$lang]}</a></li>
LAP_ITEM;
        }

        $ret .= <<<LAP_END
</ul>
LAP_END;
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
    $return = <<<LA_START
<table border="1" width="100%" class="articles_list">
LA_START;
    // название, авторы, сборник, в котором она опубликована
    // atitle, $all_articles[$id]['authors'], btitle
    if ($articles_count>0) {
        foreach ($all_articles as $a_id => $an_article) {
            $return .= <<<LA_EACH
<tr>
    <td>{$an_article['add_date']}</td>
    <td>{$an_article['atitle']}</td>
    <td>{$an_article['authors']}</td>
    <td>{$an_article['btitle']}</td>
    <td>
        <button class="more_info" name="{$an_article['id']}" data-text="More"> >>>>>> </button>
    </td>
</tr>
LA_EACH;
        };
    } else {
        // статей по заданному критерию нет
        //@MESSAGE: "нет статей по заданному критерию"
        switch ($loadmode) {
            case 'search' : {
                $return .= $MESSAGES['LoadArticlesList_OnloadNoArticles'][$lang];
                break;
            }
            case 'onload' : {
                $return .= $MESSAGES['LoadArticlesList_OnloadNoArticles'][$lang];
                break;
            }
        } // case loadmode
    } // else

    $return .= <<<LA_END
</table>
LA_END;

    return $return;
} // function

// Загружает список авторов с отбором по первой букве, буква и язык передаются параметрами
// вызывает нас ajax.php @ load_authors_selected_by_letter
function DBLoadAuthorsSelectedByLetter($letter, $lang)
{
    if ($letter != '0') {
        $like = " AND authors.name_{$lang} LIKE "."'".strtolower($letter)."%'";
    } else {
        $like = '';
    }

    $q = "SELECT * FROM authors WHERE `deleted`=0 ".$like;
    $r = mysql_query($q) or Die(0);
    $n = mysql_num_rows($r);
    // ФИО, научное звание/ученая степень
    //@todo: MESSAGE форматная строка вывода @ load_authors_selected_by_letter
    // LASBL_* лишние, если авторов нет.
    $return = <<<LASBL_START
        <ul>
LASBL_START;
    if ($n > 0) {
        while ($i = mysql_fetch_assoc($r)){
            //@this: эктор-ссылка на /authors/info/(id) - работает, якорь для замены в модреврайт
            $name = $i['name_'.$lang];
            $title = $i['title_'.$lang];

            $return .= <<<LASBL_EACH
<li>
<label>
<a href="?fetch=authors&with=info&id={$i['id']}">{$name} , {$title}, {$i['email']}</a>
</label>
</li>
LASBL_EACH;
        } // while
    } // if
    $return .= <<<LASBL_EMPTY
</ul>
LASBL_EMPTY;

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