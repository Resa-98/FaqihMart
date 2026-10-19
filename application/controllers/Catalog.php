<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Catalog extends Public_Controller {

	const PER_PAGE = 12;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('product_model');
		$this->load->model('category_model');
	}

	/**
	 * Katalog: ?kategori=slug&q=teks&page=N
	 * Filter dari URL GET — hasilkan query string yang sama di link paginasi.
	 */
	public function index($category_slug = NULL)
	{
		$filters = array(
			'q'             => trim((string) $this->input->get('q', TRUE)),
			'category_slug' => $category_slug !== NULL ? $category_slug : trim((string) $this->input->get('kategori', TRUE)),
		);

		// kategori tidak dikenal / non-aktif = 404, bukan katalog kosong
		if ($filters['category_slug'] !== '' && ! $this->category_model->find_by_slug($filters['category_slug']))
		{
			show_404();
		}

		$total  = $this->product_model->catalog_count($filters);
		$pages  = max(1, (int) ceil($total / self::PER_PAGE));
		$page   = min($pages, max(1, (int) $this->input->get('page', TRUE)));
		$offset = ($page - 1) * self::PER_PAGE;

		$category = $filters['category_slug'] !== '' ? $this->category_model->find_by_slug($filters['category_slug']) : NULL;

		$data = $this->page_data(array(
			'title'     => $category ? $category->name : 'Katalog',
			'products'  => $this->product_model->attach_sold($this->product_model->catalog($filters, self::PER_PAGE, $offset)),
			'filters'   => $filters,
			'total'     => $total,
			'page'      => $page,
			'pages'     => $pages,
			'category'  => $category,
		));
		$this->load->view('templates/header', $data);
		$this->load->view('catalog/index', $data);
		$this->load->view('templates/footer', $data);
	}

	/** Detail produk by slug. */
	public function detail($slug)
	{
		$product = $this->product_model->find_active_by_slug($slug);
		if ( ! $product)
		{
			show_404();
		}
		$this->product_model->attach_sold_one($product);

		$data = $this->page_data(array(
			'title'   => $product->name,
			'product' => $product,
		));
		$this->load->view('templates/header', $data);
		$this->load->view('catalog/detail', $data);
		$this->load->view('templates/footer', $data);
	}
}
