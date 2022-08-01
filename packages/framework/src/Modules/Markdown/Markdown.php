<?php

namespace Hyde\Framework\Modules\Markdown;

use Hyde\Framework\Services\MarkdownService;

/**
 * Markdown facade to access Markdown services.
 */
class Markdown
{
    /**
     * Parse a Markdown string into HTML.
     *
     * @return string $html
     */
    public static function parse(string $markdown): string
    {
        return app(MarkdownConverter::class)->convert($markdown);
    }
}
