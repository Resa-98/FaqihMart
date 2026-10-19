<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="mb-5">
  <h1 class="text-2xl font-bold tracking-tight text-gray-900">Kelola Admin</h1>
  <p class="text-sm text-gray-500 mt-0.5">Nomor WhatsApp konfirmasi, kredensial akun, dan daftar akun admin.</p>
</div>

<div class="grid lg:grid-cols-2 gap-5 items-start">

  <!-- Nomor WhatsApp konfirmasi -->
  <section class="bg-white border border-gray-200 rounded-xl p-5 sm:p-6">
    <h2 class="font-semibold text-gray-900">Nomor WhatsApp Konfirmasi</h2>
    <p class="text-sm text-gray-500 mt-1">
      Nomor tujuan yang dihubungi pembeli untuk mengonfirmasi pesanan — dipakai pada tombol
      <em>Konfirmasi via WhatsApp</em> di halaman konfirmasi pesanan.
    </p>

    <div class="mt-4 flex flex-wrap items-center gap-x-2 gap-y-1 bg-gray-50 border border-gray-100 rounded-lg px-3.5 py-2.5">
      <span class="text-xs font-medium text-gray-500">Saat ini:</span>
      <span class="text-sm font-bold text-gray-900"><?= esc($wa_display) ?></span>
      <a href="<?= esc(wa_link($wa_number, 'Halo ' . $store_name . ', saya ingin bertanya.')) ?>"
         target="_blank" rel="noopener noreferrer" class="text-xs font-medium text-brand hover:underline">uji tautan ↗</a>
    </div>

    <?= form_open('admin/kelola-admin/whatsapp', array('class' => 'mt-5')) ?>
      <label for="store_whatsapp" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor WhatsApp <span class="text-red-500">*</span></label>
      <div class="flex flex-col sm:flex-row gap-2">
        <input type="tel" id="store_whatsapp" name="store_whatsapp" required maxlength="20"
               value="<?= esc($wa_number) ?>"
               class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand"
               placeholder="08123456789">
        <button type="submit" class="shrink-0 bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
          Simpan Nomor
        </button>
      </div>
      <p class="text-xs text-gray-500 mt-1.5">
        Format yang diterima: <span class="font-mono">08123456789</span>, <span class="font-mono">6281234567890</span>,
        atau <span class="font-mono">+62 812-3456-7890</span>. Disimpan sebagai <span class="font-mono">62…</span>.
      </p>
    </form>
  </section>

  <!-- Ubah kredensial akun sendiri -->
  <section class="bg-white border border-gray-200 rounded-xl p-5 sm:p-6">
    <h2 class="font-semibold text-gray-900">Ubah Kredensial Saya</h2>
    <p class="text-sm text-gray-500 mt-1">
      Ganti <strong>username</strong> dan/atau <strong>password</strong> untuk akun yang sedang Anda pakai login.
      Kosongkan kolom password baru bila hanya ingin mengganti username.
    </p>

    <?= form_open('admin/kelola-admin/akun', array('class' => 'mt-5 space-y-4')) ?>
      <div>
        <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
        <input type="text" id="username" name="username" required maxlength="60"
               value="<?= esc($admin['username']) ?>" autocomplete="username"
               class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand">
      </div>

      <div>
        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1.5">Password Saat ini <span class="text-red-500">*</span></label>
        <input type="password" id="current_password" name="current_password" required autocomplete="current-password"
               class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand">
        <p class="text-xs text-gray-500 mt-1">Diminta untuk mengonfirmasi bahwa benar Anda pemilik akun.</p>
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru <span class="text-gray-400 font-normal">(opsional)</span></label>
          <input type="password" id="new_password" name="new_password" minlength="8" autocomplete="new-password"
                 class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand"
                 placeholder="Minimal 8 karakter">
        </div>
        <div>
          <label for="new_password_confirm" class="block text-sm font-medium text-gray-700 mb-1.5">Ulangi Password Baru</label>
          <input type="password" id="new_password_confirm" name="new_password_confirm" minlength="8" autocomplete="new-password"
                 class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand">
        </div>
      </div>

      <button type="submit" class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
        Simpan Perubahan
      </button>
    </form>
  </section>

</div>

