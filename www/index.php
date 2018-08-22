<?php
define('__ROOT__', __DIR__);
define('__CONFIG__', __DIR__ . 'config');

require_once (__ROOT__ . '/core/__required.php');
require_once 'frontend.php';

$site_language = GetSiteLanguage();

// defaults fields and variables
$maincontent_html = '';
$maincontent_js = '';
$maincontent_css = '';

$main_template_data = array();

$main_theme_name    = Config::get('frontend/theme/frontend_template_name');
$main_theme_dir     = Config::get('frontend/theme/template_dir');

$main_template_file = "index.{$site_language}.html";

/**
 *  Устанавливаем значения для основного шаблона | Override variables in INDEX.*.HTML template
 *
 *  ВОЗМОЖНО это надо делать через $main_template = new Template(filename, filepath);
 *
 *  а потом
 *
 *  $main_template->set('template_name', $main_theme_name);
 *
 *  или
 *
 *  $main_template->set('inner_html', $subtemplate->render() )
 *
*/
$main_template_data['template_name'] = $main_theme_name; // template name , defined in config
$main_template_data['template_theme_dir'] = $main_theme_dir;

/** META  */
$main_template_data['meta'] = Config::get('frontend_meta');
$main_template_data['meta']['copyright'] = Config::get('meta/copyright', '');

/**
 * Main switch
 */
$fetch  = at( $_GET, 'fetch', '' );
$with   = at( $_GET, 'with' , '' );

/**
 * ВАЖНО !!!
 *
 * Так как шаблоны ETKS и AAIT в основном совпадают (различия только в "рамке картины", в некоторых элементах
 * основного шаблона, а внутренний контент и шаблоны не отличаются, имеет смысл создать шаблон-родитель в папке
 * `template`, к элементам которого мы будем обращаться, генерируя "суб-контент".
 *
 * Таким образом, снижается нагрузка на своевременное обновление обоих шаблонов.
 *
 * В будущем, возможно, стоит перейти на класс WebSunTemplater, который должен выяснять существование нужного ему файла
 * в папке конкретного шаблона и, если его не находит - обращаться к шаблону-родителю.
 *
 * В случае же НЕОБХОДИМОСТИ разделения к примеру, страницы /template/page/default на две разных в зависимости от ETKS или AAIT
 * нам нужно будет
 *
 * 1. исправить пути в секции switch/switch
 * 2. скопировать файлы template/page/default в template.etks/page/default И template.aait/page/default
 * 3. соответственно изменить эти шаблоны ИНДИВИДУАЛЬНО
 *
 * Путь к ТЕМЕ находится в переменной $main_theme_dir
 *
 *
 */

