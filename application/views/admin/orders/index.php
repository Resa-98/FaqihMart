<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
  <div>
    <h1 class="text-xl font-bold">Manajemen Pesanan</h1>
    <p class="text-sm text-gray-500"><?= (int) $total ?> pesanan</p>
  </div>

  <!-- filter status -->
  <div class="flex flex-wrap gap-1.5 text-xs">
    <a href="<?= site_url('admin/pesanan') ?>"
       class="px-3 py-1.5 rounded-full border <?= $status === '' ? 'bg-brand text-white border-brand' : 'bg-white border-gray-300 hover:border-brand' ?>">Semua</a>
    <?php foreach ($statuses as $key => $label): ?>
      <a href="<?= site_url('admin/pesanan?status=' . $key) ?>"
         class="px-3 py-1.5 rounded-full border <?= $status === $key ? 'bg-brand text-white border-brand' : 'bg-white border-gray-300 hover:border-brand' ?>">
        <?= esc($label) ?>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<div class="bg-white border border-gray-200 rounded-lg overflow-x-auto">
  <table class="w-full text-sm min-w-[680px]">
    <thead class="bg-gray-50 text-xs text-gray-500">
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
    <tbody class="divide-y">
      <?php if (empty($orders)): ?>
        <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500 text-sm">Tidak ada pesanan<?= $status !== '' ? ' dengan status ini' : '' ?>.</td></tr>
      <?php endif; ?>
      <?php foreach ($orders as $o): ?>
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-2.5 font-mono text-xs"><?= esc($o->order_number) ?></td>
          <td class="px-4 py-2.5"><?= esc($o->customer_name) ?></td>
          <td class="px-4 py-2.5 text-xs text-gray-500"><?= esc($o->whatsapp) ?></td>
          <td class="px-4 py-2.5 text-right rp"><?= rupiah($o->total_amount) ?></td>
          <td class="px-4 py-2.5 text-center">
            <span class="text-xs px-2 py-0.5 rounded-full <?= order_status_badge($o->status) ?>">
              <?= esc($statuses[$o->status] ?? $o->status) ?>
            </span>
          </td>
          <td class="px-4 py-2.5 text-right text-xs text-gray-500"><?= date('d/m/Y H:i', strtotime($o->created_at)) ?></td>
          <td class="px-4 py-2.5 text-right">
            <a href="<?= site_url('admin/pesanan/' . $o->order_number) ?>" class="text-brand text-xs hover:underline">Detail →</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php if ($pages > 1): ?>
  <div class="flex justify-center gap-1 p-4 border-t">
    <?php for ($n = 1; $n <= $pages; $n++): ?>
      <?php $qs = http_build_query(array_filter(array('status' => $status, 'page' => $n > 1 ? $n : NULL))); ?>
      <?php if ($n === $page): ?>
        <span class="px-3 py-1 rounded border border-brand bg-brand text-white text-xs"><?= $n ?></span>
      <?php else: ?>
        <a href="<?= site_url('admin/pesanan' . ($qs !== '' ? '?' . $qs : '')) ?>" class="px-3 py-1 rounded border bg-white text-xs hover:border-brand"><?= $n ?></a>
      <?php endif; ?>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>
