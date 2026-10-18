<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($title) ?> — Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = { theme: { extend: { colors: { brand: { DEFAULT: '#16A34A', dark: '#15803D' }, accent: '#F59E0B' } } } }
</script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white border border-gray-200 rounded-lg p-8 w-full max-w-sm shadow">
  <h1 class="text-xl font-bold text-center text-brand">Faqih Mart</h1>
  <p class="text-sm text-gray-500 text-center mt-1 mb-6">Login Admin</p>

  <?php if (!empty($error)): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-3 py-2 rounded-md mb-4" role="alert">
      <?= esc($error) ?>
    </div>
  <?php endif; ?>

  <?= form_open('admin/login/proses') ?>
    <div class="space-y-4">
      <div>
        <label for="username" class="block text-sm font-medium mb-1">Username</label>
        <input type="text" id="username" name="username" required autofocus autocomplete="username"
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand">
      </div>
      <div>
        <label for="password" class="block text-sm font-medium mb-1">Password</label>
        <input type="password" id="password" name="password" required autocomplete="current-password"
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand">
      </div>
      <button type="submit"
              class="w-full bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2.5 rounded-md transition-colors">
        Masuk
      </button>
    </div>
  </form>

  <p class="text-xs text-gray-400 text-center mt-6"><a href="<?= site_url('') ?>" class="hover:text-brand">← Kembali ke toko</a></p>
</div>

</body>
</html>
