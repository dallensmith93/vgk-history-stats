<?php // public/index.php ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>VGK History Stats</title>

  <!-- Fonts (Teko for headings) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Teko:wght@400;600&display=swap" rel="stylesheet">

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- jQuery + Chart.js -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

  <style>
    :root{
      --vgk-gold:#B4975A; --vgk-steel:#333F48; --vgk-red:#C8102E;

      /* Light theme – strong legibility */
      --glass-bg: rgba(255,255,255,0.94);
      --glass-bd: rgba(0,0,0,0.2);
      --glass-shadow: 0 10px 25px rgba(0,0,0,.10);

      --tick-color: rgba(0,0,0,0.9);
      --grid-color: rgba(0,0,0,0.2);
      --table-stripe: rgba(0,0,0,.05);
      --hover-row: rgba(0,0,0,.06);
      --hover-row-strong: rgba(0,0,0,.1);

      /* Button palette */
      --btn-fg: #0f1113;
      --btn-bg: rgba(255,255,255,0.85);
      --btn-bd: rgba(0,0,0,0.18);
      --btn-hover: rgba(0,0,0,0.06);
      --btn-active: rgba(0,0,0,0.08);

      /* Shimmer colors */
      --shim-a: rgba(0,0,0,.06);
      --shim-b: rgba(0,0,0,.14);
      --shim-c: rgba(0,0,0,.06);
    }
    [data-bs-theme="dark"]{
      --glass-bg: rgba(20,22,26,0.88);
      --glass-bd: rgba(255,255,255,0.12);
      --glass-shadow: 0 10px 25px rgba(0,0,0,.55);

      --tick-color: rgba(255,255,255,0.92);
      --grid-color: rgba(255,255,255,0.22);
      --table-stripe: rgba(255,255,255,.06);
      --hover-row: rgba(255,255,255,.08);
      --hover-row-strong: rgba(255,255,255,.12);

      --btn-fg: #f2f4f7;
      --btn-bg: rgba(255,255,255,0.06);
      --btn-bd: rgba(255,255,255,0.14);
      --btn-hover: rgba(255,255,255,0.12);
      --btn-active: rgba(255,255,255,0.18);

      --shim-a: rgba(255,255,255,.08);
      --shim-b: rgba(255,255,255,.22);
      --shim-c: rgba(255,255,255,.08);
      color-scheme: dark;
    }

    /* Athletic headings */
    h1,h2,h3,.h1,.h2,.h3{ font-family:"Teko",system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif; letter-spacing:.3px; }

    /* Backgrounds */
    body{
      background:
        radial-gradient(1200px 600px at -10% -20%, rgba(180,151,90,.18), transparent 60%),
        radial-gradient(1000px 500px at 110% 0%, rgba(51,63,72,.22), transparent 55%),
        linear-gradient(180deg, rgba(0,0,0,.02), transparent);
    }
    [data-bs-theme="dark"] body{
      background:
        radial-gradient(1200px 600px at -10% -20%, rgba(180,151,90,.08), transparent 60%),
        radial-gradient(1000px 500px at 110% 0%, rgba(51,63,72,.5), transparent 55%),
        linear-gradient(180deg, rgba(0,0,0,.6), rgba(0,0,0,.65));
    }

    /* Glassy cards */
    .card{
      background: var(--glass-bg) !important;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border: 1px solid var(--glass-bd) !important;
      box-shadow: var(--glass-shadow);
      border-radius: 1rem;
    }
    .p-3.border.rounded{ text-wrap:balance; border-radius:.8rem !important; border-color:var(--glass-bd) !important; }

    /* UNIVERSAL VGK BUTTONS */
    .btn-vgk{
      --fg: var(--btn-fg); --bg: var(--btn-bg); --bd: var(--btn-bd);
      color: var(--fg) !important;
      background: var(--bg) !important;
      border: 1px solid var(--bd) !important;
      border-radius: 999px !important;
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      transition: background-color .15s ease, box-shadow .15s ease, border-color .15s ease, transform .04s ease;
    }
    .btn-vgk:hover{ background: var(--btn-hover) !important; border-color: var(--btn-bd) !important; color: var(--fg) !important; }
    .btn-vgk:active{ background: var(--btn-active) !important; transform: translateY(0); }
    .btn-vgk.shimmer{ position: relative; overflow: hidden; }
    .btn-vgk.shimmer::after{
      content:""; position:absolute; inset:0;
      background: linear-gradient(110deg, transparent 0%, rgba(255,255,255,.18) 35%, transparent 70%);
      background-size: 200% 100%;
      animation: btnsheen 1.8s ease-in-out infinite;
      pointer-events:none;
    }
    @keyframes btnsheen{ 0%{background-position: 200% 0} 100%{background-position: -200% 0} }

    /* FILTER PILLS — colored when active */
    .filter-bar .btn-vgk{ padding-inline: .85rem; }
    .filter-playoffs.active{
      background:#198754 !important; border-color:#157347 !important; color:#fff !important;
      box-shadow:0 6px 14px rgba(25,135,84,.35);
    }
    .filter-scf.active{
      background:#6c757d !important; border-color:#5c636a !important; color:#fff !important;
      box-shadow:0 6px 14px rgba(108,117,125,.35);
    }
    .filter-champs.active{
      background: linear-gradient(180deg, rgba(180,151,90,1), rgba(180,151,90,.92)) !important;
      border-color:#9c8450 !important; color:#111 !important;
      box-shadow:0 6px 14px rgba(180,151,90,.45);
    }
    .filter-reset.active{
      background:#f1f3f5 !important; border-color:#d0d4d8 !important; color:#111 !important;
      box-shadow:0 6px 14px rgba(0,0,0,.1);
    }
    [data-bs-theme="dark"] .filter-reset.active{
      background:#2b2f33 !important; border-color:#3a3f44 !important; color:#eee !important;
      box-shadow:0 6px 14px rgba(0,0,0,.35);
    }

    /* Badges */
    .badge-yes{ background: var(--vgk-gold); color:#1b1b1b; }
    .badge-no{ background: #8b8f94; }

    /* Table polish + safe hover */
    #seasonsTable{ --bs-table-striped-bg: var(--table-stripe); }
    #seasonsTable thead th{ border-bottom: 1px solid var(--glass-bd) !important; font-weight: 600; }
    [data-bs-theme="dark"] .table td, [data-bs-theme="dark"] .table th { border-color: rgba(255,255,255,.14); }
    #seasonsTable tbody tr{ transition: background-color .12s ease; }
    #seasonsTable tbody tr:hover{ background: var(--hover-row); }
    #seasonsTable tbody tr:active{ background: var(--hover-row-strong); }

    /* Accordion edges */
    .accordion .accordion-item{ border-radius:.75rem; overflow:hidden; }
    .accordion-button{ background:transparent; }
    .accordion-button:not(.collapsed){ background: rgba(180,151,90,.12); }
    [data-bs-theme="dark"] .accordion-button:not(.collapsed){ background: rgba(180,151,90,.18); }

    /* Chart container */
    #chartWrap{ max-width: 980px; margin: 0 auto; }
    #playoffChart { max-height: 220px; }

    /* SKELETON / SHIMMER */
    .skeleton{
      position:relative; overflow:hidden; border-radius:.6rem;
      background:linear-gradient(90deg, var(--shim-a), var(--shim-b), var(--shim-c));
      background-size:300% 100%; animation:sheen 1.25s ease-in-out infinite;
    }
    @keyframes sheen{ 0%{background-position:200% 0}100%{background-position:-200% 0} }

    .metric { font-variant-numeric: tabular-nums; }
    .sortable { cursor: pointer; user-select: none; }
    .sort-indicator { font-size: 0.8em; opacity: 0.7; margin-left: .25rem; }

    /* TOP LOADING BAR (with shimmer) */
    #topLoader{
      position: fixed; top: 0; left: 0; width: 100%; height: 3px; z-index: 2000;
      display: none; background: linear-gradient(90deg, var(--shim-a), var(--shim-b), var(--shim-c));
      background-size: 200% 100%; animation: topline 1.1s linear infinite;
      box-shadow: 0 2px 8px rgba(0,0,0,.15);
    }
    @keyframes topline{ 0%{background-position: 200% 0} 100%{background-position: -200% 0} }

    /* BIGGER SHIMMERY THEME SWITCH */
    .vgk-switch .form-check-input{
      width: 3.8rem;           /* larger track */
      height: 2rem;
      cursor: pointer;
      background: #adb5bd;
      border: none;
      position: relative;
      transition: background-color .2s, box-shadow .2s;
      box-shadow: inset 0 0 0 2px rgba(0,0,0,.1);
      border-radius: 2rem;
      overflow: hidden;        /* ensure shimmer stays inside */
    }
    .vgk-switch .form-check-input:focus{ box-shadow:0 0 0 .2rem rgba(180,151,90,.38); }
    .vgk-switch .form-check-input:checked{ background: var(--vgk-gold); }

    /* knob */
    .vgk-switch .form-check-input::before{
      content:"";
      position:absolute; top:50%; left:.3rem;
      width:1.6rem; height:1.6rem; transform:translateY(-50%);
      border-radius:50%; background:#fff;
      transition:left .2s;
      box-shadow:0 2px 6px rgba(0,0,0,.25);
      z-index:2;
    }
    .vgk-switch .form-check-input:checked::before{ left: 1.9rem; } /* 3.8 - .3 - 1.6 = 1.9 */

    /* shimmer over track */
    .vgk-switch .form-check-input::after{
      content:"";
      position:absolute; inset:0; border-radius:inherit;
      background: linear-gradient(110deg, transparent 0%, rgba(255,255,255,.28) 40%, transparent 70%);
      background-size: 220% 100%;
      animation: btnsheen 2.2s ease-in-out infinite;
      z-index:1; pointer-events:none;
    }
    .vgk-switch .label{ font-size:1rem; margin-left:.6rem; user-select:none; }
  </style>
</head>
<body class="bg-light">
  <!-- top shimmer loading bar -->
  <div id="topLoader"></div>

  <div class="container py-4">

    <!-- Header -->
    <header class="mb-4 d-flex align-items-center justify-content-between gap-3">
      <div class="d-flex align-items-center gap-3">
        <img src="Vegas_Golden_Knights_logo.svg.png"
             alt="Vegas Golden Knights Logo" style="height:60px; width:auto;">
        <div>
          <h1 class="h3 mb-1">Vegas Golden Knights — History Stats</h1>
          <p class="text-muted mb-0">Playoffs, Stanley Cup Final appearances, and championship seasons.</p>
        </div>
      </div>

      <div class="vgk-switch form-check form-switch d-flex align-items-center">
        <input class="form-check-input" type="checkbox" role="switch" id="themeToggle" aria-label="Toggle dark mode">
        <label for="themeToggle" class="label text-muted"><span id="themeLabel">Dark</span> Mode</label>
      </div>
    </header>

    <!-- Summary -->
    <section class="mb-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h2 class="h5 mb-0">Summary</h2>
            <div class="d-flex flex-wrap gap-2">
              <a href="../api/export_seasons_csv.php" class="btn btn-sm btn-vgk shimmer"><i class="bi bi-filetype-csv me-1"></i>Download Seasons CSV</a>
              <a href="../api/export_scf_csv.php" class="btn btn-sm btn-vgk shimmer"><i class="bi bi-filetype-csv me-1"></i>Download SCF CSV</a>
              <button id="downloadViewCsv" class="btn btn-sm btn-vgk shimmer"><i class="bi bi-download me-1"></i>Current View CSV</button>
              <button class="btn btn-sm btn-vgk shimmer" data-bs-toggle="modal" data-bs-target="#aboutModal"><i class="bi bi-info-circle me-1"></i>About Data</button>
              <button id="shareView" class="btn btn-sm btn-vgk shimmer"><i class="bi bi-link-45deg me-1"></i>Share Current View</button>
            </div>
          </div>

          <!-- Summary skeleton -->
          <div id="summarySkeleton" class="row g-3 mt-2">
            <div class="col-6 col-md-3"><div class="skeleton" style="height:86px;"></div></div>
            <div class="col-6 col-md-3"><div class="skeleton" style="height:86px;"></div></div>
            <div class="col-6 col-md-3"><div class="skeleton" style="height:86px;"></div></div>
            <div class="col-6 col-md-3"><div class="skeleton" style="height:86px;"></div></div>
          </div>

          <div id="summary" class="row g-3 mt-2" style="display:none;">
            <div class="col-6 col-md-3">
              <div class="p-3 border rounded text-center">
                <div class="small text-muted">Total Seasons</div>
                <div id="totalSeasons" class="fs-4 fw-bold metric">–</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="p-3 border rounded text-center">
                <div class="small text-muted">Playoff Appearances</div>
                <div id="playoffCount" class="fs-4 fw-bold metric">–</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="p-3 border rounded text-center">
                <div class="small text-muted">SCF Appearances</div>
                <div id="scfCount" class="fs-4 fw-bold metric">–</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="p-3 border rounded text-center">
                <div class="small text-muted">Stanley Cups Won</div>
                <div id="cupCount" class="fs-4 fw-bold metric">–</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Reports + Chart -->
    <section class="mb-4">
      <div class="card">
        <div class="card-body">
          <h2 class="h5 mb-3">Reports</h2>

          <div class="row g-3">
            <div class="col-12 col-xl-6">
              <div class="p-3 border rounded h-100">
                <div class="d-flex justify-content-between mb-2">
                  <div><strong>Playoff Seasons</strong></div>
                  <div><span id="playoffPct" class="metric">–%</span> (<span id="playoffCountRpt">–</span>/<span id="totalSeasonsRpt">–</span>)</div>
                </div>
                <div class="progress" style="height:10px;">
                  <div id="playoffBar" class="progress-bar" role="progressbar" style="width:0%;" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            </div>

            <div class="col-12 col-xl-6">
              <div class="p-3 border rounded h-100">
                <div class="row">
                  <div class="col-6">
                    <div class="small text-muted">Longest Playoff Streak</div>
                    <div id="longestStreak" class="fs-5 fw-semibold metric">–</div>
                  </div>
                  <div class="col-6">
                    <div class="small text-muted">Current Playoff Streak</div>
                    <div id="currentStreak" class="fs-5 fw-semibold metric">–</div>
                  </div>
                </div>
                <div class="mt-2 small text-muted" id="longestStreakSpan"></div>
              </div>
            </div>

            <div class="col-12 col-xl-6">
              <div class="p-3 border rounded h-100">
                <div class="small text-muted">Average Division Finish</div>
                <div id="avgDivision" class="fs-5 fw-semibold metric">–</div>
                <div class="text-muted small mt-1">Ignores seasons with unknown/blank finish.</div>
              </div>
            </div>

            <div class="col-12 col-xl-6">
              <div class="p-3 border rounded h-100">
                <div class="small text-muted mb-2">SCF Seasons</div>
                <div id="scfList" class="fw-semibold">–</div>
              </div>
            </div>

            <!-- Chart skeleton then real -->
            <div class="col-12">
              <div id="chartWrap" class="p-3 border rounded h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div><strong>Playoffs, SCF & Division Finish by Season</strong></div>
                  <div class="small text-muted">Bars: Playoffs/SCF (1=yes). Line: Division (1=best)</div>
                </div>
                <div id="chartSkeleton" class="skeleton" style="height:160px;border-radius:.6rem;"></div>
                <canvas id="playoffChart" height="160" style="display:none;"></canvas>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- Seasons table + search + filters -->
    <section class="mb-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <h2 class="h5 mb-0">All Seasons</h2>

            <div class="d-flex align-items-center gap-2">
              <!-- Search -->
              <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input id="seasonSearch" type="text" class="form-control" placeholder="e.g., 2022-23 or 1st">
                <button id="clearSearch" class="btn btn-vgk btn-sm shimmer" type="button" title="Clear"><i class="bi bi-x-lg"></i></button>
              </div>

              <!-- Filters (colored pills) -->
              <div class="btn-group btn-group-sm filter-bar" role="group" aria-label="Season filters">
                <button id="filter-playoffs" class="btn btn-vgk shimmer filter-playoffs"><i class="bi bi-trophy me-1"></i>Playoffs</button>
                <button id="filter-scf" class="btn btn-vgk shimmer filter-scf"><i class="bi bi-award me-1"></i>SCF</button>
                <button id="filter-champs" class="btn btn-vgk shimmer filter-champs"><i class="bi bi-star me-1"></i>Champions</button>
                <button id="filter-reset" class="btn btn-vgk shimmer filter-reset"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</button>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-sm align-middle table-striped" id="seasonsTable">
              <thead>
                <tr>
                  <th class="sortable" data-key="season_label">Season <span class="sort-indicator" id="ind-season_label"></span></th>
                  <th class="sortable" data-key="division_finish">Division Finish <span class="sort-indicator" id="ind-division_finish"></span></th>
                  <th class="sortable" data-key="made_playoffs">Playoffs <span class="sort-indicator" id="ind-made_playoffs"></span></th>
                  <th class="sortable" data-key="made_scf">SCF <span class="sort-indicator" id="ind-made_scf"></span></th>
                  <th class="sortable" data-key="champions">Champions <span class="sort-indicator" id="ind-champions"></span></th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

          <!-- Table skeleton -->
          <div id="skeletonRows" class="mt-2">
            <div class="skeleton mb-2" style="height:34px;"></div>
            <div class="skeleton mb-2" style="height:34px;"></div>
            <div class="skeleton mb-2" style="height:34px;"></div>
          </div>

          <!-- Details panel -->
          <div id="details" class="mt-4" style="display:none;">
            <h3 class="h6 mb-3">Playoff Series: <span id="detailsSeason" class="fw-semibold"></span></h3>
            <div id="seriesContainer" class="accordion"></div>
          </div>

          <!-- Loading / error -->
          <div id="loading" class="text-muted small mt-2" style="display:none;">Loading…</div>
          <div id="error" class="text-danger small mt-2" style="display:none;"></div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="text-center small mt-5">
      <div class="text-muted">
        <a href="#" class="link-secondary" data-bs-toggle="modal" data-bs-target="#aboutModal">About</a>
        &nbsp;•&nbsp;
        <a href="#" class="link-secondary" id="repoLink">Repository</a>
        &nbsp;•&nbsp;
        <a href="#" class="link-secondary" id="contactLink">Contact</a>
      </div>
      <div class="text-muted mt-2">Data current through 2024–25.</div>
    </footer>
  </div>

  <!-- About the Data Modal -->
  <div class="modal fade" id="aboutModal" tabindex="-1" aria-labelledby="aboutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="aboutModalLabel">About the Data</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="mb-2">
            This app summarizes the Vegas Golden Knights’ franchise seasons, playoff appearances,
            Stanley Cup Final appearances, and championships.
          </p>
          <ul class="mb-2">
            <li>Coverage: 2017–18 through 2024–25.</li>
            <li>Division finish shows regular-season divisional placement where available.</li>
            <li>Playoff series details appear for seasons with playoff participation.</li>
          </ul>
          <p class="mb-0 text-muted">
            Notes: 2017–18 reached the SCF; 2022–23 won the Stanley Cup.
            Data is stored in your local MySQL database (<code>vgk_stats</code>) and surfaced via simple PHP APIs.
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-vgk btn-sm shimmer" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Toast container -->
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index:1080">
    <div id="vgkToast" class="toast align-items-center text-bg-dark border-0" role="status" aria-live="polite" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <i class="bi bi-check2-circle me-2"></i><span id="toastText">Done.</span>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>

<script>
let seasonsCache = [];
let currentFilter = 'all'; // 'all' | 'playoffs' | 'scf' | 'champs'
let searchTerm = '';
let sortKey = 'season_label';
let sortDir = 'asc'; // 'asc' | 'desc'
let playoffChart = null;

// ---------- Theme ----------
function applyTheme(theme) {
  document.documentElement.setAttribute('data-bs-theme', theme);
  const label = document.getElementById('themeLabel');
  const toggle = document.getElementById('themeToggle');
  if (label) label.textContent = (theme === 'dark') ? 'Light' : 'Dark';
  if (toggle) toggle.checked = (theme === 'dark');
  setTimeout(() => renderChart(), 0);
}
// DEFAULT THEME = DARK
(function initTheme() {
  const saved = localStorage.getItem('vgk_theme') || 'dark';
  applyTheme(saved);
})();
document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.getElementById('themeToggle');
  if (toggle) {
    toggle.addEventListener('change', (e) => {
      const next = e.target.checked ? 'dark' : 'light';
      localStorage.setItem('vgk_theme', next);
      applyTheme(next);
    });
  }
});

