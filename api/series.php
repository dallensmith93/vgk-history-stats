<?php
// api/series.php
header('Content-Type: application/json');
require __DIR__ . '/db.php';

// Accept either ?season_label=2017-18 OR ?start_year=2017
$label = $_GET['season_label'] ?? null;
$startYear = isset($_GET['start_year']) ? (int)$_GET['start_year'] : null;

if (!$label && !$startYear) {
  http_response_code(400);
  echo json_encode(['error' => 'Provide season_label or start_year']);
  exit;
}

if ($label) {
  $stmt = $pdo->prepare("
    SELECT r.round_label, r.opponent, r.result, r.outcome
    FROM series r
    JOIN seasons s ON s.id = r.season_id
    WHERE s.season_label = ?
    ORDER BY FIELD(r.round_label,
      'First Round','Second Round','Conference Final','Stanley Cup Semifinal','Stanley Cup Final'
    ), r.id ASC
  ");
  $stmt->execute([$label]);
} else {
  $stmt = $pdo->prepare("
    SELECT r.round_label, r.opponent, r.result, r.outcome
    FROM series r
    JOIN seasons s ON s.id = r.season_id
    WHERE s.start_year = ?
    ORDER BY FIELD(r.round_label,
      'First Round','Second Round','Conference Final','Stanley Cup Semifinal','Stanley Cup Final'
    ), r.id ASC
  ");
  $stmt->execute([$startYear]);
}

echo json_encode($stmt->fetchAll());
