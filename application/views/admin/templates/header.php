<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($title) ?> — Admin <?= esc($store_name) ?></title>
<meta name="robots" content="noindex, nofollow">
<link rel="icon" type="image/png" href="<?= base_url('assets/images/brand/icon-logo.png') ?>">
<link rel="apple-touch-icon" href="<?= base_url('assets/images/brand/icon-logo.png') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
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
  .product-img { object-fit: contain; }
  .rp { white-space: nowrap; font-variant-numeric: tabular-nums; }
</style>
</head>
<body class="bg-gray-100 text-gray-700 min-h-screen flex flex-col antialiased">

<header class="bg-white border-b border-gray-200 sticky top-0 z-40">
  <div class="max-w-6xl mx-auto px-4">
    <div class="flex items-center gap-3 h-16">

      <a href="<?= site_url('admin') ?>" class="flex items-center gap-2 shrink-0" aria-label="Dashboard Admin">
        <img src="<?= base_url('assets/images/brand/primary-logo.webp') ?>" alt="<?= esc($store_name) ?>" class="h-11 sm:h-12 w-auto">
        <span class="hidden sm:inline text-sm font-semibold text-gray-400 border-l border-gray-200 pl-2">Admin</span>
      </a>

      <!-- navigasi desktop -->
      <nav class="hidden md:flex items-center gap-1 ml-4">
        <a href="<?= site_url('admin') ?>" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-brand rounded-lg hover:bg-gray-50 transition-colors">Dashboard</a>
        <a href="<?= site_url('admin/produk') ?>" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-brand rounded-lg hover:bg-gray-50 transition-colors">Produk</a>
        <a href="<?= site_url('admin/kategori') ?>" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-brand rounded-lg hover:bg-gray-50 transition-colors">Kategori</a>
        <a href="<?= site_url('admin/pesanan') ?>" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-brand rounded-lg hover:bg-gray-50 transition-colors">Pesanan</a>
        <a href="<?= site_url('admin/kelola-admin') ?>" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-brand rounded-lg hover:bg-gray-50 transition-colors">Kelola Admin</a>
      </nav>

      <div class="hidden md:flex items-center gap-2 ml-auto">
        <a href="<?= site_url('') ?>" class="text-sm text-gray-500 hover:text-brand px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">Lihat toko ↗</a>
        <?= form_open('admin/logout', array('class' => 'inline')) ?>
          <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold px-3 py-2 rounded-lg transition-colors">Logout (<?= esc($admin['username']) ?>)</button>
        </form>
      </div>

      <!-- tombol hamburger mobile -->
      <button type="button" id="admin-menu-toggle" aria-expanded="false" aria-controls="admin-mobile-menu" aria-label="Buka menu"
              class="md:hidden ml-auto p-2.5 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
        <svg id="admin-menu-icon-open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
        <svg id="admin-menu-icon-close" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6 hidden"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
      </button>
    </div>
  </div>

  <!-- menu mobile -->
  <nav id="admin-mobile-menu" class="md:hidden hidden border-t border-gray-200 bg-white max-h-[calc(100vh-4rem)] overflow-y-auto">
    <div class="px-4 py-3 space-y-0.5">
      <a href="<?= site_url('admin') ?>" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-brand">Dashboard</a>
      <a href="<?= site_url('admin/produk') ?>" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-brand">Produk</a>
      <a href="<?= site_url('admin/kategori') ?>" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-brand">Kategori</a>
      <a href="<?= site_url('admin/pesanan') ?>" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-brand">Pesanan</a>
      <a href="<?= site_url('admin/kelola-admin') ?>" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-brand">Kelola Admin</a>

      <div class="pt-3 mt-3 border-t border-gray-100 space-y-0.5">
        <a href="<?= site_url('') ?>" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-brand">Lihat toko ↗</a>
        <?= form_open('admin/logout') ?>
          <button type="submit" class="w-full text-left px-3 py-2.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50">Logout (<?= esc($admin['username']) ?>)</button>
        </form>
      </div>
    </div>
  </nav>
</header>

<main class="flex-1 max-w-6xl mx-auto px-4 py-6 w-full">

<?php
$flash_success = $this->session->flashdata('success');
$flash_error   = $this->session->flashdata('error');
?>
<?php if ($flash_success): ?>
  <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm mb-5" role="status">
    <?= esc($flash_success) ?>
  </div>
<?php endif; ?>
<?php if ($flash_error): ?>
  <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm mb-5" role="alert">
    <?= esc($flash_error) ?>
  </div>
<?php endif; ?>
