<?php
// public/healthcheck.php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$status = ['app' => 'ok', 'php' => PHP_VERSION];

try {
  require_once __DIR__ . '/../api/db.php'; // pulls bootstrap + $pdo
  $row = $pdo->query('SELECT 1')->fetchColumn();
  $status['db'] = ($row == 1) ? 'ok' : 'degraded';
} catch (Throwable $e) {
  $status['db'] = 'down';
}

http_response_code(($status['db'] === 'ok') ? 200 : 503);
echo json_encode($status, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
