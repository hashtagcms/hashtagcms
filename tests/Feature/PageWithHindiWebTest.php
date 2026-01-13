<?php

namespace HashtagCms\Tests\Feature;

use Tests\TestCase;

class PageWithHindiWebTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_root_page()
    {
        $response = $this->get('/hi/web?clearCache=true');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['सहयोग', 'Powered by HashtagCms.org']);

    }

    public function test_example_page()
    {
        $response = $this->get('/hi/web/example?clearCache=true');
        $response->assertStatus(200);
        $response->assertSee('यह एक क्वेरी मॉड्यूल है');
        $response->assertSeeInOrder(['सहयोग', 'Powered by HashtagCms.org']);
    }

    public function test_blog_page()
    {
        $response = $this->get('/hi/web/blog?clearCache=true');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['सहयोग', 'Powered by HashtagCms.org']);

    }

    public function test_blog_test_page()
    {
        $response = $this->get('/hi/web/blog/test-blog?clearCache=true');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['सहयोग', 'Powered by HashtagCms.org']);

    }

    public function test_contact_page()
    {
        $response = $this->get('/hi/web/contact?clearCache=true');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['सहयोग', 'Powered by HashtagCms.org']);

    }

    public function test_profile_page()
    {
        $response = $this->get('/hi/web/profile?clearCache=true');
        $response->assertStatus(302);
    }

    public function test_support_tnc_page()
    {
        $response = $this->get('/hi/web/support/tnc?clearCache=true');
        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_root_page_with_cache()
    {
        $response = $this->get('/hi/web/');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['सहयोग', 'Powered by HashtagCms.org']);
    }

    public function test_example_page_with_cache()
    {
        $response = $this->get('/hi/web/example');
        $response->assertStatus(200);
        $response->assertSee('यह एक क्वेरी मॉड्यूल है');
        $response->assertSeeInOrder(['सहयोग', 'Powered by HashtagCms.org']);
    }

    public function test_blog_page_with_cache()
    {
        $response = $this->get('/hi/web/blog');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['सहयोग', 'Powered by HashtagCms.org']);
    }

    public function test_blog_test_page_with_cache()
    {
        $response = $this->get('/hi/web/blog/test-blog');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['सहयोग', 'Powered by HashtagCms.org']);
    }

    public function test_contact_page_with_cache()
    {
        $response = $this->get('/hi/web/contact');
        $response->assertStatus(200);
        $response->assertSeeInOrder(['सहयोग', 'Powered by HashtagCms.org']);
    }

    public function test_profile_page_with_cache()
    {
        $response = $this->get('/hi/web/profile');
        $response->assertStatus(302);
    }

    public function test_support_tnc_page_with_cache()
    {
        $response = $this->get('/hi/web/support/tnc');
        $response->assertStatus(200);
    }
}
