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
require_once($BASEPATH.'/core/tools/sitemap/sitemap.core.php');
require_once($BASEPATH.'/core/tools/sitemap/sitemap.rules.php');

$exit_message =
    $_GET['target'] == 'iframe'
        ? '<a href="#" onclick="javascript:window.parent.closeIframe()">Close window</a>'
        : '<a href="/core/"><<< Back</a>';

$HTTPHOST = "http://" . $_SERVER['HTTP_HOST'] . "/";
$NOW = ConvertTimestampToDate();

/* =============================== void MAIN(void) ========================== */

$link = ConnectDB();

$sitemap = '';
$sitemap_mode = 'xml';

printSiteMapReportHeader();


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

echo <<<FINAL_MESSAGE
<hr>
{$exit_message}
</body>
</html>
FINAL_MESSAGE;
die();
