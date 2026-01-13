<?php

namespace HashtagCms\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PageTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_root_page()
    {
        //Artisan::call("config:cache");
        $response = $this->get('/?clearCache=true');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['CORPORATES', 'Intuitive interface for managing modules',
            'Modern Design', 'Frequently asked questions', 'Subscribe to our newsletter',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);

    }

    public function test_example_page()
    {
        $response = $this->get('/example?clearCache=true');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Custom Module', 'Static Module', 'Query Module',
            'Service Module', 'QueryService Module', 'UrlService Module',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);
    }

    public function test_blog_page()
    {
        $response = $this->get('/blog?clearCache=true');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Test Blog',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);

    }

    public function test_blog_test_page()
    {
        $response = $this->get('/blog/test-blog?clearCache=true');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Test Blog', 'This is test blog content',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);

    }

    public function test_contact_page()
    {
        $response = $this->get('/contact?clearCache=true');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Comment',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);

    }

    public function test_profile_page()
    {
        $response = $this->get('/profile?clearCache=true');
        $response->assertStatus(302);
    }

    public function test_support_tnc_page()
    {
        $response = $this->get('/support/tnc?clearCache=true');
        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_root_page_with_cache()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['CORPORATES', 'Intuitive interface for managing modules',
            'Modern Design', 'Frequently asked questions', 'Subscribe to our newsletter',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);
    }

    public function test_example_page_with_cache()
    {
        $response = $this->get('/example');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Custom Module', 'Static Module', 'Query Module',
            'Service Module', 'QueryService Module', 'UrlService Module',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);
    }

    public function test_blog_page_with_cache()
    {
        $response = $this->get('/blog');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Test Blog',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);
    }

    public function test_blog_test_page_with_cache()
    {
        $response = $this->get('/blog/test-blog');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Test Blog', 'This is test blog content',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);
    }

    public function test_contact_page_with_cache()
    {
        $response = $this->get('/contact');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Comment',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);
    }

    public function test_profile_page_with_cache()
    {
        $response = $this->get('/profile');
        $response->assertStatus(302);
    }

    public function test_support_tnc_page_with_cache()
    {
        $response = $this->get('/support/tnc');
        $response->assertStatus(200);
    }
}