// А теперь надо загрузить контент в основной блок
switch ($fetch) {
    case 'authors' : {
        /* секция обработки авторов - информация или список */
        switch ($with) {
            case 'info': {
                /*расширенная информация по автору + список его статей + фото */
                $id = intval($_GET['id'] ?? 0);

                $subtemplate_dir = "$/template/authors/info/";
                $subtemplate_filename = "authors__info";

                $author_information = LoadAuthorInformation_ById($id, $site_language);
                $author_publications = LoadArticles_ByAuthor($id, $site_language);

                /**
                 * HTML
                 */
                $inner_html_data = [
                    'author_publications'   => $author_publications,

                    'author_name'           => $author_information['author_name'] ?? '',
                    'author_title'          => $author_information['author_title'] ?? '',

                    'author_email'          => $author_information['author_email'] ?? '',
                    'author_orcid'          => $author_information['author_orcid'] ?? '',

                    'author_workplace'      => $author_information['author_workplace'] ?? '',
                    'author_bio'            => $author_information['author_bio'] ?? '',
                    'author_photo_id'       => $author_information['author_photo_id'] ?? -1,
                    'author_photo_link'
                            => (($author_information['author_photo_id'] ?? -1) == -1)
                                ?  "/template/_assets/images/no_photo_{$site_language}.png"
                                :  "core/get.image.php?id={$author_information['author_photo_id']}"
                ];
                $maincontent_html = websun_parse_template_path($inner_html_data, "{$subtemplate_filename}.{$site_language}.html", $subtemplate_dir);

                /** single CSS style file */
                $maincontent_css = websun_parse_template_path([], "{$subtemplate_filename}.css", $subtemplate_dir);

                break;
            }
            case 'all' : {
                // список ВСЕХ авторов - для поисковых систем: фио, титул, email -> link to author page
                $subtemplate_dir = "$/template/authors/all/";
                $subtemplate_filename = "authors__all";

                /**
                 * HTML
                 */
                $inner_html_data = [
                    'site_language'         =>  $site_language,
                    'all_authors_list'      =>  LoadAuthors_ByLetter('', $site_language, 'no')
                ];

                // Зачем так? WebSun имеет проблему с тяжелой проверкой {?**} {?} на больших данных. Ломается прекомпиляция PCRE-выражения.
                // поэтому мы подставляем соотв. файл шаблона в зависимости от - пусты или нет данные?
                // можно было бы использовать ТРИ файла с разными строками, но я решил чуть усложнить шаблон,
                // но обойтись одним файлом с проверкой языка сайта - и разными сообщениями

                $subtemplate_filename_html
                    = (! empty($inner_html_data['all_authors_list']) )
                    ? "{$subtemplate_filename}.{$site_language}.html"
                    : "authors__all__notfound.html";

                $maincontent_html = websun_parse_template_path($inner_html_data, $subtemplate_filename_html, $subtemplate_dir);

                /** single CSS style file */
                $maincontent_css = websun_parse_template_path([], "{$subtemplate_filename}.css", $subtemplate_dir);

                break;
            }
            case 'estaff' : {
                $subtemplate_dir = "$/template/authors/estaff/";
                $subtemplate_filename = "authors__estaff";

                /**
                 * HTML, warning, MAGIC NUMBERS (see table `ref_estaff_roles`)
                 */
                $inner_html_data = [
                    // почетный редактор = 7
                    'honorary_editor'               => getAuthors_EStaffList(7, $site_language),

                    // главный редактор = 5
                    'chief_editor'                  => getAuthors_EStaffList(5, $site_language),

                    // замглавного редактора = 4
                    'chief_editor_assistants'       => getAuthors_EStaffList(4, $site_language),

                    // редакционная коллегия = 3
                    'editorial_board_local'         => getAuthors_EStaffList(3, $site_language),

                    // международная редакционная коллегия = 1
                    'editorial_board_international' => getAuthors_EStaffList(1, $site_language),

                    // редакторы = 6 (в шаблоне таких нет и в базе тоже)
                    'other_editors'                 => getAuthors_EStaffList(6, $site_language),

                    // ответственный секретарь = 8
                    'assistant_editor'              => getAuthors_EStaffList(8, $site_language),
                ];
                $maincontent_html = websun_parse_template_path($inner_html_data, "{$subtemplate_filename}.{$site_language}.html", $subtemplate_dir);

                /** single CSS style file */
                $maincontent_css = websun_parse_template_path([], "{$subtemplate_filename}.css", $subtemplate_dir);

                break;
            }
            case 'list' : {
                $subtemplate_dir = "$/template/authors/list/";
                $subtemplate_filename = "authors__list";

                /**
                 * HTML - used AJAX loaded data
                 */
                $inner_html_data = [];
                $maincontent_html = websun_parse_template_path($inner_html_data, "{$subtemplate_filename}.{$site_language}.html", $subtemplate_dir);

                /**
                 * Здесь можно использовать единый JS-файл с замещаемым значением, но я оставлю так - в файле используется
                 * select/option с дефолтным значением
                 */
                $inner_js_data = [ 'site_language' =>  $site_language ];
                $maincontent_js = websun_parse_template_path($inner_js_data, "{$subtemplate_filename}.{$site_language}.js", $subtemplate_dir);

                /** single CSS style file */
                $maincontent_css = websun_parse_template_path([], "{$subtemplate_filename}.css", $subtemplate_dir);

                break;
            }
        } // end $with authors switch
        break;
    } // end /authors/* case
    case 'articles' : {
        switch ($with) {
            case 'extended' : {
                $subtemplate_dir = "$/template/articles/extended/";
                $subtemplate_filename = "articles__extended";

                /**
                 * HTML
                 */
                $inner_html_data = []; // результаты поиска загружаются аяксом, а в шаблонах никаких замещаемых переменных нет (ну, кроме языка)
                $maincontent_html = websun_parse_template_path($inner_html_data, "{$subtemplate_filename}.{$site_language}.html", $subtemplate_dir);

                /**
                 * Здесь можно использовать единый JS-файл с замещаемым значением, но я оставлю так - в файле генерируется
                 * select/option с дефолтным значением (пока везде на английском, но это, возможно, надо будет изменить)
                 */
                $inner_js_data = [ 'site_language' =>  $site_language ];
                $maincontent_js = websun_parse_template_path($inner_js_data, "{$subtemplate_filename}.{$site_language}.js", $subtemplate_dir);

                break;
            }
            case 'topic' : {
                $id = intval($_GET['id']) ?? 0;

                $subtemplate_dir = "$/template/articles/topic/";
                $subtemplate_filename = "articles__topic";

                /**
                 * HTML
                 */
                $inner_html_data = [
                    'topic_data'    =>  LoadTopicInfo($id, $site_language),
                    'topic_id'      =>  $id,
                    'site_language' =>  $site_language
                ];
                // результаты поиска загружаются аяксом,
                $maincontent_html = websun_parse_template_path($inner_html_data, "{$subtemplate_filename}.{$site_language}.html", $subtemplate_dir);

                /** используются разные JS-файлы
                 * можно было бы сделать один и передавать ему заменяемую переменную, но это лишний код для её генерации на основе языка
                 */
                $maincontent_js = websun_parse_template_path([], "{$subtemplate_filename}.{$site_language}.js", $subtemplate_dir);

                break;
            }
            case 'book' : {
                $id = intval($_GET['id']) ?? 0;

                $subtemplate_dir = "$/template/articles/book/";
                $subtemplate_filename = "articles__book";

                /**
                 * HTML
                 */
                $inner_html_data = [
                    'site_language' =>  $site_language,
                    'book_id'       =>  $id,
                    'book_info'     =>  LoadBookInfo($id),
                ];
                //@todo: в шаблоне используются ссылки на /files/books_file_cover , которые МОГУТ отличаться для разных шаблонов. Надо передать путь к этим файлам

                // результаты поиска загружаются аяксом,
                $maincontent_html = websun_parse_template_path($inner_html_data, "{$subtemplate_filename}.{$site_language}.html", $subtemplate_dir);

                /* JS */
                $maincontent_js = websun_parse_template_path([], "{$subtemplate_filename}.{$site_language}.js", $subtemplate_dir);

                break;
            }
            case 'info' : {
                $id = intval($_GET['id']);

                $subtemplate_dir = "$/template/articles/info/";
                $subtemplate_filename = "articles__info";

                /**
                 * HTML
                 */
                $article_info = LoadArticles_ByQuery(array('article_id' => $id ) , $site_language)[ $id ];

                // список авторов, писавших статью
                $article_authors = $article_info['authors'];

                $inner_html_data = [
                    'article_title'         => $article_info['article_title'] ?? '',
                    'article_abstract'      => $article_info['article_abstract'] ?? '',
                    'article_authors_list'  => $article_authors,
                    'article_keywords'      => $article_info['article_keywords'] ?? '',
                    'article_book_title'    => $article_info['book_title'] ?? '',
                    'article_book_year'     => $article_info['book_year'] ?? '',
                    'article_pdfid'         => $article_info['pdfid'] ?? '',
                    'article_refs'          => $article_info['article_refs'] ?? '',
                    'article_doi'           => $article_info['doi'] ?? '',
                    'article_pdf_last_download_date'
                                            => $article_info['pdf_last_download_date'],

                    'site_language'         => $site_language
                ];

                if (isset($article_info['keywords']) && $article_info['keywords'] != '')
                    $main_template_data['meta']['keywords'] = $article_info['keywords'];

                $maincontent_html = websun_parse_template_path($inner_html_data, "{$subtemplate_filename}.{$site_language}.html", $subtemplate_dir);

                /** single CSS file */
                $maincontent_css = websun_parse_template_path([], "{$subtemplate_filename}.css", $subtemplate_dir);

                break;
            }
            case 'all' : {
                // список ВСЕХ СТАТЕЙ - для поисковых систем -- фио, титул, email -> link to author page

                $subtemplate_dir = "$/template/articles/all/";
                $subtemplate_filename = "articles__all";

                /**
                 * HTML
                 */
                $inner_html_data = [
                    'all_articles_list' => getArticles_PlainList([], $site_language)
                ];

                // Зачем так? WebSun имеет проблему с тяжелой проверкой {?**} {?} на больших данных. Ломается прекомпиляция PCRE-выражения.
                // поэтому мы подставляем соотв. файл шаблона в зависимости от - пусты или нет данные?
                // можно было бы использовать ТРИ файла с разными строками, но я решил чуть усложнить шаблон,
                // но обойтись одним файлом с проверкой языка сайта - и разными сообщениями
                $subtemplate_filename_html
                    = (! empty($inner_html_data['all_articles_list']) )
                    ? "{$subtemplate_filename}.{$site_language}.html"
                    : "articles__all__notfound.html";

                $maincontent_html = websun_parse_template_path($inner_html_data, $subtemplate_filename_html, $subtemplate_dir);

                break;
            }
        } // end $with articles switch
        break;
    } // end /articles/* case
    case 'news' : {
        /* секция вывода новостей */
        switch ($with) {
            case 'the' : {
                /* конкретная новость */

                if (isset($_GET['id'])) {
                    $id = intval($_GET['id']);
                } else {
                    Redirect('?fetch=news&with=list');
                }

                $subtemplate_dir = "$/template/news/the/";
                $subtemplate_filename = "news__the";

                $the_news_item = LoadNewsItem($id, $site_language);

                //@todo: Почему не просто 'news_item' => $the_news_item с доступом по news_item.title / publish_date / text?
                $local_template_data = [
                    'news_item_title'   => $the_news_item['title'] ?? '',
                    'news_item_date'    => $the_news_item['publish_date'] ?? '',
                    'news_item_text'    => $the_news_item['text'] ?? ''
                ];
                $maincontent_html = websun_parse_template_path($local_template_data, "{$subtemplate_filename}.{$site_language}.html", $subtemplate_dir);

                /** single CSS file */
                $maincontent_css = websun_parse_template_path([], "{$subtemplate_filename}.css", $subtemplate_dir);

                break;
            }
            case 'list' : {
                /* список новостей */

                $subtemplate_dir = "$/template/news/list/";
                $subtemplate_filename = "news__list";

                $local_template_data = [
                    'news_list' => LoadNewsListTOC($site_language)
                ];
                $maincontent_html = websun_parse_template_path($local_template_data, "{$subtemplate_filename}.{$site_language}.html", $subtemplate_dir);

                /** single CSS file */
                $maincontent_css = websun_parse_template_path([], "{$subtemplate_filename}.css", $subtemplate_dir);

                break;
            }
        } // /switch $with
        break;
    } // end /news/* case

    case 'page' : {
        /* секция вывода статических или условно-статических страниц */
        $page_alias = ($with === '') ? 'default' : $with;

        $subtemplate_dir = "$/template/page/static/";
        $subtemplate_filename = "page__static";

        /**
         * HTML
         */
        $inner_html_data = [
            'site_language' =>  $site_language,
            'page_alias'    =>  $page_alias,
            'page_data'     =>  LoadStaticPage($page_alias, $site_language)
        ];

        $maincontent_html = websun_parse_template_path($inner_html_data, "{$subtemplate_filename}.{$site_language}.html", $subtemplate_dir);

        break;
    } // case /page

    default : {
        // это статическая страница "о журнале" + список статей в последнем выпуске
        $subtemplate_dir = "$/template/page/default/";
        $subtemplate_filename = "default";

        // load last book

        $last_book = LoadLastBookInfo();
        $last_book_id = $last_book['id'] ?? FALSE;

        $last_book_articles_list = $last_book_id ? getArticlesList([ 'book'  =>  $last_book['id'] ], $site_language, 'no') : [];

        $page_data = LoadStaticPage('about', $site_language);

        /**
         * HTML
         */
        $inner_html_data = [
            'site_language'         =>  $site_language,
            'page_alias'            =>  'about',
            'page_data'             =>  $page_data,
            'articles_list'         =>  $last_book_articles_list,
            'last_book'             =>  $last_book,
        ];

        //@todo: в шаблоне используются ссылки на /files/books_file_cover , которые МОГУТ отличаться для разных шаблонов. Надо передать путь к этим файлам

        $maincontent_html = websun_parse_template_path($inner_html_data, "{$subtemplate_filename}.{$site_language}.html", $subtemplate_dir);

        /** JS file */
        $inner_js_data = [ 'site_language' => $site_language ];
        $maincontent_js = websun_parse_template_path($inner_js_data, "{$subtemplate_filename}.js", $subtemplate_dir);

        break;
    } // end default case

} // end global (fetch) switch

