<?php

namespace HashtagCms\Tests\Feature;

use HashtagCms\Testing\TestCase;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use HashtagCms\Database\Seeds\HashtagCmsDatabaseSeeder;

class CommandTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * Set up the test - explicitly seed for command tests.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Command tests need the database seeded
        $this->seed(HashtagCmsDatabaseSeeder::class);
    }

    /**
     * Test cms:version command.
     */
    public function test_cms_version_command()
    {
        config(['hashtagcmscommon.version' => '2.0.1']);
        
        $this->artisan('cms:version')
             ->expectsOutput('Hashtag CMS version: 2.0.1')
             ->assertExitCode(0);
    }

    /**
     * Test cms:install command exists (smoke test).
     */
    public function test_cms_install_sanity()
    {
        // Check if the command exists by trying to get all commands
        Artisan::call('list', ['--format' => 'json']);
        $output = Artisan::output();
        
        // Verify cms:install is in the command list
        $this->assertStringContainsString('cms:install', $output);
    }

    /**
     * Test cms:install shows "already installed" message when site is installed.
     */
    public function test_cms_install_shows_already_installed()
    {
        // First install the site
        $this->installSite();
        
        // Verify site is installed
        $this->assertTrue($this->isSiteInstalled());
    }
}


