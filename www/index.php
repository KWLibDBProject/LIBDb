<?php
require_once('core/core.php');
require_once('core/core.db.php');
require_once('core/core.kwt.php');
require_once('frontend.php');
require_once('translations.php');

$site_language = 'en';

$tpl_index = new kwt('tpl/index.tpl.html');
$tpl_index->config('<!--{','}-->');
$tpl_index->contentstart();

$override = array();

$link = ConnectDB();

$override['topics'] = DBLoadTopics($site_language); // в переменной тематические разделы, только LI-элементы вне UL
$override['books'] = DBLoadBooks($site_language); // в переменной книжки в двухэтажном списке,

$fetch = isset($_GET['fetch']) ? $_GET['fetch'] : '';
$with = isset($_GET['with']) ? $_GET['with'] : '';
$message = '';
switch ($fetch) {
    case 'authors': {
        // работа с авторами
        switch ($with) {
            case 'list': {
//+         fetch=authors   &   with=list           Список авторов с селектом по 1 букве
                $path = "tpl/fetch=authors/with=list/";

                $tpl_content = new kwt($path.'f_auth+w_list.tpl[en].html');
                $tpl_content->contentstart();
                $content = $tpl_content->getcontent();

                $tpl_js = new kwt($path.'f_auth+w_list.tpl[en].js');
                $tpl_js->contentstart();
                $jscripts = $tpl_js->getcontent();

                break;
            }
            case 'info': {
//+        ?fetch=authors  &   with=info   & id=?  Расширенная информация по автору + список его статей
                $path = 'tpl/fetch=authors/with=info/';

                $id = $_GET['id'];
                $tpl_content = new kwt($path.'f_auth+w_info.tpl[en].html');

                $air = DBLoadAuthorInformation($id, $site_language);

                $tpl_content_over = array(
                    'author_publications' => DBLoadAuthorPublications($id, $site_language), // = articles.author.id
                    'author_name' => $air['author_name'],
                    'author_title' => $air['author_title'],
                    'author_email' => $air['author_email'],
                    'author_workplace' => $air['author_workplace'],
                );

                $tpl_content->override($tpl_content_over);
                $tpl_content->contentstart();
                $content = $tpl_content->getcontent();

                $tpl_js = new kwt($path.'f_auth+w_info.tpl[en].js');
                $tpl_js->override( array( "author_id" => $id ) );
                $tpl_js->contentstart();
                $jscripts = $tpl_js->getcontent();

                break;
            }
            case 'all': {
                // список ВСЕХ авторов - для поисковых систем
                // И без кнопки перехода на автора, только ссылкой!
                $content = 'ПОЛНЫЙ список авторов без всяких селектов - для поисковых систем';
                $path = 'tpl/fetch=authors/with=extended/';
            }
        } // switch with authors
        break;
    }
    case 'articles': {
        switch ($with) {
            case 'extended': {
//                  ?fetch=articles &   with=extended
//                  Список статей с расширенным отбором
                $content = 'Список статей с расширенным отбором';
                $path = "tpl/fetch=articles/with=extended/";

                $tpl_content = new kwt($path.'f_articles+w_extended.tpl[en].html');
                $tpl_content->contentstart();

                $content = $tpl_content->getcontent();

                $tpl_js = new kwt($path.'f_articles+w_extended.tpl[en].js');
                $tpl_js->contentstart();
                $jscripts = $tpl_js->getcontent();

                break;
            }
            case 'topic' : {
//          ?fetch=articles &   with=with=topic  &   id=? Список статей в топике + селект по сборнику
                $content = 'Список статей с селектом по сборнику';
                $path = 'tpl/fetch=articles/with=topic/';

                $tpl_content = new kwt($path.'f_articles+w_topic.tpl[en].html');
                $tpl_content->contentstart();
                $content = $tpl_content->getcontent();

                $tpl_js = new kwt($path.'f_articles+w_topic.tpl[en].js');
                $tpl_js->config('/*','*/');
                $tpl_js->override( array( "plus_topic_id" => "+".$_GET['id'] ) );

                $tpl_js->contentstart();
                $jscripts = $tpl_js->getcontent();

                break;
            }
            case 'book': {
//              ?fetch=articles &   with=book   &   id=?
//              Список статей в сборнике
                $content = 'Список статей в сборнике';
                $path = 'tpl/fetch=articles/with=book/';

                $tpl_content = new kwt($path.'f_articles+w_book.tpl[en].html');
                $tpl_content->contentstart();
                $content = $tpl_content->getcontent();

                $tpl_js = new kwt($path.'f_articles+w_book.tpl[en].js');
                $tpl_js->config('/*','*/');
                $tpl_js->override( array( "plus_book_id" => "+".$_GET['id'] ) );

                $tpl_js->contentstart();
                $jscripts = $tpl_js->getcontent();


                break;
            }
            case 'info': { //@todo: NOW: Полная информация по статье id=
//          ?fetch=articles &   with=info   &   id=? Полная информация по статье
                $id = $_GET['id'];

                $path = 'tpl/fetch=articles/with=info/';

                $id = $_GET['id'];
                $tpl_content = new kwt($path.'f_article+w_info.tpl[en].html');

                $ai = DBLoadArticleInfo($id, $site_language); // id is article ID
                $al = DBLoadArticleInfoAuthorsList($id, $site_language); // id is article ID

                printr($al);

                $tpl_content_over = array(
                    'article-title' => $ai['title_'.$site_language],
                    'article-abstract' => $ai['abstract_'.$site_language],
                    'article-authors-list' => $al,
                    'article-keywords' => $ai['keywords_'.$site_language],
                    'article-book-title' => $ai['btitle'],
                    'article-book-year' => $ai['byear'],
                    'article-pdfid' => $ai['pdfid']
                );

                $tpl_content->override($tpl_content_over);
                $tpl_content->contentstart();
                $content = $tpl_content->getcontent();

                $tpl_js = new kwt($path.'f_article+w_info.tpl[en].js');
                // $tpl_js->override( array( "article_id" => $id ) );
                $tpl_js->contentstart();
                $jscripts = $tpl_js->getcontent();





                break;
            }
            case 'all': { //@todo: LATER: ПОЛНЫЙ список статей без всяких селектов - для поисковых систем
//              ?fetch=articles &   with=extended   &   id=?
//              ПОЛНЫЙ список статей без всяких селектов - для поисковых систем
                // И без кнопки перехода на статью, только ссылкой!
                $content = 'ПОЛНЫЙ список статей без всяких селектов - для поисковых систем';
                break;
            }

        }; // switch with articles
        break;
    }
    case 'news': {
        $content = "Новости";
        break;
    }
    case 'estuff': {
        $content = 'Редколлегия';
        break;
    }
    default: {
        // эктор "fetch" не установлен
    }
}

$override['content'] = $content;
$override['content_jquery'] = $jscripts;

$tpl_index->override($override);
$tpl_index->contentstart(); // если есть вложенные темплейты, этот вызов обязателен!!!!!!
$tpl_index->out();
?>