<?php

namespace App\Support;

use App\Models\Watch;
use Illuminate\Support\HtmlString;

/**
 * Escapes text and wraps every occurrence of the search words in a highlight span.
 * Safe to echo unescaped: the input is escaped before any markup is added.
 */
class SearchHighlighter
{
    public static function highlight(?string $text, ?string $phrase, string $class): HtmlString
    {
        $escaped = e((string) $text);
        $terms = Watch::searchTerms($phrase);

        if ($terms === [] || $escaped === '') {
            return new HtmlString($escaped);
        }

        // Longest first so "philippe" wins over "ph"; terms are escaped the same way as the text.
        usort($terms, fn (string $a, string $b): int => mb_strlen($b) <=> mb_strlen($a));
        $words = implode('|', array_map(fn (string $term): string => preg_quote(e($term), '/'), $terms));

        // Entities (&quot; &amp; …) are matched first and passed through untouched, so a
        // search for "quot" can never split one apart.
        return new HtmlString((string) str($escaped)->replaceMatches(
            '/(&#?\w+;)|('.$words.')/iu',
            fn (array $match): string => ($match[1] ?? '') !== ''
                ? $match[1]
                : '<span class="'.e($class).'">'.$match[2].'</span>',
        ));
    }
}
