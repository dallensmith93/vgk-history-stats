<?php
// api/scf.php
header('Content-Type: application/json');
require __DIR__ . '/db.php';

$stmt = $pdo->query("
  SELECT season_label, champions
  FROM seasons
  WHERE made_scf = 1
  ORDER BY start_year ASC
");
echo json_encode($stmt->fetchAll());
