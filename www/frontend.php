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
            case 'ua' : {$lang = 'ua'; break;}
        }
    }
    return $lang;
}

/**
 * получение языка сайта из куки
 * @return string
 */
function GetSiteLanguage()
{
    global $CONFIG;

    $cookie_name = $CONFIG['cookie_site_language'];

    $lang = 'en';
    if (isset($_COOKIE[ $cookie_name ]) && $_COOKIE[ $cookie_name ] != '') {
        switch ($_COOKIE[ $cookie_name ]) {
            case 'ru': { $lang = 'ru'; break; }
            case 'ua': { $lang = 'ua'; break; }
            case 'en':
            default:   { $lang = 'en'; break; }
        }
    }
    return $lang;
}

/*
 * Массив с переводами месяцев на разные языки.
 *
 * */
$TRANSLATED_MONTHS = array(
    'en' => array("", "Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sept", "Oct", "Nov", "Dec"),
    'ru' => array("", "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря"),
    'ua' => array("", "Січень", "Лютий", "Березень", "Квітень", "Травень", "Червень", "Липень", "Серпень", "Вересень", "Жовтень", "Листопад", "Грудень"),
);

/**
 * Конвертирует дату (строку) по языку. Использует массив-месяцеслов $TRANSLATED_MONTHS.
 * Версия для PHP7
 * @param $date_as_string
 * @param $lang
 * @return string
 */
function __langDate($date_as_string, $lang)
{
    global $TRANSLATED_MONTHS;
    $date_as_array = date_parse_from_format('d.m.Y',$date_as_string);

    $day = $date_as_array['day'] ?? '';
    $month = $TRANSLATED_MONTHS[$lang][ $date_as_array['month'] ] ?? '';
    $year = $date_as_array['year'] ?? '';

    return "{$day} {$month} {$year}";
}

/**
 * Конвертирует дату (строку) по языку. Использует массив-месяцеслов $TRANSLATED_MONTHS
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
    } else {
        $date_as_array = date_parse($date_as_string);
    }
    $return = "{$date_as_array['day']} {$TRANSLATED_MONTHS[$lang][ $date_as_array['month'] ]} {$date_as_array['year']}";
    return $return;
}

/**
 * возврат массива "первых букв" для списка авторов для указанного языка
 * используется в ajax.php
 * @param $lang
 * @return mixed
 */
