<?php
define('__ROOT__', __DIR__);

require_once (__ROOT__ . '/core/__required.php');
require_once 'frontend.php';
require_once 'template.bootstrap24.php';

$site_language = GetSiteLanguage();

// defaults fields and variables
$maincontent_html = '';
$maincontent_js = '';
$maincontent_css = '';

// init template override array
// main_template_data
$main_template_data = array();

// $x = \LIBDb\Config::get('frontend_template_name');

$main_template_dir = '$/template.bootstrap24';
$main_template_file = "index.{$site_language}.html";

// load default index file for template, based on language
// $tpl_index = new kwt($tpl_path."/index.{$site_language}.html", '<!--{', '}-->' );

// init template engine
$template_engine = new Template($CONFIG['frontend_template_name'], $site_language); //@todo disable this after full refactoring

/* Override variables in INDEX.*.HTML template */
$main_template_data['template_name'] = $CONFIG['frontend_template_name']; // template name , defined in config

/**
 * Блок "Тематика" (нужно возвращать ARRAY, который разбирается в шаблоне)
 */
$main_template_data['rubrics']    = $template_engine->getTopicsTree(); //@TODO: работает - не трогай (там очень уж замороченно, используется HEREDOC)

/**
 * Блок "выпуски" (возвращает рендер WEBSUN. нужно возвращать ARRAY, который разбирается в шаблоне)
 */
$main_template_data['books']      = $template_engine->getBooks();

/**
 * Блок "баннеры" (возвращает рендер WEBSUN, нужно возвращать ARRAY, который разбирается в шаблоне)
 */
$main_template_data['banners']    = $template_engine->getBanners();

/*
 * Блок "последние новости" (возвращает рендер WEBSUN, нужно возвращать ARRAY, который разбирается в шаблоне)
 */
$main_template_data['last_news_shortlist'] = $template_engine->getLastNews(3);

// Main switch
$fetch  = at( $_GET, 'fetch', '' );
$with   = at( $_GET, 'with' , '' );

