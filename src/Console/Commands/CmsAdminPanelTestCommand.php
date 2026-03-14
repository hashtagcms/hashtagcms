<?php

namespace HashtagCms\Console\Commands;

use Illuminate\Console\Command;

class CmsAdminPanelTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:test-adminpanel {--filter= : Filter tests} {--group=adminpanel : Test group to run} {--testdox : Show tests as a checklist} {--no-dusk : Skip browser tests, run HTTP tests only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[#CMS] Run HashtagCMS Admin Panel browser-based tests';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Starting HashtagCMS Admin Panel Tests...");

        $group   = $this->option('group');
        $filter  = $this->option('filter');
        $testdox = $this->option('testdox');
        $noDusk  = $this->option('no-dusk');

        if ($noDusk) {
            // Run HTTP tests only (no browser, no screenshots)
            $command = "php artisan test vendor/hashtagcms/hashtagcms --group={$group} --ansi";
            if ($filter)  $command .= " --filter={$filter}";
            if ($testdox) $command .= " --testdox";

            $this->info("Running: {$command}");
            $this->line('');
            $process = proc_open($command, [0 => STDIN, 1 => STDOUT, 2 => STDERR], $pipes);
            $exitCode = is_resource($process) ? proc_close($process) : 1;
        } else {
            // Default: Run Dusk browser tests with screenshots
            $exitCode = $this->runDuskTests($filter);
        }

        return $exitCode;
    }

