<?php

namespace Hyde\Core\Models;

use Hyde\Core\Hyde;

/**
 * A simple class that contains the content of a Documentation Page.
 */
class DocumentationPage
{
    /**
     * The Page Title
     * @var string
     */
    public string $title;

    /**
     * The Markdown Content
     * @var string
     */
    public string $content;

    /**
     * The Post Slug
     * @var string
     */
    public string $slug;

    /**
     * Construct the object.
     *
     * @param string $slug
     * @param string $title
     * @param string $content
     */
    public function __construct(string $slug, string $title, string $content)
    {
        $this->slug = $slug;
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * Get an array of all the available Markdown Pages
     * @return array
     */
    public static function allAsArray(): array
    {
        $array = [];

        foreach (glob(Hyde::path('_docs/*.md')) as $filepath) {
            $array[basename($filepath, '.md')] = $filepath;
        }

        return $array;
    }
}