// ---------- helpers ----------
function normalize(str) { return (str ?? '').toString().toLowerCase().trim(); }
function parseDivisionFinish(val) {
  if (!val) return null;
  const m = ('' + val).match(/^(\d+)/);
  return m ? parseInt(m[1], 10) : null;
}
function badge(val) {
  const yes = (val === 1 || val === true || val === '1' || val === 'Yes');
  return `<span class="badge ${yes ? 'badge-yes' : 'badge-no'}">${yes ? 'Yes' : 'No'}</span>`;
}
function showToast(message){
  $('#toastText').text(message || 'Done.');
  const el = document.getElementById('vgkToast');
  const toast = new bootstrap.Toast(el, { autohide: true, delay: 1800 });
  toast.show();
}

// ---------- URL state (shareable) ----------
function stateToParams() {
  const p = new URLSearchParams();
  if (currentFilter !== 'all') p.set('filter', currentFilter);
  if (searchTerm) p.set('search', searchTerm);
  if (sortKey !== 'season_label') p.set('sort', sortKey);
  if (sortDir !== 'asc') p.set('dir', sortDir);
  return p;
}
function applyParams(params) {
  const f = params.get('filter'); if (f) currentFilter = f;
  const s = params.get('search'); if (s) { searchTerm = s; $('#seasonSearch').val(s); }
  const k = params.get('sort');   if (k) sortKey = k;
  const d = params.get('dir');    if (d) sortDir = d;
  updateFilterButtons();
}
function updateURLFromState(replace=false) {
  const p = stateToParams();
  const url = p.toString() ? `${location.pathname}?${p.toString()}` : location.pathname;
  if (replace) history.replaceState(null, '', url); else history.pushState(null, '', url);
}
function copyShareURL() {
  const full = `${location.origin}${location.pathname}` + (stateToParams().toString() ? `?${stateToParams().toString()}` : '');
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(full).then(() => showToast('Shareable link copied!'));
  } else {
    const ta = document.createElement('textarea'); ta.value = full; document.body.appendChild(ta);
    ta.select(); document.execCommand('copy'); document.body.removeChild(ta);
    showToast('Shareable link copied!');
  }
}

