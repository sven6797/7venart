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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($album['name']) ?></title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #141427, #1f1f3a, #2a1a40);
            color: white;
        }

        /* ================= TOPBAR ================= */

        .topbar {
            position: sticky;
            top: 0;
            z-index: 1000;

            background: rgba(20, 20, 35, .75);
            backdrop-filter: blur(12px);

            border-bottom: 1px solid rgba(255, 255, 255, .08);
            box-shadow: 0 6px 25px rgba(0, 0, 0, .35);
        }

        .topbar-inner {
            max-width: 1400px;
            margin: auto;
            padding: 14px 20px;

            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar a {
            text-decoration: none;
            color: white;
            background: rgba(255, 255, 255, .08);
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 14px;
        }

        .title {
            margin-left: auto;
            opacity: .7;
            font-size: 14px;
        }

        /* ================= MASONRY GRID ================= */

        .grid {
            column-width: 260px;
            column-gap: 14px;

            max-width: 1600px;
            margin: 24px auto 60px auto;
            padding: 0 18px;
        }

        .grid img {
            width: 100%;
            margin-bottom: 14px;
            border-radius: 14px;
            cursor: zoom-in;
            transition: transform .25s ease, box-shadow .25s ease;
            display: block;
        }

        @media (hover:hover) {
            .grid img:hover {
                transform: scale(1.02);
                box-shadow: 0 12px 30px rgba(0, 0, 0, .5);
            }
        }

        /* ================= LIGHTBOX ================= */

        .lightbox {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .96);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            touch-action: pan-y;
        }

        .lightbox img {
            max-width: 95vw;
            max-height: 90vh;
            border-radius: 12px;
            object-fit: contain;
        }

        .nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 42px;
            cursor: pointer;
            padding: 20px;
            user-select: none;
            opacity: .7;
        }

        .nav:hover {
            opacity: 1;
        }

        .prev {
            left: 20px;
        }

        .next {
            right: 20px;
        }

        .close {
            position: absolute;
            top: 20px;
            right: 25px;
            font-size: 28px;
            cursor: pointer;
            opacity: .7;
        }

        .close:hover {
            opacity: 1;
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width:768px) {
            .grid {
                column-width: 200px;
                padding: 0 12px;
            }
        }

        @media (max-width:480px) {
            .grid {
                column-width: 100%;
                padding: 0 10px;
            }

            .nav {
                font-size: 34px;
            }
        }
    </style>
</head>

<body>

    <div class="topbar">
        <div class="topbar-inner">
            <a href="index.php">🏠 Home</a>
            <a href="albums.php">📁 Albums</a>
            <div class="title"><?= htmlspecialchars($album['name']) ?></div>
        </div>
    </div>

    <div class="grid">
        <?php foreach ($images as $i => $img): ?>
            <img
                loading="lazy"
                src="image.php?zip=<?= $album['zipname'] ?>&file=<?= urlencode($img) ?>"
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
        const lightbox = document.getElementById('lightbox');
        const viewer = document.getElementById('viewer');

        function openLightbox(i) {
            index = i;
            show();
            lightbox.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            lightbox.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function show() {
            viewer.src = "image.php?zip=" + zip + "&file=" + encodeURIComponent(images[index]);
        }

        function next() {
            index = (index + 1) % images.length;
            show();
        }

        function prev() {
            index = (index - 1 + images.length) % images.length;
            show();
        }

        document.addEventListener('keydown', e => {
            if (lightbox.style.display === 'flex') {
                if (e.key === "ArrowRight") next();
                if (e.key === "ArrowLeft") prev();
                if (e.key === "Escape") closeLightbox();
            }
        });

        /* Swipe Support */
        let startX = 0;

        viewer.addEventListener("touchstart", e => {
            startX = e.touches[0].clientX;
        });

        viewer.addEventListener("touchend", e => {
            let endX = e.changedTouches[0].clientX;

            if (endX - startX > 60) prev();
            if (startX - endX > 60) next();
        });
    </script>

</body>

</html>