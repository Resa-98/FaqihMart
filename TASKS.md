# TASKS.md — Faqih Mart MVP

Sumber manajemen task. Perbarui setiap tahap benar-benar selesai.

## Keputusan yang sudah disepakati
- Harga seed = placeholder Rp10.000 (semua produk), diedit manual via admin setelahnya.
- Nomor WhatsApp toko = placeholder `6281234567890` di `application/config/store.php`.
- Admin seed = `admin` / `admin123` (bcrypt hash di `database.sql`). Ganti sebelum go-live.
- Kategori = 6 folder berisi gambar saja (folder kosong 07-10 & 99 tidak dibuat agar tidak ada halaman kategori kosong).
- Subfolder aset (Shampoo, Popok, dll.) hanya grouping sumber; tidak jadi sub-kategori.
- Nama produk diturunkan dari filename; typo pada nama file diperbaiki di nama tampilan, file asli tidak diubah. Daftar kasus khusus: `lifebuouy`, `jhonsons/jhonson`, `coikies`, `invisble`, `ff-puch` (putih), `butterschot`.
- Gambar disalin ke `assets/images/products/` dengan nama tersanitasi (tanpa `&`, spasi, `+`, `@`). Sumber tidak disentuh.
- Stok seed = 10 (placeholder operasional, bukan data nyata).
- Paginasi katalog di-handroll sederhana (filter/search + halaman) alih-alih library pagination CI3 yang kaku dengan query filter.

## Status Tahap

### Phase 0 — Fondasi
- [x] Fix `config.php`: base_url, csrf_protection=TRUE
- [x] Isi `database.php` (root, db `faqihmart`)
- [x] Autoload: library `database`+`session`, helper `url`+`form`+`store`
- [x] Config `application/config/store.php` (WA placeholder, batas upload)
- [x] Audit aset: 193 gambar .webp, 6 kategori berisi, 5 kategori + Gula/Tepung kosong

### Phase 1 — Skema & Seed
- [x] `database.sql`: categories, products, orders, order_items, admins + seed
- [x] Salin + sanitasi 192 gambar ke `assets/images/products/` (1 file duplikat MD5 dibuang)
- [x] Buat database `faqihmart` + import (6 kategori, 192 produk, 1 admin; hash terverifikasi)

### Phase 2 — Fondasi aplikasi
- [x] `application/core/MY_Controller.php` (Public_Controller + Admin_Controller)
- [x] Helper `store_helper.php` (format Rupiah, dst.)
- [x] Models: Product, Category, Order, Admin
- [x] Routes + layout store (header/footer, Tailwind CDN, warna #16A34A)

### Phase 3 — Katalog publik
- [x] Beranda (hero, kategori, produk unggulan)
- [x] Katalog (search + filter kategori + paginasi)
- [x] Detail produk (gambar fallback, alt, lazy load)
- [x] Informasi Faqih Mart (route `informasi` → `Home::about`)

### Phase 4 — Keranjang & Checkout
- [x] Keranjang session (tambah/ubah/hapus, total)
- [x] Checkout form + validasi CI
- [x] Simpan pesanan: transaksi DB, stok atomik, nomor pesanan unik, snapshot item
- [x] Halaman Konfirmasi + link wa.me

### Phase 5 — Admin
- [x] Login/logout + otorisasi (MY_Controller Admin)
- [x] Dashboard ringkas
- [x] CRUD produk (upload gambar tervalidasi)
- [x] CRUD kategori
- [x] Pesanan: daftar, detail, transisi status (+restok saat batal, sekali saja)

### Phase 6 — Verifikasi & Laporan
- [x] `php -l` semua file aplikasi (bersih)
- [x] Cek routes/MVC/CSRF secara manual (uji HTTP menyeluruh via curl + Apache)
- [x] Laporan akhir (file, verifikasi, kendala, asumsi)

## Koreksi saat verifikasi (semua sudah diterapkan)
- CI3 tidak punya `esc()`/`is_post_request()` → wrapper `esc()` + `is_post()` ditambahkan di `store_helper.php`.
- `ci_get_instance()` salah nama → diperbaiki jadi `get_instance()` (menyebabkan 500 di /keranjang).
- CI3 tidak auto-timestamp → `created_at`/`updated_at` diberi `DEFAULT CURRENT_TIMESTAMP`/`ON UPDATE` di `database.sql` + ALTER DB live.
- `application/config/store.php` tidak termuat otomatis (CI3) → didaftarkan di `$autoload['config']` (dampak: nama toko kosong, upload menolak semua tipe).
- `mimes.php` ditambah tipe `webp`.
- Route `admin/login/proses` ditambah; route keranjang/checkout hanya POST.
- `index.php` development: `error_reporting` kecualikan `E_DEPRECATED` (CI 3.1.13 memancarkan dynamic-property deprecation di PHP 8.2 dari `system/` yang tak boleh diubah).
- View checkout: peringatan "Jumlah dibatasi stok (N)" ditampilkan di ringkasan (sebelumnya hanya di keranjang — clamp stok bisa tak terlihat saat bayar).

## Hasil uji HTTP (Apache, bukan php -S)
- Semua route publik 200; `kategori/tidak-ada` & `produk/tidak-ada` 404; `/checkout`, `/konfirmasi`, `/admin` redirect sesuai kasus.
- Alur belanja end-to-end: add-to-cart → checkout → pesanan tersimpan (nomor FM-YYYYMMDD-NNNN, snapshot nama+harga, total benar) → stok berkurang atomik.
- Anti oversell: dua sesi bersaing atas stok 8 → hanya terjual total 8 (0 stok negatif); clamp qty tampil dengan peringatan di keranjang & checkout.
- CSRF: token lama dipakai ulang → 403, pesanan TIDAK tersimpan; regenerate per-POST bekerja.
- Admin: login sukses, dashboard tampil; batal pesanan → restok tepat sekali (7→8); transisi dari status final ditolak tanpa perubahan.
- Upload produk webp → file ter-encrypt, tampil di katalog; hapus produk dengan riwayat pesanan → `order_items.product_id` NULL, snapshot nama/harga tetap, file gambar terhapus, produk 404.
- Logout hanya-POST; setelah logout sesi bersih (akses /admin → 307).
- CRUD kategori: tambah → muncul di filter katalog; hapus → bersih.
- Semua data uji dibersihkan; DB dikembalikan persis ke state seed (6 kategori, 192 produk, 0 pesanan, stok 49 = 10).

## Hambatan / Catatan
- Tidak ada blokir. Catatan: harga/WA/admin password/stok seed tetap placeholder sampai diganti manual.

## Task Berikutnya
- Selesai. Pra go-live (manual, di luar scope coding): ganti placeholder harga via admin, nomor WA & kredensial admin, lalu uji di browser sungguhan.