// ---------- sorting ----------
function compare(a, b, key) {
  let av = a[key], bv = b[key];
  if (key === 'division_finish') { av = parseDivisionFinish(av); bv = parseDivisionFinish(bv); }
  if (key === 'made_playoffs' || key === 'made_scf' || key === 'champions') { av = +a[key]; bv = +b[key]; }
  if (av == null && bv != null) return -1;
  if (av != null && bv == null) return 1;
  if (av == null && bv == null) return 0;
  if (typeof av === 'string' && typeof bv === 'string') { av = av.toLowerCase(); bv = bv.toLowerCase(); }
  if (av < bv) return -1; if (av > bv) return 1; return 0;
}
function sortRows(rows) {
  const sorted = [...rows].sort((a, b) => compare(a, b, sortKey));
  if (sortDir === 'desc') sorted.reverse();
  return sorted;
}
function setSortIndicator() {
  $('.sort-indicator').text('');
  $('#ind-' + sortKey).text(sortDir === 'asc' ? '↑' : '↓');
}

// ---------- table render / filters / search ----------
function renderTable(rows) {
  const tbody = $('#seasonsTable tbody');
  tbody.empty();
  const sorted = sortRows(rows);
  setSortIndicator();
  sorted.forEach(r => {
    const tr = $('<tr/>').addClass('clickable-row').data('season', r.season_label);
    tr.append($('<td/>').html(`<strong>${r.season_label}</strong>`));
    tr.append($('<td/>').text(r.division_finish || '—'));
    tr.append($('<td/>').html(badge(r.made_playoffs)));
    tr.append($('<td/>').html(badge(r.made_scf)));
    tr.append($('<td/>').html(badge(r.champions)));
    tbody.append(tr);
  });
}
function getFilteredRows() {
  const term = normalize(searchTerm);
  let rows = seasonsCache;
  if (currentFilter === 'playoffs') rows = rows.filter(r => +r.made_playoffs === 1);
  if (currentFilter === 'scf')      rows = rows.filter(r => +r.made_scf === 1);
  if (currentFilter === 'champs')   rows = rows.filter(r => +r.champions === 1);
  if (term.length) {
    rows = rows.filter(r => {
      const hay = [
        normalize(r.season_label),
        normalize(r.division_finish),
        (+r.made_playoffs ? 'playoffs yes' : 'playoffs no'),
        (+r.made_scf ? 'scf yes' : 'scf no'),
        (+r.champions ? 'champions yes' : 'champions no')
      ].join(' ');
      return hay.includes(term);
    });
  }
  return sortRows(rows);
}
function applyFilters(push=true) {
  renderTable(getFilteredRows());
  updateFilterButtons();
  if (push) updateURLFromState(true);
}
function updateFilterButtons() {
  $('#filter-playoffs,#filter-scf,#filter-champs,#filter-reset').removeClass('active');
  if (currentFilter === 'playoffs') $('#filter-playoffs').addClass('active');
  else if (currentFilter === 'scf') $('#filter-scf').addClass('active');
  else if (currentFilter === 'champs') $('#filter-champs').addClass('active');
  else $('#filter-reset').addClass('active');
}

