<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Checkout tanpa registrasi. Alur: tampilkan ringkasan (GET) -> proses (POST,
 * simpan pesanan) -> halaman konfirmasi dengan nomor pesanan + link WhatsApp.
 *
 * Sumber barang: session 'buy_now' (Beli Sekarang, hanya 1 produk) diutamakan,
 * kalau tidak ada baru pakai session 'cart'.
 *
 * Harga dan stok dihitung ulang dari DB di server; input browser hanya
 * product_id => qty dari session.
 */
class Checkout extends Public_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('order_model');
		$this->load->model('product_model');
	}

	/** Ringkasan belanja + form checkout. */
	public function index()
	{
		list($mode, $source) = $this->_source();
		list($lines, $total) = cart_lines($source);
		$payable = $this->_payable_lines($lines);

		if (empty($payable))
		{
			// jangan biarkan user terjebak di mode Beli Sekarang yang sudah tidak valid
			if ($mode === 'buy_now')
			{
				$this->session->unset_userdata('buy_now');
			}
			$this->session->set_flashdata('error', 'Keranjang kosong.');
			redirect('keranjang');
			return;
		}

		$data = $this->page_data(array(
			'title'   => 'Checkout',
			'mode'    => $mode,
			'lines'   => $lines,
			'payable' => $payable,
			'total'   => $this->_sum($payable),
			'wa'      => store_whatsapp(),
		));
		$this->load->view('templates/header', $data);
		$this->load->view('checkout/index', $data);
		$this->load->view('templates/footer', $data);
	}

	/** POST: validasi -> transaksi simpan pesanan -> redirect konfirmasi. */
	public function process()
	{
		if ( ! is_post())
		{
			redirect('checkout');
			return;
		}

		$this->load->library('form_validation');

		// Nomor WhatsApp: normalisasi ke format lokal '08xxxxxxxxxx' SEBELUM validasi,
		// supaya '+62...', '62...', '08...' (dengan spasi/tanda hubung) semuanya diterima
		// dan hanya nomor seluler Indonesia yang wajar yang lolos.
		$_POST['whatsapp'] = (string) wa_normalize($this->input->post('whatsapp', TRUE));

		// Lokasi Google Maps (opsional): kalau diisi harus link Google Maps yang dikenal.
		$map_link = trim((string) $this->input->post('map_link', TRUE));
		$map_invalid = ($map_link !== '' && ! maps_link_valid($map_link));
		if ($map_invalid)
		{
			// kosongkan juga POST-nya: kalau tidak, rule 'required' malah lolos
			// karena masih membaca nilai asli yang non-kosong.
			$_POST['map_link'] = '';
			$map_link = '';
		}

		$this->form_validation->set_rules('customer_name', 'Nama', 'required|trim|min_length[3]|max_length[150]', array(
			'required'   => 'Nama wajib diisi.',
			'min_length' => 'Nama minimal {param} karakter.',
			'max_length' => 'Nama maksimal {param} karakter.',
		));
		$this->form_validation->set_rules('whatsapp', 'Nomor WhatsApp', 'required|regex_match[/^08[0-9]{8,11}$/]', array(
			'required' => 'Nomor WhatsApp wajib diisi dan harus nomor seluler Indonesia yang valid (contoh: 08123456789).',
		));
		$this->form_validation->set_rules('address', 'Alamat', 'required|trim|min_length[10]|max_length[500]', array(
			'required'   => 'Alamat pengiriman wajib diisi.',
			'min_length' => 'Alamat minimal {param} karakter agar kurir mudah menemukan lokasi.',
			'max_length' => 'Alamat maksimal {param} karakter.',
		));
		$this->form_validation->set_rules('note', 'Catatan', 'trim|max_length[500]', array(
			'max_length' => 'Catatan maksimal {param} karakter.',
		));
		if ($map_invalid)
		{
			$this->form_validation->set_rules('map_link', 'Lokasi', 'required', array(
				'required' => 'Lokasi harus berupa link Google Maps yang valid (contoh: https://maps.google.com/... ).',
			));
		}

		if ($this->form_validation->run() === FALSE)
		{
			// ulangi tampilan form dengan error validasi
			$this->index();
			return;
		}

		list($mode, $source) = $this->_source();
		if (empty($source))
		{
			$this->session->set_flashdata('error', 'Keranjang kosong.');
			redirect('keranjang');
			return;
		}

		// koordinat: dari input tersembunyi (geolocation), atau diekstrak dari link maps
		$lat = $this->input->post('latitude', TRUE);
		$lng = $this->input->post('longitude', TRUE);
		$coords = maps_clamp_coords($lat, $lng);
		if ($coords === NULL && $map_link !== '')
		{
			$coords = maps_parse_coords($map_link);
		}

		$customer = array(
			'customer_name' => $this->input->post('customer_name', TRUE),
			'whatsapp'      => $this->input->post('whatsapp', TRUE),
			'address'       => $this->input->post('address', TRUE),
			'note'          => trim((string) $this->input->post('note', TRUE)),
			'map_link'      => $map_link,
			'latitude'      => $coords !== NULL ? $coords[0] : NULL,
			'longitude'     => $coords !== NULL ? $coords[1] : NULL,
		);

		$result = $this->order_model->create_with_items($customer, $source);

		if ( ! $result['ok'])
		{
			$this->session->set_flashdata('error', $result['error']);
			redirect('checkout');
			return;
		}

		// pesanan tersimpan: bersihkan sumber keranjang, bawa nomor ke halaman konfirmasi
		$this->session->unset_userdata($mode === 'buy_now' ? 'buy_now' : 'cart');
		$this->session->set_userdata('last_order_number', $result['order']->order_number);

		redirect('konfirmasi');
	}

	/** POST: batalkan mode Beli Sekarang, kembali pakai keranjang biasa. */
	public function cancel_buy_now()
	{
		if ( ! is_post())
		{
			redirect('checkout');
			return;
		}
		$this->session->unset_userdata('buy_now');
		redirect('checkout');
	}

	/**
	 * Halaman konfirmasi: nomor pesanan + link WhatsApp.
	 * Nomor diambil dari session (sekali tampil setelah checkout sukses) —
	 * bukan dari parameter URL supaya tidak bisa menebak pesanan orang.
	 */
	public function confirmation()
	{
		$order = $this->_session_order();
		if ( ! $order)
		{
			redirect('');
			return;
		}

		$wa_number = store_whatsapp();
		$wa_message = "Halo " . $this->config->item('store_name') . ", saya mau konfirmasi pesanan.\n"
			. "Nomor pesanan: " . $order->order_number . "\n"
			. "Nama: " . $order->customer_name . "\n"
			. "Total: " . rupiah($order->total_amount);

		$data = $this->page_data(array(
			'title'   => 'Konfirmasi Pesanan',
			'order'   => $order,
			'items'   => $this->order_model->items($order->id),
			'wa_link' => wa_link($wa_number, $wa_message),
		));
		$this->load->view('templates/header', $data);
		$this->load->view('checkout/confirmation', $data);
		$this->load->view('templates/footer', $data);
	}

	/**
	 * Struk digital yang bisa dicetak. Nomor pesanan dari session (sama seperti
	 * konfirmasi) agar tidak bisa menebak pesanan orang lain.
	 */
	public function receipt()
	{
		$order = $this->_session_order();
		if ( ! $order)
		{
			redirect('');
			return;
		}

		$data = array(
			'store_name' => $this->config->item('store_name'),
			'store_area' => $this->config->item('store_area'),
			'order'      => $order,
			'items'      => $this->order_model->items($order->id),
			'statuses'   => order_statuses(),
			'back_url'   => site_url('konfirmasi'),
			'back_label' => 'Kembali ke konfirmasi',
		);
		$this->load->view('receipt/print', $data);
	}

	// ------------------------------------------------------------------

	/**
	 * Sumber barang yang dipakai checkout.
	 * @return array [mode ('buy_now'|'cart'), session data]
	 */
	protected function _source()
	{
		$buy = $this->session->userdata('buy_now');
		if (is_array($buy) && ! empty($buy))
		{
			return array('buy_now', $buy);
		}
		return array('cart', $this->session->userdata('cart') ?: array());
	}

	/** Pesanan dari session last_order_number (untuk konfirmasi & struk). */
	protected function _session_order()
	{
		$order_number = $this->session->userdata('last_order_number');
		if ( ! $order_number)
		{
			return NULL;
		}
		return $this->order_model->find_by_number($order_number);
	}

	/** Baris yang bisa dibayar (qty > 0) — baris bermasalah tidak ditagih. */
	protected function _payable_lines(array $lines)
	{
		return array_values(array_filter($lines, function ($line) {
			return $line['qty'] > 0;
		}));
	}

	protected function _sum(array $lines)
	{
		$total = 0.0;
		foreach ($lines as $line)
		{
			$total += $line['subtotal'];
		}
		return $total;
	}
}
