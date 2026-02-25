<?php

header("Cache-Control: public, max-age=31536000");
echo $data;
header("Expires: " . gmdate("D, d M Y H:i:s", time() + 31536000) . " GMT");

$zipFile = $_GET['zip'];
$cacheFile = "cache/thumbs/" . $zipFile . ".jpg";

/* kalau sudah pernah dibuat → langsung tampilkan */
if (file_exists($cacheFile)) {
    header('Content-Type: image/jpeg');
    readfile($cacheFile);
    exit;
}

$zip = new ZipArchive;
$path = 'zips/' . $zipFile;

if ($zip->open($path)) {

    for ($i = 0; $i < $zip->numFiles; $i++) {

        $name = $zip->getNameIndex($i);

        if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $name)) {

            $data = $zip->getFromIndex($i);

            /* simpan cache */
            file_put_contents($cacheFile, $data);

            header('Content-Type: image/jpeg');
            echo $data;

            break;
        }
    }

    $zip->close();
}
