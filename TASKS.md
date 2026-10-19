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

## Pembaruan UI — Logo, Favicon, Tipografi & Mobile (2026-10-19)
- [x] Aset brand: `assets/images/brand/primary-logo.webp` (logo navbar), `icon-logo.png` (favicon), `icon-logo.webp`
- [x] Logo `primary-logo` dipasang di navbar toko, navbar admin, footer toko, dan halaman login admin
- [x] Favicon `icon-logo` + apple-touch-icon di ketiga header (toko, admin, login)
- [x] Tipografi: font **Plus Jakarta Sans** (Google Fonts) + skala konsisten (judul `text-2xl font-bold tracking-tight`, sub-judul `text-xl/4xl`, angka `tabular-nums`)
- [x] Navbar diubah menjadi **putih bersih** (logo berlatar putih jadi menyatu profesional) dengan aksen hijau; strip kategori abu-abu di desktop
- [x] **Hamburger menu** (SVG inline + script vanilla, tanpa library) di navbar toko & admin untuk layar < 768px
- [x] Tabel admin (produk, pesanan, kategori, dashboard) → **kartu vertikal di mobile**, tabel di ≥ sm
- [x] Keranjang: kontrol qty + subtotal menumpuk rapi di mobile; ringkasan `sticky` di desktop
- [x] Chip filter kategori jadi horizontal-scroll di mobile
- [x] Detail produk: breadcrumb, harga lebih tegas, layout menumpuk di mobile
- [x] Kartu produk: sudut `rounded-xl`, hover zoom gambar, tombol lebih besar (nyaman disentuh)
- Verifikasi: `php -l` semua view bersih; semua route 200; aset logo/favicon HTTP 200; nol error box
- Catatan: `Auth::login()` ditambah `store_name` (dipakai view login baru)

## Pembaruan Logika Bisnis & UX (2026-10-19)
Fokus: belanja lebih cepat, transparansi stok/penjualan, validasi kontak, lokasi kirim, dan struk.

- [x] **Beli Sekarang** di setiap kartu produk & detail → langsung ke checkout untuk 1 produk itu saja
      (session `buy_now` terpisah; **keranjang tidak diubah**; ada tombol "Batal Beli Sekarang" di checkout)
- [x] Tombol **Beli Sekarang** diletakkan bersebelahan dengan tombol tambah keranjang
- [x] Tombol "Tambah keranjang" diganti ikon **"+"** modern (aria-label & title tetap "Tambah ke keranjang")
- [x] Klik **"+" / tambah keranjang tetap di halaman** katalog/detail:
      AJAX (JSON) + refresh token CSRF; fallback tanpa-JS memakai field `return`
- [x] **Counter jumlah** (numeric, tombol − / +) di kartu produk & halaman detail
- [x] **"Terjual N"** dengan logika nyata: `SUM(order_items.quantity)` untuk pesanan **bukan** `dibatalkan`;
      hanya ditampilkan bila benar-benar > 0
- [x] Keranjang: **total dihitung ulang real-time** saat qty diubah
      (subtotal per baris + total grand di browser)
- [x] Qty keranjang > stok → **alert konfirmasi** ("Stok tersedia hanya N, ubah jumlah menjadi N?")
      lalu di-clamp; juga dicek ulang di server
- [x] Update keranjang **via AJAX** supaya sesi & tampilan tidak pernah beda (tanpa reload); fallback POST biasa
- [x] Checkout: **validasi nomor WhatsApp diperbaiki** — normalisasi `+62`/`62`/`0`/spasi/tanda hubung
      ke `08xxxxxxxxxx` lalu divalidasi `^08[0-9]{8,11}$`; pesan error Bahasa Indonesia
- [x] **"Gunakan lokasi saya"** (geolocation) + input link Google Maps di checkout;
      disimpan ke `orders.map_link/latitude/longitude` dan **tampil di panel admin** (tombol buka Google Maps)
- [x] Link Maps juga divalidasi (hanya domain Google Maps) & koordinat diekstrak dari `@lat,lng`, `?q=`, `!3d..!4d`
- [x] **Struk digital** yang bisa dicetak: `konfirmasi/struk` (pelanggan) & `admin/pesanan/{no}/struk`
      → view bersama `receipt/print.php` + tombol `window.print()`
