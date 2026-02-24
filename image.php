<?php
$zip = new ZipArchive;
$zip->open('zips/' . $_GET['zip']);

$file = $_GET['file'];
$data = $zip->getFromName($file);

header('Content-Type: image/jpeg');
echo $data;

$zip->close();
