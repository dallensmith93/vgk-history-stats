<?php
// api/summary.php
header('Content-Type: application/json');
require __DIR__ . '/db.php';

// total seasons
$total = $pdo->query("SELECT COUNT(*) AS c FROM seasons")->fetch()['c'] ?? 0;

// playoff appearances
$playoffs = $pdo->query("SELECT COUNT(*) AS c FROM seasons WHERE made_playoffs = 1")->fetch()['c'] ?? 0;

// SCF appearances
$scf = $pdo->query("SELECT COUNT(*) AS c FROM seasons WHERE made_scf = 1")->fetch()['c'] ?? 0;

// Cups
$cups = $pdo->query("SELECT COUNT(*) AS c FROM seasons WHERE champions = 1")->fetch()['c'] ?? 0;

echo json_encode([
  'total_seasons' => (int)$total,
  'playoff_count' => (int)$playoffs,
  'scf_count' => (int)$scf,
  'cup_count' => (int)$cups,
]);
