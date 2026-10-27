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
        'name' => 'Voucherku',
        'tagline' => 'Voucher belanja digital, praktis & hemat',
        'description' => 'Pilih voucher sesuai kebutuhan Anda, bayar dengan mudah, dan gunakan langsung di seluruh outlet kami.',
        'support_contact' => 'cs@voucherku.test',
    ],

    'vouchers' => [
        [
            'code' => 'VCR-050',
            'name' => 'Voucher Belanja 50 Ribu',
            'description' => 'Cocok untuk belanja kebutuhan harian.',
            'face_value' => 50000,
            'price' => 47500,
            'validity_days' => 30,
            'is_available' => true,
            'is_featured' => false,
        ],
        [
            'code' => 'VCR-100',
            'name' => 'Voucher Belanja 100 Ribu',
            'description' => 'Pilihan favorit pelanggan kami.',
            'face_value' => 100000,
            'price' => 92500,
            'validity_days' => 60,
            'is_available' => true,
            'is_featured' => true,
        ],
        [
            'code' => 'VCR-250',
            'name' => 'Voucher Belanja 250 Ribu',
            'description' => 'Hemat lebih banyak untuk belanja bulanan.',
            'face_value' => 250000,
            'price' => 225000,
            'validity_days' => 90,
            'is_available' => true,
            'is_featured' => false,
        ],
        [
            'code' => 'VCR-500',
            'name' => 'Voucher Belanja 500 Ribu',
            'description' => 'Ideal untuk hadiah atau kebutuhan keluarga.',
            'face_value' => 500000,
            'price' => 440000,
            'validity_days' => 180,
            'is_available' => false,
            'is_featured' => false,
        ],
    ],

    'steps' => [
        ['title' => 'Pilih voucher', 'description' => 'Tentukan nominal voucher dan jumlah yang ingin dibeli.'],
        ['title' => 'Periksa ringkasan', 'description' => 'Pastikan voucher, jumlah, dan total harga sudah sesuai.'],
        ['title' => 'Lakukan pembayaran', 'description' => 'Selesaikan pembayaran melalui metode yang tersedia.'],
        ['title' => 'Terima voucher', 'description' => 'Kode voucher dikirim dan siap digunakan di outlet kami.'],
    ],

    'max_quantity' => 10,

];
