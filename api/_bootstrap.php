<?php
// /api/_bootstrap.php

// --- Security headers ---
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer-when-downgrade');
header('X-Frame-Options: DENY');
header('Permissions-Policy: interest-cohort=()'); // disable FLoC-like
// Simple CSP (relaxed for CDN & images); tighten later if you self-host
header("Content-Security-Policy: default-src 'none'; img-src 'self' data: https:; script-src 'self' https://cdn.jsdelivr.net https://code.jquery.com https://cdn.jsdelivr.net/npm; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; connect-src 'self'; frame-ancestors 'none'");

// --- CORS (same-origin) ---
// If your UI and API are same origin, keep this. If you serve UI elsewhere, adjust allowed origin carefully.
// header('Access-Control-Allow-Origin: https://your.app'); header('Vary: Origin');

// --- Error behavior ---
ini_set('display_errors', '0'); // don't leak errors
error_reporting(E_ALL);

// --- JSON helpers ---
function json_ok($data, int $maxAge=30) {
  header('Cache-Control: public, max-age=' . $maxAge);
  echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}
function json_fail(string $message='Request failed', int $code=400) {
  http_response_code($code);
  echo json_encode(['error' => $message], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

// --- Input helpers ---
function qstr($key, $default='') {
  return isset($_GET[$key]) ? (string)$_GET[$key] : $default;
}
function normalize_season_label(?string $label): ?string {
  if ($label === null) return null;
  $label = trim($label);
  // Convert Unicode en dash/emdash to ASCII hyphen
  $label = str_replace(["\xE2\x80\x93", "\xE2\x80\x94"], '-', $label);
  // Accept formats: YYYY-YY or YYYY-YYYY (we’ll pass through as-is if it matches)
  if (!preg_match('/^\d{4}-(\d{2}|\d{4})$/', $label)) return null;
  return $label;
}
// --- ETag / conditional GET helpers ---
function send_etag_and_maybe_304(string $etag, int $maxAge=120): void {
  header('Cache-Control: public, max-age=' . $maxAge);
  header('ETag: "'.$etag.'"');
  $ifNone = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
  if (trim($ifNone, '"') === $etag) {
    http_response_code(304);
    exit;
  }
}

function json_ok_with_etag($data, int $maxAge=120): void {
  $payload = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  $etag = substr(sha1($payload), 0, 16);
  send_etag_and_maybe_304($etag, $maxAge);
  echo $payload;
  exit;
}
