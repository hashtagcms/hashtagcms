<?php

namespace HashtagCms\Tests\Feature;

use HashtagCms\Testing\TestCase;
use HashtagCms\Models\SiteProp;
use HashtagCms\Database\Seeds\HashtagCmsDatabaseSeeder;

use Illuminate\Foundation\Testing\DatabaseMigrations;

class InstallationTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * Set up the test - explicitly seed for installation tests.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Installation tests need the database seeded
        $this->seed(HashtagCmsDatabaseSeeder::class);
    }

    /**
     * Test that the installation page is accessible when not installed.
     *
     * @return void
     */
    public function test_install_page_loads()
    {
        // Check if already installed and skip
        if ($this->isSiteInstalled()) {
            $this->markTestSkipped('Site is already installed.');
        }

        $response = $this->get('/install');
        $response->assertStatus(200);
        $response->assertViewIs('hashtagcms::installer.index');
    }

    /**
     * Test the installation process via POST request.
     */
    public function test_installation_logic()
    {
        // Check if already installed and skip
        if ($this->isSiteInstalled()) {
            $this->markTestSkipped('Site is already installed. Cannot test installation logic on installed site.');
        }

        $data = [
            'site_name' => 'My Awesome Site',
            'site_title' => 'Awesome Title',
            'site_context' => 'web',
            'site_domain' => 'localhost',
            'name' => 'Super Admin',
            'user_email' => 'admin@test.com',
            'user_password' => 'secret123',
        ];

        // Submit the installation form
        $response = $this->postJson('/install/save', $data);

        // Assert response confirms installation (controller returns JSON with isInstalled=1)
        $response->assertStatus(200);
        $response->assertJson(['isInstalled' => 1]);

        // Verify DB updates
        $this->assertDatabaseHas('sites', [
            'id' => 1,
            'name' => 'My Awesome Site',
            'context' => 'web',
            'domain' => 'localhost'
        ]);

        $this->assertDatabaseHas('site_props', [
            'name' => 'site_installed',
            'value' => '1'
        ]);
        
        // Users table check
        $this->assertDatabaseHas('users', [
             'id' => 1,
             'email' => 'admin@test.com',
             'name' => 'Super Admin'
        ]);

    }

    /**
     * Test that /install redirects if already installed.
     */
    public function test_install_redirects_if_installed()
    {
        // Manually set installed
        $prop = SiteProp::where('name', 'site_installed')->first();
        if ($prop) {
            $prop->value = 1;
            $prop->save();
        }

        $response = $this->get('/install');
        $response->assertRedirect('/');
    }

    /**
     * Test that already installed site shows appropriate message in console.
     */
    public function test_site_already_installed_check()
    {
        // Simulate installation
        $this->installSite();

        // Verify site is installed
        $this->assertTrue($this->isSiteInstalled(), 'Site should be marked as installed');
    }
}

