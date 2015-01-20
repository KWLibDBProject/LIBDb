<?php
/**
 * User: Arris
 * Date: 20.01.2015, time: 2:18 - 4:10
 * Sitemap generation script
 * http://www.sitemaps.org/ru/protocol.html
 */
$BASEPATH = $_SERVER['DOCUMENT_ROOT'];
require_once($BASEPATH.'/core/core.php');
require_once($BASEPATH.'/core/core.db.php');
require_once($BASEPATH.'/core/core.kwt.php');
require_once($BASEPATH.'/core/tools/sitemap/sitemap.rules.php');

$HTTPHOST = "http://" . $_SERVER['HTTP_HOST'] . "/";
$NOW = ConvertTimestampToDate();

function getSiteMapDynamic( $rules , $mode = 'string')
{
    global $HTTPHOST, $NOW;
    $return = '';

    $query          = $rules ['request'];
    $request        = mysql_query($query);
    $format_line    = $rules [ 'location' ];

    while ($each = mysql_fetch_assoc($request))
    {
        $url = $HTTPHOST . str_replace("???", $each[ $rules['iterator'] ], $format_line);

        if ($mode == 'xml') {
            $tpl = new kwt('sitemap_template_each_url.xml');
            $tpl->override( array(
                'location'  =>  $url,
                'lastmod'   =>  isset( $each[ $rules['lastmod'] ] ) ? $each[ $rules['lastmod'] ] : $NOW,
                'changefreq'    =>  $rules['period']
            ));
            $return .= $tpl->getcontent() . "\r\n";
        } else {
            $return .= $url . "\r\n";
        }


    }
    return $return;
}

function getSiteMapStatic( $rules , $mode = 'string')
{
    global $HTTPHOST;
    $return = '';
    foreach ( $rules as $rule )
    {
        $url = $HTTPHOST . $rule [ 'location' ];

        if ($mode == 'xml') {
            $tpl = new kwt('sitemap_template_each_url.xml');
            $tpl->override( array(
                'location'  =>  $url,
                'lastmod'   =>  isset( $each[ $rules['lastmod'] ] ) ? $each[ $rules['lastmod'] ] : $NOW,
                'changefreq'    =>  $rules['period']
            ));
            $return .= $tpl->getcontent() . "\r\n";
        } else {
            $return .= $url . "\r\n";
        }
    }
    return $return;
}

/* =============================== void MAIN(void) ========================== */

$link = ConnectDB();

$sitemap = '';
$sitemap_mode = 'xml';

// Generate sitemap records based on dynamic (iterated) content
foreach ($SITEMAP_CONFIG_DYNAMIC as $rulename => $rule) {
    $sitemap .= getSiteMapDynamic( $rule, $sitemap_mode );
    println("Sitemap records for [{$rulename}] generated. ");
}

// Generate sitemap records based on static content
$sitemap .= getSiteMapStatic( $SITEMAP_CONFIG_STATIC, $sitemap_mode );

println("Sitemap records for static pages generated. ");

// Encode '&' entites to masked '&amp;'
$sitemap = str_replace('&', '&amp;', $sitemap);

if ($sitemap_mode == 'xml') {
    // Generate result XML file.
    // We can't use kwt, due 'include()' for XML file generated parsing error!!!
    // @Todo: KWT: обеспечить возможность вставки XML-файлов и файлов с близокой к ПХП разметкой (<?xml ) без попытки их "исполнения". Вероятно, следует использовать вместо include(file) read()?
$sitemap_final = <<<XML_SITEMAP
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    {$sitemap}</urlset>
XML_SITEMAP;
} else {
    $sitemap_final = $sitemap;
}

// Write to file
$f = fopen($BASEPATH."/sitemap.{$sitemap_mode}", "w+");
fwrite( $f, $sitemap_final );
fclose($f);

println("Sitemap file written.");


CloseDB( $link );

