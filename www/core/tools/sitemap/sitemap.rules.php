<?php
/**
 * User: Arris
 * Date: 20.01.15, time: 5:04
 */

$SITEMAP_CONFIG_DYNAMIC = array(
    'articles'      =>  array(
        'iterator'  =>  'id',
        'request'   =>  "SELECT id, stat_date_update FROM articles",
        'location'  =>  "?fetch=articles&with=info&id=???",
        'period'    =>  'weekly',
        'lastmod'   =>  'stat_date_update'
    ),
    'authors'       =>  array(
        'iterator'  =>  'id',
        'request'   =>  "SELECT id, stat_date_update FROM authors",
        'location'  =>  "?fetch=authors&with=info&id=???",
        'period'    =>  'weekly',
        'lastmod'   =>  'stat_date_update'
    ),
    'topics'       =>  array(
        'iterator'  =>  'id',
        'request'   =>  "SELECT id FROM topics",
        'location'  =>  "?fetch=articles&with=topic&id=???",
        'period'    =>  'weekly',
        'lastmod'   =>  'stat_date_update'
    ),
    'books'         => array(
        'iterator'  =>  'id',
        'request'   =>  "SELECT id, title, published FROM books WHERE published = 1",
        'location'  =>  "?fetch=articles&with=book&id=???",
        'period'    =>  'weekly',
        'lastmod'   =>  'stat_date_update'
    ),
    'staticpages'         => array(
        'iterator'  =>  'alias',
        'request'   =>  "SELECT id, alias, public FROM staticpages WHERE public = 1",
        'location'  =>  "?fetch=page&with=???",
        'period'    =>  'daily',
        'lastmod'   =>  'stat_date_update'
    ),
    'news'         => array(
        'iterator'  =>  'id',
        'request'   =>  "SELECT id, stat_date_update FROM news",
        'location'  =>  "?fetch=news&with=the&id=???",
        'period'    =>  'daily',
        'lastmod'   =>  'stat_date_update'
    ),
);

$SITEMAP_CONFIG_STATIC = array(
    '/'      =>  array(
        'period'    =>  'daily',
        'location'  =>  ''
    ),
    '/authors/list'      =>  array(
        'period'    =>  'daily',
        'location'  =>  '?fetch=authors&with=list'
    ),
    '/news/list'      =>  array(
        'period'    =>  'daily',
        'location'  =>  '?fetch=news&with=list'
    ),
    '/articles/all'      =>  array(
        'period'    =>  'daily',
        'location'  =>  '?fetch=articles&with=all'
    ),
    '/authors/all'      =>  array(
        'period'    =>  'daily',
        'location'  =>  '?fetch=authors&with=all'
    ),
);


?>