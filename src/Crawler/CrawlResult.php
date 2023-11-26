<?php

namespace App\Crawler;

use PHPHtmlParser\Dom;

class CrawlResult
{
    private string $url;
    private int $status;
    private Dom $contentDOM;
    private string|null $referer;

    /**
     * @param string $url
     * @param int $status
     * @param Dom $contentDOM
     * @param string|null $referer
     */
    public function __construct(string $url, int $status, Dom $contentDOM, ?string $referer = null)
    {
        $this->url = $url;
        $this->status = $status;
        $this->contentDOM = $contentDOM;
        $this->referer = $referer;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getContentDOM(): Dom
    {
        return $this->contentDOM;
    }

    public function getReferer(): ?string
    {
        return $this->referer;
    }
}
