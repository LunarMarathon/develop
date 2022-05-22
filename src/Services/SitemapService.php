<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Helpers\Features;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\BladePage;
use Hyde\Framework\Models\DocumentationPage;
use Hyde\Framework\Models\MarkdownPage;
use Hyde\Framework\Models\MarkdownPost;
use SimpleXMLElement;

/**
 * @see \Tests\Feature\Services\SitemapServiceTest
 * @see https://www.sitemaps.org/protocol.html
 */
class SitemapService
{
    public SimpleXMLElement $xmlElement;
    protected float $time_start;

    public function __construct()
    {
        $this->time_start = microtime(true);

        $this->xmlElement = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');
        $this->xmlElement->addAttribute('generator', 'HydePHP '.Hyde::version());
    }

    public function generate(): self
    {
        if (Features::hasBladePages()) {
            $this->addPageModelUrls(
                BladePage::class
            );
        }

        if (Features::hasMarkdownPages()) {
            $this->addPageModelUrls(
                MarkdownPage::class
            );
        }

        if (Features::hasBlogPosts()) {
            $this->addPageModelUrls(
                MarkdownPost::class,
                'posts/'
            );
        }

        if (Features::hasDocumentationPages()) {
            $this->addPageModelUrls(
                DocumentationPage::class,
                Hyde::docsDirectory().'/'
            );
        }

        return $this;
    }

    public function getXML(): string
    {
        $this->xmlElement->addAttribute('processing_time_ms', (string) round((microtime(true) - $this->time_start) * 1000, 2));

        return $this->xmlElement->asXML();
    }

    public function addPageModelUrls(string $pageClass, string $routePrefix = ''): void
    {
        $collection = CollectionService::getSourceFileListForModel($pageClass);

        foreach ($collection as $slug) {
            $urlItem = $this->xmlElement->addChild('url');
            $urlItem->addChild('loc', htmlentities(Hyde::uriPath(Hyde::pageLink($routePrefix.$slug.'.html'))));
            $urlItem->addChild('lastmod', htmlentities($this->getLastModDate($pageClass, $slug)));
            $urlItem->addChild('changefreq', 'daily');
            if (config('hyde.sitemap.dynamic_priority', true)) {
                $urlItem->addChild('priority', $this->getPriority($pageClass, $slug));
            }
        }
    }

    protected function getLastModDate(string $pageClass, string $slug): string
    {
        return date('c', filemtime(
            Hyde::path($pageClass::$sourceDirectory.DIRECTORY_SEPARATOR.$slug.$pageClass::$fileExtension)
        ));
    }

    protected function getPriority(string $pageClass, string $slug): string
    {
        $priority = 0.5;

        if (in_array($pageClass, [BladePage::class, MarkdownPage::class])) {
            $priority = 0.9;
            if ($slug === 'index') {
                $priority = 1;
            }
            if ($slug === '404') {
                $priority = 0.5;
            }
        }

        if ($pageClass === DocumentationPage::class) {
            $priority = 0.9;
        }

        if ($pageClass === MarkdownPost::class) {
            $priority = 0.75;
        }

        return (string) $priority;
    }

    public static function generateSitemap(): string
    {
        return (new static)->generate()->getXML();
    }

    public static function canGenerateSitemap(): bool
    {
        return (Hyde::uriPath() !== false) && config('hyde.generateSitemap', true);
    }
}
