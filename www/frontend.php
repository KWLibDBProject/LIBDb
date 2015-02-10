<?php
/* ------------------------------- Служебные функции ----------------------------- */

/**
 * @param $data
 */
function debug($data)
{
    print('<pre>'.print_r($data, true).'</pre>');
}

/**
 *
 * @param $request_string
 * @return string       -- sql-безопасный результат
 */
function GetRequestLanguage($request_string)
{
    $lang = 'en';
    if (isset($request_string)) {
        switch ($request_string) {
            case 'ru' : {$lang = 'ru'; break;}
            case 'en' : {$lang = 'en'; break;}
            case 'uk' : {$lang = 'uk'; break;}
            case 'ua' : {$lang = 'uk'; break;}
        }
    }
    return $lang;
}

/* получение языка сайта из куки  */
/**
 * @return string
 */
function GetSiteLanguage()
{
    $lang = 'en';
    if (isset($_COOKIE['libdb_sitelanguage']) && $_COOKIE['libdb_sitelanguage'] != '') {
        switch ($_COOKIE['libdb_sitelanguage']) {
            /* case 'en': {
                $lang = 'en';
                break;
            } */
            case 'ru': { $lang = 'ru'; break; }
            case 'uk': { $lang = 'uk'; break; }
            case 'en':
            default: {   $lang = 'en'; break; }
        }
    }
    return $lang;
}

/* Конвертирует дату (строку) по языку ; */
$TRANSLATED_MONTHS = array(
    'en' => array("", "Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sept", "Oct", "Nov", "Dec"),
    'ru' => array("", "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря"),
    'uk' => array("", "Січень", "Лютий", "Березень", "Квітень", "Травень", "Червень", "Липень", "Серпень", "Вересень", "Жовтень", "Листопад", "Грудень"),
);
/**
 * @param $date_as_string
 * @param $lang
 * @return string
 */
function ConvertDateByLang($date_as_string, $lang)
{
    // в PHP младше 5.2 date_parse() не определена. Смотри stewarddb.
    /* $return = date("d M Y", strtotime($date_as_string)); */
    global $TRANSLATED_MONTHS;
    if (function_exists('date_parse_from_format')) {
        $date_as_array = date_parse_from_format('d.m.Y',$date_as_string);
        // $return = "{$date_as_array['day']} {$TRANSLATED_MONTHS[$lang][ $date_as_array['month'] ]} {$date_as_array['year']}";
    } else {
        $date_as_array = date_parse($date_as_string);
        // $return = "{$date_as_array['day']} {$TRANSLATED_MONTHS[$lang][ $date_as_array['month'] ]} {$date_as_array['year']}";
    }
    $return = "{$date_as_array['day']} {$TRANSLATED_MONTHS[$lang][ $date_as_array['month'] ]} {$date_as_array['year']}";
    return $return;
}

// возврат массива "первых букв" для списка авторов для указанного языка
// используется в ajax.php
/**
 * @param $lang
 * @return mixed
 */
