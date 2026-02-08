<?php

namespace HashtagCms\Tests\Unit;

use HashtagCms\Testing\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class HelperTest extends TestCase
{
    /**
     * Test sanitize helper.
     */
    public function test_sanitize_helper()
    {
        $dirty = "<script>alert('xss')</script>Hello";
        $clean = sanitize($dirty);
        
        // sanitize strips tags but may leave <> artifacts
        $this->assertStringContainsString("Hello", $clean);
        $this->assertStringNotContainsString("<script>", $clean);
        $this->assertEquals("Safe string", sanitize("Safe string"));
    }

    /**
     * Test htcms_trans helper.
     */
    public function test_htcms_trans_helper()
    {
        // 1. Mock a translation in hashtagcms namespace if possible
        // but easier to test fallback logic
        
        // Fallback to substring after dot
        $this->assertEquals("welcome", htcms_trans("messages.welcome"));
        
        // Fallback to key itself if no dot
        $this->assertEquals("Submit", htcms_trans("Submit"));
    }

    /**
     * Test getFormattedDate helper.
     */
    public function test_formatted_date_helper()
    {
        $date = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $formatted = getFormattedDate($date);
        
        $this->assertEquals("1 hour ago", $formatted);
        $this->assertEquals("", getFormattedDate(null));
    }

    /**
     * Test admin config helpers.
     */
    public function test_admin_config_helpers()
    {
        Config::set('hashtagcmsadmin.cmsInfo.theme', 'neo');
        Config::set('hashtagcmsadmin.cmsInfo.version', '1.0.0');
        Config::set('hashtagcmsadmin.cmsInfo.theme_assets', 'assets/fe');
        Config::set('hashtagcmsadmin.base_path', 'admin');
        Config::set('hashtagcmsadmin.cmsInfo.app_url', 'http://localhost');

        $this->assertEquals('neo', htcms_admin_theme());
        // htcms_admin_path returns path with leading slash
        $this->assertEquals('/admin/dashboard', htcms_admin_path('dashboard'));
        
        // htcms_admin_asset check
        $asset = htcms_admin_asset('style.css');
        $this->assertStringContainsString('http://localhost/assets/fe/style.css', $asset);
        $this->assertStringContainsString('verions=1.0.0', $asset);
    }

    /**
     * Test admin session helpers.
     */
    public function test_admin_session_helpers()
    {
        htcms_set_language_id_for_admin(5);
        $this->assertEquals(5, htcms_get_language_id_for_admin());

        htcms_set_siteId_for_admin(10);
        $this->assertEquals(10, htcms_get_siteId_for_admin());
    }
}
