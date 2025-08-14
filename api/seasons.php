<?php
// api/seasons.php
header('Content-Type: application/json');
require __DIR__ . '/db.php';

$stmt = $pdo->query("
  SELECT season_label, division_finish, made_playoffs, made_scf, champions
  FROM seasons
  ORDER BY start_year ASC, end_year ASC
");
echo json_encode($stmt->fetchAll());
