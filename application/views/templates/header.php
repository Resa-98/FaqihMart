<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($title ?? 'Beranda') ?> — <?= esc($store_name) ?></title>
<meta name="description" content="Toko online kebutuhan pokok dan rumah tangga dengan delivery area <?= esc($store_area) ?>.">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        brand: { DEFAULT: '#16A34A', dark: '#15803D', light: '#DCFCE7' },
        accent: '#F59E0B'
      }
    }
  }
}
</script>
<style>
  .product-img { object-fit: contain; }
  .rp { white-space: nowrap; }
</style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

<header class="bg-brand text-white shadow sticky top-0 z-40">
  <div class="max-w-6xl mx-auto px-4 py-3 flex items-center gap-4">
    <a href="<?= site_url('') ?>" class="text-xl font-bold whitespace-nowrap"><?= esc($store_name) ?></a>

    <form action="<?= site_url('katalog') ?>" method="get" class="flex-1 max-w-md hidden sm:flex">
      <input type="search" name="q" value="<?= esc($this->input->get('q', TRUE)) ?>"
             placeholder="Cari produk..."
             class="w-full rounded-l-md px-3 py-2 text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-accent">
      <button type="submit" class="bg-accent px-3 py-2 rounded-r-md text-sm font-semibold hover:bg-amber-600">Cari</button>
    </form>

    <nav class="flex items-center gap-3 text-sm ml-auto">
      <a href="<?= site_url('katalog') ?>" class="hover:underline hidden sm:inline">Katalog</a>
      <a href="<?= site_url('informasi') ?>" class="hover:underline hidden sm:inline">Informasi</a>
      <a href="<?= site_url('keranjang') ?>" class="bg-white text-brand font-semibold px-3 py-1.5 rounded-full hover:bg-gray-100">
        Keranjang<?php if (!empty($cart_count)): ?> (<?= (int) $cart_count ?>)<?php endif; ?>
      </a>
    </nav>
  </div>

  <!-- pencarian versi mobile -->
  <form action="<?= site_url('katalog') ?>" method="get" class="px-4 pb-3 sm:hidden">
    <input type="search" name="q" value="<?= esc($this->input->get('q', TRUE)) ?>"
           placeholder="Cari produk..."
           class="w-full rounded-md px-3 py-2 text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-accent">
  </form>

  <div class="bg-brand-dark text-xs">
    <div class="max-w-6xl mx-auto px-4 py-1 flex flex-wrap gap-x-4 gap-y-1">
      <a href="<?= site_url('katalog') ?>" class="hover:underline">Semua Produk</a>
      <?php foreach (($nav_categories ?? []) as $cat): ?>
        <a href="<?= site_url('kategori/' . $cat->slug) ?>" class="hover:underline opacity-90"><?= esc($cat->name) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</header>

<?php if ($this->session->flashdata('success')): ?>
<div class="max-w-6xl mx-auto px-4 pt-4 w-full">
  <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md text-sm" role="status">
    <?= esc($this->session->flashdata('success')) ?>
  </div>
</div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
<div class="max-w-6xl mx-auto px-4 pt-4 w-full">
  <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md text-sm" role="alert">
    <?= esc($this->session->flashdata('error')) ?>
  </div>
</div>
<?php endif; ?>

<main class="flex-1 max-w-6xl mx-auto px-4 py-6 w-full">
