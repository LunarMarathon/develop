<?php

namespace Hyde\Framework\Testing\Feature;

use Hyde\Framework\Actions\StaticPageBuilder;
use Hyde\Framework\Hyde;
use Hyde\Framework\HydeServiceProvider;
use Hyde\Framework\Models\Pages\BladePage;
use Hyde\Framework\Models\Pages\DocumentationPage;
use Hyde\Framework\Models\Pages\HtmlPage;
use Hyde\Framework\Models\Pages\MarkdownPage;
use Hyde\Framework\Models\Pages\MarkdownPost;
use Hyde\Testing\TestCase;
use Illuminate\Support\Facades\Config;

/**
 * Feature tests for the StaticPageBuilder class.
 *
 * @covers \Hyde\Framework\Actions\StaticPageBuilder
 */
class StaticPageBuilderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->resetSite();
    }

    protected function tearDown(): void
    {
        $this->resetSite();

        parent::tearDown();
    }

    protected function validateBasicHtml(string $html)
    {
        $this->assertStringContainsString('<!DOCTYPE html>', $html);
        $this->assertStringContainsString('<html lang="en">', $html);
        $this->assertStringContainsString('<head>', $html);
        $this->assertStringContainsString('<title>', $html);
        $this->assertStringContainsString('</title>', $html);
        $this->assertStringContainsString('</head>', $html);
        $this->assertStringContainsString('<body', $html);
        $this->assertStringContainsString('</body>', $html);
        $this->assertStringContainsString('</html>', $html);
    }

    public function test_can_build_blade_page()
    {
        file_put_contents(BladePage::$sourceDirectory.'/foo.blade.php', 'bar');

        $page = new BladePage('foo');

        new StaticPageBuilder($page, true);

        $this->assertFileExists(Hyde::path('_site/foo.html'));
        $this->assertStringEqualsFile(Hyde::path('_site/foo.html'), 'bar');

        unlink(BladePage::$sourceDirectory.'/foo.blade.php');
        unlink(Hyde::path('_site/foo.html'));
    }

    public function test_can_build_markdown_post()
    {
        $page = MarkdownPost::make('foo', [
            'title' => 'foo',
            'author' => 'bar',
        ], '# Body');

        new StaticPageBuilder($page, true);

        $this->assertFileExists(Hyde::path('_site/posts/foo.html'));
        $this->validateBasicHtml(file_get_contents(Hyde::path('_site/posts/foo.html')));
    }

    public function test_can_build_markdown_page()
    {
        $page = MarkdownPage::make('foo', [], '# Body');

        new StaticPageBuilder($page, true);

        $this->assertFileExists(Hyde::path('_site/foo.html'));
        $this->validateBasicHtml(file_get_contents(Hyde::path('_site/foo.html')));
        unlink(Hyde::path('_site/foo.html'));
    }

    public function test_can_build_documentation_page()
    {
        $page = DocumentationPage::make('foo', [], '# Body');

        new StaticPageBuilder($page, true);

        $this->assertFileExists(Hyde::path('_site/'.'docs/foo.html'));
        $this->validateBasicHtml(file_get_contents(Hyde::path('_site/'.'docs/foo.html')));
    }

    public function test_can_build_html_page()
    {
        $this->file('_pages/foo.html', 'bar');
        $page = new HtmlPage('foo');

        new StaticPageBuilder($page, true);

        $this->assertFileExists(Hyde::path('_site/foo.html'));
        $this->assertStringEqualsFile(Hyde::path('_site/foo.html'), 'bar');
        unlink(Hyde::path('_site/foo.html'));
    }

    public function test_can_build_nested_html_page()
    {
        mkdir(Hyde::path('_pages/foo'));
        file_put_contents(Hyde::path('_pages/foo/bar.html'), 'baz');
        $page = new HtmlPage('foo/bar');

        new StaticPageBuilder($page, true);

        $this->assertFileExists(Hyde::path('_site/foo/bar.html'));
        $this->assertStringEqualsFile(Hyde::path('_site/foo/bar.html'), 'baz');

        unlink(Hyde::path('_site/foo/bar.html'));
        unlink(Hyde::path('_pages/foo/bar.html'));
        rmdir(Hyde::path('_pages/foo'));
    }

    public function test_creates_custom_documentation_directory()
    {
        $page = DocumentationPage::make('foo');

        Config::set('docs.output_directory', 'docs/foo');
        (new HydeServiceProvider($this->app))->register(); // Re-register the service provider to pick up the new config.

        new StaticPageBuilder($page, true);

        $this->assertFileExists(Hyde::path('_site/docs/foo/foo.html'));
        $this->validateBasicHtml(file_get_contents(Hyde::path('_site/docs/foo/foo.html')));
        unlink(Hyde::path('_site/docs/foo/foo.html'));
    }
}
