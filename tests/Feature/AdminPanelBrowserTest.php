<?php

namespace HashtagCms\Tests\Feature;

use HashtagCms\Testing\TestCase;
use HashtagCms\User;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\DataProvider;
use HashtagCms\Models\Site;
use HashtagCms\Models\SiteProp;

#[Group('adminpanel')]
class AdminPanelBrowserTest extends TestCase
{
    /**
     * Use MySQL without migrations for admin panel tests.
     * Assumes MySQL DB is already set up with data.
     */
    protected bool $usesSqlite = false;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Force the auth model to HashtagCms\User for these tests
        config(['auth.providers.users.model' => User::class]);

        // Bypass all permission checks dynamically without modifying database
        \Illuminate\Support\Facades\Gate::before(fn() => true);

        // Disable CSRF for admin tests
        $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        ]);
    }

    /**
     * Data provider for Admin Controllers that are guaranteed to be seeded and functional.
     */
    public static function adminControllerProvider()
    {
        $controllers = [
            'dashboard', 'author', 'blog', 'category', 'city', 
            'cmslog', 'cmsmodule', 'comment', 'contact', 'country', 
            'currency', 'festival', 'gallery', 'homepage', 'hook', 
            'language', 'layout', 'module', 'moduleproperty', 
            'page', 'platform', 'role', 
            'rolesright', 'site', 'siteprop', 'staticmodule', 
            'subscriber', 'theme', 'zone'
        ];

        $data = [];
        foreach ($controllers as $controller) {
            $data[$controller] = [$controller];
        }
        return $data;
    }

    /**
     * Test that admin controller listing pages load correctly.
     */
    #[DataProvider('adminControllerProvider')]
    public function test_all_admin_controllers_listing_pages_load($controller)
    {
        $user = $this->getStaffUser();
        $adminPath = $this->getAdminPath();
        
        $response = $this->actingAs($user, 'sanctum')->get("/{$adminPath}/{$controller}");

        if ($response->isRedirect()) {
            $response = $this->followRedirects($response);
        }
        
        $response->assertStatus(200);
        
        // Verify we are within the admin panel layout
        $response->assertSee('js_left_panel');
    }

    /**
     * Test Validation UI (Rose Border & Amber Box logic).
     */
    public function test_validation_visual_logic_is_present()
    {
        $user = $this->getStaffUser();
        $adminPath = $this->getAdminPath();
        
        // Trigger a validation failure by omitting required fields (site_id and link_rewrite are required)
        $response = $this->actingAs($user, 'sanctum')
            ->from("/{$adminPath}/category/edit")
            ->post("/{$adminPath}/category/store", [
                'lang_name' => 'Test Category',
            ]);

        // Check session errors on the redirect response
        $response->assertSessionHasErrors(['site_id']);

        if ($response->isRedirect()) {
            $response = $this->followRedirects($response);
        }
        
        $response->assertStatus(200);
        
        // Verify script is present on the redirected-to page
        $response->assertSee('window.error_messages');
        $response->assertSee('error-handler.js');
    }

    /**
     * Test Advanced Site Management: Site Settings.
     */
    public function test_site_management_settings_tabs()
    {
        $user = $this->getStaffUser();
        $adminPath = $this->getAdminPath();
        
        $response = $this->actingAs($user, 'sanctum')->get("/{$adminPath}/site/settings/1/platforms");
        $response->assertStatus(200);
        $response->assertSee('site-wise');
    }

    /**
     * Test Layout Management: Homepage UI.
     */
    public function test_homepage_layout_manager_ui()
    {
        $user = $this->getStaffUser();
        $adminPath = $this->getAdminPath();
        
        $response = $this->actingAs($user, 'sanctum')->get("/{$adminPath}/homepage/ui");
        $response->assertStatus(200);
        $response->assertSee('layout');
    }

    /**
     * Test AJAX Action: Status Toggle.
     */
    public function test_ajax_status_toggle_endpoint()
    {
        $user = $this->getStaffUser();
        $adminPath = $this->getAdminPath();
        
        // Toggling status for category ID 1
        $response = $this->actingAs($user, 'sanctum')->get("/{$adminPath}/category/publish/1");
        
        $response->assertStatus(200);
        $response->assertJsonStructure(['id', 'status', 'meta']);
    }

    /**
     * Test Security: Unauthorized access to admin paths.
     */
    public function test_unauthorized_users_are_redirected_to_login()
    {
        $adminPath = $this->getAdminPath();
        
        $response = $this->get("/{$adminPath}/dashboard");
        $response->assertStatus(302);
        $response->assertRedirectContains('login');
    }
}
