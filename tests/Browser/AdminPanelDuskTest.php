<?php

namespace HashtagCms\Tests\Browser;

use HashtagCms\Testing\DuskTestCase;
use Laravel\Dusk\Browser;
use HashtagCms\User;
use PHPUnit\Framework\Attributes\DataProvider;
use HashtagCms\Models\SiteProp;

class AdminPanelDuskTest extends DuskTestCase
{
    /**
     * Use MySQL without migrations for admin panel Dusk tests.
     * Assumes MySQL DB is already set up with data.
     */
    protected bool $usesSqlite = false;

    protected $duskTestUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Mark the site as installed for the browser to work correctly.
        $siteProp = SiteProp::where('name', '=', 'site_installed')->first();
        if ($siteProp && $siteProp->value != 1) {
            $siteProp->value = 1;
            $siteProp->save();
        }
    }

    /**
     * After all tests, generate a visual HTML report from the screenshots.
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::generateScreenshotReport();
    }

    /**
     * Build an HTML report page from all screenshots.
     */
    protected static function generateScreenshotReport(): void
    {
        $screenshotDir = base_path('tests/Browser/screenshots');
        $reportFile    = $screenshotDir . '/report.html';

        if (!is_dir($screenshotDir)) {
            return;
        }

        $screenshots = glob($screenshotDir . '/admin_*.png');
        if (empty($screenshots)) {
            return;
        }

        sort($screenshots);

        // Build ordered list for lightbox navigation & group by controller
        $allImages = [];
        $grouped   = [];
        foreach ($screenshots as $path) {
            $name = pathinfo($path, PATHINFO_FILENAME);
            if (preg_match('/^admin_(.+)_(grid|table)$/', $name, $m)) {
                $file = basename($path);
                $label = ucfirst($m[1]) . ' — ' . ucfirst($m[2]) . ' layout';
                $allImages[] = ['src' => $file, 'label' => $label];
                $grouped[$m[1]][$m[2]] = ['file' => $file, 'index' => count($allImages) - 1];
            }
        }

        $generated  = date('Y-m-d H:i:s');
        $total      = count($grouped);
        $totalImgs  = count($allImages);

        // Build JS image array
        $jsImages = json_encode(array_values($allImages));

        // Build table rows
        $rows = '';
        foreach ($grouped as $controller => $views) {
            $makeCell = function($view) use ($controller) {
                if (!isset($view)) return '<span class="missing">—</span>';
                return '<img src="' . $view['file'] . '" alt="' . $controller . '" loading="lazy" data-index="' . $view['index'] . '" class="thumb">';
            };
            $gridCell  = isset($views['grid'])  ? $makeCell($views['grid'])  : '<span class="missing">—</span>';
            $tableCell = isset($views['table']) ? $makeCell($views['table']) : '<span class="missing">—</span>';
            $rows .= <<<HTML
            <tr>
                <td class="ctrl"><span class="ctrl-badge">{$controller}</span></td>
                <td class="shot">{$gridCell}</td>
                <td class="shot">{$tableCell}</td>
            </tr>
HTML;
        }

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>HashtagCMS — Dusk Report</title>
<style>
  :root {
    --bg:#0d0f1a; --card:#141727; --surface:#1c2036;
    --accent:#7c6ff7; --accent2:#a78bfa;
    --text:#e2e8f0; --muted:#64748b; --border:#252a42;
    --green:#10b981; --shadow:0 8px 32px rgba(0,0,0,.5);
  }
  *{box-sizing:border-box;margin:0;padding:0}
  html{scroll-behavior:smooth}
  body{background:var(--bg);color:var(--text);font-family:'Segoe UI',system-ui,sans-serif;min-height:100vh}

  /* ── Header ── */
  header{background:var(--card);border-bottom:1px solid var(--border);padding:2rem;text-align:center;
         position:sticky;top:0;z-index:50;backdrop-filter:blur(8px)}
  header h1{font-size:1.8rem;background:linear-gradient(135deg,var(--accent),var(--accent2));
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;letter-spacing:-.5px}
  header p{color:var(--muted);margin-top:.3rem;font-size:.85rem}
  .badges{display:flex;gap:.5rem;justify-content:center;margin-top:.6rem;flex-wrap:wrap}
  .badge{display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .75rem;border-radius:999px;
         font-size:.75rem;font-weight:600;letter-spacing:.04em}
  .badge-purple{background:rgba(124,111,247,.2);color:var(--accent2);border:1px solid rgba(124,111,247,.3)}
  .badge-green{background:rgba(16,185,129,.15);color:var(--green);border:1px solid rgba(16,185,129,.25)}

  /* ── Table ── */
  .wrap{padding:1.5rem 2rem 4rem}
  table{width:100%;border-collapse:collapse;border-radius:12px;box-shadow:var(--shadow)}
  thead{background:var(--surface)}
  th{padding:.85rem 1.2rem;text-align:left;font-size:.75rem;text-transform:uppercase;letter-spacing:.08em;
     color:var(--accent2);border-bottom:2px solid var(--border);position:sticky;top:82px;
     background:var(--surface);z-index:40}
  td{padding:.7rem 1.2rem;border-bottom:1px solid var(--border);vertical-align:middle}
  tr:last-child td{border-bottom:none}
  tr:hover td{background:rgba(124,111,247,.04);transition:background .15s}

  .ctrl{width:140px}
  .ctrl-badge{background:rgba(124,111,247,.15);color:var(--accent2);border:1px solid rgba(124,111,247,.25);
              padding:.2rem .6rem;border-radius:6px;font-size:.8rem;font-weight:600;white-space:nowrap}
  .shot{width:calc(50% - 70px)}
  .missing{color:var(--muted);font-style:italic;font-size:.8rem}

  /* ── Thumbnails ── */
  .thumb{width:100%;max-height:220px;object-fit:cover;object-position:top;border-radius:8px;
         border:1px solid var(--border);cursor:zoom-in;transition:transform .2s,box-shadow .2s,border-color .2s;
         display:block}
  .thumb:hover{transform:scale(1.02);box-shadow:0 6px 24px rgba(124,111,247,.4);border-color:var(--accent)}

  /* ── Lightbox ── */
  #lb{display:none;position:fixed;inset:0;background:rgba(7,9,20,.96);z-index:999;
      flex-direction:column;align-items:center;justify-content:center;padding:1rem;
      animation:fadeIn .18s ease}
  #lb.open{display:flex}
  @keyframes fadeIn{from{opacity:0}to{opacity:1}}

  #lb-img-wrap{position:relative;display:flex;align-items:center;justify-content:center;
               width:100%;max-width:1400px;flex:1;min-height:0}
  #lb-img{max-width:100%;max-height:calc(100vh - 120px);border-radius:10px;
          border:1px solid var(--border);box-shadow:var(--shadow);
          transition:opacity .18s ease;object-fit:contain}
  #lb-img.fade{opacity:0}

  /* Nav arrows */
  .lb-arrow{position:absolute;top:50%;transform:translateY(-50%);
            background:rgba(124,111,247,.15);border:1px solid rgba(124,111,247,.3);
            color:#fff;width:48px;height:48px;border-radius:50%;font-size:1.4rem;
            display:flex;align-items:center;justify-content:center;cursor:pointer;
            transition:background .15s,transform .15s;user-select:none;z-index:10}
  .lb-arrow:hover{background:rgba(124,111,247,.45);transform:translateY(-50%) scale(1.1)}
  #lb-prev{left:-64px}
  #lb-next{right:-64px}
  @media(max-width:900px){
    #lb-prev{left:4px} #lb-next{right:4px}
    .lb-arrow{width:38px;height:38px;font-size:1.1rem}
  }

  /* Bottom bar */
  #lb-bar{display:flex;align-items:center;justify-content:space-between;width:100%;max-width:1400px;
          padding:.6rem 0;flex-shrink:0}
  #lb-label{color:var(--text);font-size:.9rem;font-weight:500;flex:1;padding-left:.5rem}
  #lb-counter{color:var(--muted);font-size:.8rem;font-variant-numeric:tabular-nums}
  #lb-close-btn{background:rgba(255,255,255,.08);border:1px solid var(--border);color:var(--text);
                width:36px;height:36px;border-radius:8px;cursor:pointer;font-size:1.1rem;
                display:flex;align-items:center;justify-content:center;margin-left:1rem;
                transition:background .15s}
  #lb-close-btn:hover{background:rgba(255,80,80,.3);border-color:rgba(255,80,80,.5)}

  /* Dots */
  #lb-dots{display:flex;gap:4px;justify-content:center;padding:.4rem 0;flex-wrap:wrap;max-width:1400px;width:100%}
  .dot{width:6px;height:6px;border-radius:50%;background:var(--border);cursor:pointer;transition:background .15s,transform .15s}
  .dot.active{background:var(--accent);transform:scale(1.5)}

  footer{text-align:center;padding:1.5rem;color:var(--muted);font-size:.75rem;border-top:1px solid var(--border)}
