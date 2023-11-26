<?php

use App\Crawler\Crawler;
use App\Logger\FileLogger;
use App\RegexSpider;
use GuzzleHttp\Client;

require_once __DIR__ . '/vendor/autoload.php';

error_reporting(E_ERROR | E_WARNING | E_PARSE);

const CSV_SEPARATOR = ';';

$crawler = new Crawler(new Client());
$logger = new FileLogger('requests.log');
$spider = new RegexSpider($crawler, $logger);

$result = $spider->RegexSearch($argv[1], '/' . $argv[2] . '/i');

foreach ($result as $entry)
{
    echo $entry->getUrl() . CSV_SEPARATOR . implode(CSV_SEPARATOR, $entry->getMatches()) . PHP_EOL;
}