// ---------- API loads ----------
function loadSummary() {
  $.getJSON('../api/summary.php')
    .done(d => {
      $('#summarySkeleton').hide();
      $('#summary').show();
      $('#totalSeasons').text(d.total_seasons);
      $('#playoffCount').text(d.playoff_count);
      $('#scfCount').text(d.scf_count);
      $('#cupCount').text(d.cup_count);
    })
    .fail(() => $('#error').text('Failed to load summary.').show());
}
function loadSeasons(cb) {
  $.getJSON('../api/seasons.php')
    .done(d => {
      seasonsCache = d;
      applyFilters();           // render using current state
      computeAndRenderReports();
      renderChart();
      $('#skeletonRows').hide(); // hide table skeleton after data loads
      if (cb) cb();
    })
    .fail(() => $('#error').text('Failed to load seasons.').show());
}
function loadSeries(seasonLabel) {
  $('#loading').show(); $('#error').hide(); $('#details').hide();
  $('#seriesContainer').empty(); $('#detailsSeason').text(seasonLabel);
  $.getJSON('../api/series.php', { season_label: seasonLabel })
    .done(series => {
      $('#loading').hide();
      if (!series || !series.length) {
        $('#seriesContainer').html('<div class="text-muted">No series data for this season.</div>');
      } else {
        series.forEach((s, i) => {
          $('#seriesContainer').append(`
            <div class="accordion-item border rounded mb-2">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#round-${i}">
                  ${s.round_label}
                </button>
              </h2>
              <div id="round-${i}" class="accordion-collapse collapse">
                <div class="accordion-body">
                  <div><strong>Opponent:</strong> ${s.opponent}</div>
                  <div><strong>Result:</strong> ${s.result} (${s.outcome})</div>
                </div>
              </div>
            </div>`);
        });
      }
      $('#details').show();
    })
    .fail(() => { $('#loading').hide(); $('#error').text('Failed to load series for ' + seasonLabel).show(); });
}

