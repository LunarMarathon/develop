<?php

namespace Hyde\RealtimeCompiler\Routing;

use Desilva\Microserve\Request;
use Desilva\Microserve\Response;
use Hyde\Framework\Contracts\AbstractPage;
use Hyde\Framework\Contracts\PageContract;
use Hyde\Framework\Hyde;
use Hyde\Framework\Models\Pages\BladePage;
use Hyde\Framework\Models\Pages\DocumentationPage;
use Hyde\Framework\Models\Pages\MarkdownPage;
use Hyde\Framework\Models\Pages\MarkdownPost;
use Hyde\Framework\Models\Route;
use Hyde\Framework\StaticPageBuilder;
use Hyde\RealtimeCompiler\Actions\Compiler;
use Hyde\RealtimeCompiler\Concerns\InteractsWithLaravel;
use Hyde\RealtimeCompiler\Concerns\SendsErrorResponses;

/**
 * Handle routing for a web page request.
 */
class PageRouter
{
    use SendsErrorResponses;
    use InteractsWithLaravel;

    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->bootApplication();
    }

    protected function handlePageRequest(): Response
    {
        $html = $this->getHtml(Route::getFromKey(
            $this->normalizePath($this->request->path)
        )->getSourceModel());

        return (new Response(200, 'OK', [
            'body' => $html,
        ]))->withHeaders([
            'Content-Type'   => 'text/html',
            'Content-Length' => strlen($html),
        ]);
    }

    protected function normalizePath(string $path): string
    {
        // If uri ends in .html, strip it
        if (str_ends_with($path, '.html')) {
            $path = substr($path, 0, -5);
        }

        // If the path is empty, serve the index file
        if (empty($path) || $path == '/') {
            $path = '/index';
        }

        return ltrim($path, '/');
    }

    protected function getHtml(PageContract $page): string
    {
        // todo add caching as we don't need to recompile pages that have not changed
        return file_get_contents((new StaticPageBuilder($page))->__invoke());
    }

    public static function handle(Request $request): Response
    {
        return (new self($request))->handlePageRequest();
    }
}
