<?php

header("Cache-Control: public, max-age=31536000");
echo $data;
header("Expires: " . gmdate("D, d M Y H:i:s", time() + 31536000) . " GMT");

$zipFile = $_GET['zip'];
$file = $_GET['file'];

$hash = md5($zipFile . $file);
$cacheFile = "cache/images/" . $hash;

/* kalau sudah ada → langsung kirim */
if (file_exists($cacheFile)) {
    header('Content-Type: image/jpeg');
    readfile($cacheFile);
    exit;
}

$zip = new ZipArchive;
$zip->open('zips/' . $zipFile);

$data = $zip->getFromName($file);

/* simpan cache */
file_put_contents($cacheFile, $data);

header('Content-Type: image/jpeg');
echo $data;

$zip->close();