    /**
     * Run Dusk browser tests following the full pre-flight checklist.
     * MySQL is never touched. Only SQLite is managed.
     */
    protected function runDuskTests(?string $filter): int
    {
        $envFile     = '.env.dusk.local';
        $duskEnvPath = base_path($envFile);
        $mainEnvPath = base_path('.env');

        // Helper: read a value from any .env-format file using regex
        $readEnvVal = function (string $filePath, string $key): ?string {
            if (!file_exists($filePath)) return null;
            $content = file_get_contents($filePath);
            if (preg_match('/^' . preg_quote($key, '/') . '=(.*)$/m', $content, $matches)) {
                return trim($matches[1], " \t\n\r\0\x0B\"'");
            }
            return null;
        };

        // Helper: write/update a single key=value in a .env file
        $writeEnvVal = function (string $filePath, string $key, string $value) {
            $content = file_get_contents($filePath);
            if (preg_match('/^' . preg_quote($key, '/') . '=.*$/m', $content)) {
                $content = preg_replace('/^' . preg_quote($key, '/') . '=.*$/m', "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
            file_put_contents($filePath, $content);
        };

        // ─────────────────────────────────────────────
        // STEP 1 & 2: Read APP_URL from main .env and check if server is running
        // ─────────────────────────────────────────────
        $mainAppUrl = $readEnvVal($mainEnvPath, 'APP_URL') ?? 'http://localhost:8000';
        $mainHost   = parse_url($mainAppUrl, PHP_URL_HOST) ?? 'localhost';
        $mainPort   = parse_url($mainAppUrl, PHP_URL_PORT) ?? 80;
        $mainScheme = parse_url($mainAppUrl, PHP_URL_SCHEME) ?? 'http';

        $this->line("  Checking server at: <fg=cyan>{$mainAppUrl}</>");

        // Ping the server (short timeout)
        $context = stream_context_create(['http' => ['timeout' => 3, 'ignore_errors' => true]]);
        $ping    = @file_get_contents("{$mainScheme}://{$mainHost}:{$mainPort}", false, $context);

        if ($ping === false) {
            $this->error("❌ Server is NOT running at {$mainAppUrl}");
            $this->line("");
            $this->line("Please start your server first, then re-run this command:");
            $this->line("  <fg=yellow>php artisan serve --host={$mainHost} --port={$mainPort}</>");
            $this->line("");
            return 1;
        }
        $this->line("  <fg=green>✓</> Server is running at <fg=cyan>{$mainAppUrl}</>");
        $this->line('');

        // ─────────────────────────────────────────────
        // STEP 3: Verify .env.dusk.local exists
        // ─────────────────────────────────────────────
        if (!file_exists($duskEnvPath)) {
            $this->error("❌ '{$envFile}' not found!");
            $this->line("HashtagCMS requires a '{$envFile}' file to run Dusk tests safely.");
            $this->line("Create it with the following contents:");
            $this->line("");
            $this->line("  APP_ENV=testing");
            $this->line("  DB_CONNECTION=sqlite");
            $this->line("  DB_DATABASE=/absolute/path/to/dusk.sqlite");
            $this->line("  CACHE_STORE=array");
            $this->line("  SESSION_DRIVER=file");
            $this->line("  QUEUE_CONNECTION=sync");
            $this->line("");
            return 1;
        }

        $duskConn = $readEnvVal($duskEnvPath, 'DB_CONNECTION');
        $duskDb   = $readEnvVal($duskEnvPath, 'DB_DATABASE');
        $mainConn = $readEnvVal($mainEnvPath, 'DB_CONNECTION');
        $mainDb   = $readEnvVal($mainEnvPath, 'DB_DATABASE');

        // ─────────────────────────────────────────────
        // STEP 4: Hard block — Dusk must use SQLite ONLY
        // ─────────────────────────────────────────────
        if (in_array(strtolower((string) $duskConn), ['mysql', 'pgsql', 'sqlsrv', 'mariadb'])) {
            $this->error("❌ BLOCKED: Dusk cannot run against MySQL/PostgreSQL/SQL Server.");
            $this->line("  '{$envFile}' has DB_CONNECTION={$duskConn}");
            $this->line("  Change it to: <fg=yellow>DB_CONNECTION=sqlite</>  and set DB_DATABASE to a .sqlite file path.");
            $this->line("  Your MySQL database (<fg=yellow>{$mainDb}</>) will NEVER be touched.");
            return 1;
        }

        // ─────────────────────────────────────────────
        // STEP 5: Hard block — No DB collision
        // ─────────────────────────────────────────────
        if ($duskConn === $mainConn && $duskDb === $mainDb && !empty($duskDb)) {
            $this->error("❌ BLOCKED: DB collision detected!");
            $this->line("  Both '.env' and '{$envFile}' point to the same database:");
            $this->line("  Connection : {$duskConn}");
            $this->line("  Database   : {$duskDb}");
            $this->line("  Please use a separate .sqlite file in '{$envFile}'.");
            return 1;
        }

        // ─────────────────────────────────────────────
        // STEP 6: Sync APP_URL from main .env → .env.dusk.local
        // (So Chrome always hits the correct running server)
        // ─────────────────────────────────────────────
        $writeEnvVal($duskEnvPath, 'APP_URL', $mainAppUrl);
        $this->line("  <fg=green>✓</> APP_URL synced to: <fg=cyan>{$mainAppUrl}</>");

        // ─────────────────────────────────────────────
        // STEP 7: Handle SQLite database (create/overwrite)
        // ─────────────────────────────────────────────
        $this->line('');
        $this->line("  Main DB  : <fg=gray>{$mainConn}://{$mainDb}</>");
        $this->line("  Test DB  : <fg=yellow>{$duskConn}://{$duskDb}</>");
        $this->line('');

        $dbExists    = !empty($duskDb) && file_exists($duskDb);
        $shouldSeed  = true;

        $seederClass = 'HashtagCms\Database\Seeds\HashtagCmsDatabaseSeeder';

        if ($dbExists) {
            $this->line("  <fg=yellow>⚠</>  SQLite database already exists at:");
            $this->line("       <fg=yellow>{$duskDb}</>");
            $this->line('');
            if ($this->confirm("  Overwrite this database with a fresh migration + seed?", false)) {
                // Wipe and migrate fresh
                $this->line("  <fg=gray>Wiping and re-creating the SQLite database...</>");
                @unlink($duskDb);
                touch($duskDb);
            } else {
                $this->line("  <fg=green>✓</> Using existing database. Tests will run on current data.");
                $shouldSeed = false;
            }
        } else {
            // Create the file if it doesn't exist
            if (!empty($duskDb)) {
                $dir = dirname($duskDb);
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                touch($duskDb);
                $this->line("  <fg=green>✓</> Created new SQLite file: <fg=cyan>{$duskDb}</>");
            }
        }

        if ($shouldSeed) {
            $this->line("  <fg=gray>Running migrate:fresh and seed on SQLite...</>");

            // Run migrations against the dusk.sqlite file
            $migrateCmd = "DB_CONNECTION=sqlite DB_DATABASE=\"{$duskDb}\" php artisan migrate --force 2>&1";
            $migrateOut = shell_exec($migrateCmd);
            if (str_contains((string) $migrateOut, 'ERROR') || str_contains((string) $migrateOut, 'error')) {
                $this->warn("Migration output: " . $migrateOut);
            } else {
                $this->line("  <fg=green>✓</> Migrations complete.");
            }

            // Seed the database
            $seedCmd = "DB_CONNECTION=sqlite DB_DATABASE=\"{$duskDb}\" php artisan db:seed --class=\"{$seederClass}\" --force 2>&1";
            $seedOut = shell_exec($seedCmd);
            if (str_contains((string) $seedOut, 'ERROR') || str_contains((string) $seedOut, 'error')) {
                $this->warn("Seed output: " . $seedOut);
            } else {
                $this->line("  <fg=green>✓</> Database seeded successfully.");
            }
        }

        // ─────────────────────────────────────────────
        // STEP 8: Restart server with --no-reload to prevent env-swap hang
        // ─────────────────────────────────────────────
        $this->line('');
        $this->line("  <fg=gray>Restarting server with --no-reload to prevent env-swap hang...</>");
        shell_exec("kill \$(lsof -ti :{$mainPort}) 2>/dev/null; sleep 1");
        shell_exec("php artisan serve --host=0.0.0.0 --port={$mainPort} --no-reload > /dev/null 2>&1 &");
        sleep(2);

        // Verify server is up again
        $ping = @file_get_contents("{$mainScheme}://{$mainHost}:{$mainPort}", false, $context);
        if ($ping === false) {
            // Try via 127.0.0.1 (hostname DNS might be slow)
            $ping = @file_get_contents("{$mainScheme}://127.0.0.1:{$mainPort}", false, $context);
        }
        if ($ping === false) {
            $this->error("❌ Server failed to restart. Please start it manually and re-run.");
            return 1;
        }
        $this->line("  <fg=green>✓</> Server ready at <fg=cyan>{$mainAppUrl}</>");
        $this->line('');

        // ─────────────────────────────────────────────
        // STEP 9: Run Dusk tests
        // ─────────────────────────────────────────────
        $command = "php artisan dusk vendor/hashtagcms/hashtagcms/tests/Browser";
        if ($filter) $command .= " --filter={$filter}";

        $this->info("Running Dusk Browser Tests...");
        $this->line("  <fg=gray>{$command}</>");
        $this->line('');

        $process  = proc_open($command, [0 => STDIN, 1 => STDOUT, 2 => STDERR], $pipes);
        $exitCode = is_resource($process) ? proc_close($process) : 1;

        // ─────────────────────────────────────────────
        // STEP 10: Cleanup — kill the --no-reload server so user can use their normal server
        // ─────────────────────────────────────────────
        shell_exec("kill \$(lsof -ti :{$mainPort}) 2>/dev/null");
        $this->line('');
        $this->line("  <fg=gray>Test server stopped. Restart your server with:</>");
        $this->line("  <fg=yellow>php artisan serve --host={$mainHost} --port={$mainPort}</>");
        $this->line('');

        // Generate screenshot report
        $reportPath = $this->generateScreenshotReport();
        if ($reportPath) {
            $relativePath = 'tests/Browser/screenshots/report.html';
            $this->line('  ┌──────────────────────────────────────────────────────────────┐');
            $this->line('  │                   📸  Screenshots Ready!                     │');
            $this->line('  │                                                              │');
            $this->line("  │  Open the report in your browser:                           │");
            $this->line("  │   <fg=cyan;options=bold>{$relativePath}</>                ");
            $this->line("  │                                                              │");
            $this->line('  └──────────────────────────────────────────────────────────────┘');
            $this->line('');
            $this->info("  👉 Run:  open \"{$relativePath}\"");
            $this->line('');
        }

        return $exitCode;
    }


    /**
     * Generate an HTML report of all screenshots.
     */
    protected function generateScreenshotReport(): ?string
    {
        $screenshotDir = base_path('tests/Browser/screenshots');

        if (!is_dir($screenshotDir)) {
            return null;
        }

        // Collect all _grid and _table screenshots, skip legacy _listing
        $allFiles = glob("{$screenshotDir}/admin_*.png");
        if (empty($allFiles)) {
            $this->warn("No screenshots found in {$screenshotDir}");
            return null;
        }

        // Group by controller name
        $controllers = [];
        foreach ($allFiles as $path) {
            $filename = basename($path, '.png');

            // Skip legacy _listing screenshots
            if (str_ends_with($filename, '_listing')) {
                continue;
            }

            if (str_ends_with($filename, '_grid')) {
                $controller = substr($filename, strlen('admin_'), -strlen('_grid'));
                $controllers[$controller]['grid'] = $path;
            } elseif (str_ends_with($filename, '_table')) {
                $controller = substr($filename, strlen('admin_'), -strlen('_table'));
                $controllers[$controller]['table'] = $path;
            }
        }

        ksort($controllers);

        if (empty($controllers)) {
            $this->warn("No _grid or _table screenshots found.");
            return null;
        }

        $timestamp = date('Y-m-d H:i:s');
        $count = count($controllers);
        $tabs = '';
        $panels = '';
        $firstActive = true;

        foreach ($controllers as $controller => $views) {
            $label = ucwords(str_replace('_', ' ', $controller));
            $activeTab   = $firstActive ? 'active' : '';
            $activePanelClass = $firstActive ? 'active' : '';
            $id = "tab-{$controller}";

            $tabs .= "<button class=\"tab-btn {$activeTab}\" data-target=\"{$id}\">{$label}</button>\n";

            // Build grid panel
            $gridHtml = '<div class="no-screenshot">No grid screenshot</div>';
            if (!empty($views['grid'])) {
                $imageData = base64_encode(file_get_contents($views['grid']));
                $imageSize = round(filesize($views['grid']) / 1024, 1);
                $gridHtml = <<<HTML
                    <div class="view-header">
                        <span class="view-badge grid-badge">⊞ Grid</span>
                        <span class="panel-meta">📦 {$imageSize} KB</span>
                    </div>
                    <div class="panel-image" title="Click to enlarge">
                        <img src="data:image/png;base64,{$imageData}" alt="{$label} Grid"
                             class="lightbox-trigger" data-title="{$label} — Grid View" />
                    </div>
                HTML;
            }

            // Build table panel
            $tableHtml = '<div class="no-screenshot">No table screenshot</div>';
            if (!empty($views['table'])) {
                $imageData = base64_encode(file_get_contents($views['table']));
                $imageSize = round(filesize($views['table']) / 1024, 1);
                $tableHtml = <<<HTML
                    <div class="view-header">
                        <span class="view-badge table-badge">☰ Table</span>
                        <span class="panel-meta">📦 {$imageSize} KB</span>
                    </div>
                    <div class="panel-image" title="Click to enlarge">
                        <img src="data:image/png;base64,{$imageData}" alt="{$label} Table"
                             class="lightbox-trigger" data-title="{$label} — Table View" />
                    </div>
                HTML;
            }

            $panels .= <<<HTML
                <div class="panel {$activePanelClass}" id="{$id}">
                    <div class="panel-header">
                        <span class="panel-title">{$label}</span>
                    </div>
                    <div class="views-grid">
                        <div class="view-col">{$gridHtml}</div>
                        <div class="view-col">{$tableHtml}</div>
                    </div>
                </div>
            HTML;

            $firstActive = false;
        }

        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            <title>HashtagCMS Admin Panel — Screenshot Report</title>
            <style>
                *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, sans-serif;
                    background: #0f1117;
                    color: #e2e8f0;
                    min-height: 100vh;
                    display: flex;
                    flex-direction: column;
                }

                header {
                    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
                    border-bottom: 1px solid #334155;
                    padding: 20px 30px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    position: sticky;
                    top: 0;
                    z-index: 100;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.4);
                }

                header .logo {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                }

                header .logo span {
                    font-size: 20px;
                    font-weight: 700;
                    background: linear-gradient(90deg, #60a5fa, #a78bfa);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                }

                header .meta { font-size: 13px; color: #64748b; }

                header .badge {
                    background: #1d4ed8;
                    color: #fff;
                    padding: 4px 12px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: 600;
                }

                .layout { display: flex; flex: 1; overflow: hidden; }

                .sidebar {
                    width: 210px;
                    flex-shrink: 0;
                    background: #151b28;
                    border-right: 1px solid #1e293b;
                    overflow-y: auto;
                    padding: 16px 0;
                }

                .sidebar-title {
                    font-size: 11px;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    color: #475569;
                    padding: 0 16px 10px;
                }

                .tab-btn {
                    display: block;
                    width: 100%;
                    text-align: left;
                    padding: 10px 16px;
                    border: none;
                    background: transparent;
                    color: #94a3b8;
                    font-size: 13px;
                    cursor: pointer;
                    transition: all 0.15s ease;
                    border-left: 3px solid transparent;
                }

                .tab-btn:hover { background: #1e293b; color: #e2e8f0; }

                .tab-btn.active {
                    background: #1e293b;
                    color: #60a5fa;
                    border-left-color: #3b82f6;
                    font-weight: 600;
                }

                .content {
                    flex: 1;
                    overflow-y: auto;
                    padding: 30px;
                    background: #0f1117;
                }

                .panel { display: none; }
                .panel.active { display: block; }

                .panel-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    margin-bottom: 20px;
                    padding-bottom: 16px;
                    border-bottom: 1px solid #1e293b;
                }

                .panel-title {
                    font-size: 22px;
                    font-weight: 700;
                    color: #f1f5f9;
                }

                /* Side-by-side grid layout */
                .views-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                }

                .view-col {}

                .view-header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    margin-bottom: 10px;
                }

                .view-badge {
                    font-size: 12px;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    padding: 4px 12px;
                    border-radius: 20px;
                }

                .grid-badge { background: #1d4ed8; color: #fff; }
                .table-badge { background: #065f46; color: #fff; }

                .panel-meta {
                    font-size: 12px;
                    color: #64748b;
                    background: #1e293b;
                    padding: 4px 10px;
                    border-radius: 20px;
                }

                .panel-image {
                    background: #1e293b;
                    border-radius: 10px;
                    overflow: hidden;
                    border: 1px solid #334155;
                    box-shadow: 0 6px 24px rgba(0,0,0,0.4);
                }

                .panel-image img {
                    display: block;
                    width: 100%;
                    height: auto;
                    cursor: zoom-in;
                    transition: opacity 0.15s ease;
                }
                .panel-image img:hover { opacity: 0.88; }

                .no-screenshot {
                    background: #1e293b;
                    border-radius: 10px;
                    border: 1px dashed #334155;
                    height: 200px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #475569;
                    font-size: 13px;
                }

                .search-wrap { padding: 10px 12px 6px; }
                .search-wrap input {
                    width: 100%;
                    background: #1e293b;
                    border: 1px solid #334155;
                    border-radius: 6px;
                    padding: 7px 10px;
                    color: #e2e8f0;
                    font-size: 12px;
                    outline: none;
                }
                .search-wrap input::placeholder { color: #475569; }
                .search-wrap input:focus { border-color: #3b82f6; }

                /* ── Lightbox ── */
                #lightbox {
                    display: none;
                    position: fixed;
                    inset: 0;
                    z-index: 9999;
                    background: rgba(0,0,0,0.92);
                    backdrop-filter: blur(6px);
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    animation: lbFadeIn 0.18s ease;
                }
                #lightbox.open { display: flex; }
                @keyframes lbFadeIn { from { opacity:0; } to { opacity:1; } }

                #lb-inner {
                    position: relative;
                    max-width: 92vw;
                    max-height: 88vh;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 14px;
                }

                #lb-img {
                    display: block;
                    max-width: 92vw;
                    max-height: 80vh;
                    border-radius: 10px;
                    box-shadow: 0 20px 80px rgba(0,0,0,0.8);
                    object-fit: contain;
                    animation: lbSlideIn 0.2s ease;
                }
                @keyframes lbSlideIn { from { opacity:0; transform:scale(0.96);} to { opacity:1; transform:scale(1);} }

                #lb-title {
                    font-size: 14px;
                    font-weight: 600;
                    color: #94a3b8;
                    letter-spacing: 0.3px;
                    text-align: center;
                }
                #lb-counter {
                    font-size: 12px;
                    color: #475569;
                }

                .lb-btn {
                    position: fixed;
                    top: 50%;
                    transform: translateY(-50%);
                    background: rgba(255,255,255,0.07);
                    border: 1px solid rgba(255,255,255,0.12);
                    color: #e2e8f0;
                    width: 48px;
                    height: 48px;
                    border-radius: 50%;
                    font-size: 22px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: background 0.15s ease;
                    backdrop-filter: blur(4px);
                    z-index: 10000;
                }
                .lb-btn:hover { background: rgba(255,255,255,0.15); }
                #lb-prev { left: 20px; }
                #lb-next { right: 20px; }

                #lb-close {
                    position: fixed;
                    top: 16px;
                    right: 20px;
                    background: rgba(255,255,255,0.07);
                    border: 1px solid rgba(255,255,255,0.12);
                    color: #e2e8f0;
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    font-size: 20px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: background 0.15s ease;
                    z-index: 10000;
                }
                #lb-close:hover { background: rgba(239,68,68,0.4); }
            </style>
        </head>
        <body>
            <header>
                <div class="logo">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="24" height="24" rx="6" fill="#3b82f6"/>
                        <path d="M7 12h10M12 7v10" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                    <span>HashtagCMS — Admin Panel Screenshot Report</span>
                </div>
                <div style="display:flex;gap:12px;align-items:center;">
                    <span class="meta">Generated: {$timestamp}</span>
                    <span class="badge">{$count} Pages</span>
                </div>
            </header>

