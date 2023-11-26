<?php

namespace App\Utils;

/**
 * Queue contains links to be crawled.
 * This queue stores all unique links being pushed in queue,
 * if link were already pushed before it cannot be pushed again.
 */
class UniqueLinksQueue
{
    /**
     * @var array<string>
     */
    private array $linksQueue;

    /**
     * @var array<string>
     */
    private array $visitedLinks;

    public function __construct()
    {
        $this->linksQueue = [];
        $this->visitedLinks = [];
    }

    public function push(string $link): void
    {
        if (!$this->isLinkVisited($link))
        {
            $this->linksQueue[] = $link;

            $this->visitedLinks[] = LinkUtils::removeQueryStringAndAnchor($link);
        }
    }

    public function pushMany(array $links): void
    {
        $unvisitedLinks = $this->getUnvisitedLinks($links);

        array_walk($unvisitedLinks, function (string $link) {
            $this->visitedLinks[] = LinkUtils::removeQueryStringAndAnchor($link);
        });

        $this->linksQueue = array_merge($this->linksQueue, $unvisitedLinks);
    }

    public function pop(): ?string
    {
        if (!empty($this->linksQueue))
        {
            return array_shift($this->linksQueue);
        }

        return null;
    }

    public function empty(): bool
    {
        return count($this->linksQueue);
    }

    private function getUnvisitedLinks(array $links): array
    {
        return array_filter($links, function (string $link) {
            return !$this->isLinkVisited($link);
        });
    }

    private function isLinkVisited(string $link): bool
    {
        return in_array(LinkUtils::removeQueryStringAndAnchor($link), $this->visitedLinks);
    }
}
