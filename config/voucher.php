<?php

/*
|--------------------------------------------------------------------------
| Data Dummy Landing Voucher
|--------------------------------------------------------------------------
|
| Data sementara untuk tahap pertama. Hanya dibaca melalui
| App\Services\VoucherCatalog, sehingga nantinya cukup kelas tersebut
| yang diganti untuk mengambil data dari database.
|
*/

return [

    'brand' => [
        'name' => 'Connect Pro',
        'tagline' => 'Voucher WiFi cepat, praktis & hemat',
        'description' => 'Pilih paket internet sesuai kebutuhan Anda, dan bayar dengan mudah.',
        'support_contact' => 'cs@connectpro.test',
    ],

    'vouchers' => [
        [
            'code' => 'CP-1JAM',
            'name' => 'Voucher WiFi 1 Jam',
            'description' => 'Pas untuk browsing singkat dan cek pesan.',
            'face_value' => 5000,
            'price' => 5000,
            'duration' => '1 Jam',
            'is_available' => true,
            'is_featured' => false,
        ],
        [
            'code' => 'CP-1HARI',
            'name' => 'Voucher WiFi 1 Hari',
            'description' => 'Internet seharian penuh, pilihan favorit pelanggan.',
            'face_value' => 10000,
            'price' => 10000,
            'duration' => '1 Hari',
            'is_available' => true,
            'is_featured' => true,
        ],
        [
            'code' => 'CP-1MGG',
            'name' => 'Voucher WiFi 1 Minggu',
            'description' => 'Lebih hemat untuk pemakaian sepanjang minggu.',
            'face_value' => 25000,
            'price' => 25000,
            'duration' => '1 Minggu',
            'is_available' => true,
            'is_featured' => false,
        ],
    ],

    'steps' => [
        ['title' => 'Pilih paket', 'description' => 'Tentukan paket voucher dan jumlah yang ingin dibeli.'],
        ['title' => 'Periksa ringkasan', 'description' => 'Pastikan voucher, jumlah, dan total harga sudah sesuai.'],
        ['title' => 'Lakukan pembayaran', 'description' => 'Selesaikan pembayaran melalui metode yang tersedia.'],
        ['title' => 'Login WiFi', 'description' => 'Kode voucher dikirim dan siap dipakai untuk login WiFi Connect Pro.'],
    ],

    'max_quantity' => 10,

];
