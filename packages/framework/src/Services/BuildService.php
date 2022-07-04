<?php

namespace Hyde\Framework\Services;

use Hyde\Framework\Models\Pages\BladePage;
use Hyde\Framework\Models\Pages\DocumentationPage;
use Hyde\Framework\Models\Pages\MarkdownPage;
use Hyde\Framework\Models\Pages\MarkdownPost;
use Hyde\Framework\Modules\Routing\Router;
use Hyde\Framework\StaticPageBuilder;
use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Console\OutputStyle;

/**
 * Moves logic from the build command to a service.
 *
 * Handles the build loop which generates the static site.
 */
class BuildService
{
    use InteractsWithIO;

    protected Router $router;

    public function __construct(OutputStyle $output)
    {
        $this->output = $output;

        $this->router = Router::getInstance();
    }

    public function run(): void
    {
        $this->runBuildAction(BladePage::class);
        $this->runBuildAction(MarkdownPage::class);
        $this->runBuildAction(MarkdownPost::class);
        $this->runBuildAction(DocumentationPage::class);
    }

    protected function canRunBuildAction(array $collection, string $model): bool
    {
        $name = $this->getModelPluralName($model);

        if (sizeof($collection) < 1) {
            $this->line("No $name found. Skipping...\n");

            return false;
        }

        $this->comment("Creating $name...");

        return true;
    }

    /** @internal */
    protected function runBuildAction(string $model): void
    {
        $collection = CollectionService::getSourceFileListForModel($model);
        if ($this->canRunBuildAction($collection, $model)) {
            $this->withProgressBar(
                $collection,
                $this->compileModel($model)
            );
            $this->newLine(2);
        }
    }

    protected function compileModel(string $model): callable
    {
        return function ($basename) use ($model) {
            new StaticPageBuilder(
                DiscoveryService::getParserInstanceForModel(
                    $model,
                    $basename
                )->get(),
                true
            );
        };
    }

    /** @internal */
    protected function getModelPluralName(string $model): string
    {
        return preg_replace('/([a-z])([A-Z])/', '$1 $2', class_basename($model)).'s';
    }
}
