<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Checkout tanpa registrasi. Alur: tampilkan ringkasan (GET) -> proses (POST,
 * simpan pesanan) -> halaman konfirmasi dengan nomor pesanan + link WhatsApp.
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
		list($lines, $total) = cart_lines($this->session->userdata('cart') ?: array());
		$payable = $this->_payable_lines($lines);

		if (empty($payable))
		{
			$this->session->set_flashdata('error', 'Keranjang kosong.');
			redirect('keranjang');
			return;
		}

		$data = $this->page_data(array(
			'title'   => 'Checkout',
			'lines'   => $lines,
			'payable' => $payable,
			'total'   => $this->_sum($payable),
			'wa'      => $this->config->item('store_whatsapp'),
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
		$this->form_validation->set_rules('customer_name', 'Nama', 'required|trim|min_length[3]|max_length[150]');
		// nomor WhatsApp: digit, boleh diawali +, panjang wajar
		$this->form_validation->set_rules('whatsapp', 'Nomor WhatsApp', 'required|trim|min_length[8]|max_length[20]|regex_match[/^\+?[0-9\s\-]+$/]');
		$this->form_validation->set_rules('address', 'Alamat', 'required|trim|min_length[10]|max_length[500]');
		$this->form_validation->set_rules('note', 'Catatan', 'trim|max_length[500]');

		if ($this->form_validation->run() === FALSE)
		{
			// ulangi tampilan form dengan error validasi
			$this->index();
			return;
		}

		$cart = $this->session->userdata('cart') ?: array();
		if (empty($cart))
		{
			$this->session->set_flashdata('error', 'Keranjang kosong.');
			redirect('keranjang');
			return;
		}

		$customer = array(
			'customer_name' => $this->input->post('customer_name', TRUE),
			'whatsapp'      => preg_replace('/[^\d+]/', '', $this->input->post('whatsapp', TRUE)),
			'address'       => $this->input->post('address', TRUE),
			'note'          => trim((string) $this->input->post('note', TRUE)),
		);

		$result = $this->order_model->create_with_items($customer, $cart);

		if ( ! $result['ok'])
		{
			$this->session->set_flashdata('error', $result['error']);
			redirect('checkout');
			return;
		}

		// pesanan tersimpan: bersihkan keranjang, bawa nomor ke halaman konfirmasi
		$this->session->unset_userdata('cart');
		$this->session->set_userdata('last_order_number', $result['order']->order_number);

		redirect('konfirmasi');
	}

	/**
	 * Halaman konfirmasi: nomor pesanan + link WhatsApp.
	 * Nomor diambil dari session (sekali tampil setelah checkout sukses) —
	 * bukan dari parameter URL supaya tidak bisa menebak pesanan orang.
	 */
	public function confirmation()
	{
		$order_number = $this->session->userdata('last_order_number');
		if ( ! $order_number)
		{
			redirect('');
			return;
		}

		$order = $this->order_model->find_by_number($order_number);
		if ( ! $order)
		{
			redirect('');
			return;
		}

		$wa_number = $this->config->item('store_whatsapp');
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

	// ------------------------------------------------------------------

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
