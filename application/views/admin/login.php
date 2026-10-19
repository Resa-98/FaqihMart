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
<style>html { -webkit-text-size-adjust: 100%; }</style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4 antialiased">

<div class="bg-white border border-gray-200 rounded-xl p-8 w-full max-w-sm shadow-sm">
  <div class="flex flex-col items-center mb-6">
    <img src="<?= base_url('assets/images/brand/primary-logo.webp') ?>" alt="<?= esc($store_name) ?>" class="h-14 w-auto">
    <p class="text-sm font-medium text-gray-400 mt-2">Panel Admin</p>
  </div>

  <?php if (!empty($error)): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-3.5 py-2.5 rounded-lg mb-4" role="alert">
      <?= esc($error) ?>
    </div>
  <?php endif; ?>

  <?= form_open('admin/login/proses') ?>
    <div class="space-y-4">
      <div>
        <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
        <input type="text" id="username" name="username" required autofocus autocomplete="username"
               class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand">
      </div>
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
        <input type="password" id="password" name="password" required autocomplete="current-password"
               class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand">
      </div>
      <button type="submit"
              class="w-full bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors">
        Masuk
      </button>
    </div>
  </form>

  <p class="text-xs text-gray-400 text-center mt-6"><a href="<?= site_url('') ?>" class="hover:text-brand">← Kembali ke toko</a></p>
</div>

</body>
</html>
