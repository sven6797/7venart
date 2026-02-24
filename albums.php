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
            $file = 'zips/' . $album['zipname'];

            if (file_exists($file)) {
                unlink($file); // hapus zip
            }

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
    <title>My Albums</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&family=M+PLUS+Rounded+1c:wght@500&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            color: #fff;
            background: linear-gradient(135deg, #141427, #1f1f3a, #2a1a40);
        }

        .topbar {
            padding: 20px 30px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        /* dorong home ke kanan */
        .topbar .home-btn {
            margin-left: auto;
        }

        button {
            padding: 10px 18px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            color: white;
        }

        .select-btn {
            background: linear-gradient(45deg, #7b2ff7, #f107a3);
        }

        .delete-btn {
            background: linear-gradient(45deg, #ff416c, #ff4b2b);
            display: none;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 16px;
            padding: 16px;
        }

        .card {
            position: relative;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(14px);
            border-radius: 16px;
            overflow: hidden;
            transition: .25s;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .card:hover {
            transform: translateY(-6px);
        }

        .card img {
            width: 100%;
            aspect-ratio: 3/4;
            object-fit: cover;
        }

        .card h3 {
            margin: 0;
            padding: 14px;
            font-family: 'M PLUS Rounded 1c', sans-serif;
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

        .left-menu {
            display: flex;
            gap: 10px;
        }

        @media (max-width:600px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width:380px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width:600px) {
            .topbar {
                flex-wrap: wrap;
                gap: 8px;
            }

            .home-btn {
                margin-left: 0;
                width: 100%;
                text-align: center;
            }

            .delete-btn {
                flex: 1;
            }

            .select-btn {
                flex: 1;
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