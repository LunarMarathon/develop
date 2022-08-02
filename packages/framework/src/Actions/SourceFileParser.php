<?php

namespace Hyde\Framework\Actions;

use Hyde\Framework\Concerns\ValidatesExistence;
use Hyde\Framework\Contracts\PageContract;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\Pages\BladePage;
use Hyde\Framework\Models\Pages\DocumentationPage;
use Hyde\Framework\Models\Pages\MarkdownPage;
use Hyde\Framework\Models\Pages\MarkdownPost;
use Hyde\Framework\Models\Parsers\DocumentationPageParser;
use Hyde\Framework\Modules\Markdown\MarkdownFileParser;

/**
 * Parses a source file and returns a new page model instance for it.
 *
 * @see \Hyde\Framework\Testing\Feature\SourceFileParserTest
 */
class SourceFileParser
{
    use ValidatesExistence;

    protected string $slug;
    protected PageContract $page;

    public function __construct(string $pageClass, string $slug)
    {
        $this->validateExistence($pageClass, $slug);

        $this->slug = $slug;

        $this->page = match ($pageClass) {
            BladePage::class => $this->parseBladePage(),
            MarkdownPage::class => $this->parseMarkdownPage(),
            MarkdownPost::class => $this->parseMarkdownPost(),
            DocumentationPage::class => $this->parseDocumentationPage(),
        };
    }

    protected function parseBladePage(): BladePage
    {
        return new BladePage($this->slug);
    }

    protected function parseMarkdownPage(): MarkdownPage
    {
        $document = (new MarkdownFileParser(
            Hyde::getMarkdownPagePath("/$this->slug.md")
        ))->get();

        $matter = array_merge($document->matter, [
            'slug' => $this->slug,
        ]);

        $body = $document->body;

        return new MarkdownPage(
            matter: $matter,
            body: $body,
            title: FindsTitleForDocument::get($this->slug, $matter, $body),
            slug: $this->slug
        );
    }

    protected function parseMarkdownPost(): MarkdownPost
    {
        $document = (new MarkdownFileParser(
            Hyde::getMarkdownPostPath("/$this->slug.md")
        ))->get();

        $matter = array_merge($document->matter, [
            'slug' => $this->slug,
        ]);

        $body = $document->body;

        return new MarkdownPost(
            matter: $matter,
            body: $body,
            title: FindsTitleForDocument::get($this->slug, $matter, $body),
            slug: $this->slug
        );
    }

    protected function parseDocumentationPage(): DocumentationPage
    {
        return (new DocumentationPageParser($this->slug))->get();
    }

    public function get(): PageContract
    {
        return $this->page;
    }
}
