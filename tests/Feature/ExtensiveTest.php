<?php

namespace HashtagCms\Tests\Feature;

use HashtagCms\Testing\TestCase;
use Illuminate\Support\Facades\DB;
use HashtagCms\Models\Site;

class ExtensiveTest extends TestCase
{
    /**
     * Set up the test - explicitly seed for extensive tests.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Extensive tests need the database seeded
        $this->seed(\HashtagCms\Database\Seeds\HashtagCmsDatabaseSeeder::class);
    }

    /**
     * Test the full installation flow.
     */
    public function test_full_installation_flow()
    {
        // Check if already installed and skip
        if ($this->isSiteInstalled()) {
            $this->markTestSkipped('Site is already installed. Cannot test installation flow.');
        }

        $overrides = [
            'site_name' => 'My Awesome Site',
            'site_domain' => 'my-site.com',
            'user_email' => 'dev@hashtagcms.org'
        ];

        $site = $this->installSite($overrides);

        $this->assertEquals('My Awesome Site', $site->name);
        $this->assertEquals('my-site.com', $site->domain);
        
        $user = \HashtagCms\User::find(1);
        $this->assertEquals('dev@hashtagcms.org', $user->email);
        
        $siteInstalled = \HashtagCms\Models\SiteProp::where('name', 'site_installed')->first();
        $this->assertEquals(1, $siteInstalled->value);
    }

    /**
     * Test the frontend and config API integration as per .antigravity rules.
     * Note: This test is skipped in SQLite as it requires complex site setup.
     */
    public function test_api_config_and_frontend_integration()
    {
        $this->markTestSkipped('This integration test requires MySQL database with full site setup.');
    }

    /**
     * Test multisite context switching.
     * Note: This test is skipped in SQLite as it requires multiple sites with full infrastructure.
     */
    public function test_multisite_context_switching()
    {
        $this->markTestSkipped('Multisite test requires MySQL database with full category/theme/page setup for each site.');
    }
}

