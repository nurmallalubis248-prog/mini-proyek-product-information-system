<?php

function hitungTotalNilaiStok($harga, $stok = null)
{
    
    if (is_array($harga)) {
        $stokVal = $harga['stok'] ?? 0;
        $hargaVal = $harga['harga'] ?? 0;
        return (int)$hargaVal * (int)$stokVal;
    }

    return (int)$harga * (int)$stok;
}

function cekStatusStok($stok)
{
    if ((int)$stok < 3) {
        return [
            'status' => 'Stok Menipis',
            'class'  => 'table-danger'
        ];
    }

    return [
        'status' => 'Aman',
        'class'  => ''
    ];
}