<?php
require_once('core/core.php');
require_once('core/core.db.php');
require_once('core/core.kwt.php');
require_once('frontend.php');
/*  вызов нужного шаблона и его движка. ВАЖНО: такую же строчку надо поменять в ajax.php
    путь до каталога шаблонов определяется во включаемом файле движка шаблона */
require_once('template.bootstrap24.php');

$site_language = GetSiteLanguage();

// init
// defaults fields and variables
$maincontent_html = '';
$maincontent_js = '';
$maincontent_css = '';
// init template override array
$override = array();

// load default index file for template, based on language
$tpl_index = new kwt($tpl_path."/index.{$site_language}.html", '<!--{', '}-->' );

$link = ConnectDB();

// init template engine
$template_engine = new Template($tpl_path, $site_language);

/* Override variables in INDEX.*.HTML template */
$override['template_path'] = $tpl_path; // template directory name (not a path!), defined in template.xxx.php
$override['rubrics']    = $template_engine->getTopics();
$override['books']      = $template_engine->getBooks();
$override['banners']    = $template_engine->getBanners();
$override['last_news_shortlist'] = $template_engine->getLastNews(3);
/* insert menu from template */
$override['main_menu_content'] = $template_engine->getMenu();

// Main switch

$fetch = isset($_GET['fetch']) ? $_GET['fetch'] : '';
$with = isset($_GET['with']) ? $_GET['with'] : '';

