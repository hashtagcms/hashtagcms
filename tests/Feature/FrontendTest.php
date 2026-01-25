<?php

namespace HashtagCms\Tests\Feature;

use HashtagCms\Testing\TestCase;

class FrontendTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_home_page_loads()
    {
        // 1. Install the site (Simulate user finishing installation)
        $this->installSite([
            'site_domain' => 'localhost',
            'site_context' => 'web'
        ]);

        // 2. Visit the homepage
        $response = $this->get('/');
        
        $response->assertStatus(200);
    }

}