</style>
</head>
<body>

<header>
  <h1>🚀 HashtagCMS Admin Panel</h1>
  <p>Dusk Screenshot Report &mdash; Generated {$generated}</p>
  <div class="badges">
    <span class="badge badge-purple">📊 {$total} controllers</span>
    <span class="badge badge-green">📸 {$totalImgs} screenshots</span>
    <span class="badge badge-purple">🟢 SQLite only</span>
  </div>
</header>

<div class="wrap">
<table>
  <thead>
    <tr>
      <th>Controller</th>
      <th>Grid Layout</th>
      <th>Table / List Layout</th>
    </tr>
  </thead>
  <tbody>
    {$rows}
  </tbody>
</table>
</div>

<footer>HashtagCMS Dusk Test Suite &mdash; Click any screenshot to open lightbox &mdash; Use ← → keys or buttons to navigate</footer>

<!-- ── Lightbox ── -->
<div id="lb">
  <div id="lb-bar">
    <span id="lb-label"></span>
    <span id="lb-counter"></span>
    <button id="lb-close-btn" title="Close (Esc)">✕</button>
  </div>

  <div id="lb-img-wrap">
    <button class="lb-arrow" id="lb-prev" title="Previous (←)">&#8592;</button>
    <img id="lb-img" src="" alt="">
    <button class="lb-arrow" id="lb-next" title="Next (→)">&#8594;</button>
  </div>

  <div id="lb-dots"></div>
