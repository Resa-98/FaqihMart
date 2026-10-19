<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between mb-5">
  <div>
    <h1 class="text-2xl font-bold tracking-tight text-gray-900">Manajemen Pesanan</h1>
    <p class="text-sm text-gray-500 mt-0.5"><?= (int) $total ?> pesanan</p>
  </div>

  <!-- filter status -->
  <div class="flex flex-wrap gap-1.5 text-xs">
    <a href="<?= site_url('admin/pesanan') ?>"
       class="px-3 py-1.5 rounded-full border font-medium <?= $status === '' ? 'bg-brand text-white border-brand' : 'bg-white border-gray-300 text-gray-600 hover:border-brand hover:text-brand' ?>">Semua</a>
    <?php foreach ($statuses as $key => $label): ?>
      <a href="<?= site_url('admin/pesanan?status=' . $key) ?>"
         class="px-3 py-1.5 rounded-full border font-medium <?= $status === $key ? 'bg-brand text-white border-brand' : 'bg-white border-gray-300 text-gray-600 hover:border-brand hover:text-brand' ?>">
        <?= esc($label) ?>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

  <!-- tabel (>= sm) -->
  <div class="hidden sm:block overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 text-xs font-medium text-gray-500">
        <tr>
          <th class="text-left px-4 py-2.5">Nomor</th>
          <th class="text-left px-4 py-2.5">Pelanggan</th>
          <th class="text-left px-4 py-2.5">WhatsApp</th>
          <th class="text-right px-4 py-2.5">Total</th>
          <th class="text-center px-4 py-2.5">Status</th>
          <th class="text-right px-4 py-2.5">Tanggal</th>
          <th></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php if (empty($orders)): ?>
          <tr><td colspan="7" class="px-4 py-10 text-center text-gray-500 text-sm">Tidak ada pesanan<?= $status !== '' ? ' dengan status ini' : '' ?>.</td></tr>
        <?php endif; ?>
        <?php foreach ($orders as $o): ?>
          <tr class="hover:bg-gray-50/70">
            <td class="px-4 py-2.5 font-mono text-xs"><?= esc($o->order_number) ?></td>
            <td class="px-4 py-2.5 font-medium text-gray-900"><?= esc($o->customer_name) ?></td>
            <td class="px-4 py-2.5 text-xs text-gray-500"><?= esc($o->whatsapp) ?></td>
            <td class="px-4 py-2.5 text-right rp"><?= rupiah($o->total_amount) ?></td>
            <td class="px-4 py-2.5 text-center">
              <span class="text-xs font-medium px-2 py-0.5 rounded-full <?= order_status_badge($o->status) ?>">
                <?= esc($statuses[$o->status] ?? $o->status) ?>
              </span>
            </td>
            <td class="px-4 py-2.5 text-right text-xs text-gray-500"><?= date('d/m/Y H:i', strtotime($o->created_at)) ?></td>
            <td class="px-4 py-2.5 text-right">
              <a href="<?= site_url('admin/pesanan/' . $o->order_number) ?>" class="text-brand text-xs font-medium hover:underline">Detail →</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- kartu (< sm) -->
  <ul class="sm:hidden divide-y divide-gray-100">
    <?php if (empty($orders)): ?>
      <li class="px-4 py-10 text-center text-gray-500 text-sm">Tidak ada pesanan<?= $status !== '' ? ' dengan status ini' : '' ?>.</li>
    <?php endif; ?>
    <?php foreach ($orders as $o): ?>
      <li>
        <a href="<?= site_url('admin/pesanan/' . $o->order_number) ?>" class="block p-4 active:bg-gray-50">
          <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
              <p class="font-mono text-xs text-gray-500"><?= esc($o->order_number) ?></p>
              <p class="font-medium text-gray-900 truncate mt-0.5"><?= esc($o->customer_name) ?></p>
              <p class="text-xs text-gray-400 mt-0.5"><?= date('d/m/Y H:i', strtotime($o->created_at)) ?> · <?= esc($o->whatsapp) ?></p>
            </div>
            <div class="text-right shrink-0">
              <p class="rp font-semibold text-gray-900"><?= rupiah($o->total_amount) ?></p>
              <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full mt-1 <?= order_status_badge($o->status) ?>">
                <?= esc($statuses[$o->status] ?? $o->status) ?>
              </span>
            </div>
          </div>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php if ($pages > 1): ?>
  <div class="flex flex-wrap justify-center gap-1 p-4 border-t border-gray-100">
    <?php for ($n = 1; $n <= $pages; $n++): ?>
      <?php $qs = http_build_query(array_filter(array('status' => $status, 'page' => $n > 1 ? $n : NULL))); ?>
      <?php if ($n === $page): ?>
        <span class="px-3 py-1.5 rounded-lg border border-brand bg-brand text-white text-xs font-medium tabular-nums"><?= $n ?></span>
      <?php else: ?>
        <a href="<?= site_url('admin/pesanan' . ($qs !== '' ? '?' . $qs : '')) ?>" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-xs text-gray-600 hover:border-brand hover:text-brand tabular-nums"><?= $n ?></a>
      <?php endif; ?>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>