function LoadFirstLettersForSelector($lang)
{
    $ql = "SELECT DISTINCT SUBSTRING(name_{$lang},1,1) AS letter FROM authors WHERE deleted=0 ORDER BY name_{$lang}";
    if ($qr = mysql_query($ql))
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

/* ---------------------------- Функции загрузки данных ---------------------------*/
/* функция загрузки статических страниц из БД */
/**
 * @param $alias
 * @param $lang
 * @return array
 */
function LoadStaticPage($alias, $lang)
{
    $return = array();
    $alias = mysql_real_escape_string($alias);

    $query = "SELECT content_{$lang} AS pagecontent FROM staticpages WHERE alias LIKE '{$alias}'";
    $res = mysql_query($query);
    $numrows = mysql_num_rows($res);

    if ($numrows == 1) {
        $a = mysql_fetch_assoc($res);
        $return['content']  = $a['pagecontent'];
        $return['state']    = '200';
    } else {
        $return['content']  = '';
        $return['state']    = '404';
    }
    return $return;
}

/* загружает из базы информацию об одной рубрике в зависимости от языка */
/**
 * @param $id
 * @param $lang
 * @return string
 */
function LoadTopicInfo($id, $lang)
{
    $q = "SELECT id, title_{$lang} AS title FROM topics WHERE id={$id}";
    $r = mysql_query($q);
    $ret = '';

    if (@mysql_num_rows($r) == 1)
    {
        $topic = mysql_fetch_assoc($r);
        $ret = $topic['title'];
    }
    return $ret;
}

/* загружает из базы рубрики, отдает ассоциативный массив вида [id -> title] */
/**
 * @param $lang
 * @return array
 */
function LoadTopics($lang)
{
    $q = "SELECT id, title_{$lang} AS title FROM topics ORDER BY title_{$lang}";
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

/* загружает из базы рубрики в древовидном представлении, отдает ассоциативный массив вида [id -> title] */
/**
 * @param $lang
 * @param int $withoutid
 * @return array
 */
function LoadTopicsTree($lang, $withoutid=1)
{
    $withoutid = $withoutid || 1;

    $query = "
SELECT
topics.id,
topics.title_{$lang} AS title_topic,
topicgroups.title_{$lang}  AS title_group
FROM topics
LEFT JOIN topicgroups ON topicgroups.id = topics.rel_group
ORDER BY topicgroups.display_order, topics.title_{$lang}
";

    $r = mysql_query($query);
    $data = array();

    if (@mysql_num_rows($r) > 0)
    {
        $group = '';
        $i = 1;
        while ($row = mysql_fetch_assoc($r))
        {
            if ($group != $row['title_group']) {
                // send new optiongroup
                $group_id = 'g_'.$row['id'];

                $data['data'][ $i] = array(
                    'type'      => 'group',
                    'value'     => $group_id,
                    'text'      => $row['title_group']
                );
                $i++;
                $group = $row['title_group'];
            }
            $data['data'][ $i ] = array(
                'type'      => 'option',
                'value'     => $row['id'],
                'text'      => (($withoutid==1) ? '' : "[{$row['id']}] ").(($row['title_topic'] != '') ? $row['title_topic'] : '< NONAME >')
            );
            // send option
            $i++;
        }
    } else {
        $data['data'][1] = array(
            'type'      => 'option',
            'value'     => -1,
            'text'      => "Добавьте темы (топики) в базу!!!"
        );
        $data['error'] = 1;
    }
    return $data;
}

/* загружает список сборников (книг) из базы, года в обратном порядке, сборники в прямом */
/**
 * @return array
 */
function LoadBooks()
{
    $all_books = array();

    $bq = "SELECT books.title as title, books.year AS year, books.id as bid, COUNT(books.id) AS articles_count
FROM books, articles
WHERE
articles.book = books.id AND
books.published = 1
GROUP BY books.title
ORDER BY books.title";

    $br = mysql_query($bq);
    while ($ba = mysql_fetch_assoc($br)) {
        $all_books[ $ba['year'] ][ $ba['bid'] ]['title'] = $ba['title'];
        $all_books[ $ba['year'] ][ $ba['bid'] ]['count'] = $ba['articles_count'];
    }
    return $all_books;
}

/* загружает массив отображаемых баннеров из базы */
/**
 * @return array|null
 */
function LoadBanners()
{
    $ret = array();
    $query = "SELECT * FROM banners WHERE data_is_visible=true";
    $res = mysql_query($query) or die("mysql_query_error: ".$query);
    $res_numrows = @mysql_num_rows($res);
    if ($res_numrows > 0)
    {
        while ($row = mysql_fetch_assoc($res)) {
            $ret[] = $row;
        }
    } else $ret = null;
    return $ret;
}

/* возвращает для override-переменной последние $count новостей для правого блока (под сборниками): */
/* отдает массив [id новости] => [id, title, date] */
/**
 * @param $lang
 * @param int $count
 * @return array
 */
function LoadLastNews($lang, $count=2)
{
    $ret = array();
    $query = "SELECT id, title_{$lang} AS title, date_add FROM news ORDER BY timestamp DESC LIMIT {$count}";
    $res = mysql_query($query) or die("mysql_query_error: ".$query);
    $res_numrows = @mysql_num_rows($res);
    $i = 1;
    if ($res_numrows > 0)
    {
        while ($row = mysql_fetch_assoc($res)) {
            $ret[ $i ] = $row;
            $i++;
        }
    }
    return $ret;
}

/* возвращает массив с информацией об указанном сборнике */
/**
 * @param $id
 * @return array
 */
function LoadBookInfo($id)
{
    $query = "SELECT books.title AS book_title, books.year AS book_year, file_cover, file_title_ru, file_title_en, file_toc_ru, file_toc_en FROM books WHERE id={$id}";
    $r = mysql_query($query) or die($query);
    if (@mysql_num_rows($r)==1) {
        $ret = mysql_fetch_assoc($r);
    } else {
        $ret = array();
    }
    return $ret;
}

/* возвращает асс.массив из базы с информацией о ПОСЛЕДНЕМ опубликованном сборнике или {} если нет такого */
/**
 * @return array
 */
function LoadLastBookInfo()
{
    $r = mysql_query("SELECT * FROM books WHERE published=1 ORDER BY timestamp desc LIMIT 1");
    if (@mysql_num_rows($r)==1) {
        $ret = mysql_fetch_assoc($r);
    } else {
        $ret = array();
    }
    return $ret;
}

/* построение универсального запроса WARNING: GOD OBJECT */
/**
 * @param $get
 * @param $lang
 * @return string
 */
function BuildQuery($get, $lang)
{
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
, pdfid
, doi
, filestorage.username AS pdf_filename ";
/* дополнительные поля (для /article/info ) */
    $q_select .= "
, articles.abstract_{$lang} AS article_abstract
, articles.refs_{$lang} AS article_refs
, articles.keywords_{$lang} AS article_keywords
";
    // $q_select_expert = ", articles.keywords_{$lang}";
    $q_select .= "
, books.id AS book_id
    ";

    $q_from = " FROM
articles
, books, topics
, cross_aa
, authors
, filestorage ";

    $query_show_published = '';
    $q_base_where = " WHERE
articles.pdfid = filestorage.id AND
authors.id = cross_aa.author AND
articles.id = cross_aa.article AND
books.id = articles.book AND
topics.id = articles.topic AND
articles.deleted = 0 AND
topics.deleted=0 {$query_show_published} ";

    $q_final = " GROUP BY articles.title_{$lang} ORDER BY articles.id ";

    /* condition for single article request */

    $q_base_where .= (IsSet($get['article_id']) && ($get['article_id'] != 0))          ? " AND articles.id = {$get['article_id']} " : "";

    /* Extended search conditions */
    $q_extended = '';

    $q_extended .= (IsSet($get['book']) && ($get['book'] != 0))
        ? " AND articles.book = " . intval($get['book'])
        : "";

    $q_extended .= (IsSet($get['topic']) && ($get['topic'] != 0))
        ? " AND articles.topic = " . intval($get['topic'])
        : "";

    $q_extended .= (IsSet($get['letter']) && ($get['letter'] != '0'))
        ? " AND authors.name_{$lang} LIKE '" . substr($get['letter'], 0, 6) ."%' "
        : "";

    $q_extended .= (IsSet($get['aid']) && ($get['aid'] != 0))
        ? " AND authors.id = " . intval($get['aid'])
        : "";

    $q_extended .= (IsSet($get['year']) && ($get['year'] != 0))
        ? " AND books.year = " . intval($get['year'])
        : "";

    /* Expert search conditions */
    $q_expert = '';
    if ($get['actor'] == 'load_articles_expert_search') {
        /* пример: AND authors.name_en LIKE 'Mak%' */
        /* пример: AND articles.udc LIKE '%621%' */
        /* пример: AND articles.add_date LIKE '%2013' */
        /* пример: AND (articles.keywords_en LIKE '%robot%' OR ... OR ... )*/
        /*@todo: critical: экранировать значения: possible SQL injection and script crush! */

        $q_expert .= ($get['expert_name'] != '')
            ? " AND authors.name_{$lang} LIKE '" . mysql_real_escape_string($get['expert_name']) . "%' "
            : "";

        $q_expert .= ($get['expert_udc'] != '')
            ? " AND articles.udc LIKE '%" . mysql_real_escape_string($get['expert_udc']) . "%' "
            : "";

        $q_expert .= ($get['expert_add_date'] != '')
            ? " AND articles.add_date LIKE '%" . mysql_real_escape_string( $get['expert_add_date']) . "' "
            : "";

        /* это оптимизированная достраивалка запроса на основе множественных keywords */
        $keywords = explode(' ', mysql_real_escape_string( $get['expert_keywords'] ) );
        $q_expert .= " AND ( ";
        foreach ($keywords as $keyword) {
            $q_expert .= " articles.keywords_{$lang} LIKE '%{$keyword}%' OR ";
        }
        $q_expert = substr($q_expert , 0 , (strlen($q_expert)-4));
        $q_expert .= " ) ";
    }

    // склейка строки запроса
    $q = $q_select . $q_from . $q_base_where . $q_extended . $q_expert . $q_final;
    return $q;
}

/* Загрузка статей по сложному запросу ($with_email - передается в LoadAuthorsByArticle, который отдает МАССИВ авторов по статье) */
/* ВАЖНО: если мы получили ОДНОГО автора - его можно будет получить вызовом: reset(...) */
/**
 * @param $get
 * @param $lang
 * @return array
 */
function LoadArticles_ByQuery($get, $lang)
{
    $query = BuildQuery($get, $lang);
    $res = mysql_query($query) or die("ОШИБКА: Доступ к базе данных ограничен, запрос: ".$query);
    $articles_count = @mysql_num_rows($res);
    $all_articles = array();

    if ($articles_count > 0) {
        while ($an_article = mysql_fetch_assoc($res))
        {
            $id = $an_article['id'];
            $all_articles[$id] = $an_article;
            $all_articles[$id]['authors'] = LoadAuthors_ByArticle($id, $lang);
            //@todo: REFACTORING + OVER-OPTIMISATION : для того чтобы обойтись без дополнительного селекта - надо переписать полностью BuildQuery() чтобы она отдавала еще и ВСЕХ авторов пофамильно. Надо ли? У нас не миллион запросов... пока что.
        } //end while
    }
    return $all_articles;
}

/* загружает данные для списка новостей [id] => [id => '', title => '', date => ''] */
/**
 * @param $lang
 * @return null|string
 */
function LoadNewsListTOC($lang)
{
    $ret = '';
    $query = "SELECT id, title_{$lang} AS title, date_add AS date FROM news ORDER BY timestamp DESC LIMIT 15";
    $r = @mysql_query($query);
    if ($r) {
        while ($row = mysql_fetch_assoc($r)) {
            $ret[ $row['id'] ] = $row;
        }
    } else $ret = null;
    return $ret;
}

/* загружает в асс.массив новость с указанным id, usable: используется для pure-вставки в шаблон */
/**
 * @param $id
 * @param $lang
 * @return array|null|string
 */
function LoadNewsItem($id, $lang)
{
    $ret = '';
    $query = "SELECT id, title_{$lang} AS title, text_{$lang} AS text, date_add FROM news where id={$id}";
    $r = @mysql_query($query);
    if ($r) {
        if (@mysql_num_rows($r) > 0) {
            $ret = mysql_fetch_assoc($r);
        }
    } else $ret = null;
    return $ret;
}

/* загружает информацию об авторе в ассциативный массив */
/**
 * @param $id
 * @param $lang
 * @return string
 */
function LoadAuthorInformation_ById($id, $lang)
{
    $ret = '';
    $q = "SELECT * FROM authors WHERE id=$id";
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

/* возвращает список статей, которые написал указанный ($id) автор, но только в опубликованных сборниках */
/**
 * @param $id
 * @param $lang
 * @return array
 */
function LoadArticles_ByAuthor($id, $lang)
{
    $ret = array();
    $q = "SELECT
articles.id AS aid,
articles.title_{$lang} AS atitle,
articles.pdfid,
books.title AS btitle,
SUBSTRING(books.date,7,4) AS bdate
FROM articles, cross_aa, books
WHERE books.id=articles.book
AND cross_aa.article = articles.id
AND books.published=1
AND cross_aa.author = $id
ORDER BY add_date
";
    $r = mysql_query($q);
    if (@mysql_num_rows($r) > 0) {
        while ($article = mysql_fetch_assoc($r)) {
            $ret [ $article['aid'] ] = $article;
        }
    }
    return $ret;
}

// загрузка списка авторов с отбором по первой букве (в зависимости от языка)
// значение буквы по умолчанию '0', что означает ВСЕ авторы
// функция используется в аякс-ответах, в выгрузке полного списка авторов и выгрузке
// списка авторов по первой букве
/**
 * @param $letter
 * @param $lang
 * @param string $is_es
 * @param $selfhood
 * @return array
 */
function LoadAuthors_ByLetter($letter, $lang, $is_es='no', $selfhood=-1)
{
    $authors = array();
    // check for letter, '0' is ANY first letter
    if ($letter == '') {
        $letter = '0';
    } else {
        $letter = mysql_real_escape_string($letter);
    }

    $where_like = ($letter != '0') ? " AND authors.name_{$lang} LIKE '{$letter}%'" : " ";

    // check for 'is author in editorial stuff', default is 'no'
    $where_es = ($is_es != 'no') ? ' AND is_es=1 ' : '';

    // optional parameter selfhood (for extended estuff)
    $where_selfhood = ($selfhood != -1 )
        ? " AND selfhood= " . intval($selfhood)
        : " ";

    $order = " ORDER BY authors.name_{$lang}";

    $q = "SELECT id, email,
    name_{$lang} AS name,
    title_{$lang} AS title,
    workplace_{$lang} AS workplace
    FROM authors
    WHERE deleted=0
    {$where_es}
    {$where_selfhood}
    {$where_like}
    {$order}";

    $r = mysql_query($q) or Die(0);

    if ( @mysql_num_rows($r) > 0 ) {
        while ($i = mysql_fetch_assoc($r)) {
            $authors[ $i['id'] ] = $i;
        }
    }
    return $authors;
}


// возвращает базовую информацию о статье как асс.массив (single-версия LoadArticlesByQuery() )
/**
 * @param $id
 * @param $lang
 * @return mixed
 */
function LoadArticleInformation_ById($id, $lang)
{
    $ret = reset(LoadArticles_ByQuery(array('article_id' => $id ) , $lang));
    return $ret;
}


// возвращает список авторов, участвовавших в создании статьи - как асс.массив c учетом языка!
// вызывается из LoadArticles_ByQuery (в основном) и единоразово из template::вывод авторов, писавших статью (по шаблону вывода)
/**
 * @param $id
 * @param $lang
 * @return array
 */
function LoadAuthors_ByArticle($id, $lang)
{
    $q = "SELECT authors.id AS author_id, name_{$lang} AS author_name, title_{$lang} AS author_title , email AS author_email FROM authors, cross_aa WHERE cross_aa.author = authors.id AND cross_aa.article=$id ORDER BY name_{$lang}";
    $ret = array();
    if ($r = mysql_query($q)) {
        while ($row = @mysql_fetch_assoc($r)) {
            $ret[ $row['author_id'] ] = $row;
        }
    }
    return $ret;
}

?>