<?php
/**
 * User: Arris
 * Date: 12.06.15, time: 0:32
 */

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
    global $HTTPHOST, $NOW;
    $return = '';
    foreach ( $rules as $rule )
    {
        $url = $HTTPHOST . $rule [ 'location' ];

        if ($mode == 'xml') {
            $tpl = new kwt('sitemap_template_each_url.xml');
            $tpl->override( array(
                'location'  =>  $url,
                'lastmod'   =>  $NOW,
                'changefreq'    =>  $rule['period']
            ));
            $return .= $tpl->getcontent() . "\r\n";
        } else {
            $return .= $url . "\r\n";
        }
    }
    return $return;
}

function printSiteMapReportHeader()
{
    echo <<<HEADER_REPORT
<!DOCTYPE HTML>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Sitemap generate report</title>
</head>
<body>
HEADER_REPORT;
}
 
