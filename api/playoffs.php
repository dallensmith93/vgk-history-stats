<?php
// api/playoffs.php
header('Content-Type: application/json');
require __DIR__ . '/db.php';

$stmt = $pdo->query("
  SELECT season_label
  FROM seasons
  WHERE made_playoffs = 1
  ORDER BY start_year ASC
");
echo json_encode($stmt->fetchAll());
