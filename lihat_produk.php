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

// 1. Fungsi Helper untuk Membaca File JSON Terbaru
function loadJsonData($filePath,$fallbackData) {
    if (file_exists($filePath)) {
        $jsonString = file_get_contents($filePath);
        $data = json_decode($jsonString, true);
        if (is_array($data) && !empty($data)) {
            return $data;
        }
    }
    return $fallbackData;
}

$dataProduk = loadJsonData($jsonFile, $products ?? $katalogProduk ?? []);

$statusPesan =$_GET['pesan'] ?? '';
if (isset($_GET['success']) && $_GET['success'] == '1') {$statusPesan = 'success_add';
}

// LOGIKA PAGINASI (PAGINATION)
$limit = 5; 
$totalData = count($dataProduk);
$totalPages = ceil($totalData / $limit);$totalPages = $totalPages > 0 ?$totalPages : 1;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1)$page = 1;
if ($page >$totalPages) $page =$totalPages;

$offset = ($page - 1) *$limit;
$produkTampil = array_slice($dataProduk, $offset,$limit);

$startRecord = $totalData > 0 ?$offset + 1 : 0;
$endRecord = min($offset + $limit,$totalData);

$username =$_SESSION['username'] ?? 'nurmala';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Inventori Produk - Product Information System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --pink-primary: #ff69b4;     /* Hot Pink */
            --pink-hover: #d63384;       /* Pink Gelap */
            --pink-light: #fff0f5;       /* Background Lavender Blush */
            --pink-card-bg: #ffe6f0;     /* Pink Soft Sidebar & Highlight */
            --pink-border: #fcdbe9;      /* Border Soft Pink */
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
        .sidebar { width: 230px; background: white; padding: 20px 15px; border-right: 1px solid var(--pink-border); }
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

        .main-content { flex: 1; padding: 30px 35px; }

        /* Header Dashboard Layout */
        .dashboard-header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;
        }

        /* Widget Jam & Tanggal Dashboard */
        .dashboard-time-card {
            background: white; border: 1px solid var(--pink-border); border-radius: 12px;
            padding: 10px 18px; display: flex; align-items: center; gap: 12px;
            box-shadow: 0 2px 8px rgba(255, 105, 180, 0.08);
        }
        .dashboard-time-card .icon-calendar { font-size: 24px; }
        .dashboard-time-card .time-text { text-align: right; }
        .dashboard-time-card .date-str { font-size: 13px; font-weight: 600; color: #475569; }
        .dashboard-time-card .clock-str { font-size: 14px; font-weight: 800; color: var(--pink-hover); }

        /* Search Input */
        .filter-bar { display: flex; gap: 15px; margin-bottom: 20px; }
        .search-input { border: 1px solid var(--pink-border); border-radius: 10px; padding: 10px 15px; font-size: 13px; background-color: white; }
        .search-input:focus { border-color: var(--pink-primary); box-shadow: 0 0 0 0.25rem rgba(255, 105, 180, 0.25); }

        /* Table Design */
        .table-card { background: white; border-radius: 16px; padding: 25px; border: 1px solid var(--pink-border); box-shadow: 0 4px 15px rgba(255, 105, 180, 0.08); }
        .custom-table { width: 100%; font-size: 13px; border-collapse: separate; border-spacing: 0; }
        .custom-table th { padding: 12px 10px; color: #475569; border-bottom: 2px solid var(--pink-border); text-align: left; font-weight: 700; }
        .custom-table td { padding: 14px 10px; border-bottom: 1px solid var(--pink-border); vertical-align: middle; }

        /* Badges */
        .cat-badge { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }
        .cat-gaming { background: #f3e8ff; color: #9333ea; }
        .cat-audio { background: #e0f2fe; color: #0284c7; }
        .cat-display { background: #ffedd5; color: #ea580c; }
        .cat-accessories { background: #ccfbf1; color: #0d9488; }
        .cat-default { background: var(--pink-card-bg); color: var(--pink-hover); }

        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; }
        .status-aman { background: #dcfce7; color: #16a34a; }
        .status-menipis { background: var(--pink-card-bg); color: var(--pink-hover); }

        /* Footer & Pagination */
        .table-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; font-size: 12px; color: #64748b; }
        .pagination-container { display: flex; gap: 5px; }
        .page-btn {
            display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;
            border-radius: 8px; border: 1px solid var(--pink-border); text-decoration: none; color: #64748b;
            font-weight: 600; font-size: 12px; background: white; transition: 0.2s;
        }
        .page-btn:hover { background: var(--pink-card-bg); color: var(--pink-hover); }
        .page-btn.active { background: var(--pink-primary); color: white; border-color: var(--pink-primary); }
        .page-btn.disabled { opacity: 0.4; pointer-events: none; }
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
                <li class="active"><a href="lihat_produk.php"><span>📦</span> Lihat Produk</a></li>
                <li><a href="tambah_produk.php"><span>➕</span> Tambah Produk</a></li>
                <li><a href="logout.php" style="color: var(--pink-hover);"><span>🚪</span> Logout</a></li>
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">

            <?php if ($statusPesan === 'success_add'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Produk baru berhasil ditambahkan!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Header Atas (Judul Halaman + Card Jam & Tanggal Dashboard) -->
            <div class="dashboard-header">
                <div>
                    <h2 class="fw-bold mb-1" style="font-size: 24px; color: #2c3e50;">Daftar Inventori Produk</h2>
                    <p class="text-muted small mb-0">Kelola data produk dan stok barang secara terpusat.</p>
                </div>
                <div class="dashboard-time-card">
                    <span class="icon-calendar">📅</span>
                    <div class="time-text">
                        <div class="date-str" id="dashDate">Jumat, 18 September 2026</div>
                        <div class="clock-str" id="dashClock">00:00:00 WIB</div>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="filter-bar">
                <input type="text" id="searchProduct" class="form-control search-input" placeholder="🔍 Cari nama produk, kategori, atau ID...">
            </div>

            <!-- Table Card -->
            <div class="table-card">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Subtotal Aset</th>
                            <th style="width: 30%;">Deskripsi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        <?php if (!empty($produkTampil)): ?>
                            <?php foreach ($produkTampil as$produk): ?>
                                <?php 
                                    $idProduct = $produk['id'] ?? $produk['sku'] ?? '-';
                                    
                                    $stokVal =$produk['stok'] ?? 0;
                                    $hargaVal =$produk['harga'] ?? 0;
                                    
                                    if (function_exists('hitungTotalNilaiStok')) {
                                        $subtotal = hitungTotalNilaiStok($hargaVal,$stokVal);
                                    } else {
                                        $subtotal = $hargaVal * $stokVal;
                                    }

                                    $katLower = strtolower($produk['kategori'] ?? '');$catClass = 'cat-default';
                                    if (strpos($katLower, 'gaming') !== false)$catClass = 'cat-gaming';
                                    elseif (strpos($katLower, 'audio') !== false)$catClass = 'cat-audio';
                                    elseif (strpos($katLower, 'display') !== false)$catClass = 'cat-display';
                                    elseif (strpos($katLower, 'accessories') !== false)$catClass = 'cat-accessories';

                                    // Gabungkan field untuk pencarian JavaScript komprehensif
                                    $searchableText = strtolower(htmlspecialchars($idProduct . ' ' . ($produk['nama'] ?? '') . ' ' . ($produk['kategori'] ?? '') . ' ' . ($produk['deskripsi'] ?? '')));
                                ?>
                                <tr class="product-row" data-search="<?= $searchableText; ?>">
                                    <td><?= htmlspecialchars($idProduct); ?></td>
                                    <td><strong><?= htmlspecialchars($produk['nama'] ?? '-'); ?></strong></td>
                                    <td><span class="cat-badge <?= $catClass; ?>"><?= htmlspecialchars($produk['kategori'] ?? 'Umum'); ?></span></td>
                                    <td>Rp <?= number_format($hargaVal, 0, ',', '.'); ?></td>
                                    <td style="font-weight: 700; color: <?= $stokVal < 3 ? 'var(--pink-hover)' : 'inherit'; ?>"><?= $stokVal; ?></td>
                                    <td>Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
                                    <td style="color: #64748b; font-size: 12px;"><?= htmlspecialchars($produk['deskripsi'] ?? '-'); ?></td>
                                    <td>
                                        <?php if ($stokVal >= 3): ?>
                                            <span class="status-badge status-aman">Aman</span>
                                        <?php else: ?>
                                            <span class="status-badge status-menipis">Stok Menipis</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; color: #94a3b8; padding: 25px;">Belum ada data produk tersimpan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Bottom Pagination Bar -->
                <div class="table-footer">
                    <div>
                        Menampilkan <?= $startRecord; ?> - <?= $endRecord; ?> dari <?=$totalData; ?> produk
                    </div>
                    <div class="pagination-container">
                        <a href="?page=<?= $page - 1; ?>" class="page-btn <?= $page <= 1 ? 'disabled' : ''; ?>">&lt;</a>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i; ?>" class="page-btn <?= $i === $page ? 'active' : ''; ?>"><?= $i; ?></a>
                        <?php endfor; ?>
                        <a href="?page=<?= $page + 1; ?>" class="page-btn <?= $page >=$totalPages ? 'disabled' : ''; ?>">&gt;</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Real-time Search (Menjangkau ID, Nama, Kategori, & Deskripsi)
        const searchInput = document.getElementById("searchProduct");
        const productRows = document.querySelectorAll(".product-row");

        if (searchInput) {
            searchInput.addEventListener("keyup", function () {
                const keyword = this.value.toLowerCase().trim();
                productRows.forEach(function (row) {
                    const searchData = row.getAttribute("data-search");
                    if (searchData.includes(keyword)) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                });
            });
        }

        // Script Jam Digital & Tanggal Dashboard
        function updateDashboardClock() {
            const now = new Date();
            
            // Format Jam (HH:MM:SS WIB)
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('dashClock').innerText = `${hours}:${minutes}:${seconds} WIB`;

            // Format Tanggal (Hari, Tgl Bulan Tahun)
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const dayName = days[now.getDay()];
            const monthName = months[now.getMonth()];
            document.getElementById('dashDate').innerText = `${dayName}, ${now.getDate()} ${monthName} ${now.getFullYear()}`;
        }

        setInterval(updateDashboardClock, 1000);
        updateDashboardClock();
    </script>
</body>
</html>