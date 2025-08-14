<?php
// api/export_seasons_csv.php
require __DIR__ . '/db.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="vgk_seasons.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['season_label','division_finish','made_playoffs','made_scf','champions']);

$q = $pdo->query("SELECT season_label, division_finish, made_playoffs, made_scf, champions FROM seasons ORDER BY start_year ASC");
while ($row = $q->fetch()) {
  // cast flags to 0/1
  $row['made_playoffs'] = (int)$row['made_playoffs'];
  $row['made_scf'] = (int)$row['made_scf'];
  $row['champions'] = (int)$row['champions'];
  fputcsv($out, $row);
}
fclose($out);
