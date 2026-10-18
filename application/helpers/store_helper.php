<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * esc() — alias html_escape() gaya view (CI3 tidak punya esc() bawaan).
 * Semua output dinamatis wajib lewat ini.
 */
if ( ! function_exists('esc'))
{
	function esc($value)
	{
		return html_escape($value);
	}
}

/**
 * TRUE jika request POST — CI3 tidak punya is_post_request().
 */
if ( ! function_exists('is_post'))
{
	function is_post()
	{
		return strtolower(get_instance()->input->method()) === 'post';
	}
}

/**
 * Derive garis keranjang + total dari DB (dipakai Cart & Checkout).
 * $cart: session [product_id => qty]. Harga & stok selalu dari DB.
 *
 * @return array [$lines, $total]
 *   $lines[]: product (obj DB), qty, subtotal, issue (string|null)
 */
function cart_lines(array $cart)
{
	$CI =& get_instance();
	$CI->load->model('product_model');

	$lines = array();
	$total = 0.0;

	$cart = array_filter(array_map('intval', $cart), function ($q) {
		return $q > 0;
	});
	if (empty($cart))
	{
		return array($lines, $total);
	}

	$products = $CI->product_model->find_many(array_keys($cart));
	foreach ($products as $p)
	{
		$qty = min((int) $cart[$p->id], max(0, (int) $p->stock));
		$issue = NULL;
		if ((int) $p->is_active !== 1)
		{
			$issue = 'Tidak dijual lagi';
			$qty = 0;
		}
		elseif ((int) $cart[$p->id] > (int) $p->stock)
		{
			$issue = 'Jumlah dibatasi stok (' . (int) $p->stock . ')';
		}
		elseif ((int) $p->stock <= 0)
		{
			$issue = 'Stok habis';
		}

		$subtotal = (float) $p->price * $qty;
		$lines[] = array(
			'product'  => $p,
			'qty'      => $qty,
			'subtotal' => $subtotal,
			'issue'    => $issue,
		);
		$total += $subtotal;
	}

	// produk di session yang sudah tidak ada di DB: tidak dihitung (lenyap)
	return array($lines, $total);
}

/**
 * Format angka jadi Rupiah tanpa desimal: 10000 -> Rp10.000
 * Angka pecahan (bukan bulat) tetap ditampilkan 2 desimal.
 */
function rupiah($amount)
{
	$amount = (float) $amount;
	$decimals = (fmod($amount, 1) !== 0.0) ? 2 : 0;
	return 'Rp' . number_format($amount, $decimals, ',', '.');
}

/**
 * Link WhatsApp dengan pesan ter-encode.
 * $number: digit saja (format internasional tanpa '+').
 */
function wa_link($number, $message)
{
	$number = preg_replace('/\D/', '', (string) $number);
	return 'https://wa.me/' . $number . '?text=' . rawurlencode($message);
}

/**
 * Label status pesanan yang konsisten (satu sumber untuk tampilan & validasi).
 */
function order_statuses()
{
	return array(
		'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
		'diproses'            => 'Diproses',
		'dikirim'             => 'Dikirim',
		'selesai'             => 'Selesai',
		'dibatalkan'          => 'Dibatalkan',
	);
}

/**
 * Transisi status yang diizinkan — status harus mencerminkan proses nyata.
 */
function order_status_transitions()
{
	return array(
		'menunggu_konfirmasi' => array('diproses', 'dibatalkan'),
		'diproses'            => array('dikirim', 'dibatalkan'),
		'dikirim'             => array('selesai', 'dibatalkan'),
		'selesai'             => array(),
		'dibatalkan'          => array(),
	);
}

/**
 * Kelas warna Tailwind untuk badge status.
 */
function order_status_badge($status)
{
	switch ($status) {
		case 'diproses':
			return 'bg-blue-100 text-blue-800';
		case 'dikirim':
			return 'bg-indigo-100 text-indigo-800';
		case 'selesai':
			return 'bg-green-100 text-green-800';
		case 'dibatalkan':
			return 'bg-red-100 text-red-800';
		default:
			return 'bg-yellow-100 text-yellow-800';
	}
}
