<?php
require_once('./phpQuery-onefile.php');
require_once('./log.php');
set_time_limit(600);
$log = Logger::getInstance();

define('URL', 'https://www.javadrive.jp/php/');

$html = file_get_contents(URL);
$doc = phpQuery::newDocument($html);
$d = pq($doc);
$maxPage = $d->find('.nav-links a:nth-child(4)')->text();
$arrInfos = [
    'タイトル', '概要', 'url', '<title>', '<meta name="description">', '<keywords>',
];

$time_start = microtime(true);
$fp = fopen('outputCsv\javaDrivePhp.csv', 'w');
fputcsv($fp, $arrInfos);

foreach ($doc->find('.menubox li') as $tmp) {
    $dd = pq($tmp);
    $url = $dd->find('a')->attr('href');
    $showPage = file_get_contents(URL . $url);
    $showDoc = phpQuery::newDocument($showPage);
    
    foreach($showDoc as $tmp) {
        $showD = pq($tmp);
        $showData = [
            $showD->find('.main h1')->text(),
            $showD->find('meta[name="description"]')->attr('content'),
            URL . $url,
            '<title>' . $showD->find('title')->text() . '</title>',
            '<meta name="description" content="' . $showD->find('meta[name="description"]')->attr('content') . '">',
            '<meta name="keywords" content="' . $showD->find('meta[name="keywords"]')->attr('content') . '">',
        ];
        fputcsv($fp, $showData);
        $log->debug(implode(', ', $showData));
    }
}

fclose($fp);
$time = microtime(true) - $time_start;
echo "{$time} 秒";
?>