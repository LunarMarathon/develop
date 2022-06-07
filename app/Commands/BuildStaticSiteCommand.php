<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use App\Hyde\Actions\GetMarkdownPostList;
use App\Hyde\DocumentationPageParser;
use App\Hyde\MarkdownPostParser;
use App\Hyde\MarkdownPageParser;
use App\Hyde\Models\BladePage;
use App\Hyde\Models\DocumentationPage;
use App\Hyde\Models\MarkdownPage;
use App\Hyde\Models\MarkdownPost;
use App\Hyde\Services\CollectionService;
use App\Hyde\StaticPageBuilder;

class BuildStaticSiteCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'build';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Build the static site';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle(): int
    {
        $time_start = microtime(true);

        $this->title('Building your static site!');

        $this->line('Creating Markdown Posts...');
        $this->withProgressBar(CollectionService::getSourceSlugsOfModels(MarkdownPost::class), function ($slug) {
            (new StaticPageBuilder((new MarkdownPostParser($slug))->get(), true));
        });

        $this->newLine(2);
        $this->line('Creating Markdown Pages...');
        $this->withProgressBar(CollectionService::getSourceSlugsOfModels(MarkdownPage::class), function ($slug) {
            (new StaticPageBuilder((new MarkdownPageParser($slug))->get(), true));
        });

        $this->newLine(2);
        $this->line('Creating Documentation Pages...');
        $this->withProgressBar(CollectionService::getSourceSlugsOfModels(DocumentationPage::class), function ($slug) {
            (new StaticPageBuilder((new DocumentationPageParser($slug))->get(), true));
        });

        $this->newLine(2);
        $this->line('Creating Blade Pages...');
        $this->withProgressBar(CollectionService::getSourceSlugsOfModels(BladePage::class), function ($slug) {
            (new StaticPageBuilder((new BladePage($slug)), true));
        });

        $this->newLine(2);

        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
        $this->info('All done! Finished in ' . number_format($execution_time, 2) .' seconds. (' . number_format(($execution_time * 1000), 2) . 'ms)');

        $this->info('Congratulations! 🎉 Your static site has been built!');
        $this->info('Your new homepage is stored here -> ' . base_path('_site'. DIRECTORY_SEPARATOR .'index.html'));

        return 0;
    }
}
