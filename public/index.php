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

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

  <style>
    :root {
      --vgk-gold: #B4975A;
      --vgk-steel: #333F48;
      --vgk-red: #C8102E;
    }
    .badge-yes { background-color: #198754; }   /* Bootstrap green-ish */
    .badge-no  { background-color: #6c757d; }   /* secondary */
    .clickable-row { cursor: pointer; }
    .sortable { cursor: pointer; user-select: none; }
    .sort-indicator { font-size: 0.8em; opacity: 0.6; margin-left: .25rem; }
    .metric { font-variant-numeric: tabular-nums; }

    /* Headings get the athletic vibe */
    h1, h2, h3, .display-*, .h1, .h2, .h3 {
      font-family: "Teko", system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      letter-spacing: .3px;
    }

    /* Keep chart compact to avoid layout jumps/scroll */
    #chartWrap { max-width: 980px; margin: 0 auto; }
    #playoffChart { max-height: 220px; }

    /* Dark-mode fine-tuning for table borders */
    [data-bs-theme="dark"] .table td,
    [data-bs-theme="dark"] .table th { border-color: rgba(255,255,255,.1); }

    /* Sleek VGK theme switch */
    .vgk-switch .form-check-input {
      width: 3.25rem;
      height: 1.6rem;
      cursor: pointer;
      background-color: #adb5bd;
      border: none;
      position: relative;
      transition: background-color .2s ease, box-shadow .2s ease;
      box-shadow: inset 0 0 0 2px rgba(0,0,0,.1);
    }
    .vgk-switch .form-check-input:focus {
      outline: none;
      box-shadow: 0 0 0 .2rem rgba(180,151,90,.25);
    }
    .vgk-switch .form-check-input:checked {
      background-color: var(--vgk-gold);
    }
    .vgk-switch .form-check-input::before {
      content: "";
      position: absolute;
      top: 50%;
      left: .25rem;
      width: 1.2rem;
      height: 1.2rem;
      transform: translateY(-50%);
      border-radius: 50%;
      background-color: #fff;
      transition: left .2s ease;
      box-shadow: 0 2px 6px rgba(0,0,0,.25);
    }
    .vgk-switch .form-check-input:checked::before {
      left: 1.8rem;
    }
    .vgk-switch .label {
      font-size: .9rem;
      margin-left: .5rem;
      user-select: none;
    }
  </style>
</head>
<body class="bg-light">
  <div class="container py-4">

    <!-- Header with VGK logo + Sleek Dark Mode Switch -->
    <header class="mb-4 d-flex align-items-center justify-content-between gap-3">
      <div class="d-flex align-items-center gap-3">
        <img src="Vegas_Golden_Knights_logo.svg.png"
             alt="Vegas Golden Knights Logo"
             style="height:60px; width:auto;">
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

    <!-- Summary + CSV -->
    <section class="mb-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h2 class="h5 mb-0">Summary</h2>
            <div class="d-flex gap-2">
              <a href="../api/export_seasons_csv.php" class="btn btn-sm btn-outline-primary">Download Seasons CSV</a>
              <a href="../api/export_scf_csv.php" class="btn btn-sm btn-outline-primary">Download SCF CSV</a>
              <button id="downloadViewCsv" class="btn btn-sm btn-outline-secondary">Download Current View</button>
              <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#aboutModal">About Data</button>
            </div>
          </div>
          <div id="summary" class="row g-3 mt-2">
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

    <!-- Reports + Fancy Chart -->
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
                <div class="progress" style="height: 10px;">
                  <div id="playoffBar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuemin="0" aria-valuemax="100"></div>
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

            <!-- Compact chart -->
            <div class="col-12">
              <div id="chartWrap" class="p-3 border rounded h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div><strong>Playoffs, SCF & Division Finish by Season</strong></div>
                  <div class="small text-muted">Bars: Playoffs/SCF (1=yes). Line: Division (1=best)</div>
                </div>
                <canvas id="playoffChart" height="160"></canvas>
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
                <span class="input-group-text">Search</span>
                <input id="seasonSearch" type="text" class="form-control" placeholder="e.g., 2022-23 or 1st">
                <button id="clearSearch" class="btn btn-outline-secondary" type="button" title="Clear">&times;</button>
              </div>

              <!-- Filters -->
              <div class="btn-group btn-group-sm" role="group" aria-label="Season filters">
                <button id="filter-playoffs" class="btn btn-outline-secondary">Playoffs</button>
                <button id="filter-scf" class="btn btn-outline-secondary">SCF</button>
                <button id="filter-champs" class="btn btn-outline-secondary">Champions</button>
                <button id="filter-reset" class="btn btn-outline-secondary">Reset</button>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-sm align-middle" id="seasonsTable">
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

    <!-- Footer with links -->
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
            Notes: 2017–18 (inaugural season) reached the SCF; 2022–23 won the Stanley Cup.
            Data is stored in your local MySQL database (<code>vgk_stats</code>) and surfaced via simple PHP APIs.
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        </div>
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

// ---------- Theme (Bootstrap 5.3 data-bs-theme + localStorage) ----------
function applyTheme(theme) {
  const root = document.documentElement;
  root.setAttribute('data-bs-theme', theme);
  const label = document.getElementById('themeLabel');
  const toggle = document.getElementById('themeToggle');
  if (label) label.textContent = (theme === 'dark') ? 'Light' : 'Dark';
  if (toggle) toggle.checked = (theme === 'dark');
  // Re-render chart so grid/labels adapt
  setTimeout(() => renderChart(), 0);
}
(function initTheme() {
  const saved = localStorage.getItem('vgk_theme') || 'light';
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
  return m ? parseInt(m[1], 10) : null; // '1st' -> 1, '3rd' -> 3
}

function badge(val) {
  const yes = (val === 1 || val === true || val === '1' || val === 'Yes');
  return `<span class="badge ${yes ? 'badge-yes' : 'badge-no'}">${yes ? 'Yes' : 'No'}</span>`;
}

// ---------- sorting ----------
function compare(a, b, key) {
  let av = a[key], bv = b[key];

  if (key === 'division_finish') {
    av = parseDivisionFinish(av);
    bv = parseDivisionFinish(bv);
  }
  if (key === 'made_playoffs' || key === 'made_scf' || key === 'champions') {
    av = +a[key];
    bv = +b[key];
  }
  if (av == null && bv != null) return -1;
  if (av != null && bv == null) return 1;
  if (av == null && bv == null) return 0;

  if (typeof av === 'string' && typeof bv === 'string') {
    av = av.toLowerCase(); bv = bv.toLowerCase();
  }
  if (av < bv) return -1;
  if (av > bv) return 1;
  return 0;
}

function sortRows(rows) {
  const sorted = [...rows].sort((a, b) => compare(a, b, sortKey));
  if (sortDir === 'desc') sorted.reverse();
  return sorted;
}

function setSortIndicator() {
  $('.sort-indicator').text('');
  const el = $('#ind-' + sortKey);
  el.text(sortDir === 'asc' ? '↑' : '↓');
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

function applyFilters() {
  const rows = getFilteredRows();
  renderTable(rows);
}

// ---------- API loads ----------
function loadSummary() {
  $.getJSON('../api/summary.php')
    .done(d => {
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
      applyFilters(); // render with search/filter/sort state
      computeAndRenderReports();
      renderChart(); // draw the chart
      if (cb) cb();
    })
    .fail(() => $('#error').text('Failed to load seasons.').show());
}

function loadSeries(seasonLabel) {
  $('#loading').show();
  $('#error').hide();
  $('#details').hide();
  $('#seriesContainer').empty();
  $('#detailsSeason').text(seasonLabel);

  $.getJSON('../api/series.php', { season_label: seasonLabel })
    .done(series => {
      $('#loading').hide();
      if (!series || !series.length) {
        $('#seriesContainer').html('<div class="text-muted">No series data for this season.</div>');
      } else {
        // Build an accordion of rounds
        series.forEach((s, i) => {
          const item = `
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
            </div>`;
          $('#seriesContainer').append(item);
        });
      }
      $('#details').show();
    })
    .fail(() => {
      $('#loading').hide();
      $('#error').text('Failed to load series for ' + seasonLabel).show();
    });
}

// ---------- Reports (client-side) ----------
function computeAndRenderReports() {
  if (!seasonsCache || !seasonsCache.length) return;

  const total = seasonsCache.length;
  const playoffCount = seasonsCache.filter(s => +s.made_playoffs === 1).length;
  const pct = total ? Math.round((playoffCount / total) * 100) : 0;

  $('#playoffPct').text(pct + '%');
  $('#playoffBar').css('width', pct + '%').attr('aria-valuenow', pct);
  $('#playoffCountRpt').text(playoffCount);
  $('#totalSeasonsRpt').text(total);

  // Longest and current playoff streaks
  const byYear = [...seasonsCache].sort((a,b) => (a.season_label > b.season_label) ? 1 : -1);
  let longest = 0, longestStart = null, longestEnd = null;
  let current = 0;
  let run = 0, runStart = null;

  byYear.forEach((s) => {
    if (+s.made_playoffs === 1) {
      if (run === 0) runStart = s.season_label;
      run++;
      if (run > longest) { longest = run; longestStart = runStart; longestEnd = s.season_label; }
    } else {
      run = 0;
      runStart = null;
    }
  });

  // Current streak = count from the most recent season backwards
  const reversed = [...byYear].reverse();
  current = 0;
  for (const s of reversed) {
    if (+s.made_playoffs === 1) current++;
    else break;
  }

  $('#longestStreak').text(longest || 0);
  $('#currentStreak').text(current || 0);
  $('#longestStreakSpan').text(longest ? `From ${longestStart} to ${longestEnd}` : '—');

  // Average division finish (skip blanks)
  const finishes = seasonsCache
    .map(s => parseDivisionFinish(s.division_finish))
    .filter(n => Number.isFinite(n));
  const avg = finishes.length ? (finishes.reduce((a,b)=>a+b,0) / finishes.length) : null;
  $('#avgDivision').text(avg ? avg.toFixed(2) : '—');

  // SCF list
  const scfSeasons = seasonsCache.filter(s => +s.made_scf === 1).map(s => s.season_label);
  $('#scfList').text(scfSeasons.length ? scfSeasons.join(', ') : '—');
}

// ---------- Chart (no animation; VGK colors; compact; theme-aware) ----------
function renderChart() {
  const ctx = document.getElementById('playoffChart');
  if (!ctx) return;

  const theme = document.documentElement.getAttribute('data-bs-theme') || 'light';
  const isDark = theme === 'dark';
  const styles = getComputedStyle(document.documentElement);
  const gridColor = isDark ? 'rgba(255,255,255,0.15)' : 'rgba(0,0,0,0.1)';
  const tickColor = isDark ? 'rgba(255,255,255,0.8)' : 'rgba(0,0,0,0.7)';

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

  if (playoffChart) { playoffChart.destroy(); }

  playoffChart = new Chart(ctx, {
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

// ---------- CSV (current view) ----------
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

// ---------- debounce ----------
function debounce(fn, wait = 200) {
  let t;
  return (...args) => {
    clearTimeout(t);
    t = setTimeout(() => fn.apply(null, args), wait);
  };
}

// ---------- init ----------
$(function() {
  loadSummary();
  loadSeasons();

  // Filters
  $('#filter-playoffs').on('click', () => { currentFilter = 'playoffs'; applyFilters(); });
  $('#filter-scf').on('click',      () => { currentFilter = 'scf';      applyFilters(); });
  $('#filter-champs').on('click',   () => { currentFilter = 'champs';   applyFilters(); });
  $('#filter-reset').on('click',    () => {
    currentFilter = 'all';
    searchTerm = '';
    $('#seasonSearch').val('');
    applyFilters();
  });

  // Search
  const onSearch = debounce(() => {
    searchTerm = $('#seasonSearch').val() || '';
    applyFilters();
  }, 200);
  $('#seasonSearch').on('input', onSearch);
  $('#clearSearch').on('click', function() {
    $('#seasonSearch').val('');
    searchTerm = '';
    applyFilters();
  });

  // Sorting
  $(document).on('click', 'th.sortable', function() {
    const key = $(this).data('key');
    if (sortKey === key) {
      sortDir = (sortDir === 'asc') ? 'desc' : 'asc';
    } else {
      sortKey = key;
      sortDir = 'asc';
    }
    applyFilters();
  });

  // Row click → load series
  $(document).on('click', '#seasonsTable .clickable-row', function() {
    const season = $(this).data('season');
    loadSeries(season);
  });

  // Download current view
  $('#downloadViewCsv').on('click', () => {
    const rows = getFilteredRows();
    downloadCsvFromRows(rows);
  });

  // Footer placeholder links (customize if you host)
  document.getElementById('repoLink').addEventListener('click', (e) => {
    e.preventDefault();
    alert('Hook this up to your Git repo URL when ready.');
  });
  document.getElementById('contactLink').addEventListener('click', (e) => {
    e.preventDefault();
    alert('Swap this for your contact page or email link.');
  });
});
</script>

<!-- Bootstrap JS (for accordion, modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
