<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<h1 class="text-2xl font-bold tracking-tight text-gray-900 mb-5"><?= esc($title) ?></h1>

<?php if (isset($mode) && $mode === 'buy_now'): ?>
  <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3 rounded-lg mb-5 flex flex-wrap items-center justify-between gap-3">
    <span><strong>Mode Beli Sekarang</strong> — checkout hanya untuk item ini. Keranjang Anda tidak berubah.</span>
    <?= form_open('checkout/batal-beli', array('class' => 'inline')) ?>
      <button type="submit" class="text-xs font-semibold underline hover:no-underline">Batal Beli Sekarang</button>
    </form>
  </div>
<?php endif; ?>

<div class="grid lg:grid-cols-3 gap-6 items-start">
  <!-- form -->
  <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl p-5 sm:p-6">
    <?= form_open('checkout/proses') ?>
      <?php $vf = validation_errors(); ?>
      <?php if ($vf): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg mb-4" role="alert">
          <?= $vf /* CI3 validation_errors meng-escape label; pesan runtime dari form rule — aman */ ?>
        </div>
      <?php endif; ?>

      <div class="space-y-4">
        <div>
          <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
          <input type="text" id="customer_name" name="customer_name" required maxlength="150"
                 value="<?= esc(set_value('customer_name')) ?>"
                 class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand"
                 placeholder="Nama Anda">
        </div>

        <div>
          <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-1.5">Nomor WhatsApp <span class="text-red-500">*</span></label>
          <input type="tel" id="whatsapp" name="whatsapp" required maxlength="20"
                 value="<?= esc(set_value('whatsapp')) ?>"
                 class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand"
                 placeholder="08xxxxxxxxxx">
          <p class="text-xs text-gray-500 mt-1">Nomor seluler Indonesia, mis. <span class="font-mono">08123456789</span> atau <span class="font-mono">+62 812-3456-789</span>. Dipakai toko untuk menghubungi Anda tentang pesanan ini.</p>
        </div>

        <div>
          <label for="address" class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Pengiriman <span class="text-red-500">*</span></label>
          <textarea id="address" name="address" required rows="3" maxlength="500"
                    class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand"
                    placeholder="Nama perumahan, blok, nomor rumah, RT/RW, patokan"><?= esc(set_value('address')) ?></textarea>
        </div>

        <!-- lokasi via Google Maps -->
        <div class="border border-gray-200 rounded-xl p-4 bg-gray-50/60">
          <label for="map_link" class="block text-sm font-medium text-gray-700 mb-1.5">
            Titik Lokasi (opsional)
          </label>
          <p class="text-xs text-gray-500 mb-2">
            Bagikan lokasi Anda agar kurir lebih mudah menemukan alamat. Bisa dengan menekan tombol
            <em>Gunakan lokasi saya</em> (izinkan akses lokasi di browser) atau menempelkan link Google Maps.
          </p>
          <div class="flex flex-col sm:flex-row gap-2">
            <input type="url" id="map_link" name="map_link" maxlength="500"
                   value="<?= esc(set_value('map_link')) ?>"
                   class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand"
                   placeholder="https://maps.google.com/... atau https://maps.app.goo.gl/...">
            <button type="button" id="btn-geoloc"
                    class="shrink-0 inline-flex items-center justify-center gap-2 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
              Gunakan lokasi saya
            </button>
          </div>
          <input type="hidden" name="latitude" id="latitude" value="<?= esc(set_value('latitude')) ?>">
          <input type="hidden" name="longitude" id="longitude" value="<?= esc(set_value('longitude')) ?>">
          <p id="geo-status" class="text-xs text-gray-500 mt-2 hidden"></p>
        </div>

        <div>
          <label for="note" class="block text-sm font-medium text-gray-700 mb-1.5">Catatan (opsional)</label>
          <textarea id="note" name="note" rows="2" maxlength="500"
                    class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand"
                    placeholder="Contoh: titip ke satpam kalau tidak ada orang"><?= esc(set_value('note')) ?></textarea>
        </div>
      </div>

      <div class="mt-6 flex flex-col sm:flex-row sm:items-center gap-3">
        <button type="submit" class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-6 py-3 rounded-lg transition-colors">
          Buat Pesanan
        </button>
        <a href="<?= site_url('keranjang') ?>" class="text-sm font-medium text-gray-500 hover:text-brand text-center sm:text-left">← Kembali ke keranjang</a>
      </div>
    </form>
  </div>

  <!-- ringkasan -->
  <div class="bg-white border border-gray-200 rounded-xl p-5 lg:sticky lg:top-24">
    <h2 class="font-bold tracking-tight text-gray-900 mb-3">Ringkasan Belanja</h2>
    <ul class="text-sm space-y-2.5 mb-3 border-b border-gray-100 pb-3">
      <?php foreach ($payable as $line): $p = $line['product']; ?>
        <li class="flex justify-between gap-2">
          <span class="text-gray-600">
            <?= esc($p->name) ?>
            <span class="text-gray-400">×<?= (int) $line['qty'] ?></span>
            <?php if ($line['issue']): ?>
              <span class="block text-xs text-red-600"><?= esc($line['issue']) ?></span>
            <?php endif; ?>
          </span>
          <span class="rp font-medium text-gray-900 shrink-0"><?= rupiah($line['subtotal']) ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="flex justify-between items-center mb-4">
      <span class="text-gray-500 text-sm">Total</span>
      <span class="rp font-bold text-brand text-xl"><?= rupiah($total) ?></span>
    </div>
    <p class="text-xs text-gray-500 leading-relaxed">
      Harga dan total dihitung ulang oleh server saat pesanan dibuat.
      Setelah tersimpan, konfirmasikan lewat WhatsApp toko
      <a href="<?= esc(wa_link($wa, 'Halo ' . $store_name . ', saya ingin bertanya.')) ?>" target="_blank" rel="noopener noreferrer" class="text-brand font-medium hover:underline"><?= esc(wa_display($wa)) ?></a>.
    </p>
  </div>
</div>
