<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Admin_Controller {

	public function index()
	{
		$data = $this->admin_page(array(
			'title'   => 'Dashboard',
			'counts'  => $this->order_model->count_by_status(),
			'total_products' => $this->product_model->admin_count(),
			'total_orders'   => $this->order_model->admin_count(),
			'pending_orders' => $this->order_model->admin_list(5, 0, 'menunggu_konfirmasi'),
			'flash'   => $this->session->flashdata('success') ?: $this->session->flashdata('error'),
		));
		$this->load->view('admin/templates/header', $data);
		$this->load->view('admin/dashboard', $data);
		$this->load->view('admin/templates/footer', $data);
	}
}
