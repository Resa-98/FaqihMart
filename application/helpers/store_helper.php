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
 * $number: digit saja (format internasional tanpa '+'). Nomor lokal yang diawali
 * '0' otomatis dikonversi ke awalan negara '62' (Indonesia) agar link tetap benar.
 */
function wa_link($number, $message)
{
	$number = preg_replace('/\D/', '', (string) $number);
	if ($number !== '' && $number[0] === '0')
	{
		$number = '62' . substr($number, 1);
	}
	return 'https://wa.me/' . $number . '?text=' . rawurlencode($message);
}

/**
 * Normalisasi nomor WhatsApp seluler Indonesia ke format lokal '08xxxxxxxxxx'.
 * Menerima '+62...', '62...', '08...' (dengan spasi/tanda hubung/tanda kurung).
 * Return NULL kalau bukan nomor seluler Indonesia yang wajar.
 *
 * Panjang yang diterima: 08 + 8..11 digit (total 10..13 digit).
 */
function wa_normalize($input)
{
	$digits = preg_replace('/\D+/', '', (string) $input);
	if ($digits === '')
	{
		return NULL;
	}

	if (strpos($digits, '62') === 0)
	{
		$digits = '0' . substr($digits, 2);
	}
	elseif ($digits[0] === '8')
	{
		// '8123...' -> '08123...'
		$digits = '0' . $digits;
	}

	return preg_match('/^08[0-9]{8,11}$/', $digits) ? $digits : NULL;
}

/**
 * Konversi nomor WhatsApp yang valid ke format internasional tanpa '+'
 * ('62xxxxxxxxxx'). Return NULL kalau bukan nomor seluler Indonesia yang valid.
 */
function wa_international($input)
{
	$local = wa_normalize($input);
	if ($local === NULL)
	{
		return NULL;
	}
	return '62' . substr($local, 1);
}

/**
 * Tampilan nomor WhatsApp yang enak dibaca, contoh: '+62 812-3456-7890'.
 * Tidak memvalidasi; kalau bukan format yang dikenal, kembalikan apa adanya.
 */
function wa_display($number)
{
	$digits = preg_replace('/\D/', '', (string) $number);
	if ($digits === '')
	{
		return '';
	}
	if ($digits[0] === '0')
	{
		$digits = '62' . substr($digits, 1);
	}
	if (strpos($digits, '62') !== 0)
	{
		return '+' . $digits;
	}

	// kelompokkan 3-4-4 (pola umum nomor seluler Indonesia)
	$rest  = substr($digits, 2);
	$parts = array();
	if (strlen($rest) > 3)
	{
		$parts[] = substr($rest, 0, 3);
		$rest    = substr($rest, 3);
	}
	if (strlen($rest) > 4)
	{
		$parts[] = substr($rest, 0, 4);
		$rest    = substr($rest, 4);
	}
	if ($rest !== '')
	{
		$parts[] = $rest;
	}

	return '+62' . (empty($parts) ? '' : ' ' . implode('-', $parts));
}

/**
 * Nomor WhatsApp toko yang dipakai untuk konfirmasi pesanan.
 * Diambil dari tabel `settings` (bisa diubah admin); kalau belum diset,
 * jatuh ke nilai default di application/config/store.php.
 */
function store_whatsapp()
{
	static $loaded = FALSE;
	static $value  = NULL;

	if ( ! $loaded)
	{
		$CI    =& get_instance();
		$value = NULL;

		// tabel settings mungkin belum ada (DB lama) — jangan sampai halaman
		// checkout error; cukup pakai nilai default dari config.
		if ($CI->db->table_exists('settings'))
		{
			$CI->load->model('setting_model');
			$value = $CI->setting_model->get('store_whatsapp');
		}
		if ($value === NULL)
		{
			$value = $CI->config->item('store_whatsapp');
		}
		$loaded = TRUE;
	}

	return $value;
}

/**
 * TRUE kalau URL adalah link Google Maps yang dikenal (untuk input lokasi).
 */
function maps_link_valid($url)
{
	$url = trim((string) $url);
	if ($url === '')
	{
		return FALSE;
	}
	return (bool) preg_match(
		'~^https://(www\.)?(google\.[a-z.]{2,}/maps|maps\.google\.[a-z.]{2,}|maps\.app\.goo\.gl|goo\.gl/maps)([/?#].*)?$~i',
		$url
	);
}

/**
 * Ambil koordinat dari URL Google Maps bila ada.
 * Mendukung pola '@lat,lng', '?q=lat,lng', dan '!3dlat!4dlng'.
 * Return array(lat, lng) atau NULL.
 */
function maps_parse_coords($url)
{
	$url = (string) $url;
	$patterns = array(
		'~@(-?\d{1,3}\.\d+),(-?\d{1,3}\.\d+)~',
		'~[?&]q=(-?\d{1,3}\.\d+),(-?\d{1,3}\.\d+)~',
		'~!3d(-?\d{1,3}\.\d+)!4d(-?\d{1,3}\.\d+)~',
	);
	foreach ($patterns as $p)
	{
		if (preg_match($p, $url, $m))
		{
			return maps_clamp_coords($m[1], $m[2]);
		}
	}
	return NULL;
}

/** Validasi & batasi rentang koordinat; return array(lat, lng) atau NULL. */
function maps_clamp_coords($lat, $lng)
{
	// Jangan pakai (float) langsung: string kosong akan jadi 0.0 dan lolos.
	if ( ! is_numeric($lat) || ! is_numeric($lng))
	{
		return NULL;
	}

	$lat = (float) $lat;
	$lng = (float) $lng;
	if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180)
	{
		return NULL;
	}
	return array(round($lat, 7), round($lng, 7));
}

/** URL Google Maps dari koordinat. */
function maps_url_from_coords($lat, $lng)
{
	return 'https://www.google.com/maps?q=' . rawurlencode($lat . ',' . $lng);
}

/**
 * Path halaman saat ini yang aman untuk dipakai sebagai tujuan redirect internal
 * (untuk form yang harus kembali ke halaman asal). Selalu diawali '/'.
 */
function current_return_path()
{
	$CI =& get_instance();
	$path = '/' . ltrim($CI->uri->uri_string(), '/');

	$qs = (string) $CI->input->server('QUERY_STRING');
	// buang karakter kontrol (cegah injeksi header) dan tolak apa pun yang
	// mengandung skema '://' supaya redirect tetap internal.
	$qs = preg_replace('/[\x00-\x1F\x7F]/', '', $qs);
	if ($qs !== '' && strpos($qs, '://') === FALSE)
	{
		$path .= '?' . $qs;
	}

	return $path;
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
