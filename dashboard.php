<?php
session_start();

// Redirect jika belum login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Buat nama file untuk menyimpan jumlah login per user
// Menggunakan direktori 'login_counts' untuk kerapian
$login_counts_dir = 'login_counts/';
if (!is_dir($login_counts_dir)) {
    mkdir($login_counts_dir, 0777, true); // Buat direktori jika belum ada (pastikan izin server)
}
$file = $login_counts_dir . "login_count_{$username}.txt";

// Cek apakah file sudah ada, jika ya ambil isinya, kalau belum mulai dari 0
if (file_exists($file)) {
    $count = (int)file_get_contents($file);
} else {
    $count = 0;
}

// Tambah 1 setiap kali halaman dibuka
$count++;

// Simpan kembali ke file
file_put_contents($file, $count);

// Inisialisasi array daftar jika belum ada
if (!isset($_SESSION["daftar"])) {
    $_SESSION["daftar"] = [];
}

// Menambahkan atau mengupdate data
if (isset($_POST["nama"]) && isset($_POST["umur"])) {
    $daftar_data = [
        "nama" => htmlspecialchars($_POST["nama"]), // Sanitize input
        "umur" => (int)$_POST["umur"] // Pastikan umur adalah integer
    ];

    // Jika ada parameter index yang valid di URL (mode update)
    if (isset($_GET["index"]) && $_GET["index"] !== null) {
        $index_to_update = (int)$_GET["index"];
        if (isset($_SESSION["daftar"][$index_to_update])) {
            $_SESSION["daftar"][$index_to_update] = $daftar_data;
        }
    } else { // Jika tidak ada index, ini adalah entri baru
        $_SESSION["daftar"][] = $daftar_data;
    }

    // Redirect untuk membersihkan POST data dan GET index (jika ada) setelah submit/update
    header("Location: dashboard.php");
    exit();
}

// Persiapan data untuk form update (jika ada index di URL)
$data_daftar = [
    "nama" => "",
    "umur" => "",
];

$form_action_target = "dashboard.php"; // Default action untuk submit
$is_editing = false;

if (isset($_GET["index"])) {
    $index_to_edit = (int)$_GET["index"];
    if (isset($_SESSION["daftar"][$index_to_edit])) {
        $data_daftar = $_SESSION["daftar"][$index_to_edit];
        $form_action_target = "dashboard.php?index=" . $index_to_edit; // Action untuk update
        $is_editing = true;
    }
}

