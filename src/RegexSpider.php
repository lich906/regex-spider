<?php

namespace App;

use App\Crawler\Crawler;
use App\Logger\LoggerInterface;
use Exception;

class RegexSpider
{
    private Crawler $crawler;

    private LoggerInterface $logger;

    /**
     * @param Crawler $crawler
     * @param LoggerInterface $logger
     */
    public function __construct(Crawler $crawler, LoggerInterface $logger)
    {
        $this->crawler = $crawler;
        $this->logger = $logger;
    }

    /**
     * @param string $url
     * @param string $regex
     * @return RegexSearchResult[]
     */
    public function RegexSearch(string $url, string $regex): array
    {
        $searchResults = [];

        try
        {
            foreach ($this->crawler->Crawl($url, true) as $crawlResult)
            {
                $url = htmlspecialchars_decode(($crawlResult->getUrl()));
                $this->logger->log($crawlResult->getStatus() . ' ' . $crawlResult->getUrl());
                $rawContent = $crawlResult->getContentDOM()->root->outerhtml;
                $matches = [];
                preg_match($regex, $rawContent, $matches);

                if (!empty($matches))
                {
                    $searchResults[] = new RegexSearchResult($url, $matches);
                }
            }
        }
        catch (Exception $e)
        {
            echo $e->getMessage() . PHP_EOL;
        }

        return $searchResults;
    }
}