switch ($fetch) {
    case 'authors' : {
        /* секция обработки авторов - информация или список */
        switch ($with) {
            case 'info': {
                /*расширенная информация по автору + список его статей + фото */
                $filename = $tpl_path.'/fetch=authors/with=info/f_authors+w_info.'.$site_language;
                $id = $_GET['id'];
                $author_information = LoadAuthorInformation_ById($id, $site_language);
                $author_publications = $template_engine->getArticles_ByAuthor($id);
                /* HTML Template */
                $inner_html = new kwt($filename.".html");
                $inner_html->override( array(
                    'author_publications'   => $author_publications,
                    'author_publications_display_class' => (empty($author_publications)) ? ' hidden ' : ' ',
                    'author_name'           => $author_information['author_name'],
                    'author_title'          => $author_information['author_title'],
                    'author_email'          => $author_information['author_email'],
                    'author_workplace'      => $author_information['author_workplace'],
                    'author_bio'            => $author_information['author_bio'],
                    'author_bio_display_class' => (empty($author_information['author_bio'])) ? ' hidden ' : ' ',
                    'author_photo_id'       => $author_information['author_photo_id'],
                    'author_photo_link'     => ($author_information['author_photo_id'] == -1) ? "/".$tpl_path."/images/no_photo_{$site_language}.png" : "core/getimage.php?id={$author_information['author_photo_id']}"
                ));
                $maincontent_html = $inner_html->get();

                /* JS Template */
                $inner_js = new kwt($filename.".js");
                $inner_js->override( array(
                        "author_is_es" => ($author_information['author_is_es'])==1 ? 'block' : 'none' )
                );
                $maincontent_js = $inner_js->get();

                /* CSS Template */
                $inner_css = new kwt($filename.".css");
                $maincontent_css = $inner_css->get();
                break;
            }
            case 'all' : {
                // список ВСЕХ авторов - для поисковых систем
                // фио, титул, email -> link to author page
                $filename = $tpl_path.'/fetch=authors/with=all/f_authors+w_all.'.$site_language;

                $all_authors_list = $template_engine->getAuthors_PlainList('');

                $inner_html = new kwt($filename.".html");
                $inner_html->override( array (
                    'all_authors_list' => $all_authors_list
                ));
                $maincontent_html = $inner_html->get();

                $inner_js = new kwt($filename.".js");
                $maincontent_js = $inner_js->get();

                $inner_css = new kwt($filename.".css");
                $maincontent_css = $inner_css->get();
                break;
            }
            case 'estaff' : {
                $filename = $tpl_path.'/fetch=authors/with=estaff/f_authors+w_estaff.'.$site_language;

                /* HTML Template */
                $inner_html = new kwt($filename.".html");
                $inner_html->override( array (
                    // главный редактор = 5
                    'estaff_main_editor'        => $template_engine->getAuthors_EStaffList(5),
                    // замглавного редактора = 4
                    'estaff_main_subeditors'    => $template_engine->getAuthors_EStaffList(4),
                    // редакционная коллегия = 3
                    'estaff_local_editors'      => $template_engine->getAuthors_EStaffList(3),
                    // международная редакционная коллегия = 1
                    'estaff_remote_editors'     => $template_engine->getAuthors_EStaffList(1),
                    // редакторы = 6
                    'estaff_simple_editors'     => $template_engine->getAuthors_EStaffList(6),
                ));
                $maincontent_html = $inner_html->getcontent();

                /* JS Template */
                $inner_js = new kwt($filename.".js");
                $maincontent_js = $inner_js->getcontent();

                /* CSS Template */
                $inner_css = new kwt($filename.".css");
                $maincontent_css = $inner_css->getcontent();
                break;
            }
            case 'list' : {
                $filename = $tpl_path.'/fetch=authors/with=list/f_authors+w_list.'.$site_language;

                $inner_html = new kwt($filename.".html");
                $maincontent_html = $inner_html->get();

                $inner_js = new kwt($filename.".js");
                $maincontent_js = $inner_js->get();

                $inner_css = new kwt($filename.".css");
                $maincontent_css = $inner_css->get();
                break;
            }
        } // end $with authors switch
        break;
    } // end /authors/* case
    case 'articles' : {
        switch ($with) {
            case 'extended' : {
                $filename = $tpl_path.'/fetch=articles/with=extended/f_articles+w_extended.'.$site_language;

                $inner_html = new kwt($filename.'.html');
                $maincontent_html = $inner_html->get();

                $inner_js = new kwt($filename.'.js');
                $maincontent_js = $inner_js->get();

                $inner_css = new kwt($filename.".css");
                $maincontent_css = $inner_css->get();
                break;
            }
            case 'topic' : {
                $filename = $tpl_path.'/fetch=articles/with=topic/f_articles+w_topic.'.$site_language;
                $id = $_GET['id'];

                $inner_html = new kwt($filename.'.html');
                $inner_html->override(array(
                    'topic_title' => $template_engine->getTopicTitle($id)
                ));
                $maincontent_html = $inner_html->get();

                $inner_js = new kwt($filename.'.js', '/*' ,'*/');
                $inner_js->override( array( "plus_topic_id" => "+".$_GET['id'] ) );
                $maincontent_js = $inner_js->get();

                $inner_css = new kwt($filename.".css");
                $maincontent_css = $inner_css->get();
                break;
            }
            case 'book' : {
                $id = $_GET['id'];
                $filename = $tpl_path.'/fetch=articles/with=book/f_articles+w_book.'.$site_language;

                $inner_html = new kwt($filename.'.html');
                $book_row = LoadBookInfo($id);

                $inner_html->override( array (
                    'file_cover'    => $book_row['file_cover'],
                    'file_title'    => $book_row['file_title'],
                    'file_toc'      => $book_row['file_toc'],
                    'file_toc_en'   => $book_row['file_toc_en'],
                    'book_title'    => $book_row['book_title'],
                    'book_year'     => $book_row['book_year']
                ));
                $maincontent_html = $inner_html->get();

                $inner_js = new kwt($filename.'.js', '/*', '*/');
                $inner_js->override( array( "plus_book_id" => "+".$_GET['id'] ) );
                $maincontent_js = $inner_js->get();

                $inner_css = new kwt($filename.".css");
                $maincontent_css = $inner_css->get();
                break;
            }
            case 'info' : {
                $id = $_GET['id'];
                $filename = $tpl_path.'/fetch=articles/with=info/f_articles+w_info.'.$site_language;

                $article_info = LoadArticleInformation_ById($id, $site_language); // EQ $article_info = LoadArticles_ByQuery(array('aid' => $id ) , $site_language);
                $article_authors = $template_engine->getAuthors_InArticle($article_info['authors'], 'with-email');
                //@warning: мы вставили в BuildQuery еще несколько полей (article_abstract, article_refs, article_keywords), при поиске по keywords может (!) возникнуть бага -- тесты!
                $inner_html = new kwt($filename.'.html');
                $inner_html->override( array (
                    'article-title'         => $article_info['article_title'],
                    'article-abstract'      => $article_info['article_abstract'],
                    'article-authors-list'  => $article_authors, // список авторов, писавших статью
                    'article-keywords'      => $article_info['article_keywords'],
                    'article-book-title'    => $article_info['book_title'],
                    'article-book-year'     => $article_info['book_year'],
                    'article-pdfid'         => $article_info['pdfid'],
                    'article-refs'          => $article_info['article_refs'],
                    'article-doi'           => $article_info['doi']
                ));
                $override['meta_keywords'] = $article_info['keywords']; // GLOBAL KEYWORDS
                $maincontent_html = $inner_html->get();

                $inner_js = new kwt($filename.'.js', '/*', '*/');
                $inner_js->override( array( "plus_book_id" => "+".$_GET['id'] ) );
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
                $filename = $tpl_path.'/fetch=articles/with=all/f_articles+w_all.'.$site_language;
                $inner_html = new kwt($filename.".html");
                $inner_html->override( array (
                    'all_articles_list' => $template_engine->getArticles_PlainList(array())
                ));
                $maincontent_html = $inner_html->get();

                $inner_js = new kwt($filename.".js");
                $maincontent_js = $inner_js->get();

                $inner_css = new kwt($filename.".css");
                $maincontent_css = $inner_css->get();
                break;
            }
        } // end $with articles switch
        break;
    } // end /articles/* case
    case 'page' : {
        /* секция вывода статических или условно-статических страниц */
        $page_alias = ($with === '') ? 'default' : $with;
        $maincontent_html = $template_engine->getStaticPage($page_alias);

        /* CSS Template */
        $filename = $tpl_path.'/fetch=page/page.'.$site_language;
        $inner_css = new kwt($filename.".css");
        $maincontent_css = $inner_css->get();
        break;
    } // case /page
    case 'news' : {
        /* секция вывода новостей */
        switch ($with) {
            case 'the' : {
                $id = 0;
                if (isset($_GET['id'])) {
                    $id = intval($_GET['id']);
                } else {
                    Redirect('?fetch=news&with=list');
                }

                $filename = $tpl_path.'/fetch=news/with=the/f_news+w_the.'.$site_language;
                $inner_html = new kwt($filename.".html");

                $the_news_item = LoadNewsItem($id, $site_language);

                $inner_html->override( array (
                    'news_item_title'   => $the_news_item['title'],
                    'news_item_date'    => $the_news_item['date_add'],
                    'news_item_text'    => $the_news_item['text']
                ));
                $maincontent_html = $inner_html->getcontent();

                $inner_js = new kwt($filename.".js");
                $maincontent_js = $inner_js->getcontent();

                $inner_css = new kwt($filename.".css");
                $maincontent_css = $inner_css->getcontent();
                break;
            }
            case 'list' : {
                /* список новостей */
                $filename = $tpl_path.'/fetch=news/with=list/f_news+w_list.'.$site_language;

                $news_list_toc = LoadNewsListTOC($site_language);
                // $news_list_toc will be used in php-section of template loaded file

                $inner_html = new kwt($filename.'.html');
                $maincontent_html = $inner_html->getcontent();

                $inner_js = new kwt($filename.'.js');
                $maincontent_js = $inner_js->getcontent();

                $inner_css = new kwt($filename.".css");
                $maincontent_css = $inner_css->getcontent();

                break;
            }
        } // /switch $with
        break;
    } // end /news/* case
    default : {
        // это статическая страница "о журнале" + свидетельство + список статей в последнем выпуске
        $filename = $tpl_path.'/default/default.'.$site_language;
        $inner_html = new kwt($filename.".html");
        $inner_html->override( array(
            'static_page_content'   => $template_engine->GetStaticPage('about')
        ));
        // load last book
        $last_book = LoadLastBookInfo();
        if (count($last_book) != 0) {
            $inner_html->override( array(
                'last_book_content'     => $template_engine->getArticlesList(
                    array(
                        'book'  =>  $last_book['id']
                    ), 'no'),
                'last_book_title_string'=> "{$last_book['title']}, {$last_book['year']}",
                'last_book_cover_id'    => $last_book['file_cover'],
                'last_book_title_id'    => $last_book['file_title'],
                'last_book_toc_id'      => $last_book['file_toc'],
                'last_book_toc_en_id'   => $last_book['file_toc_en'],
            ));
        }
        $maincontent_html = $inner_html->get();

        $inner_js = new kwt($filename.".js");
        $maincontent_js = $inner_js->get();

        /* CSS Template */
        $inner_css = new kwt($filename.".css");
        $maincontent_css = $inner_css->get();
        break;
    } // end default case
} // end global (fetch) switch

$override['content_jquery'] = $maincontent_js;
$override['content_html'] = $maincontent_html;
$override['content_css'] = $maincontent_css;

$tpl_index->override($override);
$tpl_index->out();

?>