function LoadFirstLettersForSelector($lang)
{
    global $mysqli_link;
    $ql = "SELECT DISTINCT SUBSTRING(name_{$lang},1,1) AS letter FROM authors WHERE deleted=0 ORDER BY name_{$lang}";
    $qr = mysqli_query($mysqli_link, $ql);

    if ($qr)
    {
        $qn = @mysqli_num_rows($qr);
        if ($qn > 0) {
            $return['error'] = 0;
            while ($letter = mysqli_fetch_assoc($qr)) {
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

/**
 * функция загрузки статических страниц из БД
 * @param $alias
 * @param $lang
 * @return array
 */
function LoadStaticPage($alias, $lang = 'en')
{
    global $mysqli_link;
    $return = array();
    $alias = mysqli_real_escape_string($mysqli_link, $alias);

    $query = "SELECT content_{$lang} AS pagecontent FROM staticpages WHERE alias LIKE '{$alias}'";
    $res = mysqli_query($mysqli_link, $query);
    $numrows = mysqli_num_rows($res);

    if ($numrows == 1) {
        $a = mysqli_fetch_assoc($res);
        $return['content']  = $a['pagecontent'];
        $return['state']    = '200';
    } else {
        $return['content']  = '';
        $return['state']    = '404';
    }
    return $return;
}

/**
 * загружает из базы информацию об одной рубрике (тематике) в зависимости от языка
 * @param $id
 * @param $lang
 * @return array|null [ id, title ]
 */
function LoadTopicInfo($id, $lang)
{
    global $mysqli_link;
    $q = "SELECT id, title_{$lang} AS title FROM topics WHERE id={$id}";
    $r = mysqli_query($mysqli_link, $q);
    $topic = null;

    if (@mysqli_num_rows($r) == 1)
    {
        $topic = mysqli_fetch_assoc($r);
    }

    return $topic;
}

/**
 * загружает из базы рубрики (тематики), отдает ассоциативный массив вида [id -> title]
 * @param $lang
 * @return array
 */
function LoadTopics($lang)
{
    global $mysqli_link;
    $q = "SELECT id, title_{$lang} AS title FROM topics ORDER BY title_{$lang}";
    $r = mysqli_query($mysqli_link, $q);
    $ret = array();
    $num_rows = mysqli_num_rows($r);

    if ($num_rows > 0)
    {
        while ($topic = mysqli_fetch_assoc($r)) {
            $ret[ $topic['id']  ] = $topic['title'];
        }
    }

    return $ret;
}

/**
 * загружает из базы рубрики в древовидном представлении, отдает ассоциативный массив вида [id -> title]
 * @param $lang
 * @param int $withoutid
 * @return array
 */
function LoadTopicsTree($lang, $withoutid=1)
{
    global $mysqli_link;
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

    $r = mysqli_query($mysqli_link, $query);
    $data = array();
    $num_rows = mysqli_num_rows($r);

    if ($num_rows > 0)
    {
        $group = '';
        $i = 1;
        while ($row = mysqli_fetch_assoc($r))
        {
            if ($group != $row['title_group']) {
                // send new optiongroup
                $group_id = 'g_'.$row['id'];

                $data['data'][ $i ] = array(
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

/**
 * загружает список сборников (книг) из базы, года в обратном порядке, сборники в прямом
 * @return array
 */
function LoadBooks()
{
    global $mysqli_link;
    $all_books = [];

    $bq = "SELECT
books.title AS title,
books.year  AS year,
books.id    AS bid,
COUNT(books.id) AS articles_count

FROM books, articles

WHERE
 articles.book = books.id AND  
 books.published = 1

GROUP BY books.title
ORDER BY books.title DESC ";

    $br = mysqli_query($mysqli_link, $bq);
    $is_active = 1;

    $all_books = [];

    while ($book_any = mysqli_fetch_assoc($br)) {
        $book = [
            'year'      =>  $book_any['year'],
            'bid'       =>  $book_any['bid'],
            'title'     =>  $book_any['title'],
            'count'     =>  $book_any['articles_count'],
            'is_active' =>  $is_active
        ];

        // kwt-variant
        // $all_books[ $book_any['year'] ][ $book_any['bid'] ] = $book;

        // websun variant
        $all_books[ $book_any['year'] ]['yearly_books'][ $book_any['bid'] ] = $book;
        $all_books[ $book_any['year'] ]['is_active'] = $is_active;

        $is_active = 0;
    }

    return $all_books;
}

/**
 * загружает массив отображаемых баннеров из базы
 * @return array|null
 */
function LoadBanners()
{
    global $mysqli_link;
    $ret = array();
    $query = "SELECT * FROM banners WHERE data_is_visible=true";
    $res = mysqli_query($mysqli_link, $query) or die("mysqli_query_error: ".$query);
    $res_numrows = @mysqli_num_rows($res);
    if ($res_numrows > 0)
    {
        while ($row = mysqli_fetch_assoc($res)) {
            $ret[] = $row;
        }
    } else $ret = null;
    return $ret;
}

/**
 * возвращает для override-переменной последние $count новостей для правого блока (под сборниками):
 * отдает массив [id новости] => [id, title, date]
 *
 * @param $lang
 * @param int $count
 * @return array
 */
function LoadLastNews($lang, $count=2)
{
    global $mysqli_link;
    $ret = array();
    $query = "SELECT id, title_{$lang} AS title, DATE_FORMAT(date_add, '%d.%m.%Y') as date_add FROM news ORDER BY timestamp DESC LIMIT {$count}";
    $res = mysqli_query($mysqli_link, $query) or die("mysqli_query_error: ".$query);
    $res_numrows = @mysqli_num_rows($res);
    $i = 1;
    if ($res_numrows > 0)
    {
        while ($row = mysqli_fetch_assoc($res)) {
            $row['date_add'] = __langDate($row['date_add'], $lang); // websun variant

            $ret[ $i ] = $row;
            $i++;
        }
    }
    return $ret;
}

/**
 * возвращает массив с информацией об указанном сборнике
 * @param $id
 * @return array
 */
function LoadBookInfo($id)
{
    global $mysqli_link;
    $query = "
    SELECT 
        books.title AS book_title, 
        books.year AS book_year, 
        file_cover, 
        file_title_ru, 
        file_title_en, 
        file_toc_ru, 
        file_toc_en 
    FROM books 
    WHERE id={$id}";

    $r = mysqli_query($mysqli_link, $query) or die($query);

    $ret = [];
    if (@mysqli_num_rows($r)==1) {
        $ret = mysqli_fetch_assoc($r);
    }
    return $ret;
}

/**
 * возвращает ассоциативный массив из базы с информацией о ПОСЛЕДНЕМ опубликованном сборнике
 * или {} если нет такого
 * @return array
 */
function LoadLastBookInfo()
{
    global $mysqli_link;

    //@todo: рефакторинг даты

    $r = mysqli_query($mysqli_link, "SELECT * FROM books WHERE published=1 ORDER BY date desc LIMIT 1"); // is enought for latest published book ?

    $ret = [];
    if (@mysqli_num_rows($r)==1) {
        $ret = mysqli_fetch_assoc($r);
    }
    return $ret;
}

/**
 * построение универсального запроса. @WARNING: GOD OBJECT
 *
 * @param $get
 * @param $lang
 * @return string
 */
function BuildQuery($get, $lang)
{
    global $mysqli_link;

    // DATE_FORMAT(date_add, '%d.%m.%Y') as date_add,

    $q_select = " SELECT DISTINCT
  articles.id
, articles.udc AS article_udc
, DATE_FORMAT(date_add, '%d.%m.%Y') AS article_add_date
, articles.title_{$lang} AS article_title
, articles.book
, articles.topic
, books.title AS book_title
, topics.title_{$lang} AS topic_title
, books.year AS book_year
, articles.pages AS article_pages
, pdfid
, doi
, filestorage.username AS pdf_filename
, DATE_FORMAT(filestorage.stat_date_download, '%d.%m.%Y') AS pdf_last_download_date ";
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
    if (isset($get['actor']) && ($get['actor'] == 'load_articles_expert_search')) {
        /* пример: AND authors.name_en LIKE 'Mak%' */
        /* пример: AND articles.udc LIKE '%621%' */
        /* пример: AND articles.add_date LIKE '%2013' */
        /* пример: AND (articles.keywords_en LIKE '%robot%' OR ... OR ... )*/
        /*@todo: critical: экранировать значения: possible SQL injection and script crush! */

        $q_expert .= ($get['expert_name'] != '')
            ? " AND authors.name_{$lang} LIKE '" . mysqli_real_escape_string($mysqli_link, $get['expert_name']) . "%' "
            : "";

        $q_expert .= ($get['expert_udc'] != '')
            ? " AND articles.udc LIKE '%" . mysqli_real_escape_string($mysqli_link, $get['expert_udc']) . "%' "
            : "";

        $q_expert .= ($get['expert_add_date'] != '')
            ? " AND DATE_FORMAT(date_add, '%d.%m.%Y') LIKE '%" . mysqli_real_escape_string($mysqli_link, $get['expert_add_date']) . "' "
            : "";

        /* это оптимизированная достраивалка запроса на основе множественных keywords */
        $keywords = explode(' ', mysqli_real_escape_string($mysqli_link, $get['expert_keywords'] ) );
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

/**
 * Загрузка статей по сложному запросу ($with_email - передается в LoadAuthorsByArticle, который отдает МАССИВ авторов по статье)
 * ВАЖНО: если мы получили ОДНОГО автора - его можно будет получить вызовом: reset(...)
 *
 * @param $get
 * @param $lang
 * @return array
 */
function LoadArticles_ByQuery($get, $lang)
{
    global $mysqli_link;

    $query = BuildQuery($get, $lang);

    $res = mysqli_query($mysqli_link, $query) or die("ОШИБКА: Доступ к базе данных ограничен, запрос: ".$query);
    $articles_count = @mysqli_num_rows($res);
    $all_articles = array();

    if ($articles_count > 0) {
        while ($an_article = mysqli_fetch_assoc($res))
        {
            $id = $an_article['id'];

            $an_article['pdf_last_download_date'] = __langDate($an_article['pdf_last_download_date'], $lang);

            $all_articles[$id] = $an_article;
            $all_articles[$id]['authors'] = LoadAuthors_ByArticle($id, $lang);

            /*
             * @todo: здесь делается запрос к базе - извлекаются все авторы у статьи с переданным ID.
             *
             * Когда мы выводим /articles/info - это нетяжелый запрос.
             *
             * Проблема в том, что для построения полного списка статей таких запросов делается МНОГО.
             *
             * Размышления на эту тему есть /core/core.articles/articles.action.list.php - там используется тот же механизм выборки данных
             * и изложены методы оптимизации.
             *
             */

        } //end while
    }
    return $all_articles;
}

/**
 * загружает данные для списка новостей [id] => [id => '', title => '', date => '']
 * @param $lang
 * @return null|string
 */
function LoadNewsListTOC($lang, $limit = 15)
{
    global $mysqli_link;
    $ret = null;

    $query = "SELECT id, title_{$lang} AS title, DATE_FORMAT(date_add, '%d.%m.%Y') as date FROM news ORDER BY timestamp DESC LIMIT {$limit}";
    $r = @mysqli_query($mysqli_link, $query);
    if ($r) {
        while ($row = mysqli_fetch_assoc($r)) {
            $ret[ $row['id'] ] = $row;
        }
    }

    return $ret;
}

/*  */
/**
 * загружает в ассоциативный массив новость с указанным id,
 * usable: используется для pure-вставки в шаблон
 *
 * @param $id
 * @param $lang
 * @return array|null|string
 */
function LoadNewsItem($id, $lang)
{
    global $mysqli_link;
    $ret = null;
    $query = "SELECT id, title_{$lang} AS title, text_{$lang} AS text, DATE_FORMAT(date_add, '%d.%m.%Y') as date_add FROM news where id={$id}";
    $r = @mysqli_query($mysqli_link, $query);
    if ($r) {
        if (@mysqli_num_rows($r) > 0) {
            $ret = mysqli_fetch_assoc($r);
        }
    }
    return $ret;
}

/**
 * загружает информацию об авторе
 *
 * @param $id
 * @param $lang
 * @return array
 */
function LoadAuthorInformation_ById($id, $lang)
{
    global $mysqli_link;
    $author = [];

    $q = "SELECT * FROM `authors` WHERE id=$id";
    $r = mysqli_query($mysqli_link, $q);
    if (@mysqli_num_rows($r)>0) {
        $result = mysqli_fetch_assoc($r);

        $author = [
            'author_name'   =>  $result["name_{$lang}"],
            'author_title'  =>  $result["title_{$lang}"],
            'author_workplace'  =>  $result["workplace_{$lang}"],
            'author_bio'        =>  $result["bio_{$lang}"],

            'author_email'  =>  $result['email'],
            'author_orcid'  =>  $result['orcid'],

            'author_is_es'      =>  $result["is_es"],
            'author_photo_id'   =>  $result['photo_id']
        ];
    }
    return $author;
}

/**
 * возвращает список статей, которые написал указанный ($id) автор, но только в опубликованных сборниках
 *
 * Используется для построения списка публикаций у автора (для /author/info )
 *
 * @todo: добавить флаг $is_published
 *
 * @param $id
 * @param $lang
 * @param $is_published
 * @return array
 */
function LoadArticles_ByAuthor($id, $lang, $is_published = true)
{
    global $mysqli_link;
    $ret = [];

    $query_published = $is_published ? 1 : 0;

    //@todo: здесь

    $q = "SELECT
articles.id AS aid,
articles.title_{$lang} AS atitle,
articles.pdfid,
books.title AS btitle,
SUBSTRING(books.date,7,4) AS bdate
FROM articles, cross_aa, books
WHERE books.id=articles.book
AND cross_aa.article = articles.id
AND books.published= {$query_published}
AND cross_aa.author = $id
ORDER BY date_add
";
    $r = mysqli_query($mysqli_link, $q);
    if (@mysqli_num_rows($r) > 0) {
        while ($article = mysqli_fetch_assoc($r)) {
            $ret [ $article['aid'] ] = $article;
        }
    }
    return $ret;
}

/**
 * загрузка списка авторов с отбором по первой букве (в зависимости от языка)
 * значение буквы по умолчанию '0', что означает ВСЕ авторы // @todo: рефакторнинг: замена на *
 * функция используется в аякс-ответах, в выгрузке полного списка авторов и выгрузке списка авторов по первой букве
 *
 * @param $letter
 * @param $lang
 * @param string $is_es
 * @param $selfhood
 * @return array
 */
function LoadAuthors_ByLetter($letter, $lang, $is_es='no', $selfhood=-1)
{
    global $mysqli_link;
    $authors = array();
    // check for letter, '0' is ANY first letter
    if ($letter == '') {
        $letter = '0';
    } else {
        $letter = mysqli_real_escape_string($mysqli_link, $letter);
    }

    $where_like = ($letter != '0') ? " AND authors.name_{$lang} LIKE '{$letter}%'" : " ";

    // check for 'is author in editorial stuff', default is 'no'
    $where_es = ($is_es != 'no') ? ' AND is_es=1 ' : '';

    // optional parameter selfhood (for extended estuff)
    $where_selfhood = ($selfhood != -1 )
        ? " AND selfhood= " . intval($selfhood)
        : " ";

    $order = " ORDER BY authors.name_{$lang}";

    $q = "SELECT id, email, orcid, phone, 
    name_{$lang} AS name,
    title_{$lang} AS title,
    workplace_{$lang} AS workplace
    FROM authors
    WHERE deleted=0
    {$where_es}
    {$where_selfhood}
    {$where_like}
    {$order}";

    $r = mysqli_query($mysqli_link, $q) or Die(0);

    if ( @mysqli_num_rows($r) > 0 ) {
        while ($i = mysqli_fetch_assoc($r)) {
            $authors[ $i['id'] ] = $i;
        }
    }
    return $authors;
}


/**
 * возвращает базовую информацию о статье как асс.массив (single-версия LoadArticlesByQuery() )
 * @param $id
 * @param $lang
 * @return mixed
 */
function LoadArticleInformation_ById($id, $lang)
{
    $articles = LoadArticles_ByQuery(array('article_id' => $id ) , $lang)[$id];

    // если использовать reset() - вроде бы более корректно - вернет 1 элемент массива или FALSE если элемента нет или он приводится к FALSE
    // $articles = reset($articles);

    return $articles;
}


/**
 * возвращает список авторов, участвовавших в создании статьи - как асс.массив c учетом языка!
 * вызывается из LoadArticles_ByQuery (в основном) (? и единоразово из template::вывод авторов, писавших статью (по шаблону вывода))
 *
 * @param $id
 * @param $lang
 * @return array
 */
function LoadAuthors_ByArticle($id, $lang)
{
    global $mysqli_link;

    $q = "SELECT authors.id AS author_id, name_{$lang} AS author_name, title_{$lang} AS author_title , email AS author_email FROM authors, cross_aa WHERE cross_aa.author = authors.id AND cross_aa.article=$id ORDER BY name_{$lang}";
    $ret = array();
    if ($r = mysqli_query($mysqli_link, $q)) {
        while ($row = @mysqli_fetch_assoc($r)) {
            $ret[ $row['author_id'] ] = $row;
        }
    }
    return $ret;
}