// Fungsi untuk mendapatkan keterangan umur (ditambahkan untuk kerapian)
function getKeteranganUmur($umur) {
    if ($umur < 20) {
        return "Remaja";
    } elseif ($umur >= 20 && $umur < 40) {
        return "Dewasa";
    } elseif ($umur >= 40) {
        return "Tua";
    } else {
        return "Tidak Diketahui";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang USM ke-19 - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-gradient-start: #1A004B; /* Ungu gelap sekali */
            --primary-gradient-end: #000030;   /* Biru gelap sekali */
            --secondary-gradient-start: #3F5EFB; /* Biru */
            --secondary-gradient-end: #FC466B;   /* Merah muda */
            --text-light: #f0f0f0;
            --text-dark: #333;
            --form-bg: rgba(255, 255, 255, 0.08); /* Form lebih transparan */
            --border-color: #ddd;
            --button-submit: #28a745; /* Hijau */
            --button-submit-hover: #218838;
            --button-logout: #dc3545; /* Merah */
            --button-logout-hover: #c82333;
            /* Warna header tabel yang lebih seperti semula (solid/gelap dengan sedikit gradien) */
            --table-header-bg-start: #4B0082; /* Ungu gelap */
            --table-header-bg-end: #2F005B;   /* Ungu lebih gelap */
            --table-row-even: rgba(255, 255, 255, 0.03); /* Lebih transparan lagi */
            --table-row-odd: rgba(255, 255, 255, 0.08); /* Lebih transparan lagi */
            /* Warna badge lebih kontras */
            --badge-remaja-bg: #007bff; /* Biru solid */
            --badge-remaja-color: #ffffff;
            --badge-dewasa-bg: #28a745; /* Hijau solid */
            --badge-dewasa-color: #ffffff;
            --badge-tua-bg: #ffc107; /* Kuning solid */
            --badge-tua-color: #212529; /* Teks gelap agar kontras */
            --badge-unknown-bg: #6c757d; /* Abu-abu solid */
            --badge-unknown-color: #ffffff;
            /* Warna badge baru untuk keterangan yang lebih kontras (CYAN) */
            --badge-keterangan-cyan-bg: #00BCD4; /* Cyan solid */
            --badge-keterangan-cyan-color: #FFFFFF; /* Teks putih */

            /* Shadow yang lebih menonjol dan futuristik */
            --shadow-small: 0 5px 15px rgba(0, 0, 0, 0.2);
            --shadow-medium: 0 10px 20px rgba(0, 0, 0, 0.4);
            --shadow-large: 0 20px 40px rgba(0, 0, 0, 0.6);
            --neon-glow: 0 0 10px rgba(74, 0, 183, 0.7), 0 0 20px rgba(142, 45, 226, 0.5); /* Efek glow */
            /* Warna glow meteoroid yang lebih jelas */
            --meteor-glow-color: #E0BBE4; /* Lavender terang */


            /* Warna icon aksi yang lebih kontras */
            --action-edit-color: #00bcd4; /* Cyan */
            --action-edit-hover-color: #00e5ff;
            --action-delete-color: #ff5722; /* Oranye kemerahan */
            --action-delete-hover-color: #ff8a65;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(to bottom right, var(--primary-gradient-start), var(--primary-gradient-end));
            color: var(--text-light);
            background-attachment: fixed;
            overflow: hidden; /* Penting agar partikel tidak membuat scrollbar */
            perspective: 1200px; /* Perspektif lebih dalam untuk efek 3D keseluruhan */
            position: relative; /* Untuk penempatan partikel */
        }

        /* Partikel / Meteoroid Effect */
        .particle-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none; /* Agar tidak menghalangi interaksi mouse */
            z-index: 0; /* Di bawah konten utama */
            overflow: hidden;
        }

        .meteor-trail {
            position: absolute;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.9) 0%, var(--meteor-glow-color) 50%, rgba(138, 43, 226, 0) 100%);
            border-radius: 50%;
            opacity: 0; /* Opasitas awal nol */
            box-shadow: 0 0 8px var(--meteor-glow-color), 0 0 15px var(--meteor-glow-color), 0 0 25px rgba(75, 0, 130, 0.7); /* Glow lebih kuat */
            animation: meteorShower var(--animation-duration, 10s) linear infinite;
        }

        /* Generate more meteor trails and vary their properties */
        /* Increased count, varied sizes and animation delays */
        <?php for ($i = 1; $i <= 50; $i++): ?>
        .meteor-trail:nth-child(<?php echo $i; ?>) {
            top: <?php echo rand(0, 100); ?>%;
            left: <?php echo rand(0, 100); ?>%;
            width: <?php echo rand(2, 5); ?>px; /* Ukuran sedikit diperbesar */
            height: <?php echo rand(2, 5); ?>px;
            animation-delay: <?php echo $i * 0.5; ?>s; /* Delay bervariasi */
            animation-duration: <?php echo rand(8, 15); ?>s; /* Durasi bervariasi */
            transform: rotate(<?php echo rand(0, 360); ?>deg);
        }
        <?php endfor; ?>


        @keyframes meteorShower {
            0% { transform: translate(-100vw, -100vh) rotate(45deg); opacity: 0; }
            10% { opacity: 0.8; } /* Opasitas maksimum lebih tinggi */
            80% { opacity: 0.8; }
            100% { transform: translate(100vw, 100vh) rotate(45deg); opacity: 0; }
        }


        .container {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 30px;
            padding: 40px 50px;
            box-shadow: var(--shadow-large);
            text-align: center;
            max-width: 800px;
            width: 90%;
            margin-top: 50px;
            margin-bottom: 50px;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transform: none; /* Hapus transform 3D awal */
            transition: transform 0.4s ease-out, box-shadow 0.4s ease-out; /* Transisi untuk efek hover baru */
            border-top: 5px solid rgba(255, 255, 255, 0.3);
            border-left: 5px solid rgba(255, 255, 255, 0.3);
            box-shadow: var(--shadow-large), var(--neon-glow);
        }
        .container:hover {
            transform: translateY(-10px) scale(1.02); /* Efek pop-up/timbul */
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.7), var(--neon-glow); /* Bayangan lebih kuat saat timbul */
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 30px;
            color: var(--text-light);
            font-weight: 700;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5), 0 0 20px rgba(138, 43, 226, 0.7);
            letter-spacing: 2px;
        }

        .form-section {
            background: var(--form-bg);
            border-radius: 25px;
            padding: 25px 30px;
            box-shadow: var(--shadow-medium);
            margin-bottom: 40px;
            color: var(--text-dark);
            transform: none;
            transition: none;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow-medium), 0 0 8px rgba(74, 0, 183, 0.5);
            max-width: 450px;
            margin-left: auto;
            margin-right: auto;
        }
        .form-section:hover {
            transform: none;
        }

        .form-section h2 {
            font-size: 1.8em;
            margin-bottom: 25px;
            color: var(--secondary-gradient-start);
            font-weight: 600;
            text-shadow: 0 0 5px rgba(63, 94, 251, 0.5);
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-light);
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .form-group input[type="text"],
        .form-group input[type="number"] {
            width: calc(100% - 24px);
            padding: 12px 10px;
            border: none; /* Menghilangkan semua border */
            /* border-bottom: 3px solid var(--secondary-gradient-start); */ /* Garis bawah dihapus */
            border-radius: 8px;
            outline: none;
            font-size: 1em;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.2);
            transform: none;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="number"]:focus {
            /* border-color: var(--secondary-gradient-end); */ /* Garis bawah fokus dihapus */
            box-shadow: 0 0 0 4px rgba(252, 70, 107, 0.3), inset 0 2px 5px rgba(0, 0, 0, 0.3);
            transform: none;
            background-color: rgba(255, 255, 255, 0.15);
        }

        .form-buttons {
            gap: 20px;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 10px;
            font-size: 1.05em;
            font-weight: 700;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.25);
            transform: translateZ(10px);
            transition: all 0.3s ease, transform 0.3s ease;
        }

        .btn i {
            margin-right: 10px;
            font-size: 1.2em;
        }

        .btn-submit {
            background: linear-gradient(to right, #4CAF50, #8BC34A);
        }

        .btn-submit:hover {
            background: linear-gradient(to right, #388E3C, #689F38);
            transform: translateY(-3px) translateZ(15px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.35);
        }

        .btn-logout {
            background: linear-gradient(to right, #F44336, #FF7043);
        }

        .btn-logout:hover {
            background: linear-gradient(to right, #D32F2F, #F4511E);
            transform: translateY(-3px) translateZ(15px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.35);
        }

        .data-table-section {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            box-shadow: var(--shadow-large);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 50px;
            transform: translateZ(20px);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            box-shadow: var(--shadow-large), var(--neon-glow);
            overflow-x: auto; /* Agar tabel responsif pada layar kecil */
        }
        .data-table-section:hover {
            transform: translateZ(25px);
            box-shadow: var(--shadow-large), var(--neon-glow), 0 0 30px rgba(74, 0, 183, 0.9);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }

        table thead th {
            background: linear-gradient(to right, var(--table-header-bg-start), var(--table-header-bg-end));
            padding: 15px;
            text-align: center;
            font-size: 1.1em;
            font-weight: 600;
            text-shadow: 0 0 5px rgba(255, 255, 255, 0.2);
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 25px 25px 0 0;
            white-space: nowrap;
        }
        table thead th:last-child {
            border-top-right-radius: 25px;
        }
        table thead th:first-child {
            border-top-left-radius: 25px;
        }


        table tbody td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.95em;
        }

        table tbody tr {
            transition: all 0.3s ease;
        }

        table tbody tr:nth-child(even) {
            background: var(--table-row-even);
        }

        table tbody tr:nth-child(odd) {
            background: var(--table-row-odd);
        }

        table tbody tr:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-5px) scale(1.03) translateZ(10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
        }

        .action-buttons {
            gap: 12px;
        }

        .action-buttons a, .action-buttons button {
            font-size: 1.1em;
            padding: 6px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
            transform: translateZ(5px);
            transition: all 0.2s ease;
        }

        .action-buttons a.edit {
            color: var(--action-edit-color);
        }
        .action-buttons a.delete {
            color: var(--action-delete-color);
        }


        .action-buttons a.edit:hover, .action-buttons button.edit:hover {
            color: var(--action-edit-hover-color);
            background-color: rgba(0, 188, 212, 0.3);
            transform: scale(1.15) translateZ(8px);
            box-shadow: 0 4px 10px rgba(0, 188, 212, 0.4);
        }

        .action-buttons a.delete:hover, .action-buttons button.delete:hover {
            color: var(--action-delete-hover-color);
            background-color: rgba(255, 87, 34, 0.3);
            transform: scale(1.15) translateZ(8px);
            box-shadow: 0 4px 10px rgba(255, 87, 34, 0.4);
        }

        .badge {
            padding: 7px 12px;
            border-radius: 8px;
            font-size: 0.9em;
            font-weight: 700;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
        }

        /* Kelas badge yang baru untuk warna Cyan */
        .badge-keterangan-cyan {
            background-color: var(--badge-keterangan-cyan-bg); /* Menggunakan variabel baru */
            color: var(--badge-keterangan-cyan-color);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
                transform: none;
                border-top: none;
                border-left: none;
                box-shadow: var(--shadow-small);
                max-width: 95%;
            }
            .form-section {
                padding: 15px;
                transform: none;
                box-shadow: var(--shadow-small);
                border: none;
                max-width: 95%;
            }
            h1 {
                font-size: 1.8em;
                letter-spacing: 1px;
                text-shadow: 0 0 5px rgba(255, 255, 255, 0.3);
            }
            .form-section h2 {
                font-size: 1.5em;
            }
            .form-group label {
                font-size: 0.85em;
            }
            .form-group input {
                font-size: 0.9em;
                padding: 10px 8px;
                transform: none;
            }
            .form-buttons {
                flex-direction: column;
                gap: 12px;
            }
            .btn {
                width: 100%;
                padding: 12px 20px;
                font-size: 0.9em;
                transform: none;
                box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
            }
            .data-table-section {
                padding: 10px;
                transform: none;
                box-shadow: var(--shadow-small);
                border: none;
            }
            table thead th, table tbody td {
                padding: 10px;
                font-size: 0.8em;
            }
            .action-buttons a, .action-buttons button {
                font-size: 0.9em;
                padding: 5px;
                transform: none;
                box-shadow: none;
            }
            .action-buttons a:hover, .action-buttons button:hover {
                transform: none;
            }
            .badge {
                padding: 5px 8px;
                font-size: 0.75em;
                box-shadow: none;
            }
            .meteor-trail {
                display: none; /* Sembunyikan meteor di mobile untuk performa */
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.4em;
            }
            .form-section h2 {
                font-size: 1.2em;
            }
            .form-group input {
                font-size: 0.75em;
            }
        }
    </style>
</head>
<body>
    <div class="particle-container">
        <?php for ($i = 0; $i < 50; $i++): ?>
            <div class="meteor-trail"></div>
        <?php endfor; ?>
    </div>

    <div class="container">
        <h1><?php echo "Selamat datang " . htmlspecialchars($username) . " ke-" . $count; ?></h1>

        <div class="form-section">
            <h2>DAFTAR</h2>
            <form action="<?php echo htmlspecialchars($form_action_target); ?>" method="post">
                <div class="form-group">
                    <label for="nama">Nama</label>
                    <input type="text" id="nama" name="nama" placeholder="Masukkan Nama Lengkap Anda" value="<?php echo htmlspecialchars($data_daftar["nama"]); ?>" required>
                </div>
                <div class="form-group">
                    <label for="umur">Umur</label>
                    <input type="number" id="umur" name="umur" placeholder="Masukkan Umur Anda" value="<?php echo htmlspecialchars($data_daftar["umur"]); ?>" required min="1">
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-check-circle"></i> <?php echo $is_editing ? "UPDATE" : "SUBMIT"; ?>
                    </button>
                    <a href="logout.php" class="btn btn-logout">
                        <i class="fas fa-sign-out-alt"></i> LOGOUT
                    </a>
                </div>
            </form>
        </div>

        <div class="data-table-section">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Umur</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($_SESSION["daftar"])): ?>
                        <?php foreach ($_SESSION["daftar"] as $index => $daftar): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($daftar["nama"]); ?></td>
                                <td><?php echo htmlspecialchars($daftar["umur"]); ?></td>
                                <td>
                                    <?php
                                        $keterangan = getKeteranganUmur($daftar["umur"]);
                                        // Menggunakan kelas badge baru untuk warna Cyan
                                        $badge_class = 'badge-keterangan-cyan';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo htmlspecialchars($keterangan); ?>
                                    </span>
                                </td>
                                <td class="action-buttons">
                                    <a href="hapus.php?index=<?php echo $index; ?>" class="edit" title="Ubah Data">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="hapus.php?index=<?php echo $index; ?>" class="delete" title="Hapus Data">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 20px;">Belum ada data yang terdaftar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>