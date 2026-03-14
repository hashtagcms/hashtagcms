<?php

namespace HashtagCms\Tests\Feature;

use HashtagCms\Testing\TestCase;
use Illuminate\Support\Facades\DB;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use HashtagCms\Database\Seeds\HashtagCmsDatabaseSeeder;

class FrontendTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * Test home page loads.
     * Uses SQLite in-memory database - seeds and installs CMS automatically.
     *
     * @return void
     */
    public function test_home_page_loads()
    {
        // Seed and install CMS for in-memory SQLite testing
        $this->seed(HashtagCmsDatabaseSeeder::class);
        $this->installSite([
            'site_domain' => 'localhost',
            'site_context' => 'web'
        ]);

        // Visit the homepage
        $response = $this->get('/');
        
        $response->assertStatus(200);
    }

    /**
     * Test all category routes return 200.
     * Fetches categories from database and tests each link_rewrite route.
     */
    public function test_all_category_routes_return_200()
    {
        // Seed and install CMS for in-memory SQLite testing
        $this->seed(HashtagCmsDatabaseSeeder::class);
        $this->installSite([
            'site_domain' => 'localhost',
            'site_context' => 'web'
        ]);

        // Fetch all categories for site 1
        $categories = DB::table('categories')
            ->where('site_id', 1)
            ->whereNotNull('link_rewrite')
            ->where('link_rewrite', '!=', '')
            ->get();

        $this->assertNotEmpty($categories, 'Categories should exist after installation');

        // Valid status codes: 200 (OK), 302 (redirect for auth-protected pages)
        $validStatusCodes = [200, 302];

        // Test each category route
        foreach ($categories as $category) {
            $url = '/' . $category->link_rewrite;
            
            $response = $this->get($url);
            $statusCode = $response->getStatusCode();
            
            $this->assertTrue(
                in_array($statusCode, $validStatusCodes),
                "Category route '{$url}' should return 200 or 302, got {$statusCode}"
            );
        }
    }
}




