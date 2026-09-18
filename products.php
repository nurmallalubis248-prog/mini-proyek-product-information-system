<?php

$jsonFile = __DIR__ . '/products_data.json';

$defaultProducts = [
    [
        "id"        => "001",
        "nama"      => "Mechanical Keyboard RGB Pro",
        "kategori"  => "Gaming Gear",
        "harga"     => 750000,
        "stok"      => 7,
        "deskripsi" => "Keyboard mekanikal switch blue dengan pencahayaan RGB custom."
    ],
    [
        "id"        => "002",
        "nama"      => "Gaming Mouse Wireless 4K",
        "kategori"  => "Gaming Gear",
        "harga"     => 450000,
        "stok"      => 2,
        "deskripsi" => "Sensor ultra-akurat dengan latensi rendah khusus esports."
    ],
    [
        "id"        => "003",
        "nama"      => "Headset 7.1 Surround Sound",
        "kategori"  => "Audio",
        "harga"     => 620000,
        "stok"      => 5,
        "deskripsi" => "Bantalan telinga empuk dengan audio spasial imersif."
    ],
    [
        "id"        => "004",
        "nama"      => "Monitor Curved 27 inch 165Hz",
        "kategori"  => "Display",
        "harga"     => 2850000,
        "stok"      => 1,
        "deskripsi" => "Refresh rate tinggi bebas ghosting untuk pengalaman gaming mulus."
    ],
    [
        "id"        => "005",
        "nama"      => "Mousepad Extended Waterproof",
        "kategori"  => "Accessories",
        "harga"     => 120000,
        "stok"      => 15,
        "deskripsi" => "Permukaan speed-type anti air dengan jahitan pinggir anti-frayed."
    ],
    [
        "id"        => "006", 
        "nama"      => "Stand Laptop Aluminum Ergonomis Pink",
        "kategori"  => "Accessories",
        "harga"     => 175000,
        "stok"      => 2,
        "deskripsi" => "Dudukan laptop berbahan aluminium kokoh dengan sudut kemiringan yang dapat disesuaikan dan desain lipat yang praktis."
    ],
    [
    'id'        => 'PRD-007',
    'nama'      => 'Powerbank 20000mAh Fast Charging',
    'kategori'  => 'Accessories',
    'harga'     => 280000,
    'stok'      => 25,
    'deskripsi' => 'Pengisi daya portabel kapasitas besar dengan output Type-C 22.5W dan layar indikator LED.'
],
];

// 1. Jika file JSON belum ada, buat baru dan isi data default
if (!file_exists($jsonFile)) {
    file_put_contents($jsonFile, json_encode($defaultProducts, JSON_PRETTY_PRINT));
}

// 2. Baca data dari file JSON
$jsonString = file_get_contents($jsonFile);
$products = json_decode($jsonString, true);

// 3. Jika file JSON ada tapi isinya kosong / corrupt, timpa ulang dengan data default
if (empty($products)) {
    $products = $defaultProducts;
    file_put_contents($jsonFile, json_encode($defaultProducts, JSON_PRETTY_PRINT));
}

// Alias variabel agar tetap kompatibel jika ada file lain yang memakai nama $katalogProduk
$katalogProduk = $products;
?>