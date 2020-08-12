<?php
require_once('./phpQuery-onefile.php');
require_once('./log.php');
set_time_limit(600);
$log = Logger::getInstance();

define('URL', 'https://www.sejuku.net/blog/category/programming-language/php');

$html = file_get_contents(URL);
$doc = phpQuery::newDocument(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
$d = $doc;
$maxPage = $d->find('.next')->prev()->text();

$arrInfos = [
    'タイトル',
    '概要',
    'url',
    '<title>',
    '<meta name="description">',
    '<keywords>',
    '公開日',
    '更新日',
];
$time_start = microtime(true);
$fp = fopen('outputCsv\samuraiPhp.csv', 'w');
fputcsv($fp, $arrInfos);

for ($i = 1; $i <= $maxPage; $i++) {
    if ($i !== 1) {
        $url = URL . '/page/' . $i;
    } else {
        $url = URL;
    }
    $html = file_get_contents($url);
    $doc = phpQuery::newDocument(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    $d = $doc;

    // $iページ目を記事を取得
    var_dump('一覧URL　' . $url);
    foreach ($doc->find('.entry-title') as $tmp) {
        $dd = pq($tmp);
        $url = $dd->find('a')->attr('href');
        $showPage = file_get_contents($url);
        $showDoc = phpQuery::newDocument(mb_convert_encoding($showPage, 'HTML-ENTITIES', 'UTF-8'));

        foreach($showDoc as $tmp) {            
            $showD = pq($tmp);
            $showData = [
                $showD->find('.breadcrumbs')->next()->text(),
                $showD->find('.toc_list li')->text(),
                $showD->find('link[rel="canonical"]')->attr('href'),
                '<title>' . $showD->find('title')->text() . '</title>',
                '<meta name="description" content="' . $showD->find('meta[name="description"]')->attr('content') . '">',
                '<meta name="keywords" content="' . $showD->find('meta[name="keywords"]')->attr('content') . '">',
                $showD->find('.entry-sub .meta-author-date .meta-author-get-date')->text(),
                $showD->find('.entry-sub .meta-author-date .meta-author-modified-date')->text(),
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