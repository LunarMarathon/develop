<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Class TestBuildStaticSiteCommandFlagToEnablePrettyUrls.
 */
class TestBuildStaticSiteCommandFlagToEnablePrettyUrls extends TestCase
{
    public function test_pretty_urls_can_be_enabled_with_flag()
    {
        config(['hyde.prettyUrls' => false]);

        $this->artisan('build --pretty-urls')
            ->expectsOutput('Generating site with pretty URLs')
            ->assertExitCode(0);

        $this->assertTrue(config('hyde.prettyUrls', false));
    }

    public function test_config_change_is_not_persisted()
    {
        $this->assertFalse(config('hyde.prettyUrls', false));
    }
}
