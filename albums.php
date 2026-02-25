<?php
include 'db/database.php';

/* =========================
   HANDLE MULTI DELETE
========================= */
if (isset($_POST['delete_selected']) && !empty($_POST['ids'])) {

    $ids = $_POST['ids'];

    foreach ($ids as $id) {

        // ambil nama zip
        $stmt = $db->prepare("SELECT zipname FROM albums WHERE id=?");
        $stmt->execute([$id]);
        $album = $stmt->fetch();

        if ($album) {

            $zipname = $album['zipname'];

            /* =====================
       HAPUS FILE ZIP
    ===================== */
            $file = 'zips/' . $zipname;
            if (file_exists($file)) {
                unlink($file);
            }

            /* =====================
       HAPUS CACHE THUMBNAIL
       (yang dibuat oleh thumb.php)
    ===================== */
            foreach (glob("cache/thumbs/{$zipname}*") as $thumb) {
                if (file_exists($thumb)) {
                    unlink($thumb);
                }
            }

            /* =====================
       HAPUS CACHE IMAGE
       (yang dibuat oleh image.php)
       kita bersihkan saja semua biar aman
    ===================== */
            foreach (glob("cache/images/*") as $img) {
                if (file_exists($img)) {
                    unlink($img);
                }
            }

            /* =====================
       HAPUS DATA DATABASE
    ===================== */
            $del = $db->prepare("DELETE FROM albums WHERE id=?");
            $del->execute([$id]);
        }
    }

    header("Location: albums.php");
    exit;
}

$albums = $db->query("SELECT * FROM albums ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>My Albums</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&family=M+PLUS+Rounded+1c:wght@500&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            color: #fff;
            background: linear-gradient(135deg, #141427, #1f1f3a, #2a1a40);
        }

        /* ================= TOPBAR ================= */

        .topbar {
            position: sticky;
            top: 0;
            z-index: 1000;

            display: flex;
            align-items: center;
            gap: 10px;

            padding: 14px 20px;
            margin-bottom: 18px;

            background: rgba(20, 20, 35, 0.75);
            backdrop-filter: blur(12px);

            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 6px 25px rgba(0, 0, 0, .35);
        }

        /* container biar tidak terlalu lebar di desktop */
        .topbar-inner {
            width: 100%;
            max-width: 1400px;
            margin: auto;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* dorong tombol home ke kanan */
        .home-btn {
            margin-left: auto;
        }

        button,
        .home-btn {
            padding: 10px 18px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            color: white;
            font-size: 14px;
            text-decoration: none;
            white-space: nowrap;
        }

        .select-btn {
            background: linear-gradient(45deg, #7b2ff7, #f107a3);
        }

        .delete-btn {
            background: linear-gradient(45deg, #ff416c, #ff4b2b);
            display: none;
        }

        .home-btn {
            background: rgba(255, 255, 255, 0.08);
        }

        /* ================= GRID ================= */

        .grid {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto 40px auto;

            padding: 0 20px;

            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        /* ================= CARD ================= */

        .card {
            position: relative;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(14px);
            border-radius: 18px;
            overflow: hidden;
            transition: .25s;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, .45);
        }

        .card img {
            width: 100%;
            aspect-ratio: 3/4;
            object-fit: cover;
            display: block;
        }

        .card h3 {
            margin: 0;
            padding: 12px 14px;
            font-size: clamp(14px, 2.5vw, 18px);
            font-weight: 500;
        }

        .checkbox {
            position: absolute;
            top: 10px;
            left: 10px;
            transform: scale(1.5);
            display: none;
        }

        a {
            text-decoration: none;
            color: white;
        }

        /* ================= RESPONSIVE ================= */

        /* Tablet */
        @media (max-width:1024px) {
            .grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }

        /* HP Landscape */
        @media (max-width:768px) {
            .grid {
                padding: 0 14px;
                gap: 16px;
            }
        }

        /* HP Normal (Poco F3 ideal di sini) */
        @media (max-width:480px) {

            .topbar {
                padding: 12px;
            }

            .topbar-inner {
                flex-wrap: wrap;
            }

            .home-btn {
                margin-left: 0;
                width: 100%;
                text-align: center;
            }

            button {
                flex: 1;
                padding: 12px;
                font-size: 14px;
            }

            .grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
                padding: 0 12px;
            }
        }

        /* HP kecil */
        @media (max-width:360px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <form method="POST" id="formDelete">

        <div class="topbar">

            <button type="button" class="select-btn" onclick="toggleSelect()">✔ Mode Pilih</button>

            <button type="submit" name="delete_selected" class="delete-btn" id="deleteBtn"
                onclick="return confirm('Hapus album terpilih?')">🗑 Hapus Terpilih</button>

            <a href="index.php" class="home-btn">🏠 Home</a>

        </div>

        <div class="grid">
            <?php foreach ($albums as $a): ?>
                <div class="card">

                    <!-- checkbox -->
                    <input type="checkbox" name="ids[]" value="<?= $a['id'] ?>" class="checkbox">

                    <!-- link lihat album -->
                    <a href="view.php?id=<?= $a['id'] ?>" class="album-link">
                        <img src="thumb.php?zip=<?= $a['zipname'] ?>">
                        <h3><?= htmlspecialchars($a['name']) ?></h3>
                    </a>

                </div>
            <?php endforeach; ?>
        </div>

    </form>

    <script>
        let selecting = false;

        function toggleSelect() {
            selecting = !selecting;

            document.querySelectorAll('.checkbox').forEach(cb => {
                cb.style.display = selecting ? 'block' : 'none';
            });

            document.getElementById('deleteBtn').style.display =
                selecting ? 'inline-block' : 'none';

            // disable link kalau lagi milih
            document.querySelectorAll('.album-link').forEach(link => {
                link.style.pointerEvents = selecting ? 'none' : 'auto';
            });
        }
    </script>

</body>

</html>