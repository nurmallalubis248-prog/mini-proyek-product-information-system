<?php
require_once 'config.php';
require_once 'products.php';
require_once 'functions.php';

// Cek autentikasi session
if (!isset($_SESSION['user_logged_in']) && !isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

$jsonFile = __DIR__ . '/products_data.json';

// Inisialisasi file JSON jika belum ada
if (!file_exists($jsonFile) && isset($products)) {
    file_put_contents($jsonFile, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Helper membaca data JSON
function loadJsonData($filePath, $fallbackData) {
    if (file_exists($filePath)) {
        $jsonString = file_get_contents($filePath);
        $data = json_decode($jsonString, true);
        if (is_array($data) && !empty($data)) {
            return $data;
        }
    }
    return $fallbackData;
}

$errorMsg = '';

// LOGIKA PROSES SIMPAN PRODUK
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = trim($_POST['id'] ?? '');
    $nama      = trim($_POST['nama'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? '');
    $harga     = (int)($_POST['harga'] ?? 0);
    $stok      = (int)($_POST['stok'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if (empty($id) || empty($nama) || empty($kategori)) {
        $errorMsg = 'Silakan isi ID, Nama Produk, dan Kategori dengan benar!';
    } else {
        $dataProduk = loadJsonData($jsonFile, $products ?? $katalogProduk ?? []);

        // Cek jika ID sudah ada
        $isDuplicate = false;
        foreach ($dataProduk as $item) {
            $currentId = $item['id'] ?? $item['sku'] ?? '';
            if (strcasecmp($currentId, $id) === 0) {
                $isDuplicate = true;
                break;
            }
        }

        if ($isDuplicate) {
            $errorMsg = 'ID / Kode Produk sudah terdaftar! Gunakan ID lain.';
        } else {
            // Tambahkan produk baru ke array
            $produkBaru = [
                'id'        => $id,
                'nama'      => $nama,
                'kategori'  => $kategori,
                'harga'     => $harga,
                'stok'      => $stok,
                'deskripsi' => $deskripsi
            ];

            // PERBAIKAN: Gunakan append [] agar data baru masuk ke URUTAN PALING BAWAH
            $dataProduk[] = $produkBaru;

            // Simpan perubahan ke file JSON
            file_put_contents($jsonFile, json_encode($dataProduk, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            header('Location: lihat_produk.php?success=1');
            exit;
        }
    }
}

$username = $_SESSION['username'] ?? 'nurmala';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk Baru - Product Information System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --pink-primary: #ff69b4;     /* Hot Pink sesuai Navigation Bar Dashboard */
            --pink-hover: #d63384;       /* Pink Gelap sesuai Teks Active Dashboard */
            --pink-light: #fff0f5;       /* Background Lavender Blush lembut */
            --pink-card-bg: #ffe6f0;     /* Pink Soft Sidebar Active & Field Input */
            --pink-border: #fcdbe9;      /* Garis pembatas border pink Dashboard */
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: var(--pink-light); color: #2c3e50; }

        /* Top Bar Navigation */
        .top-navbar {
            background-color: var(--pink-primary); 
            color: white; 
            padding: 14px 30px;
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            box-shadow: 0 4px 12px rgba(255, 105, 180, 0.2);
        }
        .top-navbar h2 { font-size: 18px; font-weight: 700; margin: 0; color: #ffffff; }
        .top-navbar p { font-size: 11px; color: #ffe6f0; margin: 0; }

        /* Main Layout */
        .app-container { display: flex; min-height: calc(100vh - 60px); }
        .sidebar { width: 230px; background: white; padding: 20px 15px; border-right: 1px solid #f9dbe7; }
        .sidebar ul { list-style: none; padding: 0; margin: 0; }
        .sidebar li { margin-bottom: 8px; }
        .sidebar a {
            display: flex; align-items: center; gap: 12px; padding: 12px 15px;
            text-decoration: none; color: #666; font-weight: 500; font-size: 14px; border-radius: 10px; transition: 0.2s;
        }
        .sidebar li.active a, .sidebar a:hover { 
            background-color: var(--pink-card-bg); 
            color: var(--pink-hover); 
            font-weight: 700; 
        }

        .main-content { flex: 1; padding: 40px 35px; display: flex; flex-direction: column; align-items: center; }

        /* Form Header Area */
        .form-header { text-align: center; margin-bottom: 30px; }
        .form-header h2 { font-size: 28px; font-weight: 800; color: #2c3e50; margin-bottom: 6px; }
        .form-header p { font-size: 14px; color: #7f8c8d; margin: 0; }

        /* Form Card Styling */
        .form-card {
            background: white;
            width: 100%;
            max-width: 680px;
            border-radius: 16px;
            padding: 35px 40px;
            box-shadow: 0 4px 15px rgba(255, 105, 180, 0.08);
            border: 1px solid var(--pink-border);
        }

        .form-label-custom {
            font-size: 12px;
            font-weight: 700;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: block;
        }

        .form-control-custom {
            background-color: var(--pink-card-bg);
            border: 1px solid var(--pink-border);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 14px;
            color: #2c3e50;
            transition: all 0.2s ease-in-out;
            width: 100%;
        }

        .form-control-custom:focus {
            background-color: #ffffff;
            border-color: var(--pink-primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 105, 180, 0.25);
        }

        .form-control-custom::placeholder {
            color: #a0a0a0;
        }

        /* Action Buttons */
        .btn-container {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn-batal {
            flex: 1;
            background: white;
            border: 1px solid #cbd5e1;
            color: #475569;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            text-decoration: none;
            transition: 0.2s;
        }

        .btn-batal:hover {
            background: #f8fafc;
            color: #1e293b;
        }

        .btn-simpan {
            flex: 1;
            background: var(--pink-primary);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: 0.2s;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(255, 105, 180, 0.2);
        }

        .btn-simpan:hover {
            background: var(--pink-hover);
        }
    </style>
</head>
<body>

    <!-- Top Bar Navigation -->
    <div class="top-navbar">
        <div class="d-flex align-items-center gap-2">
            <span style="font-size: 20px;">🛍️</span>
            <div>
                <h2>Product Information System</h2>
                <p>Nurmala Lubis</p>
            </div>
        </div>
        <div class="fw-semibold font-size-14">
            👤 <?= htmlspecialchars($username); ?> ▾
        </div>
    </div>

    <div class="app-container">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <ul>
                <li><a href="index.php"><span>🏠</span> Dashboard</a></li>
                <li><a href="lihat_produk.php"><span>📦</span> Lihat Produk</a></li>
                <li class="active"><a href="tambah_produk.php"><span>➕</span> Tambah Produk</a></li>
                <li><a href="logout.php" style="color: var(--pink-hover);"><span>🚪</span> Logout</a></li>
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">

            <!-- Title & Subtitle -->
            <div class="form-header">
                <h2>Tambah Produk Baru</h2>
                <p>Lengkapi formulir di bawah ini untuk menambahkan data item ke inventori.</p>
            </div>

            <!-- Form Card -->
            <div class="form-card">

                <?php if (!empty($errorMsg)): ?>
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <?= htmlspecialchars($errorMsg); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="tambah_produk.php" method="POST">
                    
                    <!-- Baris 1: ID / Kode Produk & Kategori -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">ID / KODE PRODUK</label>
                            <input type="text" name="id" class="form-control-custom" placeholder="Contoh: CG-006" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">KATEGORI</label>
                            <input type="text" name="kategori" class="form-control-custom" placeholder="Contoh: Electronics" required>
                        </div>
                    </div>

                    <!-- Baris 2: Nama Produk -->
                    <div class="mb-3">
                        <label class="form-label-custom">NAMA PRODUK</label>
                        <input type="text" name="nama" class="form-control-custom" placeholder="Contoh: Gaming Chair Pro" required>
                    </div>

                    <!-- Baris 3: Harga & Jumlah Stok -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label-custom">HARGA (RP)</label>
                            <input type="number" name="harga" class="form-control-custom" placeholder="1500000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">JUMLAH STOK</label>
                            <input type="number" name="stok" class="form-control-custom" placeholder="10" required>
                        </div>
                    </div>

                    <!-- Baris 4: Deskripsi Produk -->
                    <div class="mb-4">
                        <label class="form-label-custom">DESKRIPSI PRODUK</label>
                        <textarea name="deskripsi" rows="3" class="form-control-custom" placeholder="Deskripsi singkat mengenai produk..."></textarea>
                    </div>

                    <!-- Tombol Batal & Simpan Produk -->
                    <div class="btn-container">
                        <a href="lihat_produk.php" class="btn-batal">Batal</a>
                        <button type="submit" class="btn-simpan">
                            💾 Simpan Produk &rarr;
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>