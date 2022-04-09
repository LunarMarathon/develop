<?php

namespace Tests\Feature;

use Hyde\Framework\Hyde;
use Hyde\Framework\Models\MarkdownDocument;
use Hyde\Framework\Services\MarkdownFileService;
use Tests\TestCase;

class MarkdownFileServiceTest extends TestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create a Markdown file to work with
        copy(Hyde::path('vendor/hyde/framework/tests/stubs/_posts/test-parser-post.md'), $this->getPath());
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        // Remove the published stub file
        unlink($this->getPath());

        parent::tearDown();
    }

    /**
     * Get the path of the test Markdown file.
     *
     * @return string
     */
    public function getPath(): string
    {
        return Hyde::path('_posts/test-parser-post.md');
    }

    public function test_can_parse_markdown_file()
    {
        $document = (new MarkdownFileService(Hyde::path('_posts/test-parser-post.md')))->get();
        $this->assertInstanceOf(MarkdownDocument::class, $document);

        $this->assertEquals([
            'title' => 'My New Post',
            'category' => 'blog',
            'author' => 'Mr. Hyde',
        ], $document->matter);

        $this->assertEquals(
            '# My New PostThis is a post stub used in the automated tests',
            str_replace("\n", '', $document->body)
        );
    }

    public function test_parsed_markdown_post_contains_valid_front_matter()
    {
        $post = (new MarkdownFileService(Hyde::path('_posts/test-parser-post.md')))->get();
        $this->assertEquals('My New Post', $post->matter['title']);
        $this->assertEquals('Mr. Hyde', $post->matter['author']);
        $this->assertEquals('blog', $post->matter['category']);
    }
}