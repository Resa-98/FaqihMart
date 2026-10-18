<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categories extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('category_model');
		$this->load->library('form_validation');
	}

	public function index()
	{
		$categories = $this->category_model->all();
		// tampilkan jumlah produk per kategori
		foreach ($categories as $cat)
		{
			$cat->product_count = $this->category_model->product_count($cat->id);
		}

		$data = $this->admin_page(array(
			'title'      => 'Manajemen Kategori',
			'categories' => $categories,
			'flash'      => $this->session->flashdata('success') ?: $this->session->flashdata('error'),
		));
		$this->load->view('admin/templates/header', $data);
		$this->load->view('admin/categories/index', $data);
		$this->load->view('admin/templates/footer', $data);
	}

	public function create()
	{
		$data = $this->admin_page(array(
			'title'    => 'Tambah Kategori',
			'category' => NULL,
			'errors'   => array(),
		));
		$this->_form_view('admin/categories/form', $data);
	}

	public function process_create()
	{
		if ( ! is_post())
		{
			redirect('admin/kategori/tambah');
			return;
		}

		$errors = $this->_validate();
		if ( ! empty($errors))
		{
			$data = $this->admin_page(array(
				'title'    => 'Tambah Kategori',
				'category' => (object) $this->input->post(),
				'errors'   => $errors,
			));
			$this->_form_view('admin/categories/form', $data);
			return;
		}

		$this->category_model->create(array(
			'name'      => $this->input->post('name', TRUE),
			'is_active' => $this->input->post('is_active') ? 1 : 0,
		));
		$this->session->set_flashdata('success', 'Kategori berhasil ditambahkan.');
		redirect('admin/kategori');
	}

	public function edit($id)
	{
		$category = $this->category_model->find($id);
		if ( ! $category)
		{
			show_404();
		}
		$data = $this->admin_page(array(
			'title'    => 'Ubah Kategori',
			'category' => $category,
			'errors'   => array(),
		));
		$this->_form_view('admin/categories/form', $data);
	}

	public function process_edit($id)
	{
		$category = $this->category_model->find($id);
		if ( ! $category)
		{
			show_404();
		}
		if ( ! is_post())
		{
			redirect('admin/kategori/ubah/' . $id);
			return;
		}

		$errors = $this->_validate();
		if ( ! empty($errors))
		{
			$data = $this->admin_page(array(
				'title'    => 'Ubah Kategori',
				'category' => (object) array_merge((array) $category, $this->input->post()),
				'errors'   => $errors,
			));
			$this->_form_view('admin/categories/form', $data);
			return;
		}

		$this->category_model->update_category($id, array(
			'name'      => $this->input->post('name', TRUE),
			'is_active' => $this->input->post('is_active') ? 1 : 0,
		));
		$this->session->set_flashdata('success', 'Kategori berhasil diperbarui.');
		redirect('admin/kategori');
	}

	public function delete($id)
	{
		$category = $this->category_model->find($id);
		if ( ! $category)
		{
			show_404();
		}
		if ( ! is_post())
		{
			redirect('admin/kategori');
			return;
		}

		if ($this->category_model->has_products($id))
		{
			$this->session->set_flashdata('error', 'Kategori masih berisi produk. Pindahkan atau hapus produknya dulu.');
		}
		else
		{
			$this->category_model->delete_category($id);
			$this->session->set_flashdata('success', 'Kategori dihapus.');
		}
		redirect('admin/kategori');
	}

	// ------------------------------------------------------------------

	protected function _validate()
	{
		$this->form_validation->set_rules('name', 'Nama kategori', 'required|trim|min_length[2]|max_length[100]|callback__unique_name');
		if ($this->form_validation->run() === FALSE)
		{
			return array('general' => validation_errors(' ', ' '));
		}
		return array();
	}

	/** CI validation callback: nama kategori unik (abaikan id saat edit). */
	public function _unique_name($name)
	{
		$ignore_id = 0;
		if (is_numeric($this->uri->segment(3)))
		{
			$ignore_id = (int) $this->uri->segment(3);
		}

		$this->db->where('name', trim($name));
		if ($ignore_id > 0)
		{
			$this->db->where('id !=', $ignore_id);
		}
		if ($this->db->count_all_results('categories') > 0)
		{
			$this->form_validation->set_message('_unique_name', 'Nama kategori sudah dipakai.');
			return FALSE;
		}
		return TRUE;
	}

	protected function _form_view($view, array $data)
	{
		$this->load->view('admin/templates/header', $data);
		$this->load->view($view, $data);
		$this->load->view('admin/templates/footer', $data);
	}
}
