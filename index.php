<?php
include 'db/database.php';

if (isset($_FILES['zip'])) {

    $name = pathinfo($_FILES['zip']['name'], PATHINFO_FILENAME);
    $zipName = uniqid() . '.zip';

    move_uploaded_file($_FILES['zip']['tmp_name'], 'zips/' . $zipName);

    $stmt = $db->prepare("INSERT INTO albums(name,zipname) VALUES(?,?)");
    $stmt->execute([$name, $zipName]);

    header("Location: albums.php");
    exit; // penting!
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Album</title>
    <!-- Font Anime Style -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&family=M+PLUS+Rounded+1c:wght@500&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            color: #fff;
            background: linear-gradient(135deg, #141e30, #243b55, #2b1f4a);
            overflow: hidden;
        }

        /* glowing background orb */
        body::before {
            content: "";
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, #ff6ec7, transparent);
            filter: blur(140px);
            animation: float 6s infinite alternate;

            pointer-events: none;
            /* 🔥 ini bikin bisa diklik */
        }

        @keyframes float {
            from {
                transform: translateY(-40px);
            }

            to {
                transform: translateY(40px);
            }
        }

        .card {
            position: relative;
            z-index: 2;
            width: min(420px, 92%);
            padding: 40px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 0 40px rgba(123, 47, 247, .4);
            text-align: center;
        }

        h1 {
            margin-top: 0;
            font-family: 'M PLUS Rounded 1c', sans-serif;
            letter-spacing: 1px;
        }

        p {
            font-size: 14px;
            opacity: .7;
        }

        input[type="file"] {
            display: none;
        }

        .file-btn {
            display: inline-block;
            padding: 14px 26px;
            border-radius: 12px;
            cursor: pointer;
            margin-top: 15px;
            background: linear-gradient(45deg, #ff6ec7, #7b2ff7);
            box-shadow: 0 0 20px rgba(255, 110, 199, .7);
            transition: .25s;
        }

        .file-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 35px rgba(255, 110, 199, 1);
        }

        #filename {
            margin-top: 10px;
            font-size: 13px;
            opacity: .8;
        }

        button {
            margin-top: 25px;
            padding: 14px 30px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            color: white;
            cursor: pointer;
            background: linear-gradient(45deg, #00dbde, #fc00ff);
            box-shadow: 0 0 25px rgba(0, 219, 222, .6);
            transition: .25s;
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 40px rgba(0, 219, 222, 1);
        }

        .link {
            display: block;
            margin-top: 25px;
            text-decoration: none;
            color: #ffd6ff;
            font-size: 14px;
            transition: .2s;
        }

        .link:hover {
            color: #fff;
            text-shadow: 0 0 10px #ff6ec7;
        }

        @media (max-width:480px) {
            h1 {
                font-size: 20px;
            }

            .file-btn {
                padding: 12px 18px;
                font-size: 14px;
            }

            button {
                width: 100%;
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="box">
        <h1>Upload ZIP Artwork</h1>
        <form action="index.php" method="post" enctype="multipart/form-data"> <label class="file-btn"> 🎀 Pilih File ZIP <input type="file" name="zip" accept=".zip" required onchange="showName(this)"> </label>
            <div id="filename">Belum ada file dipilih</div> <button type="submit">✨ Upload Album</button>
        </form> <a class="link" href="albums.php">📁 Lihat Semua Album</a>
    </div>
</body>

</html>

<?php
if (isset($_FILES['zip'])) {
    $name = pathinfo($_FILES['zip']['name'], PATHINFO_FILENAME);
    $zipName = uniqid() . '.zip';

    move_uploaded_file($_FILES['zip']['tmp_name'], 'zips/' . $zipName);

    $stmt = $db->prepare("INSERT INTO albums(name,zipname) VALUES(?,?)");
    $stmt->execute([$name, $zipName]);

    header("Location: albums.php");
}
?>