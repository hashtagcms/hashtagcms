<?php

namespace HashtagCms\Tests\Feature;

use Tests\TestCase;

class PageWithLangTenantTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_root_page()
    {

        $response = $this->get('/en/web?clearCache=true');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['CORPORATES', 'Intuitive interface for managing modules',
            'Modern Design', 'Frequently asked questions', 'Subscribe to our newsletter',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);

    }

    public function test_example_page()
    {
        $response = $this->get('/en/web/example?clearCache=true');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Custom Module', 'Static Module', 'Query Module',
            'Service Module', 'QueryService Module', 'UrlService Module',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);
    }

    public function test_blog_page()
    {
        $response = $this->get('/en/web/blog?clearCache=true');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Test Blog',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);

    }

    public function test_blog_test_page()
    {
        $response = $this->get('/en/web/blog/test-blog?clearCache=true');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Test Blog', 'This is test blog content',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);

    }

    public function test_contact_page()
    {
        $response = $this->get('/en/web/contact?clearCache=true');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Comment',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);
    }

    public function test_profile_page()
    {
        $response = $this->get('/en/web/profile?clearCache=true');
        $response->assertStatus(302);
    }

    public function test_support_tnc_page()
    {
        $response = $this->get('/en/web/support/tnc?clearCache=true');
        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_root_page_with_cache()
    {
        $response = $this->get('/en/web/');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['CORPORATES', 'Intuitive interface for managing modules',
            'Modern Design', 'Frequently asked questions', 'Subscribe to our newsletter',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);
    }

    public function test_example_page_with_cache()
    {
        $response = $this->get('/en/web/example');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Custom Module', 'Static Module', 'Query Module',
            'Service Module', 'QueryService Module', 'UrlService Module',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);
    }

    public function test_blog_page_with_cache()
    {
        $response = $this->get('/en/web/blog');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Test Blog',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);
    }

    public function test_blog_test_page_with_cache()
    {
        $response = $this->get('/en/web/blog/test-blog');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Test Blog', 'This is test blog content',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);
    }

    public function test_contact_page_with_cache()
    {
        $response = $this->get('/en/web/contact');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Comment',
            'Support', 'Follow Us', 'Powered by HashtagCms.org']);
    }

    public function test_profile_page_with_cache()
    {
        $response = $this->get('/en/web/profile');
        $response->assertStatus(302);
    }

    public function test_support_tnc_page_with_cache()
    {
        $response = $this->get('/en/web/support/tnc');
        $response->assertStatus(200);
    }
}
