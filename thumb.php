<?php
$zip = new ZipArchive;
$path = 'zips/' . $_GET['zip'];

if ($zip->open($path)) {
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $name = $zip->getNameIndex($i);
        if (preg_match('/\\.(jpg|png|jpeg|webp)$/i', $name)) {
            header('Content-Type: image/jpeg');
            echo $zip->getFromIndex($i);
            break;
        }
    }
    $zip->close();
}
