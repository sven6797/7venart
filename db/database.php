<?php
/* =========================
   SHOW ERROR (DEBUG MODE)
   Biar kalau ada error kelihatan
========================= */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


/* =========================
   CONNECT SQLITE
========================= */
$dbFile = __DIR__ . '/gallery.db';

try {
    $db = new PDO('sqlite:' . $dbFile);

    // tampilkan error PDO
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // hasil query langsung array associative
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}


/* =========================
   CREATE TABLE (AUTO)
========================= */
$db->exec("
CREATE TABLE IF NOT EXISTS albums (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    zipname TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
");
