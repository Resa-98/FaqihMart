<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Faqih Mart store settings
|--------------------------------------------------------------------------
*/

// Nomor WhatsApp toko untuk konfirmasi pesanan (format internasional tanpa '+').
// PLACEHOLDER — ganti dengan nomor asli sebelum go-live.
$config['store_whatsapp'] = '6281234567890';

$config['store_name'] = 'Faqih Mart';
$config['store_area'] = 'Cikarang dan sekitarnya';

// Batas upload gambar produk (admin).
$config['upload_max_size'] = 2048; // KB
$config['upload_allowed_types'] = 'jpg|jpeg|png|webp';
