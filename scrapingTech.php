<?php
require_once('./phpQuery-onefile.php');
require_once('./log.php');
set_time_limit(600);
$log = Logger::getInstance();

define('URL', 'https://techacademy.jp/magazine/category/programming/php');

$html = file_get_contents(URL);
$doc = phpQuery::newDocument($html);
$d = pq($doc);
$maxPage = $d->find('.nav-links a:nth-child(4)')->text();
$arrInfos = [
    'タイトル', '概要', 'url', '<title>', '<meta name="description">', '<keywords>', '公開日',
];

$time_start = microtime(true);
$csvData = 'test.csv';
$fp = fopen('outputCsv\techPhp.csv', 'w');
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
    foreach ($doc->find('.entry-detail') as $tmp) {
        $dd = pq($tmp);
        $url = $dd->find('a')->attr('href');
        $showPage = file_get_contents($url);
        $showDoc = phpQuery::newDocument($showPage);
        
        foreach($showDoc as $tmp) {
            $showD = pq($tmp);
            $showData = [
                $showD->find('meta[property="og:title"]')->attr('content'),
                $showD->find('meta[name="description"]')->attr('content'),
                '<title>' . $showD->find('title')->text() . '</title>',
                $showD->find('link[rel="canonical"]')->attr('href'),
                '<meta name="description" content="' . $showD->find('meta[name="description"]')->attr('content') . '">',
                '<meta name="keywords" content="' . $showD->find('meta[name="keywords"]')->attr('content') . '">',
                $showD->find('.social-button span:last-child a')->text(),
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