- [x] Tombol **"Lihat / Cetak Struk"** di halaman konfirmasi & **"Cetak Struk"** di detail pesanan admin
- [x] Aset baru: `assets/js/app.js` (toast, format Rupiah, counter, AJAX keranjang, geolocation) — tanpa library
- Verifikasi (Apache + curl): AJAX add/update JSON + token CSRF baru; clamp stok add & update; buy-now →
      checkout hanya 1 item (Rp20.000) sedangkan keranjang tetap; batal buy-now kembali ke keranjang;
      WhatsApp `+62 812-3456-789` → `08123456789`; link Maps non-Google & WA tidak valid **ditolak** (pesan ID, 0 pesanan);
      koordinat terekstrak dari URL & dari input geolocation; struk pelanggan & admin 200;
      `php -l` bersih (controller, model, helper, view); nol error PHP di semua halaman; **DB dikembalikan ke state awal**
- Catatan: `orders.map_link/latitude/longitude` ditambah di DB live + `database.sql`

## Fitur Kelola Admin (2026-10-19)
Menu admin baru **Admin → Kelola Admin** (`admin/kelola-admin`) dengan tiga bagian.

- [x] **Nomor WhatsApp tujuan konfirmasi** — disimpan di tabel `settings` (key `store_whatsapp`),
      bisa diubah kapan saja dari panel tanpa edit file; input dinormalisasi ke format internasional `62…`
      (menerima `0812…`, `62812…`, `+62 812-…`), divalidasi, dan ada pratinjau `+62 812-3456-7890`
- [x] Nomor ini **dipakai di seluruh aplikasi**: tombol *Konfirmasi via WhatsApp* (halaman konfirmasi + checkout)
      dan tautan kontak di halaman **Informasi** — sumber tunggal `store_whatsapp()`
- [x] **Ubah username & password akun sendiri** — wajib konfirmasi **password saat ini**;
      password baru opsional (min. 8 karakter, harus diketik dua kali); hash `password_hash`;
      session diperbarui + `sess_regenerate()` setelah ganti password
- [x] **Daftar akun admin** — tabel (desktop) / kartu (mobile): username, tanggal dibuat, penanda "Anda";
      bisa **tambah akun** (username 3–60 karakter `[A-Za-z0-9._-]`, password min. 8, unik) dan **hapus akun**
- [x] Pengaman: tidak bisa menghapus akun yang sedang login & tidak bisa menghapus admin terakhir;
      semua aksi pengubah data hanya lewat **POST + CSRF** (`form_open`), PRG + flashdata
- [x] Fallback aman: `store_whatsapp()` memakai config `store.php` bila tabel `settings` belum ada
      (DB lama tidak error di halaman checkout)
- Berkas baru: `application/models/Setting_model.php`, `application/controllers/admin/Admins.php`,
  `application/views/admin/admins/index.php`; helper `wa_international()`, `wa_display()`, `store_whatsapp()`
- Verifikasi (Apache + curl): halaman 200 & nol error PHP; simpan nomor → tersimpan `6281298765432`, nomor
  tidak valid ditolak (nilai lama tetap); perubahan langsung tampil di halaman Informasi & checkout;
  ubah username/password terverifikasi lewat `password_verify` (salah password ditolak, hash lama tetap);
  tambah/hapus akun berjalan, username duplikat/karakter salah/password pendek/konfirmasi beda **ditolak**;
  hapus akun sendiri & akun tak ada ditolak dengan pesan yang benar; akses tanpa login diarahkan ke login;
  GET pada endpoint POST dialihkan; `php -l` 66 berkas bersih; `database.sql` diimpor ke **DB sementara**
  (7 tabel + seed `settings` benar, lalu dihapus) — DB live tidak disentuh
- Catatan: tabel `settings` (`name` PK, `value`, `updated_at`) ditambah di DB live + `database.sql`;
  nomor WhatsApp kini bersumber dari DB (seed `6281234567890`), config `store.php` hanya default fallback
