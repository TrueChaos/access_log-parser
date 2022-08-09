<?php

$filename = "access_log";
$fp = fopen( $filename, 'r');

$view_count = 0;
$urls = [];
$traffic_sum = 0;
$searchers = [ "Google" => 0, "Bing" => 0, "Baidu" => 0, "Yandex" => 0];
$resp_codes = [];


while( !feof( $fp)) {
    $view_count++;

    #разбиваем каждую строку на компоненты
    $str = explode(' ', fgets($fp));

    #извлечение url
    $re = '/http:\/\/.+"$/';
    $url = preg_grep($re, $str);
    $index = array_key_first($url);
    $url = substr($url[$index], 1, -1);
    if( !in_array($url, $urls)){
        $urls[] = $url;
    }

    #извлечение объемов трафика
    $traffic = (int)$str[$index-1];
    $traffic_sum += $traffic;

    #извлечение агентов
    foreach($searchers as $key => $value){
        if(preg_match("/$key/", implode($str))) $searchers[$key] += 1;
    }

    #извлечение кодов ответа
    $code = $str[$index-2];
    if( !in_array($code, array_keys($resp_codes))){
        $resp_codes[$code] = 1;
    }
    else $resp_codes[$code] += 1;
}

class logStats{
    public $views = 0;
    public $urls = 0;
    public $traffic = 0;
    public $crawlers;
    public $statusCodes;
}

$stats = new logStats();
$stats->views = $view_count;
$stats->urls = count($urls);
$stats->traffic = $traffic_sum;
$stats->crawlers = (object) $searchers;
$stats->statusCodes = (object) $resp_codes;

echo json_encode($stats);