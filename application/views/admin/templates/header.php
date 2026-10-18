<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($title) ?> — Admin <?= esc($store_name) ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = { theme: { extend: { colors: { brand: { DEFAULT: '#16A34A', dark: '#15803D' }, accent: '#F59E0B' } } } }
</script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">

<header class="bg-brand text-white shadow">
  <div class="max-w-6xl mx-auto px-4 py-3 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm">
    <a href="<?= site_url('admin') ?>" class="font-bold text-base">🛒 <?= esc($store_name) ?> <span class="opacity-70 font-normal">/ Admin</span></a>
    <nav class="flex flex-wrap gap-x-4 gap-y-1">
      <a href="<?= site_url('admin') ?>" class="hover:underline">Dashboard</a>
      <a href="<?= site_url('admin/produk') ?>" class="hover:underline">Produk</a>
      <a href="<?= site_url('admin/kategori') ?>" class="hover:underline">Kategori</a>
      <a href="<?= site_url('admin/pesanan') ?>" class="hover:underline">Pesanan</a>
    </nav>
    <div class="flex items-center gap-3 ml-auto">
      <a href="<?= site_url('') ?>" class="opacity-80 hover:opacity-100 hover:underline">Lihat toko ↗</a>
      <?= form_open('admin/logout', array('class' => 'inline')) ?>
        <button type="submit" class="bg-white/15 hover:bg-white/25 px-3 py-1 rounded-md text-xs">Logout (<?= esc($admin['username']) ?>)</button>
      </form>
    </div>
  </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-6">

<?php
$flash_success = $this->session->flashdata('success');
$flash_error   = $this->session->flashdata('error');
?>
<?php if ($flash_success): ?>
  <div class="bg-green-100 border border-green-300 text-green-800 border px-4 py-3 rounded-md text-sm mb-5" role="status">
    <?= esc($flash_success) ?>
  </div>
<?php endif; ?>
<?php if ($flash_error): ?>
  <div class="bg-red-100 border border-red-300 text-red-800 border px-4 py-3 rounded-md text-sm mb-5" role="alert">
    <?= esc($flash_error) ?>
  </div>
<?php endif; ?>
