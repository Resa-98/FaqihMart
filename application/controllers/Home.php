<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends Public_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('product_model');
	}

	/** Beranda: kategori + produk terbaru. */
	public function index()
	{
		$data = $this->page_data(array(
			'title'    => 'Beranda',
			'products' => $this->product_model->latest_active(8),
		));
		$this->load->view('templates/header', $data);
		$this->load->view('home/index', $data);
		$this->load->view('templates/footer', $data);
	}

	/** Informasi Faqih Mart — hanya fakta yang sudah ditetapkan, tanpa kebijakan karangan. */
	public function about()
	{
		$data = $this->page_data(array(
			'title' => 'Informasi Faqih Mart',
		));
		$this->load->view('templates/header', $data);
		$this->load->view('home/about', $data);
		$this->load->view('templates/footer', $data);
	}
}
