<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base controller — hanya untuk setup bersama (config toko, keranjang).
 * Jangan taruh method publik di sini (bisa ter-routing sebagai aksi).
 */
class MY_Controller extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('category_model');
	}

	/**
	 * Data dasar yang selalu dibutuhkan layout toko.
	 */
	protected function layout_data()
	{
		return array(
			'store_name'     => $this->config->item('store_name'),
			'store_area'     => $this->config->item('store_area'),
			'nav_categories' => $this->category_model->active_list(),
			'cart_count'     => $this->cart_item_count(),
		);
	}

	/**
	 * Jumlah item unik di keranjang session (untuk badge ikon keranjang).
	 */
	protected function cart_item_count()
	{
		$cart = $this->session->userdata('cart');
		return is_array($cart) ? count($cart) : 0;
	}
}

/**
 * Base untuk halaman publik — sudah punya data layout.
 */
class Public_Controller extends MY_Controller {

	/**
	 * Merge data layout dengan data halaman.
	 */
	protected function page_data(array $data = array())
	{
		return array_merge($this->layout_data(), $data);
	}
}

/**
 * Base untuk area admin — wajib login.
 * Method harus diawali '_' atau protected agar tidak jadi rute publik.
 */
class Admin_Controller extends MY_Controller {

	public $admin = NULL;

	public function __construct()
	{
		parent::__construct();
		if ( ! $this->session->userdata('admin_id'))
		{
			$this->session->set_flashdata('error', 'Silakan login terlebih dahulu.');
			redirect('admin/login');
			exit;
		}
		$this->admin = array(
			'id'       => $this->session->userdata('admin_id'),
			'username' => $this->session->userdata('admin_username'),
		);
		$this->load->model('product_model');
		$this->load->model('order_model');
	}

	protected function admin_page(array $data = array())
	{
		return array_merge(array(
			'admin'      => $this->admin,
			'store_name' => $this->config->item('store_name'),
		), $data);
	}
}
