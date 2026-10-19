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

	/** POST: tambah produk (qty dibatasi stok). Mendukung AJAX (JSON). */
	public function add()
	{
		$product_id = (int) $this->input->post('product_id');
		$qty        = max(1, (int) $this->input->post('qty'));
		$return     = $this->input->post('return') ? $this->input->post('return', TRUE) : '';

		$product = $product_id > 0 ? $this->product_model->find($product_id) : NULL;
		if ( ! $product || (int) $product->is_active !== 1)
		{
			return $this->_respond(FALSE, 'Produk tidak ditemukan.', 'katalog', $return);
		}

		$cart = $this->_cart();
		$current = isset($cart[$product->id]) ? (int) $cart[$product->id] : 0;
		$want = $current + $qty;

		if ((int) $product->stock <= 0)
		{
			$ok = FALSE;
			$message = 'Maaf, stok "' . $product->name . '" habis.';
		}
		elseif ($want > (int) $product->stock)
		{
			$cart[$product->id] = (int) $product->stock;
			$this->_save($cart);
			$ok = FALSE;
			$message = 'Jumlah "' . $product->name . '" dibatasi stok tersedia (' . (int) $product->stock . ').';
		}
		else
		{
			$cart[$product->id] = $want;
			$this->_save($cart);
			$ok = TRUE;
			$message = '"' . $product->name . '" ditambahkan ke keranjang.';
		}

		// menambah ke keranjang = keluar dari mode "Beli Sekarang"
		$this->session->unset_userdata('buy_now');

		return $this->_respond($ok, $message, 'keranjang', $return);
	}

	/**
	 * POST: Beli Sekarang — checkout hanya produk ini (tanpa mengubah keranjang).
	 * Disimpan di session 'buy_now'; Checkout memprioritaskannya.
	 */
	public function buy_now()
	{
		if ( ! is_post())
		{
			redirect('katalog');
			return;
		}

		$product_id = (int) $this->input->post('product_id');
		$qty        = max(1, (int) $this->input->post('qty'));

		$product = $product_id > 0 ? $this->product_model->find($product_id) : NULL;
		if ( ! $product || (int) $product->is_active !== 1)
		{
			$this->session->set_flashdata('error', 'Produk tidak ditemukan.');
			redirect('katalog');
			return;
		}

		if ((int) $product->stock <= 0)
		{
			$this->session->set_flashdata('error', 'Maaf, stok "' . $product->name . '" habis.');
			redirect('produk/' . $product->slug);
			return;
		}

		$qty = min($qty, (int) $product->stock);
		$this->session->set_userdata('buy_now', array((int) $product->id => $qty));

		if ($qty < max(1, (int) $this->input->post('qty')))
		{
			$this->session->set_flashdata('error', 'Jumlah dibatasi stok tersedia (' . (int) $product->stock . ').');
		}

		redirect('checkout');
	}

	/** POST: ubah jumlah (0 = hapus). Stok dicek lagi di sini. Mendukung AJAX. */
	public function update()
	{
		$product_id = (int) $this->input->post('product_id');
		$qty        = (int) $this->input->post('qty');

		$cart = $this->_cart();
		if ( ! isset($cart[$product_id]))
		{
			return $this->_update_response(FALSE, 'Produk tidak ada di keranjang.', $qty);
		}

		if ($qty <= 0)
		{
			unset($cart[$product_id]);
			$this->_save($cart);
			$this->session->unset_userdata('buy_now');
			return $this->_update_response(TRUE, 'Produk dihapus dari keranjang.', 0);
		}

		$product = $this->product_model->find($product_id);
		if ( ! $product || (int) $product->is_active !== 1)
		{
			unset($cart[$product_id]);
			$this->_save($cart);
			$this->session->unset_userdata('buy_now');
			return $this->_update_response(TRUE, 'Produk sudah tidak dijual dan dihapus dari keranjang.', 0);
		}

		$adjusted = FALSE;
		if ($qty > (int) $product->stock)
		{
			$qty = (int) $product->stock;
			$adjusted = TRUE;
		}

		$cart[$product_id] = $qty;
		$this->_save($cart);
		$this->session->unset_userdata('buy_now');

		$message = $adjusted
			? 'Jumlah dibatasi stok tersedia (' . (int) $product->stock . ').'
			: 'Keranjang diperbarui.';

		return $this->_update_response(TRUE, $message, $qty, $adjusted);
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
		$this->session->unset_userdata('buy_now');
		redirect('keranjang');
	}

	// ------------------------------------------------------------------

	/**
	 * Respons seragam untuk Cart::add: JSON kalau request AJAX (tetap di halaman),
	 * atau redirect (fallback tanpa JavaScript) ke $return/$fallback.
	 */
	protected function _respond($ok, $message, $fallback, $return = '')
	{
		if ($this->input->is_ajax_request())
		{
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(array(
					'ok'         => (bool) $ok,
					'message'    => $message,
					'cart_count' => (int) $this->cart_item_count(),
					'csrf_name'  => $this->security->get_csrf_token_name(),
					'csrf_hash'  => $this->security->get_csrf_hash(),
				)));
			return;
		}

		$this->session->set_flashdata($ok ? 'success' : 'error', $message);

		// kembali ke halaman asal (produk/katalog) — hanya path internal yang diizinkan
		$return = ($return !== '' && $return[0] === '/') ? $return : '';
		redirect($return !== '' ? ltrim($return, '/') : $fallback);
	}

	/**
	 * Respons untuk Cart::update: JSON kalau AJAX (halaman keranjang tidak reload),
	 * atau redirect ke keranjang (fallback tanpa JavaScript).
	 */
	protected function _update_response($ok, $message, $qty, $adjusted = FALSE)
	{
		if ($this->input->is_ajax_request())
		{
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(array(
					'ok'         => (bool) $ok,
					'message'    => $message,
					'qty'        => (int) $qty,
					'adjusted'   => (bool) $adjusted,
					'cart_count' => (int) $this->cart_item_count(),
					'csrf_name'  => $this->security->get_csrf_token_name(),
					'csrf_hash'  => $this->security->get_csrf_hash(),
				)));
			return;
		}

		$this->session->set_flashdata($ok ? 'success' : 'error', $message);
		redirect('keranjang');
	}

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
