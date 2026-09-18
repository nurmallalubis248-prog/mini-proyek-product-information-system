<?php
// Set zona waktu server ke Asia/Jakarta (WIB)
date_default_timezone_set('Asia/Jakarta');

require_once 'config.php';
require_once 'products.php';
require_once 'functions.php';

// Cek autentikasi login
if (!isset($_SESSION['user_logged_in']) && !isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

// Hitung Total Nilai Aset Gudang
$totalAsetGudang = 0;
if (isset($katalogProduk) && is_array($katalogProduk)) {
    foreach ($katalogProduk as$item) {
        $totalAsetGudang += hitungTotalNilaiStok($item);
    }
}

// Hitung Jumlah Stok Menipis
$stokMenipis = 0;
if (isset($katalogProduk) && is_array($katalogProduk)) {
    foreach ($katalogProduk as$item) {
        $statusInfo = cekStatusStok($item['stok']);
        if ($statusInfo['status'] === 'Stok Menipis' || $statusInfo['status'] === 'Kritis') {$stokMenipis++;
        }
    }
}

// Hitung Total Unit Stok
$totalUnitStok = 0;
if (isset($katalogProduk) && is_array($katalogProduk)) {
    foreach ($katalogProduk as$item) {
        $totalUnitStok +=$item['stok'];
    }
}

$username =$_SESSION['username'] ?? 'Nurmala Lubis';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Product Information System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #fcf4f7; color: #333; }

        /* Navigation Bar Pink */
        .navbar {
            background-color: #ff69b4;
            color: white;
            padding: 14px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(255, 105, 180, 0.2);
        }
        .navbar .brand { display: flex; align-items: center; gap: 10px; }
        .navbar .brand h2 { font-size: 18px; font-weight: 700; color: #ffffff; }
        .navbar .brand p { font-size: 11px; color: #ffe6f0; }
        .navbar .user-profile { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600; cursor: pointer; color: white; }
        .navbar .user-avatar { width: 34px; height: 34px; background: rgba(255,255,255,0.25); border-radius: 50%; display: flex; align-items: center; justify-content: center; }

        /* Container & Layout */
        .container { display: flex; min-height: calc(100vh - 60px); }

        /* Sidebar Navigation Pink Accent */
        .sidebar { width: 230px; background: white; padding: 20px; border-right: 1px solid #f9dbe7; }
        .sidebar ul { list-style: none; }
        .sidebar li { margin-bottom: 8px; }
        .sidebar a {
            display: flex; align-items: center; gap: 12px; padding: 12px 15px;
            text-decoration: none; color: #666; font-weight: 500; font-size: 14px; border-radius: 10px; transition: 0.2s;
        }
        .sidebar li.active a, .sidebar a:hover { background-color: #ffe6f0; color: #d63384; font-weight: 700; }

        /* Main Content Area */
        .main-content { flex: 1; padding: 25px 35px; }

        /* Content Header & Realtime WIB Clock */
        .content-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .welcome-text h1 { font-size: 22px; color: #2c3e50; }
        .welcome-text p { font-size: 13px; color: #7f8c8d; margin-top: 4px; }
        
        .clock-card {
            background: white; border: 1px solid #fcdbe9; padding: 10px 18px;
            border-radius: 12px; display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 10px rgba(255, 105, 180, 0.08);
        }
        .clock-card .icon { font-size: 20px; }
        .clock-card .time-text { font-size: 13px; font-weight: 600; color: #4a4a4a; text-align: right; }

        /* Pink Metric Cards Grid & Clickable Link */
        .metrics-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px; }
        
        .stat-card-link { text-decoration: none; color: inherit; display: block; }
        
        .card { 
            background: white; padding: 20px; border-radius: 16px; 
            box-shadow: 0 4px 15px rgba(255, 105, 180, 0.08); border: 1px solid #fcdbe9; 
            display: flex; flex-direction: column; justify-content: space-between;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:hover { 
            transform: translateY(-4px); 
            box-shadow: 0 8px 20px rgba(255, 105, 180, 0.18); 
            border-color: #ff69b4;
        }
        
        .card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
        .card-title { font-size: 12px; color: #888; font-weight: 700; text-transform: uppercase; }
        .card-value { font-size: 22px; font-weight: 700; color: #2c3e50; }
        .card-sub { font-size: 11px; color: #a0a0a0; margin-top: 8px; }
        
        .icon-box { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .pink-bg { background-color: #ffe6f0; color: #ff69b4; }
        .rose-bg { background-color: #fff0f5; color: #db2777; }
        .orange-bg { background-color: #fff4e5; color: #f97316; }
        .green-bg { background-color: #eefbf3; color: #22c55e; }

        @media (max-width: 992px) {
            .metrics-grid { grid-template-columns: repeat(2, 1fr); }
            .container { flex-direction: column; }
            .sidebar { width: 100%; }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="brand">
            <span style="font-size: 20px;">🛍️</span>
            <div>
                <h2>Product Information System</h2>
                <p>Nurmala Lubis</p>
            </div>
        </div>
        <div class="user-profile">
            <div class="user-avatar">👤</div>
            <span><?= htmlspecialchars($username); ?> ▾</span>
        </div>
    </div>

    <div class="container">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <ul>
                <li class="active"><a href="index.php"><span>🏠</span> Dashboard</a></li>
                <li><a href="lihat_produk.php"><span>📦</span> Lihat Produk</a></li>
                <li><a href="tambah_produk.php"><span>➕</span> Tambah Produk</a></li>
                <li><a href="logout.php" style="color: #e11d48;"><span>🚪</span> Logout</a></li>
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            
            <!-- Header Salam & Realtime Jam WIB -->
            <div class="content-header">
                <div class="welcome-text">
                    <h1>Halo, <span style="color: #d63384;"><?= htmlspecialchars($username); ?></span>! 👋</h1>
                    <p>Selamat datang di Sistem Informasi Manajemen Inventori Produk Berbasis PHP Fundamental.</p>
                </div>
                <div class="clock-card">
                    <span class="icon">📅</span>
                    <div class="time-text">
                        <div id="date-string">Memuat tanggal...</div>
                        <div id="time-string" style="color: #d63384; font-weight: 700;">Memuat jam...</div>
                    </div>
                </div>
            </div>

            <!-- Kartu Ringkasan / Statistik Dynamic (Semua Kartu Bisa Diklik) -->
            <div class="metrics-grid">
                
                <!-- 1. Variasi Produk -->
                <a href="lihat_produk.php" class="stat-card-link">
                    <div class="card">
                        <div class="card-top">
                            <span class="card-title">Variasi Produk</span>
                            <div class="icon-box pink-bg">📦</div>
                        </div>
                        <div class="card-value"><?= isset($katalogProduk) ? count($katalogProduk) : 0; ?> Jenis Item</div>
                        <div class="card-sub">📈 Produk terdaftar</div>
                    </div>
                </a>

                <!-- 2. Total Unit Stok -->
                <a href="lihat_produk.php" class="stat-card-link">
                    <div class="card">
                        <div class="card-top">
                            <span class="card-title">Total Unit Stok</span>
                            <div class="icon-box rose-bg">🧩</div>
                        </div>
                        <div class="card-value"><?= $totalUnitStok; ?> Unit</div>
                        <div class="card-sub">📉 Unit di gudang</div>
                    </div>
                </a>

                <!-- 3. Stok Menipis -->
                <a href="lihat_produk.php" class="stat-card-link">
                    <div class="card">
                        <div class="card-top">
                            <span class="card-title">Stok Menipis</span>
                            <div class="icon-box orange-bg">⚠️</div>
                        </div>
                        <div class="card-value" style="color: #ea580c;"><?= $stokMenipis; ?> Produk</div>
                        <div class="card-sub" style="color: #ea580c;">⚠️ Perlu perhatian</div>
                    </div>
                </a>

                <!-- 4. Nilai Aset Gudang -->
                <a href="lihat_produk.php" class="stat-card-link">
                    <div class="card">
                        <div class="card-top">
                            <span class="card-title">Nilai Aset Gudang</span>
                            <div class="icon-box green-bg">Rp</div>
                        </div>
                        <div class="card-value">Rp <?= number_format($totalAsetGudang, 0, ',', '.'); ?></div>
                        <div class="card-sub">📈 Total nilai aset</div>
                    </div>
                </a>

            </div>

        </div>
    </div>

    <!-- Live Clock Script WIB (Waktu Indonesia Barat) -->
    <script>
        function updateClockWIB() {
            const now = new Date();
            
            // Konversi Waktu Browser ke UTC+7 (WIB)
            const utcTime = now.getTime() + (now.getTimezoneOffset() * 60000);
            const wibTime = new Date(utcTime + (3600000 * 7));

            // Format Hari dan Tanggal Bahasa Indonesia
            const optionsDate = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
            const dateFormatter = new Intl.DateTimeFormat('id-ID', optionsDate);
            const dateString = dateFormatter.format(wibTime);

            // Format Jam, Menit, Detik WIB
            const hours = String(wibTime.getHours()).padStart(2, '0');
            const minutes = String(wibTime.getMinutes()).padStart(2, '0');
            const seconds = String(wibTime.getSeconds()).padStart(2, '0');
            
            const timeString = `${hours}:${minutes}:${seconds} WIB`;

            // Tampilkan ke Elemen HTML
            document.getElementById('date-string').textContent = dateString;
            document.getElementById('time-string').textContent = timeString;
        }

        // Jalankan pembaruan jam setiap 1 detik
        setInterval(updateClockWIB, 1000);
        updateClockWIB();
    </script>
</body>
</html>