// ---------- Reports ----------
function computeAndRenderReports() {
  if (!seasonsCache || !seasonsCache.length) return;
  const total = seasonsCache.length;
  const playoffCount = seasonsCache.filter(s => +s.made_playoffs === 1).length;
  const pct = total ? Math.round((playoffCount / total) * 100) : 0;
  $('#playoffPct').text(pct + '%');
  $('#playoffBar').css('width', pct + '%').attr('aria-valuenow', pct);
  $('#playoffCountRpt').text(playoffCount);
  $('#totalSeasonsRpt').text(total);

  const byYear = [...seasonsCache].sort((a,b) => (a.season_label > b.season_label) ? 1 : -1);
  let longest = 0, longestStart = null, longestEnd = null;
  let current = 0, run = 0, runStart = null;
  byYear.forEach((s) => {
    if (+s.made_playoffs === 1) {
      if (run === 0) runStart = s.season_label;
      run++;
      if (run > longest) { longest = run; longestStart = runStart; longestEnd = s.season_label; }
    } else { run = 0; runStart = null; }
  });
  const reversed = [...byYear].reverse();
  current = 0; for (const s of reversed) { if (+s.made_playoffs === 1) current++; else break; }
  $('#longestStreak').text(longest || 0);
  $('#currentStreak').text(current || 0);
  $('#longestStreakSpan').text(longest ? `From ${longestStart} to ${longestEnd}` : '—');

  const finishes = seasonsCache.map(s => parseDivisionFinish(s.division_finish)).filter(n => Number.isFinite(n));
  const avg = finishes.length ? (finishes.reduce((a,b)=>a+b,0) / finishes.length) : null;
  $('#avgDivision').text(avg ? avg.toFixed(2) : '—');

  const scfSeasons = seasonsCache.filter(s => +s.made_scf === 1).map(s => s.season_label);
  $('#scfList').text(scfSeasons.length ? scfSeasons.join(', ') : '—');
}

