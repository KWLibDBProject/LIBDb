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

$main_template_dir = '$/template.bootstrap24/';
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

                $template_dir = '$/template.bootstrap24/authors/info/';
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
                $template_dir = '$/template.bootstrap24/authors/all/';
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
                $template_dir = '$/template.bootstrap24/authors/estaff/';
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
                $template_dir = '$/template.bootstrap24/authors/list/';
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
                $template_dir = '$/template.bootstrap24/articles/extended/';
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
                $filename = $tpl_path.'/fetch=articles/with=topic/f_articles+w_topic.'.$site_language;
                $id = intval($_GET['id']);

                $inner_html = new kwt($filename.'.html');
                $inner_html->override(array(
                    'topic_title' => $template_engine->getTopicTitle($id)
                ));
                $maincontent_html = $inner_html->get();

                $inner_js = new kwt($filename.'.js', '/*' ,'*/');
                $inner_js->override( array( "plus_topic_id" => "+".$id ) );
                $maincontent_js = $inner_js->get();

                $inner_css = new kwt($filename.".css");
                $maincontent_css = $inner_css->get();
                break;
            }
            case 'book' : {
                $id = intval($_GET['id']);
                $filename = $tpl_path.'/fetch=articles/with=book/f_articles+w_book.'.$site_language;

                $inner_html = new kwt($filename.'.html');
                $book_row = LoadBookInfo($id);

                $inner_html->override( array (
                    'file_cover'    => $book_row['file_cover'] ?? '',
                    'file_title_ru' => $book_row['file_title_ru'] ?? '',
                    'file_title_en' => $book_row['file_title_en'] ?? '',
                    'file_toc_ru'   => $book_row['file_toc_ru'] ?? '',
                    'file_toc_en'   => $book_row['file_toc_en'] ?? '',
                    'book_title'    => $book_row['book_title'] ?? '',
                    'book_year'     => $book_row['book_year'] ?? ''
                ));
                $maincontent_html = $inner_html->get();

                $inner_js = new kwt($filename.'.js', '/*', '*/');
                $inner_js->override( array( "plus_book_id" => "+".$id ) );
                $maincontent_js = $inner_js->get();

                $inner_css = new kwt($filename.".css");
                $maincontent_css = $inner_css->get();
                break;
            }
            case 'info' : {
                $id = intval($_GET['id']);
                $filename = $tpl_path.'/fetch=articles/with=info/f_articles+w_info.'.$site_language;

                $article_info = LoadArticleInformation_ById($id, $site_language); // EQ $article_info = LoadArticles_ByQuery(array('aid' => $id ) , $site_language);
                $article_authors = $template_engine->getAuthors_InArticle($article_info['authors'], 'with-email');
                //@warning: мы вставили в BuildQuery еще несколько полей (article_abstract, article_refs, article_keywords), при поиске по keywords может (!) возникнуть бага -- тесты!
                $inner_html = new kwt($filename.'.html', '<!--{%', '%}-->');
                $inner_html->override( array (
                    'article-title'         => $article_info['article_title'] ?? '',
                    'article-abstract'      => $article_info['article_abstract'] ?? '',
                    'article-authors-list'  => $article_authors, // список авторов, писавших статью
                    'article-keywords'      => $article_info['article_keywords'] ?? '',
                    'article-book-title'    => $article_info['book_title'] ?? '',
                    'article-book-year'     => $article_info['book_year'] ?? '',
                    'article-pdfid'         => $article_info['pdfid'] ?? '',
                    'article-refs'          => $article_info['article_refs'] ?? '',
                    'article-doi'           => $article_info['doi'] ?? '',
                    'article-pdf-last-download-date' => $article_info['pdf_last_download_date']
                ));
                if (isset($article_info['keywords']))
                    $main_template_data['meta_keywords'] = $article_info['keywords'];
                $maincontent_html = $inner_html->get();

                $inner_js = new kwt($filename.'.js', '/*', '*/');
                $inner_js->override( array( "plus_book_id" => "+".$id ) );
                $maincontent_js = $inner_js->get();

                $inner_css = new kwt($filename.".css", '/*', '*/');
                $inner_css->override( array(
                    'article-doi-visibility' => ($article_info['doi']=='') ? 'display:none;' : ''
                ));
                $maincontent_css = $inner_css->get();
                break;
            }
            case 'all' : {
                // список ВСЕХ СТАТЕЙ - для поисковых систем -- фио, титул, email -> link to author page

                $template_dir = '$/template.bootstrap24/articles/all/';
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

                $template_dir = '$/template.bootstrap24/news/the/';
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

                $template_dir = '$/template.bootstrap24/news/list/';
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

        $template_dir = '$/template.bootstrap24/page/static/';
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
        $template_dir = '$/template.bootstrap24/page/default/';
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
