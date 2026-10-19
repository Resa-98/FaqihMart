<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($title ?? 'Beranda') ?> — <?= esc($store_name) ?></title>
<meta name="description" content="Toko online kebutuhan pokok dan rumah tangga dengan delivery area <?= esc($store_area) ?>.">
<link rel="icon" type="image/png" href="<?= base_url('assets/images/brand/icon-logo.png') ?>">
<link rel="apple-touch-icon" href="<?= base_url('assets/images/brand/icon-logo.png') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<meta name="csrf-name" content="<?= esc($this->security->get_csrf_token_name()) ?>">
<meta name="csrf-token" content="<?= esc($this->security->get_csrf_hash()) ?>">
<script>
tailwind.config = {
  theme: {
    extend: {
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', '-apple-system', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif']
      },
      colors: {
        brand: { DEFAULT: '#16A34A', dark: '#15803D', light: '#DCFCE7' },
        accent: '#F59E0B'
      }
    }
  }
}
</script>
<style>
  html { -webkit-text-size-adjust: 100%; }
  body { font-feature-settings: "cv02","cv03","cv04","cv11"; }
  .product-img { object-fit: contain; }
  .rp { white-space: nowrap; font-variant-numeric: tabular-nums; }
</style>
</head>
<body class="bg-gray-50 text-gray-700 min-h-screen flex flex-col antialiased">

<header class="bg-white border-b border-gray-200 sticky top-0 z-40">
  <div class="max-w-6xl mx-auto px-4">
    <div class="flex items-center gap-3 h-16">

      <!-- logo -->
      <a href="<?= site_url('') ?>" class="flex items-center shrink-0" aria-label="<?= esc($store_name) ?> — Beranda">
        <img src="<?= base_url('assets/images/brand/primary-logo.webp') ?>" alt="<?= esc($store_name) ?>"
             class="h-11 sm:h-12 w-auto">
      </a>

      <!-- pencarian desktop -->
      <form action="<?= site_url('katalog') ?>" method="get" class="hidden md:flex flex-1 max-w-md mx-auto">
        <input type="search" name="q" value="<?= esc($this->input->get('q', TRUE)) ?>"
               placeholder="Cari kebutuhan rumah tangga..."
               aria-label="Cari produk"
               class="w-full rounded-l-lg border border-r-0 border-gray-300 px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand">
        <button type="submit" class="bg-brand hover:bg-brand-dark text-white px-4 rounded-r-lg text-sm font-semibold transition-colors" aria-label="Cari">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        </button>
      </form>

      <!-- navigasi desktop -->
      <nav class="hidden md:flex items-center gap-1 ml-auto">
        <a href="<?= site_url('katalog') ?>" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-brand rounded-lg hover:bg-gray-50 transition-colors">Katalog</a>
        <a href="<?= site_url('informasi') ?>" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-brand rounded-lg hover:bg-gray-50 transition-colors">Informasi</a>
        <a href="<?= site_url('keranjang') ?>" class="ml-1 inline-flex items-center gap-2 bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
          Keranjang <span data-cart-badge class="bg-white text-brand text-xs font-bold rounded-full px-1.5 py-0.5 min-w-[20px] text-center <?= empty($cart_count) ? 'hidden' : '' ?>"><?= (int) $cart_count ?></span>
        </a>
      </nav>

      <!-- aksi mobile -->
      <div class="flex items-center gap-1 ml-auto md:hidden">
        <a href="<?= site_url('keranjang') ?>" class="relative p-2.5 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-brand transition-colors" aria-label="Keranjang">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
          <span data-cart-badge class="absolute -top-0.5 -right-0.5 bg-accent text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 <?= empty($cart_count) ? 'hidden' : '' ?>"><?= (int) $cart_count ?></span>
        </a>
        <button type="button" id="menu-toggle" aria-expanded="false" aria-controls="mobile-menu" aria-label="Buka menu"
                class="p-2.5 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
          <svg id="menu-icon-open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
          <svg id="menu-icon-close" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 hidden"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
      </div>
    </div>

    <!-- pencarian mobile -->
    <form action="<?= site_url('katalog') ?>" method="get" class="md:hidden pb-3">
      <div class="flex">
        <input type="search" name="q" value="<?= esc($this->input->get('q', TRUE)) ?>"
               placeholder="Cari produk..."
               aria-label="Cari produk"
               class="w-full rounded-l-lg border border-r-0 border-gray-300 px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand">
        <button type="submit" class="bg-brand hover:bg-brand-dark text-white px-4 rounded-r-lg text-sm font-semibold transition-colors" aria-label="Cari">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        </button>
      </div>
    </form>
  </div>

  <!-- strip kategori (desktop) -->
  <?php if (!empty($nav_categories)): ?>
  <div class="hidden md:block bg-gray-50 border-t border-gray-200">
    <div class="max-w-6xl mx-auto px-4 py-2 flex flex-wrap items-center gap-x-5 gap-y-1 text-sm">
      <a href="<?= site_url('katalog') ?>" class="font-semibold text-gray-700 hover:text-brand">Semua Produk</a>
      <?php foreach ($nav_categories as $cat): ?>
        <a href="<?= site_url('kategori/' . $cat->slug) ?>" class="text-gray-500 hover:text-brand transition-colors"><?= esc($cat->name) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- menu mobile -->
  <nav id="mobile-menu" class="md:hidden hidden border-t border-gray-200 bg-white max-h-[calc(100vh-4rem)] overflow-y-auto">
    <div class="px-4 py-3 space-y-0.5">
      <a href="<?= site_url('katalog') ?>" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-brand">Katalog</a>
      <a href="<?= site_url('informasi') ?>" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-brand">Informasi</a>
      <a href="<?= site_url('keranjang') ?>" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-brand">Keranjang<span data-cart-inline><?= empty($cart_count) ? '' : ' (' . (int) $cart_count . ')' ?></span></a>

      <?php if (!empty($nav_categories)): ?>
        <p class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-wider text-gray-400">Kategori</p>
        <?php foreach ($nav_categories as $cat): ?>
          <a href="<?= site_url('kategori/' . $cat->slug) ?>" class="block px-3 py-2.5 rounded-lg text-sm text-gray-600 hover:bg-gray-50 hover:text-brand"><?= esc($cat->name) ?></a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </nav>
</header>

<?php if ($this->session->flashdata('success')): ?>
<div class="max-w-6xl mx-auto px-4 pt-4 w-full">
  <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm" role="status">
    <?= esc($this->session->flashdata('success')) ?>
  </div>
</div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
<div class="max-w-6xl mx-auto px-4 pt-4 w-full">
  <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm" role="alert">
    <?= esc($this->session->flashdata('error')) ?>
  </div>
</div>
<?php endif; ?>

<main class="flex-1 max-w-6xl mx-auto px-4 py-6 w-full">
