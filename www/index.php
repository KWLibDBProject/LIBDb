<?php
require_once('core/core.php');
require_once('core/core.db.php');
require_once('core/core.kwt.php');
require_once('core/core.frontend.php');

$tpl_index = new kwt('tpl/index.tpl.html');
$tpl_index->config('<!--{','}-->');
$tpl_index->contentstart();

$override = array();

// загрузим "статические" поля

$link = ConnectDB();
// {topics}
// сначала поле загрузим в переменную, потом обработаем массивом foreach

$override['topics'] = DBLoadTopics(); // в переменной тематические разделы, форматированы
$override['books'] = DBLoadBooks(); // в переменной книжки в двухэтажном списке, форматированы

$fetch = isset($_GET['fetch']) ? $_GET['fetch'] : '';
$with = isset($_GET['with']) ? $_GET['with'] : '';
$message = '';
switch ($fetch) {
    case 'authors': {
        // работа с авторами
        switch ($with) {
            case 'list': {
//                  fetch=authors   &   with=list
//                  Список авторов с селектом по 1 букве
                $tpl_content = new kwt('tpl/f_auth+w_list.tpl.html');
                $tpl_content->contentstart();
                $content = $tpl_content->getcontent();
                $tpl_js = new kwt('tpl/f_auth+w_list.tpl.js');
                $tpl_js->contentstart();
                $jscripts = $tpl_js->getcontent();

                break;
            }
            case 'info': {
//                  ?fetch=authors  &   with=info   & id=?
                $id = $_GET['id'];
//                  Расширенная информация по автору + список его статей
                $tpl_content = new kwt('tpl/f_auth+w_info.tpl.html');
                $tpl_content_over = array(
                    'author_info' => DBLoadAuthorInformation($id),
                    'author_publications' => DBLoadAuthorPublications($id)
                );
                $tpl_content->override($tpl_content_over);

                $tpl_content->contentstart();
                $content = $tpl_content->getcontent();

                $tpl_js = new kwt('tpl/f_auth+w_info.tpl.js');
                $tpl_js->override( array( "author_id" => $id ) );
                $tpl_js->contentstart();
                $jscripts = $tpl_js->getcontent();

                //@todo: почему во вложенном теплейте не парсятся переменные?
                break;
            }
        } // switch with authors
        break;
    }
    case 'articles': {
        switch ($with) {
            case 'all': {
//                  ?fetch=articles &   with=all
//                  Список статей с расширенным отбором
                $message = 'Список статей с расширенным отбором';

                break;
            }
            case 'topic' : {
//                  ?fetch=articles &   with=topic  &   id=?
//                  Список статей с селектом по сборнику
                $message = 'Список статей с селектом по сборнику';

                break;
            }
            case 'book': {
//              ?fetch=articles &   with=book   &   id=?
//              Список статей в сборнике
                $message = 'Список статей в сборнике';

                break;
            }
            case 'info': {
//              ?fetch=articles &   with=info   &   id=?
//              Полная информация по статье
                $message = 'Полная информация по статье';
                break;
            }
        }; // switch with articles
        break;
    }
    case 'news': {
        $message = "Новости";
        break;
    }
    case 'estuff': {
        $message = 'Редколлегия';
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