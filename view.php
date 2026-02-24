<?php
include 'db/database.php';

$id = (int)$_GET['id'];
$album = $db->query("SELECT * FROM albums WHERE id=$id")->fetch();

$zip = new ZipArchive;
$zip->open('zips/' . $album['zipname']);

$images = [];
for ($i = 0; $i < $zip->numFiles; $i++) {
    $name = $zip->getNameIndex($i);
    if (preg_match('/\.(jpg|png|jpeg|webp)$/i', $name)) {
        $images[] = $name;
    }
}
$zip->close();
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($album['name']) ?></title>

    <style>
        body {
            margin: 0;
            font-family: sans-serif;
            background: #0d0d12;
            color: #fff;
        }

        /* TOP NAV */
        .topbar {
            padding: 15px 25px;
            display: flex;
            gap: 10px;
            align-items: center;
            background: #111;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .topbar a {
            text-decoration: none;
            color: white;
            padding: 8px 14px;
            border-radius: 8px;
            background: #1f1f2a;
        }

        .title {
            margin-left: 10px;
            opacity: .7;
        }

        /* GRID */
        .grid {
            column-count: 4;
            column-gap: 10px;
            padding: 10px;
        }

        .grid img {
            width: 100%;
            margin-bottom: 10px;
            border-radius: 8px;
            cursor: zoom-in;
            transition: .2s;
        }

        .grid img:hover {
            transform: scale(1.02);
        }

        @media(max-width:900px) {
            .grid {
                column-count: 2;
            }
        }

        /* LIGHTBOX */
        .lightbox {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .95);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .lightbox img {
            max-width: 90%;
            max-height: 90%;
        }

        .nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 40px;
            cursor: pointer;
            padding: 20px;
            user-select: none;
        }

        .prev {
            left: 20px;
        }

        .next {
            right: 20px;
        }

        #viewer img {
            max-width: 95vw;
            max-height: 88vh;
            width: auto;
            height: auto;
            object-fit: contain;
        }

        .close {
            position: absolute;
            top: 20px;
            right: 25px;
            font-size: 30px;
            cursor: pointer;
        }

        @media (max-width:1000px) {
            .grid {
                column-count: 3;
            }
        }

        @media (max-width:700px) {
            .grid {
                column-count: 2;
            }
        }

        @media (max-width:480px) {
            .grid {
                column-count: 1;
            }
        }
    </style>
</head>

<body>

    <div class="topbar">
        <a href="index.php">🏠 Home</a>
        <a href="albums.php">📁 Albums</a>
        <div class="title"><?= htmlspecialchars($album['name']) ?></div>
    </div>

    <div class="grid">
        <?php foreach ($images as $i => $img): ?>
            <img src="image.php?zip=<?= $album['zipname'] ?>&file=<?= urlencode($img) ?>"
                onclick="openLightbox(<?= $i ?>)">
        <?php endforeach; ?>
    </div>

    <!-- LIGHTBOX -->
    <div class="lightbox" id="lightbox">
        <span class="close" onclick="closeLightbox()">✕</span>
        <span class="nav prev" onclick="prev()">❮</span>
        <img id="viewer">
        <span class="nav next" onclick="next()">❯</span>
    </div>

    <script>
        const images = <?= json_encode(array_values($images)) ?>;
        const zip = "<?= $album['zipname'] ?>";

        let index = 0;

        function openLightbox(i) {
            index = i;
            show();
            document.getElementById('lightbox').style.display = 'flex';
        }

        function closeLightbox() {
            document.getElementById('lightbox').style.display = 'none';
        }

        function show() {
            document.getElementById('viewer').src =
                "image.php?zip=" + zip + "&file=" + encodeURIComponent(images[index]);
        }

        function next() {
            index = (index + 1) % images.length;
            show();
        }

        function prev() {
            index = (index - 1 + images.length) % images.length;
            show();
        }

        /* keyboard support */
        document.addEventListener('keydown', e => {
            if (document.getElementById('lightbox').style.display === 'flex') {
                if (e.key === "ArrowRight") next();
                if (e.key === "ArrowLeft") prev();
                if (e.key === "Escape") closeLightbox();
            }
        });
    </script>

    <script>
        let startX = 0;

        document.getElementById("viewer").addEventListener("touchstart", e => {
            startX = e.touches[0].clientX;
        });

        document.getElementById("viewer").addEventListener("touchend", e => {
            let endX = e.changedTouches[0].clientX;

            if (endX - startX > 50) prevImage();
            if (startX - endX > 50) nextImage();
        });
    </script>

</body>

</html>