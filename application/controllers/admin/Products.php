<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CRUD produk + upload gambar (tipe & ukuran divalidasi, nama di-encrypt,
 * path relatif disimpan di DB).
 */
class Products extends Admin_Controller {

	const PER_PAGE = 20;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('category_model');
		$this->load->library(array('form_validation', 'upload'));
	}

	public function index()
	{
		$q     = trim((string) $this->input->get('q', TRUE));
		$total = $this->product_model->admin_count($q);
		$pages = max(1, (int) ceil($total / self::PER_PAGE));
		$page  = min($pages, max(1, (int) $this->input->get('page', TRUE)));

		$data = $this->admin_page(array(
			'title'    => 'Manajemen Produk',
			'products' => $this->product_model->admin_list(self::PER_PAGE, ($page - 1) * self::PER_PAGE, $q),
			'q'        => $q,
			'page'     => $page,
			'pages'    => $pages,
			'total'    => $total,
			'flash'    => $this->session->flashdata('success') ?: $this->session->flashdata('error'),
		));
		$this->load->view('admin/templates/header', $data);
		$this->load->view('admin/products/index', $data);
		$this->load->view('admin/templates/footer', $data);
	}

	public function create()
	{
		$data = $this->admin_page(array(
			'title'      => 'Tambah Produk',
			'categories' => $this->category_model->all(),
			'product'    => NULL,
			'errors'     => array(),
		));
		$this->_form_view('admin/products/form', $data);
	}

	public function process_create()
	{
		if ( ! is_post())
		{
			redirect('admin/produk/tambah');
			return;
		}

		$errors = $this->_validate();
		if ( ! empty($errors))
		{
			$data = $this->admin_page(array(
				'title'      => 'Tambah Produk',
				'categories' => $this->category_model->all(),
				'product'    => (object) $this->input->post(),
				'errors'     => $errors,
			));
			$this->_form_view('admin/products/form', $data);
			return;
		}

		$image = $this->_do_upload();
		if ($image === FALSE)
		{
			$data = $this->admin_page(array(
				'title'      => 'Tambah Produk',
				'categories' => $this->category_model->all(),
				'product'    => (object) $this->input->post(),
				'errors'     => array('image' => $this->upload->display_errors('', '')),
			));
			$this->_form_view('admin/products/form', $data);
			return;
		}

		$this->product_model->create(array(
			'category_id' => (int) $this->input->post('category_id'),
			'name'        => $this->input->post('name', TRUE),
			'price'       => (float) str_replace(',', '.', preg_replace('/[^0-9,.]/', '', $this->input->post('price', TRUE))),
			'stock'       => max(0, (int) $this->input->post('stock')),
			'image'       => $image,
			'description' => (string) $this->input->post('description', TRUE),
			'is_active'   => $this->input->post('is_active') ? 1 : 0,
		));

		$this->session->set_flashdata('success', 'Produk berhasil ditambahkan.');
		redirect('admin/produk');
	}

	public function edit($id)
	{
		$product = $this->product_model->find($id);
		if ( ! $product)
		{
			show_404();
		}

		$data = $this->admin_page(array(
			'title'      => 'Ubah Produk',
			'categories' => $this->category_model->all(),
			'product'    => $product,
			'errors'     => array(),
		));
		$this->_form_view('admin/products/form', $data);
	}

	public function process_edit($id)
	{
		$product = $this->product_model->find($id);
		if ( ! $product)
		{
			show_404();
		}
		if ( ! is_post())
		{
			redirect('admin/produk/ubah/' . $id);
			return;
		}

		$errors = $this->_validate();
		if ( ! empty($errors))
		{
			$data = $this->admin_page(array(
				'title'      => 'Ubah Produk',
				'categories' => $this->category_model->all(),
				'product'    => (object) array_merge((array) $product, $this->input->post()),
				'errors'     => $errors,
			));
			$this->_form_view('admin/products/form', $data);
			return;
		}

		$old_image = (string) $product->image;
		$image = $this->_do_upload();
		if ($image === FALSE)
		{
			$data = $this->admin_page(array(
				'title'      => 'Ubah Produk',
				'categories' => $this->category_model->all(),
				'product'    => (object) array_merge((array) $product, $this->input->post()),
				'errors'     => array('image' => $this->upload->display_errors('', '')),
			));
			$this->_form_view('admin/products/form', $data);
			return;
		}

		$this->product_model->update_product($id, array(
			'category_id' => (int) $this->input->post('category_id'),
			'name'        => $this->input->post('name', TRUE),
			'price'       => (float) str_replace(',', '.', preg_replace('/[^0-9,.]/', '', $this->input->post('price', TRUE))),
			'stock'       => max(0, (int) $this->input->post('stock')),
			'description' => (string) $this->input->post('description', TRUE),
			'is_active'   => $this->input->post('is_active') ? 1 : 0,
		) + ($image !== NULL ? array('image' => $image) : array()));

		// ganti gambar: hapus file lama (hanya yang ada di folder upload kita)
		if ($image !== NULL && $old_image !== '')
		{
			$this->_delete_image_file($old_image);
		}

		$this->session->set_flashdata('success', 'Produk berhasil diperbarui.');
		redirect('admin/produk');
	}

	public function delete($id)
	{
		$product = $this->product_model->find($id);
		if ( ! $product)
		{
			show_404();
		}
		if ( ! is_post())
		{
			redirect('admin/produk');
			return;
		}

		$this->product_model->delete_product($id);
		// riwayat pesanan aman (snapshot nama+harga, product_id -> SET NULL)
		if ($product->image !== NULL && $product->image !== '')
		{
			$this->_delete_image_file($product->image);
		}

		$this->session->set_flashdata('success', 'Produk dihapus. Riwayat pesanan tetap utuh.');
		redirect('admin/produk');
	}

	// ------------------------------------------------------------------

	protected function _validate()
	{
		$this->form_validation->set_rules('category_id', 'Kategori', 'required|integer');
		$this->form_validation->set_rules('name', 'Nama produk', 'required|trim|min_length[3]|max_length[200]');
		$this->form_validation->set_rules('price', 'Harga', 'required|trim|regex_match[/^[0-9.,]+$/]');
		$this->form_validation->set_rules('stock', 'Stok', 'required|integer|greater_than_equal_to[0]');
		$this->form_validation->set_rules('description', 'Deskripsi', 'trim|max_length[2000]');

		if ($this->form_validation->run() === FALSE)
		{
			return array('general' => validation_errors(' ', ' '));
		}

		$cat = $this->category_model->find((int) $this->input->post('category_id'));
		if ( ! $cat)
		{
			return array('category_id' => 'Kategori tidak valid.');
		}

		return array();
	}

	/**
	 * Upload gambar produk.
	 * @return string|null path relatif jika ada file baru, NULL jika tidak ada
	 *          file dipilih (edit), FALSE jika gagal validasi.
	 */
	protected function _do_upload()
	{
		if (empty($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE)
		{
			return NULL;
		}

		$config = array(
			'upload_path'   => FCPATH . 'assets/images/products/',
			'allowed_types' => $this->config->item('upload_allowed_types'),
			'max_size'      => $this->config->item('upload_max_size'),
			'encrypt_name'  => TRUE,
			'strip_ext'     => FALSE,
		);
		$this->upload->initialize($config);

		if ( ! $this->upload->do_upload('image'))
		{
			return FALSE;
		}

		$info = $this->upload->data();
		// validasi tambahan: file harus benar-benar gambar yang ter-decode (bukan sekadar ekstensi cocok)
		if ((int) $info['image_width'] === 0 || (int) $info['image_height'] === 0)
		{
			@unlink($info['full_path']);
			return FALSE;
		}

		return 'assets/images/products/' . $info['file_name'];
	}

	/** Hapus file gambar — hanya dari folder upload produk. */
	protected function _delete_image_file($relative_path)
	{
		$relative_path = str_replace('\\', '/', (string) $relative_path);
		if (strpos($relative_path, 'assets/images/products/') !== 0 || strpos($relative_path, '..') !== FALSE)
		{
			return;
		}
		$abs = FCPATH . $relative_path;
		if (is_file($abs))
		{
			@unlink($abs);
		}
	}

	protected function _form_view($view, array $data)
	{
		$this->load->view('admin/templates/header', $data);
		$this->load->view($view, $data);
		$this->load->view('admin/templates/footer', $data);
	}
}
