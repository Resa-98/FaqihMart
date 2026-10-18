<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Keranjang berbasis session. Harga SELALU diambil dari DB saat ditampilkan
 * (session hanya menyimpan product_id => qty), jadi manipulasi cookie/session
 * tidak bisa mengubah harga.
 *
 * Semua aksi hanya menerima POST (dilindungi form_open CSRF).
 */
class Cart extends Public_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('product_model');
		if ( ! is_post())
		{
			// cegah aksi lewat URL/get — aksi keranjang hanya POST
			if ($this->uri->segment(2))
			{
				redirect('keranjang');
			}
		}
	}

	/** Halaman keranjang. */
	public function index()
	{
		list($lines, $total) = $this->_lines();

		$data = $this->page_data(array(
			'title' => 'Keranjang Belanja',
			'lines' => $lines,
			'total' => $total,
		));
		$this->load->view('templates/header', $data);
		$this->load->view('cart/index', $data);
		$this->load->view('templates/footer', $data);
	}

	/** POST: tambah produk (qty dibatasi stok). */
	public function add()
	{
		$product_id = (int) $this->input->post('product_id');
		$qty        = max(1, (int) $this->input->post('qty'));
		$return     = $this->input->post('return') ? $this->input->post('return', TRUE) : '';

		$product = $product_id > 0 ? $this->product_model->find($product_id) : NULL;
		if ( ! $product || (int) $product->is_active !== 1)
		{
			$this->session->set_flashdata('error', 'Produk tidak ditemukan.');
			redirect('katalog');
			return;
		}

		$cart = $this->_cart();
		$current = isset($cart[$product->id]) ? (int) $cart[$product->id] : 0;
		$want = $current + $qty;

		if ((int) $product->stock <= 0)
		{
			$this->session->set_flashdata('error', 'Maaf, stok "' . $product->name . '" habis.');
		}
		elseif ($want > (int) $product->stock)
		{
			$cart[$product->id] = (int) $product->stock;
			$this->_save($cart);
			$this->session->set_flashdata('error', 'Jumlah "' . $product->name . '" dibatasi stok tersedia (' . (int) $product->stock . ').');
		}
		else
		{
			$cart[$product->id] = $want;
			$this->_save($cart);
			$this->session->set_flashdata('success', '"' . $product->name . '" ditambahkan ke keranjang.');
		}

		// kembali ke halaman asal (produk) — hanya path internal yang diizinkan
		$return = ($return !== '' && $return[0] === '/') ? $return : '';
		redirect($return !== '' ? ltrim($return, '/') : 'keranjang');
	}

	/** POST: ubah jumlah (0 = hapus). Stok dicek lagi di sini. */
	public function update()
	{
		$product_id = (int) $this->input->post('product_id');
		$qty        = (int) $this->input->post('qty');

		$cart = $this->_cart();
		if ( ! isset($cart[$product_id]))
		{
			redirect('keranjang');
			return;
		}

		if ($qty <= 0)
		{
			unset($cart[$product_id]);
			$this->session->set_flashdata('success', 'Produk dihapus dari keranjang.');
		}
		else
		{
			$product = $this->product_model->find($product_id);
			if ( ! $product || (int) $product->is_active !== 1)
			{
				unset($cart[$product_id]);
				$this->_save($cart);
				$this->session->set_flashdata('error', 'Produk sudah tidak dijual dan dihapus dari keranjang.');
				redirect('keranjang');
				return;
			}
			$qty = min($qty, (int) $product->stock);
			$cart[$product_id] = $qty;
			$this->session->set_flashdata('success', 'Keranjang diperbarui.');
		}

		$this->_save($cart);
		redirect('keranjang');
	}

	/** POST: hapus satu produk. */
	public function remove()
	{
		$product_id = (int) $this->input->post('product_id');
		$cart = $this->_cart();
		if (isset($cart[$product_id]))
		{
			unset($cart[$product_id]);
			$this->_save($cart);
			$this->session->set_flashdata('success', 'Produk dihapus dari keranjang.');
		}
		redirect('keranjang');
	}

	// ------------------------------------------------------------------

	protected function _cart()
	{
		$cart = $this->session->userdata('cart');
		return is_array($cart) ? $cart : array();
	}

	protected function _save(array $cart)
	{
		// buang qty <= 0 dan product_id mati agar session tidak kotor
		$cart = array_filter(array_map('intval', $cart), function ($q) {
			return $q > 0;
		});
		$this->session->set_userdata('cart', $cart);
	}

	/**
	 * Derive garis keranjang + total dari DB (dipakai Cart & Checkout).
	 * Session hanya berisi product_id => qty; harga & stok selalu dari DB.
	 *
	 * @param  array $cart [product_id => qty]
	 * @return array [$lines, $total]
	 *   $lines: product, qty, subtotal, issue (string|null)
	 */
	protected function _lines()
	{
		return cart_lines($this->_cart());
	}
}
