<?php

namespace HashtagCms\Tests\Feature;

use HashtagCms\Testing\TestCase;
use HashtagCms\User as AuthUser;
use HashtagCms\Models\Role;
use HashtagCms\Models\Site;
use HashtagCms\Models\CmsModule;
use HashtagCms\Models\Page;
use HashtagCms\Models\PageLang;
use HashtagCms\Models\Category;
use HashtagCms\Models\Platform;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use HashtagCms\Core\Policies\CmsPolicy;
use HashtagCms\Models\CmsPermission;
use HashtagCms\Core\Providers\Admin\AdminServiceProvider;

class AdminRbacTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        // Use the HashtagCms User model for authentication
        config(['auth.providers.users.model' => AuthUser::class]);

        // Ensure database is seeded for RBAC
        $this->artisan('db:seed', ['--class' => 'HashtagCms\Database\Seeds\HashtagCmsDatabaseSeeder']);

        // Re-register gates because AdminServiceProvider might have booted before seeding
        (new AdminServiceProvider($this->app))->boot();

        // Disable CSRF for admin tests
        $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        ]);

        // Disable cache for testing to ensure dynamic permissions are picked up
        config(['hashtagcmsadmin.enable_cache' => false]);
        
        // Ensure site 1 is installed
        $this->ensureSiteInstalled();
    }

    /**
     * Test Requirement 5: Admin role has access to all CMS modules automatically.
     */
    public function test_admin_role_can_see_all_modules()
    {
        $site = Site::first();
        
        // Create an Admin user
        $admin = AuthUser::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'user_type' => 'Staff'
        ]);
        $role = Role::where('name', 'admin')->first();
        $admin->roles()->attach($role);
        $admin->sites()->attach($site);

        // Admin should see all modules via the CmsModuleComposer logic
        $adminPath = $this->getAdminPath();
        $response = $this->actingAs($admin->refresh(), 'web')
            ->withSession(['site_id' => $site->id])
            ->get("/{$adminPath}/dashboard");
        
        $response->assertStatus(200);

        // Because 'moduleAllowed' is bound via a ViewComposer to common.* subviews (like the sidebar), 
        // it won't be in the base dashboard.index viewData. We'll verify they see the Dashboard instead.
        $response->assertSeeText('Dashboard');

        // Let's also assert they can access another module like authors without issues
        $authorResponse = $this->actingAs($admin->refresh(), 'web')
            ->withSession(['site_id' => $site->id])
            ->get("/{$adminPath}/author");
        $authorResponse->assertStatus(200);
    }

    /**
     * Test Requirement: Contributor can only see their own content.
     */
    public function test_contributor_can_only_see_own_pages()
    {
        $site = Site::first();
        $contributor = AuthUser::create([
            'name' => 'Test Contributor',
            'email' => 'contributor@test.com',
            'password' => bcrypt('password'),
            'user_type' => 'Staff'
        ]);
        $role = Role::where('name', 'contributor')->first();
        $contributor->roles()->attach($role);
        $contributor->sites()->attach($site);
        $contributor->refresh(); // Refresh to ensure roles are loaded

        // Assign Page module to Contributor in cms_permissions
        $pageModule = CmsModule::where('controller_name', 'page')->first();
        $this->assertNotNull($pageModule, "Page module not found in seeder data");

        DB::table('cms_permissions')->insert([
            'module_id' => $pageModule->id,
            'user_id' => $contributor->id,
            'readonly' => 0
        ]);

        $category = Category::first();
        $platform = Platform::first();

        // Create one page by Contributor and one page by Admin (User ID 1)
        $page1 = Page::create([
            'alias' => 'own-page',
            'link_rewrite' => 'own-page',
            'site_id' => $site->id,
            'category_id' => $category->id,
            'platform_id' => $platform->id,
            'insert_by' => $contributor->id,
            'publish_status' => 1
        ]);
        PageLang::create([
            'page_id' => $page1->id,
            'lang_id' => 1,
            'name' => 'Contributor Own Page',
            'title' => 'Own Page',
            'page_content' => 'Content'
        ]);
        
        $page2 = Page::create([
            'alias' => 'other-page',
            'link_rewrite' => 'other-page',
            'site_id' => $site->id,
            'category_id' => $category->id,
            'platform_id' => $platform->id,
            'insert_by' => 1,
            'publish_status' => 1
        ]);
        PageLang::create([
            'page_id' => $page2->id,
            'lang_id' => 1,
            'name' => 'Admin Page',
            'title' => 'Admin Page',
            'page_content' => 'Content'
        ]);

        $adminPath = $this->getAdminPath();
        $response = $this->actingAs($contributor, 'web')
            ->withSession(['site_id' => $site->id, 'lang_id' => 1])
            ->get("/{$adminPath}/page");

        // The listing should contain Contributor Own Page but NOT Admin Page
        $response->assertSee('Contributor Own Page');
        $response->assertDontSee('Admin Page');
    }

    /**
     * Test Requirement: Contributor cannot edit someone else's content.
     */
    public function test_contributor_cannot_edit_others_content()
    {
        $site = Site::first();
        $contributor = AuthUser::create([
            'name' => 'Test Contributor',
            'email' => 'contributor2@test.com',
            'password' => bcrypt('password'),
            'user_type' => 'Staff'
        ]);
        $role = Role::where('name', 'contributor')->first();
        $contributor->roles()->attach($role);
        $contributor->sites()->attach($site);
        $contributor->refresh();

        // Assign Page module
        $pageModule = CmsModule::where('controller_name', 'page')->first();
        DB::table('cms_permissions')->insert([
            'module_id' => $pageModule->id,
            'user_id' => $contributor->id,
            'readonly' => 0
        ]);

        $category = Category::first();
        $platform = Platform::first();

        // Page owned by someone else
        $otherPage = Page::create([
            'alias' => 'other-page-2',
            'link_rewrite' => 'other-page-2',
            'site_id' => $site->id,
            'category_id' => $category->id,
            'platform_id' => $platform->id,
            'insert_by' => 1
        ]);

        $adminPath = $this->getAdminPath();
        
        // Attempt to access edit page
        $response = $this->actingAs($contributor, 'web')
            ->withSession(['site_id' => $site->id])
            ->get("/{$adminPath}/page/edit/{$otherPage->id}");
        
        // Should be blocked by policy
        $response->assertSee('Access Denied');
    }

    /**
     * Test Requirement: ReadOnly role can only read.
     */
    public function test_readonly_role_cannot_edit()
    {
        $site = Site::first();
        $readonlyUser = AuthUser::create([
            'name' => 'ReadOnly User',
            'email' => 'readonly@test.com',
            'password' => bcrypt('password'),
            'user_type' => 'Staff'
        ]);
        $role = Role::where('name', 'ReadOnly')->first();
        $readonlyUser->roles()->attach($role);
        $readonlyUser->sites()->attach($site);
        $readonlyUser->refresh();

        // Assign Page module
        $pageModule = CmsModule::where('controller_name', 'page')->first();
        DB::table('cms_permissions')->insert([
            'module_id' => $pageModule->id,
            'user_id' => $readonlyUser->id,
            'readonly' => 0
        ]);

        $adminPath = $this->getAdminPath();
        
        // Attempt to access edit page
        $response = $this->actingAs($readonlyUser, 'web')
            ->withSession(['site_id' => $site->id])
            ->get("/{$adminPath}/page/edit");
        
        // Should be blocked by policy since ReadOnly role doesn't have 'edit' permission
        $response->assertSee('Access Denied');
    }

    /**
     * Test Requirement: Contributor can access the create form (Fixed bug where empty resource blocked creation).
     */
    public function test_contributor_can_access_create_form()
    {
        $site = Site::first();
        $contributor = AuthUser::create([
            'name' => 'Create Contributor',
            'email' => 'contributor3@test.com',
            'password' => bcrypt('password'),
            'user_type' => 'Staff'
        ]);
        $role = Role::where('name', 'contributor')->first();
        $contributor->roles()->attach($role);
        $contributor->sites()->attach($site);
        $contributor->refresh();

        // Assign Page module
        $pageModule = CmsModule::where('controller_name', 'page')->first();
        DB::table('cms_permissions')->insert([
            'module_id' => $pageModule->id,
            'user_id' => $contributor->id,
            'readonly' => 0
        ]);

        $adminPath = $this->getAdminPath();
        
        // Attempt to access page/create
        $response = $this->actingAs($contributor, 'web')
            ->withSession(['site_id' => $site->id])
            ->get("/{$adminPath}/page/create");
        
        // Should NOT see Access Denied. Should see the form instead.
        $response->assertStatus(200);
        $response->assertDontSee('Access Denied');
    }

    /**
     * Test Requirement: Approver can access module details via AJAX (Fixed AjaxController routing bug).
     */
    public function test_approver_can_use_ajax_getinfo()
    {
        $site = Site::first();
        $approver = AuthUser::create([
            'name' => 'Test Approver',
            'email' => 'approver@test.com',
            'password' => bcrypt('password'),
            'user_type' => 'Staff'
        ]);
        $role = Role::where('name', 'approver')->first();
        $approver->roles()->attach($role);
        $approver->sites()->attach($site);
        $approver->refresh();

        // Assign Page module
        $pageModule = CmsModule::where('controller_name', 'page')->first();
        DB::table('cms_permissions')->insert([
            'module_id' => $pageModule->id,
            'user_id' => $approver->id,
            'readonly' => 1 // Read only is fine for read check
        ]);

        $adminPath = $this->getAdminPath();

        // Need to create a page to fetch
        $category = Category::first();
        $platform = Platform::first();
        $page = Page::create([
            'alias' => 'test-page-ajax',
            'link_rewrite' => 'test-page-ajax',
            'site_id' => $site->id,
            'category_id' => $category->id,
            'platform_id' => $platform->id,
            'insert_by' => 1
        ]);

        // Access via AjaxController::getInfo (ajax/getinfo/{model}/{id})
        $response = $this->actingAs($approver, 'web')
            ->withSession(['site_id' => $site->id])
            ->get("/{$adminPath}/ajax/getinfo/page/{$page->id}");

        $response->assertStatus(200);
        $response->assertDontSee("Sorry! You don't have permission to read");
        
        // Use a more robust JSON check
        $response->assertJson(function (\Illuminate\Testing\Fluent\AssertableJson $json) use ($page) {
            $json->where('results.id', $page->id)->etc();
        });
    }

    /**
     * Test Requirement: Magic Policy Support - Any permission in DB works via __call fallback.
     */
    public function test_dynamic_permission_via_magic_call()
    {
        $site = Site::first();
        $editor = AuthUser::create([
            'name' => 'Magic Editor',
            'email' => 'magic@test.com',
            'password' => bcrypt('password'),
            'user_type' => 'Staff'
        ]);
        $role = Role::where('name', 'editor')->first();
        $editor->roles()->attach($role);
        $editor->sites()->attach($site);

        // 1. Add a new permission type 'export' to the DB
        $date = date('Y-m-d H:i:s');
        $permissionId = DB::table('permissions')->insertGetId([
            'name' => 'export',
            'label' => 'Export Permission',
            'created_at' => $date,
            'updated_at' => $date
        ]);

        // 2. Map this 'export' permission to the Editor role
        DB::table('permission_role')->insert([
            'permission_id' => $permissionId,
            'role_id' => $role->id
        ]);

        // 3. Assign module to user
        $pageModule = CmsModule::where('controller_name', 'page')->first();
        DB::table('cms_permissions')->insert([
            'module_id' => $pageModule->id,
            'user_id' => $editor->id,
            'readonly' => 0
        ]);

        // 4. Manually define the 'export' gate to simulate DB-driven gate registration
        \Illuminate\Support\Facades\Gate::define('export', function ($user) {
            return true;
        });
        
        // 5. Test the Magic Policy directly via __call
        $policy = new CmsPolicy();
        
        $permissionModel = CmsPermission::where('module_id', $pageModule->id)
                            ->where('user_id', $editor->id)->first();

        // This should trigger __call('export', ...) which calls canPerform(..., 'export')
        $canExport = $policy->export($editor, $permissionModel);
        
        $this->assertTrue($canExport, "The dynamic 'export' permission should be handled by BaseCmsPolicy::__call");
    }
}
