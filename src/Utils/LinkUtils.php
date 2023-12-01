<?php

namespace App\Utils;

class LinkUtils
{
    public static function removeQueryStringAndAnchor(string $link): string
    {
        $link = self::removeAnchor($link);
        return explode('?', $link)[0];
    }

    public static function removeAnchor(string $link): string
    {
        $link = explode('#', $link)[0];
        return $link . (empty(parse_url($link, PHP_URL_PATH)) ? '/' : '');
    }
}