// ---------- Chart ----------
function renderChart() {
  const canvas = document.getElementById('playoffChart'); if (!canvas) return;
  const styles = getComputedStyle(document.documentElement);
  const gridColor = styles.getPropertyValue('--grid-color').trim();
  const tickColor = styles.getPropertyValue('--tick-color').trim();

  const rows = [...seasonsCache].sort((a,b) => (a.season_label > b.season_label) ? 1 : -1);
  const labels = rows.map(r => r.season_label);
  const playoffs = rows.map(r => +r.made_playoffs);
  const scf = rows.map(r => +r.made_scf);
  const divFinish = rows.map(r => {
    const n = parseDivisionFinish(r.division_finish);
    return Number.isFinite(n) ? n : NaN;
  });

  const gold = styles.getPropertyValue('--vgk-gold').trim() || '#B4975A';
  const steel = styles.getPropertyValue('--vgk-steel').trim() || '#333F48';
  const red = styles.getPropertyValue('--vgk-red').trim() || '#C8102E';

  if (playoffChart) playoffChart.destroy();

  // Hide shimmer, show canvas
  $('#chartSkeleton').hide();
  $(canvas).show();

  playoffChart = new Chart(canvas, {
    type: 'bar',
    data: {
      labels,
      datasets: [
        { type: 'bar', label: 'Playoffs', data: playoffs, backgroundColor: gold, borderColor: gold, borderWidth: 1, yAxisID: 'y' },
        { type: 'bar', label: 'SCF',      data: scf,      backgroundColor: steel, borderColor: steel, borderWidth: 1, yAxisID: 'y' },
        { type: 'line', label: 'Division Finish (lower is better)', data: divFinish, borderColor: red, backgroundColor: red, tension: 0.25, pointRadius: 2, pointHoverRadius: 3, spanGaps: true, yAxisID: 'yDivision' }
      ]
    },
    options: {
      animation: false,
      responsive: true,
      maintainAspectRatio: true,
      layout: { padding: { top: 6, right: 6, bottom: 0, left: 6 } },
      interaction: { mode: 'nearest', intersect: false },
      plugins: {
        legend: { position: 'top', labels: { color: tickColor } },
        tooltip: { mode: 'index', intersect: false }
      },
      scales: {
        y: { beginAtZero: true, max: 1, ticks: { stepSize: 1, color: tickColor }, grid: { color: gridColor } },
        yDivision: { position: 'right', reverse: true, suggestedMin: 8, suggestedMax: 1, ticks: { stepSize: 1, color: tickColor }, grid: { color: gridColor } },
        x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 12, color: tickColor }, grid: { color: gridColor } }
      }
    }
  });
}

