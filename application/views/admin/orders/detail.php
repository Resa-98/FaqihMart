<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
  <div>
    <nav class="text-xs text-gray-500 mb-1"><a href="<?= site_url('admin/pesanan') ?>" class="hover:text-brand">← Semua pesanan</a></nav>
    <h1 class="text-2xl font-bold tracking-tight font-mono text-gray-900"><?= esc($order->order_number) ?></h1>
  </div>
  <div class="flex items-center gap-2">
    <a href="<?= site_url('admin/pesanan/' . rawurlencode($order->order_number) . '/struk') ?>" target="_blank" rel="noopener"
       class="inline-flex items-center gap-1.5 text-xs font-medium border border-gray-300 hover:bg-gray-50 text-gray-700 px-3 py-1.5 rounded-lg transition-colors">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
      Cetak Struk
    </a>
    <span class="text-sm font-medium px-3 py-1 rounded-full <?= order_status_badge($order->status) ?>">
      <?= esc($statuses[$order->status] ?? $order->status) ?>
    </span>
  </div>
</div>

<div class="grid lg:grid-cols-3 gap-5 items-start">
  <!-- item + ringkasan -->
  <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl p-5">
    <h2 class="font-semibold text-gray-900 mb-3">Item Pesanan</h2>
    <div class="overflow-x-auto -mx-5 px-5">
    <table class="w-full text-sm min-w-[420px]">
      <thead class="text-xs font-medium text-gray-500 border-b border-gray-100">
        <tr>
          <th class="text-left py-2">Produk (nama saat dipesan)</th>
          <th class="text-right py-2">Harga</th>
          <th class="text-right py-2">Qty</th>
          <th class="text-right py-2">Subtotal</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php foreach ($items as $item): ?>
          <tr>
            <td class="py-2.5">
              <?= esc($item->product_name) ?>
              <?php if ($item->product_id === NULL): ?>
                <span class="text-xs text-gray-400">(produk sudah dihapus)</span>
              <?php endif; ?>
            </td>
            <td class="py-2.5 text-right rp"><?= rupiah($item->price) ?></td>
            <td class="py-2.5 text-right tabular-nums"><?= (int) $item->quantity ?></td>
            <td class="py-2.5 text-right rp"><?= rupiah($item->subtotal) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot class="border-t-2 border-gray-200">
        <tr>
          <td colspan="3" class="py-3 text-right font-semibold text-sm text-gray-900">Total</td>
          <td class="py-3 text-right rp font-bold text-brand"><?= rupiah($order->total_amount) ?></td>
        </tr>
      </tfoot>
    </table>
    </div>

    <!-- ubah status -->
    <div class="border-t border-gray-100 mt-5 pt-4">
      <h2 class="font-semibold text-gray-900 mb-2">Ubah Status</h2>
      <?php if (empty($next_states)): ?>
        <p class="text-xs text-gray-500 bg-gray-50 border border-gray-200 rounded-lg px-3 py-2">
          Status <strong><?= esc($statuses[$order->status]) ?></strong> adalah status akhir — tidak bisa diubah lagi.
        </p>
      <?php else: ?>
        <div class="flex flex-wrap gap-2">
          <?php foreach ($next_states as $state): ?>
            <?= form_open('admin/pesanan/' . rawurlencode($order->order_number) . '/status', array('class' => 'inline')) ?>
              <input type="hidden" name="status" value="<?= esc($state) ?>">
              <?php if ($state === 'dibatalkan'): ?>
                <button type="submit" class="text-xs font-medium bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-3 py-1.5 rounded-lg">
                  Tandai Dibatalkan (stok kembali)
                </button>
              <?php else: ?>
                <button type="submit" class="text-xs font-medium bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-3 py-1.5 rounded-lg"
                        onclick="return confirm('Ubah status pesanan ke <?= esc(addslashes($statuses[$state])) ?>?')">
                  → <?= esc($statuses[$state]) ?>
                </button>
              <?php endif; ?>
            </form>
          <?php endforeach; ?>
        </div>
        <p class="text-xs text-gray-400 mt-2">Pembukaan/mengirim pesan WhatsApp tidak mengubah status — hanya tombol di atas yang dilakukan admin.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- info pelanggan -->
  <div class="bg-white border border-gray-200 rounded-xl p-5 text-sm space-y-4">
    <div>
      <p class="text-xs font-medium text-gray-400">Tanggal pesanan</p>
      <p class="mt-0.5"><?= date('d/m/Y H:i', strtotime($order->created_at)) ?> WIB</p>
    </div>
    <div>
      <p class="text-xs font-medium text-gray-400">Nama pelanggan</p>
      <p class="font-medium text-gray-900 mt-0.5"><?= esc($order->customer_name) ?></p>
    </div>
    <div>
      <p class="text-xs font-medium text-gray-400">WhatsApp</p>
      <p class="font-mono mt-0.5"><?= esc($order->whatsapp) ?></p>
    </div>
    <div>
      <p class="text-xs font-medium text-gray-400">Alamat pengiriman</p>
      <p class="whitespace-pre-line mt-0.5"><?= esc($order->address) ?></p>
      <?php if ( ! empty($order->map_link)): ?>
        <a href="<?= esc($order->map_link) ?>" target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center gap-1.5 text-xs font-medium text-brand hover:underline mt-2">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
          Lihat titik lokasi (Google Maps)
        </a>
        <?php if ($order->latitude !== NULL && $order->longitude !== NULL): ?>
          <p class="text-xs text-gray-400 mt-1 font-mono"><?= esc($order->latitude) ?>, <?= esc($order->longitude) ?></p>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <?php if (!empty($order->note)): ?>
      <div>
        <p class="text-xs font-medium text-gray-400">Catatan pelanggan</p>
        <p class="bg-amber-50 border border-amber-100 rounded-lg p-2 whitespace-pre-line mt-0.5"><?= esc($order->note) ?></p>
      </div>
    <?php endif; ?>
  </div>
</div>
