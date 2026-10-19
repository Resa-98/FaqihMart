<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
/**
 * Struk digital yang bisa dicetak (print). Dipakai halaman konfirmasi
 * pelanggan DAN panel admin. Sengaja berdiri sendiri (tanpa layout situs)
 * agar hasil cetak bersih; CSS ditulis inline supaya tidak bergantung CDN.
 *
 * Input: $store_name, $store_area, $order, $items, $statuses, $back_url, $back_label
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Struk <?= esc($order->order_number) ?> — <?= esc($store_name) ?></title>
<style>
  :root { --ink:#111827; --muted:#6b7280; --line:#e5e7eb; --brand:#16A34A; }
  * { box-sizing: border-box; }
  body {
    margin: 0; padding: 24px 16px; background: #f3f4f6; color: var(--ink);
    font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
    font-size: 14px; line-height: 1.5;
  }
  .sheet {
    max-width: 420px; margin: 0 auto; background: #fff; border: 1px solid var(--line);
    border-radius: 16px; padding: 24px; box-shadow: 0 4px 16px rgba(0,0,0,.06);
  }
  .brand { text-align: center; border-bottom: 1px dashed var(--line); padding-bottom: 14px; }
  .brand img { height: 40px; width: auto; margin: 0 auto 6px; display: block; }
  .brand .name { font-size: 18px; font-weight: 800; letter-spacing: .3px; }
  .brand .area { color: var(--muted); font-size: 12px; }
  .meta { display: flex; justify-content: space-between; gap: 12px; padding: 14px 0; border-bottom: 1px dashed var(--line); }
  .meta div { min-width: 0; }
  .meta .k { color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: .5px; }
  .meta .v { font-weight: 600; word-break: break-word; }
  .meta .v.mono { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
  .status {
    display: inline-block; margin-top: 4px; font-size: 12px; font-weight: 700;
    padding: 2px 10px; border-radius: 999px; background: #f0fdf4; color: var(--brand); border: 1px solid #bbf7d0;
  }
  table { width: 100%; border-collapse: collapse; margin-top: 14px; }
  th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; color: var(--muted); padding: 6px 0; border-bottom: 1px solid var(--line); }
  td { padding: 8px 0; border-bottom: 1px dashed var(--line); vertical-align: top; }
  td.r, th.r { text-align: right; white-space: nowrap; }
  .qty { color: var(--muted); font-size: 12px; }
  .total { display: flex; justify-content: space-between; align-items: center; margin-top: 14px; padding-top: 12px; border-top: 2px solid var(--ink); }
  .total .k { font-weight: 700; }
  .total .v { font-weight: 800; font-size: 18px; color: var(--brand); }
  .block { margin-top: 14px; padding-top: 12px; border-top: 1px dashed var(--line); }
  .block .k { color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 3px; }
  a { color: var(--brand); }
  .note { margin-top: 16px; text-align: center; color: var(--muted); font-size: 11px; }
  .actions { max-width: 420px; margin: 16px auto 0; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
  .btn { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; padding: 9px 16px; border-radius: 10px; cursor: pointer; text-decoration: none; border: 1px solid transparent; }
  .btn-primary { background: var(--brand); color: #fff; }
  .btn-ghost { background: #fff; color: var(--ink); border-color: var(--line); }
  @media print {
    body { background: #fff; padding: 0; }
    .sheet { border: none; border-radius: 0; box-shadow: none; max-width: none; padding: 0 8px; }
    .actions { display: none !important; }
  }
</style>
</head>
<body>

<div class="sheet">
  <div class="brand">
    <img src="<?= base_url('assets/images/brand/primary-logo.webp') ?>" alt="<?= esc($store_name) ?>"
         onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
    <div class="name" style="display:none"><?= esc($store_name) ?></div>
    <div class="area"><?= esc($store_area) ?></div>
  </div>

  <div class="meta">
    <div>
      <div class="k">No. Pesanan</div>
      <div class="v mono"><?= esc($order->order_number) ?></div>
      <span class="status"><?= esc($statuses[$order->status] ?? $order->status) ?></span>
    </div>
    <div style="text-align:right">
      <div class="k">Tanggal</div>
      <div class="v"><?= esc(date('d/m/Y H:i', strtotime($order->created_at))) ?></div>
    </div>
  </div>

  <div class="block" style="border-top:none;padding-top:0">
    <div class="k">Pemesan</div>
    <div><strong><?= esc($order->customer_name) ?></strong></div>
    <div>WA: <?= esc($order->whatsapp) ?></div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Item</th>
        <th class="r">Jumlah</th>
        <th class="r">Harga</th>
        <th class="r">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
        <tr>
          <td><?= esc($item->product_name) ?></td>
          <td class="r qty"><?= (int) $item->quantity ?></td>
          <td class="r"><?= rupiah($item->price) ?></td>
          <td class="r"><?= rupiah($item->subtotal) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="total">
    <span class="k">TOTAL</span>
    <span class="v"><?= rupiah($order->total_amount) ?></span>
  </div>

  <div class="block">
    <div class="k">Alamat Pengiriman</div>
    <div><?= nl2br(esc($order->address)) ?></div>
    <?php if (!empty($order->map_link)): ?>
      <div style="margin-top:6px">
        <a href="<?= esc($order->map_link) ?>" target="_blank" rel="noopener noreferrer">Buka titik lokasi di Google Maps</a>
        <?php if ($order->latitude !== NULL && $order->longitude !== NULL): ?>
          <div class="qty" style="margin-top:2px">Koordinat: <?= esc($order->latitude) ?>, <?= esc($order->longitude) ?></div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

  <?php if ( ! empty($order->note)): ?>
    <div class="block">
      <div class="k">Catatan</div>
      <div><?= nl2br(esc($order->note)) ?></div>
    </div>
  <?php endif; ?>

  <div class="note">
    Struk digital — bukan bukti pembayaran.<br>
    Konfirmasi dan pembayaran dilakukan langsung dengan toko.
  </div>
</div>

<div class="actions">
  <button type="button" class="btn btn-primary" onclick="window.print()">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
    Cetak
  </button>
  <a class="btn btn-ghost" href="<?= esc($back_url) ?>"><?= esc($back_label) ?></a>
</div>

</body>
</html>
