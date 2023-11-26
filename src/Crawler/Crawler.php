<?php

namespace App\Crawler;

use App\Utils\UniqueLinksQueue;
use Generator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\ContentLengthException;
use PHPHtmlParser\Exceptions\LogicalException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;

class Crawler
{
    private Client $client;

    private UniqueLinksQueue $linksToCrawl;

    private string|null $initialHost = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->linksToCrawl = new UniqueLinksQueue();
    }

    /**
     * @param string $url
     * @param bool $sameHost
     * @return Generator<CrawlResult|null>
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws NotLoadedException
     * @throws StrictException
     */
    public function CrawlRecursively(string $url, bool $sameHost): Generator
    {
        $host = parse_url($url, PHP_URL_HOST);
        $scheme = parse_url($url, PHP_URL_SCHEME);

        if (empty($host) || !$this->isSchemeSupported($scheme))
        {
            return;
        }

        $this->initialHost = $host;

        $this->linksToCrawl->push($url);

        while ($link = $this->linksToCrawl->pop())
        {
            try
            {
                yield $this->CrawlOne($link, $sameHost);
            }
            catch (GuzzleException) {}
        }
    }

    /**
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws StrictException
     * @throws NotLoadedException
     * @throws ContentLengthException
     * @throws GuzzleException
     * @throws LogicalException
     */
    public function CrawlOne(string $url, bool $sameHost): CrawlResult
    {
        $response = $this->client->get($url);
        // TODO: handle 30x redirects
        // if ($response->getStatusCode() === 301)
        $body = $response->getBody();

        $document = new Dom();
        $document->loadStr($body);

        $crawlableLinks = $this->findCrawlableLinksInDOM($document, $sameHost);
        $this->linksToCrawl->pushMany($crawlableLinks);

        $referer = $response->getHeader('referer')[0] ?? null;

        return new CrawlResult($url, $response->getStatusCode(), $document, $referer);
    }

    private function isSchemeSupported(string $scheme): bool
    {
        return in_array($scheme, ['http', 'https']);
    }

    /**
     * @throws ChildNotFoundException
     * @throws NotLoadedException
     */
    private function findCrawlableLinksInDOM(Dom $doc, bool $sameHost): array
    {
        $linkElements = $doc->getElementsByTag('a');

        $crawlableLinks = [];
        /** @var Dom\Node\AbstractNode $element */
        foreach ($linkElements as $element)
        {
            if (!$element->hasAttribute('href'))
            {
                continue;
            }

            $link = trim($element->getAttribute('href'));

            // TODO: support relative links from other domains
            if ($this->isLinkCrawlable($link, $sameHost))
            {
                $this->makeLinkAbsolute($link);
                $crawlableLinks[] = $link;
            }
        }

        return array_unique($crawlableLinks);
    }

    private function isLinkCrawlable(string $link, bool $sameHost): bool
    {
        if (str_starts_with($link, 'tel:') || str_starts_with($link, 'mailto:'))
        {
            return false;
        }

        $host = parse_url($link, PHP_URL_HOST);

        if ($sameHost)
        {
            return empty($host) || $this->initialHost === $host;
        }

        return !empty(parse_url($link, PHP_URL_PATH));
    }

    private function makeLinkAbsolute(string &$link): void
    {
        $host = parse_url($link, PHP_URL_HOST);
        $scheme = parse_url($link, PHP_URL_SCHEME);

        if (empty($scheme) && !empty($host))
        {
            $link = 'https:' . $link;
        }

        if (empty($scheme) && empty($host))
        {
            $link = 'https://' . $this->initialHost . $link;
        }
    }
}
