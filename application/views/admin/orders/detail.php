<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
  <div>
    <nav class="text-xs text-gray-500 mb-1"><a href="<?= site_url('admin/pesanan') ?>" class="hover:text-brand">← Semua pesanan</a></nav>
    <h1 class="text-xl font-bold font-mono"><?= esc($order->order_number) ?></h1>
  </div>
  <span class="text-sm px-3 py-1 rounded-full <?= order_status_badge($order->status) ?>">
    <?= esc($statuses[$order->status] ?? $order->status) ?>
  </span>
</div>

<div class="grid lg:grid-cols-3 gap-5 items-start">
  <!-- item + ringkasan -->
  <div class="lg:col-span-2 bg-white border border-gray-200 rounded-lg p-5">
    <h2 class="font-semibold text-sm mb-3">Item Pesanan</h2>
    <table class="w-full text-sm">
      <thead class="text-xs text-gray-500 border-b">
        <tr>
          <th class="text-left py-2">Produk (nama saat dipesan)</th>
          <th class="text-right py-2">Harga</th>
          <th class="text-right py-2">Qty</th>
          <th class="text-right py-2">Subtotal</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        <?php foreach ($items as $item): ?>
          <tr>
            <td class="py-2.5">
              <?= esc($item->product_name) ?>
              <?php if ($item->product_id === NULL): ?>
                <span class="text-xs text-gray-400">(produk sudah dihapus)</span>
              <?php endif; ?>
            </td>
            <td class="py-2.5 text-right rp"><?= rupiah($item->price) ?></td>
            <td class="py-2.5 text-right"><?= (int) $item->quantity ?></td>
            <td class="py-2.5 text-right rp"><?= rupiah($item->subtotal) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot class="border-t-2">
        <tr>
          <td colspan="3" class="py-3 text-right font-semibold text-sm">Total</td>
          <td class="py-3 text-right rp font-bold text-brand"><?= rupiah($order->total_amount) ?></td>
        </tr>
      </tfoot>
    </table>

    <!-- ubah status -->
    <div class="border-t mt-5 pt-4">
      <h2 class="font-semibold text-sm mb-2">Ubah Status</h2>
      <?php if (empty($next_states)): ?>
        <p class="text-xs text-gray-500 bg-gray-50 border border-gray-200 rounded-md px-3 py-2">
          Status <strong><?= esc($statuses[$order->status]) ?></strong> adalah status akhir — tidak bisa diubah lagi.
        </p>
      <?php else: ?>
        <div class="flex flex-wrap gap-2">
          <?php foreach ($next_states as $state): ?>
            <?= form_open('admin/pesanan/' . rawurlencode($order->order_number) . '/status', array('class' => 'inline')) ?>
              <input type="hidden" name="status" value="<?= esc($state) ?>">
              <?php if ($state === 'dibatalkan'): ?>
                <button type="submit" class="text-xs bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-3 py-1.5 rounded-md">
                  Tandai Dibatalkan (stok kembali)
                </button>
              <?php else: ?>
                <button type="submit" class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-3 py-1.5 rounded-md"
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
  <div class="bg-white border border-gray-200 rounded-lg p-5 text-sm space-y-4">
    <div>
      <p class="text-xs text-gray-400">Tanggal pesanan</p>
      <p><?= date('d/m/Y H:i', strtotime($order->created_at)) ?> WIB</p>
    </div>
    <div>
      <p class="text-xs text-gray-400">Nama pelanggan</p>
      <p class="font-medium"><?= esc($order->customer_name) ?></p>
    </div>
    <div>
      <p class="text-xs text-gray-400">WhatsApp</p>
      <p class="font-mono"><?= esc($order->whatsapp) ?></p>
    </div>
    <div>
      <p class="text-xs text-gray-400">Alamat pengiriman</p>
      <p class="whitespace-pre-line"><?= esc($order->address) ?></p>
    </div>
    <?php if (!empty($order->note)): ?>
      <div>
        <p class="text-xs text-gray-400">Catatan pelanggan</p>
        <p class="bg-amber-50 border border-amber-100 rounded-md p-2 whitespace-pre-line"><?= esc($order->note) ?></p>
      </div>
    <?php endif; ?>
  </div>
</div>