// ---------- CSV ----------
function downloadCsvFromRows(rows) {
  const header = ['season_label','division_finish','made_playoffs','made_scf','champions'];
  const lines = [header.join(',')];
  rows.forEach(r => {
    const line = [
      `"${r.season_label}"`,
      `"${(r.division_finish ?? '').toString().replace(/"/g,'""')}"`,
      +r.made_playoffs,
      +r.made_scf,
      +r.champions
    ].join(',');
    lines.push(line);
  });
  const blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'vgk_current_view.csv';
  a.click();
  URL.revokeObjectURL(url);
}
function debounce(fn, wait = 200) { let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn.apply(null, args), wait); }; }

// ---------- init + global ajax loader ----------
$(function() {
  // Read URL state (if present) before loading
  applyParams(new URLSearchParams(location.search));

  // Global AJAX shimmer bar
  $(document).ajaxStart(() => { $('#topLoader').fadeIn(80); });
  $(document).ajaxStop(()  => { $('#topLoader').fadeOut(180); });

  loadSummary();
  loadSeasons();

  // Filters
  $('#filter-playoffs').on('click', () => { currentFilter = 'playoffs'; applyFilters(); });
  $('#filter-scf').on('click',      () => { currentFilter = 'scf';      applyFilters(); });
  $('#filter-champs').on('click',   () => { currentFilter = 'champs';   applyFilters(); });
  $('#filter-reset').on('click',    () => {
    currentFilter = 'all'; searchTerm = ''; $('#seasonSearch').val(''); applyFilters();
  });

  // Search
  const onSearch = debounce(() => { searchTerm = $('#seasonSearch').val() || ''; applyFilters(); }, 200);
  $('#seasonSearch').on('input', onSearch);
  $('#clearSearch').on('click', function(){ $('#seasonSearch').val(''); searchTerm = ''; applyFilters(); });

  // Sorting
  $(document).on('click', 'th.sortable', function() {
    const key = $(this).data('key');
    if (sortKey === key) { sortDir = (sortDir === 'asc') ? 'desc' : 'asc'; }
    else { sortKey = key; sortDir = 'asc'; }
    applyFilters();
  });

  // Row click → load series
  $(document).on('click', '#seasonsTable .clickable-row', function() {
    const season = $(this).data('season');
    loadSeries(season);
  });

  // Download current view + toast
  $('#downloadViewCsv').on('click', () => {
    const rows = getFilteredRows();
    downloadCsvFromRows(rows);
    showToast('CSV downloaded (current view).');
  });

  // Share current view
  $('#shareView').on('click', () => { copyShareURL(); });

  // Footer placeholders
  $('#repoLink').on('click', (e) => { e.preventDefault(); alert('Hook this up to your Git repo URL when ready.'); });
  $('#contactLink').on('click', (e) => { e.preventDefault(); alert('Swap this for your contact page or email link.'); });
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
