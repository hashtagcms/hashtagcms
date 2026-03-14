<?php

namespace HashtagCms\Testing;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;

abstract class DuskTestCase extends BaseTestCase
{
    /**
     * Whether this test class should use SQLite with migrations.
     * Set to false in subclasses (e.g., AdminPanelDuskTest) to use MySQL without migrations.
     */
    protected bool $usesSqlite = true;

    // Removed DatabaseMigrations to prevent wiping of database
    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        if (! static::isRunningDusk()) {
            return;
        }

        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }
    }

    /**
     * Override: Do NOT let Dusk auto-start its own built-in server.
     * We rely on the user's already-running 'php artisan serve' instance.
     * Dusk's built-in server was killing the existing server and hanging.
     */
    protected function serve(): void
    {
        // Intentionally left empty — external server is already running.
    }

    /**
     * Determine if we are running in a Dusk context.
     * ⚠️  This is a static method called BEFORE the Laravel app boots (#[BeforeClass]).
     *     NEVER call config() or app() here — use only env() and $_SERVER.
     */
    protected static function isRunningDusk(): bool
    {
        foreach ($_SERVER['argv'] ?? [] as $arg) {
            if (str_contains($arg, 'dusk') || str_contains($arg, 'phpunit.dusk.xml')) {
                return true;
            }
        }
        // env() is safe in pre-boot static context; config() is NOT
        return env('APP_ENV') === 'testing';
    }

    protected function setUp(): void
    {
        parent::setUp();

        static $bootstrapped = false;
        if (!$bootstrapped) {
            // Only bootstrap SQLite for tests that need it.
            // Admin panel Dusk tests set $usesSqlite = false to use MySQL.
            if ($this->usesSqlite) {
                TestCase::bootstrapSqliteIfNeeded();
                fwrite(STDOUT, "\n🚀 [HashtagCMS] Dusk running on SQLite: " . config('database.connections.' . config('database.default') . '.database') . "\n");
            } else {
                fwrite(STDOUT, "\n🚀 [HashtagCMS] Dusk running on MySQL (existing DB)\n");
            }
            $url = env('APP_URL');
            fwrite(STDOUT, "   URL: $url\n\n");
            $bootstrapped = true;
        }
    }


    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--disable-web-security',
            '--allow-running-insecure-content',
            '--ignore-certificate-errors',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://127.0.0.1:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }
    
    /**
     * Get the admin base path.
     *
     * @return string
     */
    protected function getAdminPath()
    {
        return ltrim(config('hashtagcmsadmin.cmsInfo.base_path', 'admin'), '/');
    }
}