            <div class="layout">
                <nav class="sidebar">
                    <div class="search-wrap">
                        <input type="text" id="search" placeholder="🔍 Filter pages..." />
                    </div>
                    <div class="sidebar-title">Pages</div>
                    <div id="tab-list">
                        {$tabs}
                    </div>
                </nav>

                <main class="content" id="content">
                    {$panels}
                </main>
            </div>

            <!-- Lightbox Modal -->
            <div id="lightbox">
                <button id="lb-close" title="Close (Esc)">✕</button>
                <button class="lb-btn" id="lb-prev" title="Previous (←)">&#8592;</button>
                <div id="lb-inner">
                    <img id="lb-img" src="" alt="" />
                    <div id="lb-title"></div>
                    <div id="lb-counter"></div>
                </div>
                <button class="lb-btn" id="lb-next" title="Next (→)">&#8594;</button>
            </div>

            <script>
                // ── Tab navigation ──
                const tabBtns = document.querySelectorAll('.tab-btn');
                const panels  = document.querySelectorAll('.panel');

                tabBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        tabBtns.forEach(b => b.classList.remove('active'));
                        panels.forEach(p => p.classList.remove('active'));
                        btn.classList.add('active');
                        document.getElementById(btn.dataset.target).classList.add('active');
                        document.getElementById('content').scrollTop = 0;
                    });
                });

                document.getElementById('search').addEventListener('input', function () {
                    const q = this.value.toLowerCase();
                    tabBtns.forEach(btn => {
                        btn.style.display = btn.textContent.toLowerCase().includes(q) ? 'block' : 'none';
                    });
                });

                // ── Lightbox ──
                const lb        = document.getElementById('lightbox');
                const lbImg     = document.getElementById('lb-img');
                const lbTitle   = document.getElementById('lb-title');
                const lbCounter = document.getElementById('lb-counter');

                // Collect ALL images across every panel in DOM order
                let allImages = [];
                let currentIdx = 0;

                function buildImageIndex() {
                    allImages = Array.from(document.querySelectorAll('.lightbox-trigger'));
                }

                function openLightbox(idx) {
                    if (!allImages.length) buildImageIndex();
                    currentIdx = idx;
                    const img = allImages[currentIdx];
                    lbImg.src       = img.src;
                    lbImg.alt       = img.alt;
                    lbTitle.textContent   = img.dataset.title || img.alt;
                    lbCounter.textContent = (currentIdx + 1) + ' / ' + allImages.length;
                    lb.classList.add('open');
                    document.body.style.overflow = 'hidden';
                }

                function closeLightbox() {
                    lb.classList.remove('open');
                    document.body.style.overflow = '';
                }

                function showPrev() {
                    openLightbox((currentIdx - 1 + allImages.length) % allImages.length);
                }

                function showNext() {
                    openLightbox((currentIdx + 1) % allImages.length);
                }

                // Attach click to all images
                document.addEventListener('click', function(e) {
                    const trigger = e.target.closest('.lightbox-trigger');
                    if (trigger) {
                        buildImageIndex();
                        openLightbox(allImages.indexOf(trigger));
                    }
                });

                document.getElementById('lb-close').addEventListener('click', closeLightbox);
                document.getElementById('lb-prev').addEventListener('click', showPrev);
                document.getElementById('lb-next').addEventListener('click', showNext);

                // Close on backdrop click (not on image/buttons)
                lb.addEventListener('click', function(e) {
                    if (e.target === lb) closeLightbox();
                });

                // Keyboard navigation
                document.addEventListener('keydown', function(e) {
                    if (!lb.classList.contains('open')) return;
                    if (e.key === 'Escape')      closeLightbox();
                    else if (e.key === 'ArrowLeft')  showPrev();
                    else if (e.key === 'ArrowRight') showNext();
                });
            </script>
        </body>
        </html>
        HTML;

        $reportPath = "{$screenshotDir}/report.html";
        file_put_contents($reportPath, $html);

        return $reportPath;
    }

}
