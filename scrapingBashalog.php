<?php
require_once('./phpQuery-onefile.php');
require_once('./log.php');
set_time_limit(600);
$log = Logger::getInstance();

define('URL', 'http://bashalog.c-brains.jp/category/programming/');
$options = [
    'http' => [
        'method' => 'GET',
        'header' => 'User-Agent: iOS',
    ],
];
$context = stream_context_create($options);
$html = file_get_contents(URL, false, $context);
$doc = phpQuery::newDocument(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
$d = $doc;
// $maxPage = $d->find('.next')->prev()->text();

$arrInfos = [
    'タイトル',
    '概要',
    'url',
    '<title>',
    '<meta name="description">',
    '<keywords>',
    '更新日',
];
$time_start = microtime(true);
$fp = fopen('outputCsv\bashaLog.csv', 'w');
fputcsv($fp, $arrInfos);

for ($i = 1; $i <= 11; $i++) {
    if ($i !== 1) {
        $url = URL . 'index_' . $i . '.php';
    } else {
        $url = URL . 'index.php';
    }
    $html = file_get_contents($url, false, $context);
    $doc = phpQuery::newDocument(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    $d = $doc;

    // $iページ目を記事を取得
    // var_dump('一覧URL　' . $url);
    foreach ($doc->find('.box-tile') as $tmp) {
        $dd = pq($tmp);
        $url = $dd->find('a')->attr('href');
        $showPage = file_get_contents($url, false, $context);
        $showDoc = phpQuery::newDocument(mb_convert_encoding($showPage, 'HTML-ENTITIES', 'UTF-8'));
        foreach($showDoc as $tmp) {            
            $showD = pq($tmp);
            $showData = [
                $showD->find('.ttl-post-a')->text(),
                $showD->find('meta[name="description"]')->attr('content'),
                $url,
                '<title>' . $showD->find('title')->text() . '</title>',
                '<meta name="description" content="' . $showD->find('meta[name="description"]')->attr('content') . '">',
                '<meta name="keywords" content="' . $showD->find('meta[name="keywords"]')->attr('content') . '">',
                // $showD->find('.entry-sub .meta-author-date .meta-author-get-date')->text(),
                $showD->find('.inner-post-detail .last-update')->text(),
            ];
            fputcsv($fp, $showData);
            $log->debug(implode(', ', $showData));
        }
    }
    $log->debug($i . 'ページ目取得完了');
}

for ($i = 12; $i <= 14; $i++) {
    if ($i !== 1) {
        $url = URL . 'index_' . $i . '.php';
    } else {
        $url = URL . 'index.php';
    }
    $html = file_get_contents($url, false, $context);
    $doc = phpQuery::newDocument(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    $d = $doc;

    // $iページ目を記事を取得
    // var_dump('一覧URL　' . $url);
    foreach ($doc->find('.box-tile') as $tmp) {
        $dd = pq($tmp);
        $url = $dd->find('a')->attr('href');
        $showPage = file_get_contents($url, false, $context);
        $showDoc = phpQuery::newDocument(mb_convert_encoding($showPage, 'HTML-ENTITIES', 'UTF-8'));
        foreach($showDoc as $tmp) {            
            $showD = pq($tmp);
            $showData = [
                $showD->find('.ttl-post-a')->text(),
                $showD->find('meta[name="description"]')->attr('content'),
                $url,
                '<title>' . $showD->find('title')->text() . '</title>',
                '<meta name="description" content="' . $showD->find('meta[name="description"]')->attr('content') . '">',
                '<meta name="keywords" content="' . $showD->find('meta[name="keywords"]')->attr('content') . '">',
                // $showD->find('.entry-sub .meta-author-date .meta-author-get-date')->text(),
                $showD->find('.inner-post-detail .last-update')->text(),
            ];
            fputcsv($fp, $showData);
            $log->debug(implode(', ', $showData));
        }
    }
    $log->debug($i . 'ページ目取得完了');
}

fclose($fp);
$time = microtime(true) - $time_start;
echo "{$time} 秒";

?>