</div>

<script>
  const IMAGES = {$jsImages};
  let current = 0;
  const lb      = document.getElementById('lb');
  const lbImg   = document.getElementById('lb-img');
  const lbLabel = document.getElementById('lb-label');
  const lbCnt   = document.getElementById('lb-counter');
  const lbDots  = document.getElementById('lb-dots');

  // Build dots
  IMAGES.forEach((_, i) => {
    const d = document.createElement('span');
    d.className = 'dot';
    d.addEventListener('click', () => goTo(i));
    lbDots.appendChild(d);
  });
  function dots() {
    document.querySelectorAll('.dot').forEach((d, i) => d.classList.toggle('active', i === current));
  }

  function goTo(idx, dir = 0) {
    if (idx < 0) idx = IMAGES.length - 1;
    if (idx >= IMAGES.length) idx = 0;
    current = idx;
    lbImg.classList.add('fade');
    setTimeout(() => {
      lbImg.src   = IMAGES[current].src;
      lbImg.alt   = IMAGES[current].label;
      lbLabel.textContent  = IMAGES[current].label;
      lbCnt.textContent    = (current + 1) + ' / ' + IMAGES.length;
      lbImg.classList.remove('fade');
      dots();
    }, 160);
  }

  function open(idx) {
    current = idx;
    lb.classList.add('open');
    document.body.style.overflow = 'hidden';
    lbImg.src  = IMAGES[current].src;
    lbImg.alt  = IMAGES[current].label;
    lbLabel.textContent = IMAGES[current].label;
    lbCnt.textContent   = (current + 1) + ' / ' + IMAGES.length;
    dots();
  }

  function close() {
    lb.classList.remove('open');
    document.body.style.overflow = '';
  }

  // Attach to thumbnails
  document.querySelectorAll('.thumb').forEach(img => {
    img.addEventListener('click', () => open(+img.dataset.index));
  });

  document.getElementById('lb-prev').addEventListener('click', () => goTo(current - 1));
  document.getElementById('lb-next').addEventListener('click', () => goTo(current + 1));
  document.getElementById('lb-close-btn').addEventListener('click', close);

  // Click backdrop to close
  lb.addEventListener('click', e => { if (e.target === lb) close(); });

  // Keyboard navigation
  document.addEventListener('keydown', e => {
    if (!lb.classList.contains('open')) return;
    if (e.key === 'ArrowRight') goTo(current + 1);
    if (e.key === 'ArrowLeft')  goTo(current - 1);
    if (e.key === 'Escape')    close();
  });

  // Touch / swipe support
  let touchX = null;
  lb.addEventListener('touchstart', e => { touchX = e.touches[0].clientX; }, {passive:true});
  lb.addEventListener('touchend',   e => {
    if (touchX === null) return;
    const dx = e.changedTouches[0].clientX - touchX;
    if (Math.abs(dx) > 50) dx < 0 ? goTo(current + 1) : goTo(current - 1);
    touchX = null;
  });
</script>
</body>
</html>
HTML;

        file_put_contents($reportFile, $html);
        fwrite(STDOUT, "\n  📊 Report: tests/Browser/screenshots/report.html\n");
    }


    /**
     * Data provider for Admin Controllers.
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
     * Test admin controller and take screenshot.
     */
    #[DataProvider('adminControllerProvider')]
    public function test_admin_controller_screenshot($controller)
    {
        $this->browse(function (Browser $browser) use ($controller) {
            $adminPath = $this->getAdminPath();
            $user = User::where('user_type', 'Staff')->first();

            if (!$user) {
                $this->fail("❌ ERROR: Staff user not found in the database.");
            }

            // Screenshot: Grid layout
            $screenshotGrid = "admin_{$controller}_grid";
            $browser->loginAs($user)
                    ->visit("/{$adminPath}/{$controller}?layout=grid")
                    ->waitFor('.js_left_panel', 10)
                    ->assertPresent('.js_left_panel')   // ← counts as a real PHPUnit assertion
                    ->screenshot($screenshotGrid);
            fwrite(STDOUT, "\n  📸 Grid:  tests/Browser/screenshots/{$screenshotGrid}.png\n");

            // Screenshot: Table/List layout
            $screenshotTable = "admin_{$controller}_table";
            $browser->visit("/{$adminPath}/{$controller}?layout=table")
                    ->waitFor('.js_left_panel', 10)
                    ->assertPresent('.js_left_panel')   // ← counts as a real PHPUnit assertion
                    ->screenshot($screenshotTable);
            fwrite(STDOUT, "  📸 Table: tests/Browser/screenshots/{$screenshotTable}.png\n");
        });
    }
}
