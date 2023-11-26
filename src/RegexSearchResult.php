<?php

namespace App;

class RegexSearchResult
{
    private string $url;

    /**
     * @var string[]
     */
    private array $matches;

    /**
     * @param string $url
     * @param string[] $matches
     */
    public function __construct(string $url, array $matches)
    {
        $this->url = $url;
        $this->matches = $matches;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getMatches(): array
    {
        return $this->matches;
    }
}