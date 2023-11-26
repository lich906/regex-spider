<?php

namespace App\Utils;

class LinkUtils
{
    public static function removeQueryStringAndAnchor(string $link): string
    {
        $link = explode('#', $link)[0];
        return explode('?', $link)[0];
    }
}
