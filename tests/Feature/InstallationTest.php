<?php

namespace HashtagCms\Tests\Feature;

use HashtagCms\Testing\TestCase;
use HashtagCms\Models\SiteProp;

class InstallationTest extends TestCase
{
    /**
     * Test that the installation page is accessible when not installed.
     *
     * @return void
     */
    public function test_install_page_loads()
    {
        // By default, seeder sets site_installed = 0
        $response = $this->get('/install');
        $response->assertStatus(200);
        $response->assertViewIs('hashtagcms::installer.index');
    }

    /**
     * Test the installation process via POST request.
     */
    public function test_installation_logic()
    {
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
        
        // Users table check (HashtagCms\User uses 'users' table or configured one? Assuming standard)
        $this->assertDatabaseHas('users', [ // Note: migration says CreateUserscmsTable
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
        $prop->value = 1;
        $prop->save();

        $response = $this->get('/install');
        $response->assertRedirect('/');
    }
}
