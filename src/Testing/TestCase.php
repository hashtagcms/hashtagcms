<?php

namespace HashtagCms\Testing;

//use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends \Tests\TestCase
{
    // use CreatesApplication; // Not needed as we run in host app context usually or use Testbench
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // We must seed because the system expects Site(1), User(1) etc to exist for installation
        $this->seed(\HashtagCms\Database\Seeds\HashtagCmsDatabaseSeeder::class);
        
        $host = parse_url(env('APP_URL'), PHP_URL_HOST);
        // Map current host to 'web' context for testing
        // We set the whole array to avoid dot-notation issues with "packages.testing"
        $domains = config('hashtagcms.domains');
        $domains[$host] = 'web';
        config(['hashtagcms.domains' => $domains]);
    }

    /**
     * Simulate a site installation functionality.
     * This mimics the InstallController logic.
     */
    protected function installSite($overrides = [])
    {
        $host = parse_url(env('APP_URL'), PHP_URL_HOST);
        
        $defaults = [
            'site_name' => 'Test Site',
            'site_title' => 'Test Site Title',
            'site_context' => 'web',
            'site_domain' => $host,
            'name' => 'Admin User',
            'user_email' => 'admin@example.com',
            'user_password' => 'password',
        ];

        $data = array_merge($defaults, $overrides);

        // Update User
        $user = \HashtagCms\User::find(1);
        $user->email = $data['user_email'];
        $user->name = $data['name'];
        $user->password = \Illuminate\Support\Facades\Hash::make($data['user_password']);
        $user->save();

        // Update Site
        $site = \HashtagCms\Models\Site::with('lang')->find(1);
        $site->name = $data['site_name'];
        $site->context = $data['site_context'];
        $site->domain = $data['site_domain'];
        $site->save();

        // Update Site Lang Title
        $site->lang()->update(['title' => $data['site_title']]);

        // Set Installed Flag
        $siteInstalled = \HashtagCms\Models\SiteProp::where('name', '=', 'site_installed')->first();
        $siteInstalled->value = 1;
        $siteInstalled->save();

        return $site;
    }

    /**
     * Create a basic site configuration for testing
     */
    protected function createSiteAndPage(string $context = 'web', string $lang = 'en')
    {
        $now = now();
        
        // 1. Create Site
        $siteId = \Illuminate\Support\Facades\DB::table('sites')->insertGetId([
            'name' => 'Test Site',
            'context' => $context,
            'lang' => $lang,
            'category_id' => 1,
            'theme_id' => 1,
            'domain' => 'localhost',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        // 2. Create Platform
        $platformId = \Illuminate\Support\Facades\DB::table('platforms')->insertGetId([
            'name' => 'Web',
            'link_rewrite' => 'web',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        // 3. Create Lang
        $langId = \Illuminate\Support\Facades\DB::table('langs')->insertGetId([
            'name' => 'English',
            'iso_code' => 'en',
            'created_at' => $now,
            'updated_at' => $now
        ]);
        
        // 4. Create Theme
        $themeId = \Illuminate\Support\Facades\DB::table('themes')->insertGetId([
            'site_id' => $siteId,
            'name' => 'Basic Theme',
            'alias' => 'basic',
            'directory' => 'basic',
            'skeleton' => '<!DOCTYPE html><html><head><title>%title%</title></head><body>%header%%content%%footer%</body></html>',
            'header_content' => '<header>Header</header>',
            'footer_content' => '<footer>Footer</footer>',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        // 5. Create Category
        $categoryId = \Illuminate\Support\Facades\DB::table('categories')->insertGetId([
            'site_id' => $siteId,
            'parent_id' => 0,
            'is_site_root' => 1,
            'category_name' => 'Home',
            'link_rewrite' => 'home',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        // 6. Create Page (root)
        \Illuminate\Support\Facades\DB::table('pages')->insert([
            'site_id' => $siteId,
            'category_id' => $categoryId,
            'alias' => 'home',
            'title' => 'Home Page',
            'header_content' => '',
            'footer_content' => '',
            'created_at' => $now,
            'updated_at' => $now
        ]);

        return $siteId;
    }
}
