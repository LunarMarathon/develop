<?php

namespace Hyde\Framework\Commands;

use Hyde\Framework\Actions\ValidationCheck;
use Hyde\Framework\Services\ValidationService;
use LaravelZero\Framework\Commands\Command;

/**
 * @see \Hyde\Testing\Feature\Commands\HydeValidateCommandTest
 */
class HydeValidateCommand extends Command
{
    protected $signature = 'validate';
    protected $description = 'Run a series of tests to validate your setup and help you optimize your site.';

    public function handle(): int
    {
        $this->info('Running validation tests!');

        foreach (ValidationService::checks() as $check) {
            $this->check($check);
        }

        $this->info('All done!');

        return 0;
    }

    protected function check(ValidationCheck $validation): void
    {
        $validation->check();

        if ($validation->passed()) {
            $this->passed($validation);
        } else {
            $this->failed($validation);
        }
    }

    protected function passed(ValidationCheck $validation): void
    {
        $this->info($validation->message());
    }

    protected function failed(ValidationCheck $validation): void
    {
        $this->error($validation->message());
        if ($validation->tip()) {
            $this->comment($validation->tip());
        }
    }
}