// А теперь надо загрузить контент в основной блок
switch ($fetch) {
    case 'authors' : {
        /* секция обработки авторов - информация или список */
        switch ($with) {
            case 'info': {
                /*расширенная информация по автору + список его статей + фото */
                $id = intval($_GET['id']);

                $template_dir = "{$main_template_dir}/authors/info/";
                $template_file_name = "authors__info.{$site_language}";

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
                            => ($author_information['author_photo_id'] == -1)
                            ?  "/".$tpl_path."/images/no_photo_{$site_language}.png"
                            :  "core/get.image.php?id={$author_information['author_photo_id']}"
                ];
                $maincontent_html = \Websun\websun::websun_parse_template_path($inner_html_data, "{$template_file_name}.html", $template_dir);

                /**
                 * CSS - can load single CSS style file or EXPORT it to common theme file
                 */
                $maincontent_css = \Websun\websun::websun_parse_template_path([], "{$template_file_name}.css", $template_dir);

                break;
            }
            case 'all' : {
                // список ВСЕХ авторов - для поисковых систем: фио, титул, email -> link to author page
                $template_dir = "{$main_template_dir}/authors/all/";
                $template_file_name = "authors__all.{$site_language}";

                /**
                 * HTML
                 */
                $inner_html_data = [
                    'all_authors_list'      => LoadAuthors_ByLetter('', $site_language, 'no')
                ];
                $maincontent_html = \Websun\websun::websun_parse_template_path($inner_html_data, "{$template_file_name}.html", $template_dir);

                /**
                 * CSS - can load single CSS style file or EXPORT it to common theme file
                 */
                $maincontent_css = \Websun\websun::websun_parse_template_path([], "{$template_file_name}.css", $template_dir);

                break;
            }
            case 'estaff' : {
                $template_dir = "{$main_template_dir}/authors/estaff/";
                $template_file_name = "authors__estaff.{$site_language}";

                /**
                 * HTML, warning, MAGIC NUMBERS (see table `ref_selfhood`)
                 */
                $inner_html_data = [
                    // почетный редактор = 7
                    'honorary_editor'               => $template_engine->getAuthors_EStaffList(7),

                    // главный редактор = 5
                    'chief_editor'                  => $template_engine->getAuthors_EStaffList(5),

                    // замглавного редактора = 4
                    'chief_editor_assistants'       => $template_engine->getAuthors_EStaffList(4),

                    // редакционная коллегия = 3
                    'editorial_board_local'         => $template_engine->getAuthors_EStaffList(3),

                    // международная редакционная коллегия = 1
                    'editorial_board_international' => $template_engine->getAuthors_EStaffList(1),

                    // редакторы = 6 (в шаблоне таких нет и в базе тоже)
                    'other_editors'                 => $template_engine->getAuthors_EStaffList(6),

                    // ответственный секретарь = 8
                    'assistant_editor'              =>  $template_engine->getAuthors_EStaffList(8),
                ];
                $maincontent_html = \Websun\websun::websun_parse_template_path($inner_html_data, "{$template_file_name}.html", $template_dir);

                /**
                 * CSS - can load single CSS style file or EXPORT it to common theme file
                 */
                $maincontent_css = \Websun\websun::websun_parse_template_path([], "{$template_file_name}.css", $template_dir);

                break;
            }
            case 'list' : {
                $template_dir = "{$main_template_dir}/authors/list/";
                $template_file_name = "authors__list.{$site_language}";

                /**
                 * HTML - used AJAX loaded data
                 */
                $inner_html_data = [];
                $maincontent_html = \Websun\websun::websun_parse_template_path($inner_html_data, "{$template_file_name}.html", $template_dir);

                /**
                 * JS - can load single JS file with exported $language value
                 */
                $inner_js_data = [
                    'site_language' =>  $site_language
                ];
                $maincontent_js = \Websun\websun::websun_parse_template_path($inner_js_data, "{$template_file_name}.js", $template_dir);

                /**
                 * CSS  - can load single CSS style file or EXPORT it to common theme file
                 */
                $maincontent_css = \Websun\websun::websun_parse_template_path([], "{$template_file_name}.css", $template_dir);

                break;
            }
        } // end $with authors switch
        break;
    } // end /authors/* case
    case 'articles' : {
        switch ($with) {
            case 'extended' : {
                $template_dir = "{$main_template_dir}/articles/extended/";
                $template_file_name = "articles__extended.{$site_language}";

                /**
                 * HTML
                 */
                $inner_html_data = []; // результаты поиска загружаются аяксом, а в шаблонах никаких замещаемых переменных нет (ну, кроме языка)
                $maincontent_html = \Websun\websun::websun_parse_template_path($inner_html_data, "{$template_file_name}.html", $template_dir);

                /**
                 * JS - технически мы можем использовать единственный JS-файл с переданным ему <языком сайта>
                 */
                $inner_js_data = [
                    'site_language' =>  $site_language
                ];
                $maincontent_js = \Websun\websun::websun_parse_template_path($inner_js_data, "{$template_file_name}.js", $template_dir);

                break;
            }
            case 'topic' : {
                $id = intval($_GET['id']);

                $template_dir = "{$main_template_dir}/articles/topic/";
                $template_file_name = "articles__topic.{$site_language}";

                /**
                 * HTML
                 */
                $inner_html_data = [
                    'topic_title'   =>  LoadTopicInfo($id, $site_language)['title'],
                    'topic_id'      =>  $id,
                    'site_language' =>  $site_language
                ];
                // результаты поиска загружаются аяксом,
                $maincontent_html = \Websun\websun::websun_parse_template_path($inner_html_data, "{$template_file_name}.html", $template_dir);

                /**
                 * JS - технически мы можем использовать единственный JS-файл
                 */
                $maincontent_js = \Websun\websun::websun_parse_template_path([], "{$template_file_name}.js", $template_dir);

                break;
            }
            case 'book' : {
                $id = intval($_GET['id']);

                $template_dir = "{$main_template_dir}/articles/book/";
                $template_file_name = "articles__book.{$site_language}";

                /**
                 * HTML
                 */
                $inner_html_data = [
                    'site_language' =>  $site_language,
                    'book_id'       =>  $id,
                    'book_info'     =>  LoadBookInfo($id),
                ];
                // результаты поиска загружаются аяксом,
                $maincontent_html = \Websun\websun::websun_parse_template_path($inner_html_data, "{$template_file_name}.html", $template_dir);

                /**
                 * JS - технически мы можем использовать единственный JS-файл
                 */
                $maincontent_js = \Websun\websun::websun_parse_template_path([], "{$template_file_name}.js", $template_dir);

                break;
            }
            case 'info' : {
                $id = intval($_GET['id']);

                $template_dir = "{$main_template_dir}/articles/info/";
                $template_file_name = "articles__info.{$site_language}";

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
                    'article_pdf_last_download_date' => $article_info['pdf_last_download_date'],
                    'site_lang'             => $site_language
                ];

                if (isset($article_info['keywords']) && $article_info['keywords'] != '') $main_template_data['meta_keywords'] = $article_info['keywords'];

                $maincontent_html = \Websun\websun::websun_parse_template_path($inner_html_data, "{$template_file_name}.html", $template_dir);

                /**
                 * CSS - can load single CSS style file or EXPORT it to common theme file
                 */
                $maincontent_css = \Websun\websun::websun_parse_template_path([], "{$template_file_name}.css", $template_dir);

                break;
            }
            case 'all' : {
                // список ВСЕХ СТАТЕЙ - для поисковых систем -- фио, титул, email -> link to author page

                $template_dir = "{$main_template_dir}/articles/all/";
                $template_file_name = "articles__all.{$site_language}";

                /**
                 * HTML
                 */
                $inner_html_data = [
                    'all_articles_list' => $template_engine->getArticles_PlainList(array())
                ];

                $maincontent_html = \Websun\websun::websun_parse_template_path($inner_html_data, "{$template_file_name}.html", $template_dir);

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

                $template_dir = "{$main_template_dir}/news/the/";
                $template_file_name = "news__the.{$site_language}";

                $the_news_item = LoadNewsItem($id, $site_language);

                $local_template_data = [
                    'news_item_title'   => $the_news_item['title'] ?? '',
                    'news_item_date'    => $the_news_item['date_add'] ?? '',
                    'news_item_text'    => $the_news_item['text'] ?? ''
                ];
                $maincontent_html = \Websun\websun::websun_parse_template_path($local_template_data, "{$template_file_name}.html", $template_dir);

                $maincontent_css = \Websun\websun::websun_parse_template_path([], "{$template_file_name}.css", $template_dir);

                break;
            }
            case 'list' : {
                /* список новостей */

                $template_dir = "{$main_template_dir}/news/list/";
                $template_file_name = "news__list.{$site_language}";

                $local_template_data = [
                    'news_list' => LoadNewsListTOC($site_language)
                ];
                $maincontent_html = \Websun\websun::websun_parse_template_path($local_template_data, "{$template_file_name}.html", $template_dir);

                $maincontent_css = \Websun\websun::websun_parse_template_path([], "{$template_file_name}.css", $template_dir);

                break;
            }
        } // /switch $with
        break;
    } // end /news/* case

    case 'page' : {
        /* секция вывода статических или условно-статических страниц */
        $page_alias = ($with === '') ? 'default' : $with;

        $template_dir = "{$main_template_dir}/page/static/";
        $template_file_name = "page__static.{$site_language}";

        /**
         * HTML
         */
        $inner_html_data = [
            'page_data'  =>  LoadStaticPage($page_alias, $site_language)
        ];

        $maincontent_html = \Websun\websun::websun_parse_template_path($inner_html_data, "{$template_file_name}.html", $template_dir);

        break;
    } // case /page

    default : {
        // это статическая страница "о журнале" + свидетельство + список статей в последнем выпуске
        $template_dir = "{$main_template_dir}/page/default/";
        $template_file_name = "default.{$site_language}";

        // load last book
        $last_book = LoadLastBookInfo(); //@todo: СЕЙЧАС возвращается latest сборник по дате, без учета флага is_published + наличие статей в сборнике

        $page_data = LoadStaticPage('about', $site_language);

        /**
         * HTML
         */
        $inner_html_data = [
            'page_data'             =>  $page_data,

            'articles_list'         =>  $template_engine->getArticlesList([ 'book'  =>  $last_book['id'] ], 'no'),

            'last_book'             =>  $last_book,
        ];

        $maincontent_html = \Websun\websun::websun_parse_template_path($inner_html_data, "{$template_file_name}.html", $template_dir);

        /**
         * JS - технически мы можем использовать единственный JS-файл с переданным ему <языком сайта>
         */
        $inner_js_data = [];
        $maincontent_js = \Websun\websun::websun_parse_template_path($inner_js_data, "{$template_file_name}.js", $template_dir);

        break;
    } // end default case

} // end global (fetch) switch

$main_template_data['content_jquery'] = $maincontent_js;
$main_template_data['content_html'] = $maincontent_html;
$main_template_data['content_css'] = $maincontent_css;
$main_template_data['frontend_assets_mode'] = $CONFIG['frontend_assets_mode'];

$content = \Websun\websun::websun_parse_template_path($main_template_data, $main_template_file, $main_template_dir);
$content = preg_replace('/^\h*\v+/m', '', $content);
echo $content;