<!-- Daftar akun admin -->
<section class="bg-white border border-gray-200 rounded-xl overflow-hidden mt-5">
  <div class="px-4 sm:px-5 py-3.5 border-b border-gray-100 flex items-center justify-between gap-3">
    <h2 class="font-semibold text-gray-900">Daftar Akun Admin</h2>
    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full"><?= (int) $total ?> akun</span>
  </div>

  <!-- tabel (>= sm) -->
  <div class="hidden sm:block overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 text-xs font-medium text-gray-500">
        <tr>
          <th class="text-left px-4 py-2.5">Username</th>
          <th class="text-left px-4 py-2.5">Dibuat</th>
          <th class="text-right px-4 py-2.5">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php foreach ($admins as $a): $is_me = ((int) $a->id === (int) $admin['id']); ?>
          <tr class="hover:bg-gray-50/70">
            <td class="px-4 py-2.5">
              <span class="font-medium text-gray-900"><?= esc($a->username) ?></span>
              <?php if ($is_me): ?>
                <span class="ml-1.5 text-xs font-medium bg-brand-light text-brand px-2 py-0.5 rounded-full">Anda</span>
              <?php endif; ?>
            </td>
            <td class="px-4 py-2.5 text-xs text-gray-500">
              <?= !empty($a->created_at) ? esc(date('d/m/Y H:i', strtotime($a->created_at))) : '—' ?>
            </td>
            <td class="px-4 py-2.5 text-right whitespace-nowrap">
              <?php if ($is_me): ?>
                <span class="text-xs text-gray-400">Akun aktif</span>
              <?php else: ?>
                <?= form_open('admin/kelola-admin/hapus/' . (int) $a->id, array('class' => 'inline', 'onsubmit' => "return confirm('Hapus akun admin ini?')")) ?>
                  <button type="submit" class="text-red-600 text-xs font-medium hover:underline">Hapus</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- kartu (< sm) -->
  <ul class="sm:hidden divide-y divide-gray-100">
    <?php foreach ($admins as $a): $is_me = ((int) $a->id === (int) $admin['id']); ?>
      <li class="px-4 py-3.5">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <p class="font-medium text-gray-900">
              <?= esc($a->username) ?>
              <?php if ($is_me): ?>
                <span class="ml-1.5 text-xs font-medium bg-brand-light text-brand px-2 py-0.5 rounded-full">Anda</span>
              <?php endif; ?>
            </p>
            <p class="text-xs text-gray-400 mt-0.5">
              <?= !empty($a->created_at) ? esc(date('d/m/Y H:i', strtotime($a->created_at))) : '—' ?>
            </p>
          </div>
          <div class="shrink-0 pt-0.5">
            <?php if ($is_me): ?>
              <span class="text-xs text-gray-400">Akun aktif</span>
            <?php else: ?>
              <?= form_open('admin/kelola-admin/hapus/' . (int) $a->id, array('class' => 'inline', 'onsubmit' => "return confirm('Hapus akun admin ini?')")) ?>
                <button type="submit" class="text-red-600 text-xs font-medium hover:underline">Hapus</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>

  <!-- tambah akun -->
  <details class="border-t border-gray-100">
    <summary class="px-4 sm:px-5 py-3.5 cursor-pointer text-sm font-semibold text-brand hover:bg-gray-50 select-none">
      + Tambah Akun Admin
    </summary>
    <div class="px-4 sm:px-5 pb-5 pt-1">
      <?= form_open('admin/kelola-admin/tambah') ?>
        <div class="grid sm:grid-cols-3 gap-4">
          <div>
            <label for="new_username" class="block text-sm font-medium text-gray-700 mb-1.5">Username <span class="text-red-500">*</span></label>
            <input type="text" id="new_username" name="username" required maxlength="60"
                   autocomplete="off"
                   class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand"
                   placeholder="contoh: kasir2">
          </div>
          <div>
            <label for="new_admin_password" class="block text-sm font-medium text-gray-700 mb-1.5">Password <span class="text-red-500">*</span></label>
            <input type="password" id="new_admin_password" name="password" required minlength="8"
                   autocomplete="new-password"
                   class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand"
                   placeholder="Minimal 8 karakter">
          </div>
          <div>
            <label for="new_admin_password_confirm" class="block text-sm font-medium text-gray-700 mb-1.5">Ulangi Password <span class="text-red-500">*</span></label>
            <input type="password" id="new_admin_password_confirm" name="password_confirm" required minlength="8"
                   autocomplete="new-password"
                   class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand">
          </div>
        </div>
        <div class="mt-4 flex items-center gap-4">
          <button type="submit" class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
            Tambah Akun
          </button>
          <span class="text-xs text-gray-500">Username hanya huruf, angka, titik, garis bawah, tanda hubung (3–60 karakter).</span>
        </div>
      </form>
    </div>
  </details>
</section>
