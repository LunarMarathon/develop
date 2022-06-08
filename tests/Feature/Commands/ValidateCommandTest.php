<?php

namespace Hyde\Testing\Feature\Commands;

use Hyde\Testing\TestCase;

class ValidateCommandTest extends TestCase
{
    public function test_validate_command_can_run()
    {
        $this->artisan('validate')
            ->expectsOutput('Running validation tests!')
            ->expectsOutput('All done!')
            ->assertExitCode(0);
    }
}