/**
 * Заполняем значения для главного шаблона
 */
/**
 * Есть мнение (2018-08-10), что надо анализировать файл theme.json , в котором типа всё написано...
 * И структуру меню можно генерировать динамически (на основе YAML или JSON), и я языковые надписи брать из theme.en.json

 $main_template_data['frontend']['menu']['title'] = Theme::get('site/title');
 $main_template_data['theme'] = Theme::all()

 */

/**   * Блок "Тематика" (нужно возвращать ARRAY, который разбирается в шаблоне) */
$main_template_data['rubrics']    = printTopicsTree($site_language);    //@todo: когда-нибудь это надо отрефакторить

/**  * Блок "выпуски"  */
$main_template_data['all_books']    = LoadBooks();

/**  * Блок "баннеры" */
$main_template_data['all_banners']  = LoadBanners();

/* Блок "последние новости" (возвращает рендер WEBSUN, нужно возвращать ARRAY, который разбирается в шаблоне) */
$main_template_data['last_news_list'] = LoadLastNews($site_language, 3);

/** Контент  */
$main_template_data['content_jquery'] = $maincontent_js;
$main_template_data['content_html'] = $maincontent_html;
$main_template_data['content_css'] = $maincontent_css;

/** Тип ассетов */
// $main_template_data['frontend_assets_mode'] = Config::get('frontend/assets_mode');

$main_template_data['frontend'] = [
    'assets_mode'           =>  Config::get('frontend/assets_mode', 'development'),
    'assets_version'        =>  Config::get('frontend/assets_version', ''),
    'cookie_site_language'  =>  Config::get('cookie_site_language', 'libdb_sitelanguage')
];

$content = \Websun\websun::websun_parse_template_path($main_template_data, $main_template_file, "$/{$main_theme_dir}");
$content = preg_replace('/^\h*\v+/m', '', $content);
echo $content;

printf("\r\n<!-- Total time: %s sec, Memory Used (current): %s , Memory Used (max): %s -->", round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 4), formatBytes(memory_get_usage()), formatBytes(memory_get_peak_usage()));

