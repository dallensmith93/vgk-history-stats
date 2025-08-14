<?php
// api/export_scf_csv.php
require __DIR__ . '/db.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="vgk_scf_runs.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['season_label','champions']);

$q = $pdo->query("SELECT season_label, champions FROM seasons WHERE made_scf = 1 ORDER BY start_year ASC");
while ($row = $q->fetch()) {
  $row['champions'] = (int)$row['champions'];
  fputcsv($out, $row);
}
fclose($out);
