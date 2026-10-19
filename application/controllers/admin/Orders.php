<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Daftar & detail pesanan + transisi status.
 * Transisi dibatasi helper order_status_transitions(); restok saat batal
 * ditangani Order_model dalam transaksi (hanya sekali).
 */
class Orders extends Admin_Controller {

	const PER_PAGE = 20;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
	}

	public function index()
	{
		$status = (string) $this->input->get('status', TRUE);
		$total  = $this->order_model->admin_count($status);
		$pages  = max(1, (int) ceil($total / self::PER_PAGE));
		$page   = min($pages, max(1, (int) $this->input->get('page', TRUE)));

		$data = $this->admin_page(array(
			'title'    => 'Manajemen Pesanan',
			'orders'   => $this->order_model->admin_list(self::PER_PAGE, ($page - 1) * self::PER_PAGE, $status),
			'status'   => $status,
			'statuses' => order_statuses(),
			'page'     => $page,
			'pages'    => $pages,
			'total'    => $total,
			'flash'    => $this->session->flashdata('success') ?: $this->session->flashdata('error'),
		));
		$this->load->view('admin/templates/header', $data);
		$this->load->view('admin/orders/index', $data);
		$this->load->view('admin/templates/footer', $data);
	}

	/** $order_number, bukan id — nomor pesanan bersifat publik-terbatas. */
	public function detail($order_number)
	{
		$order = $this->order_model->find_by_number($order_number);
		if ( ! $order)
		{
			show_404();
		}

		$data = $this->admin_page(array(
			'title'      => 'Pesanan ' . $order->order_number,
			'order'      => $order,
			'items'      => $this->order_model->items($order->id),
			'statuses'   => order_statuses(),
			'next_states' => order_status_transitions()[$order->status],
			'flash'      => $this->session->flashdata('success') ?: $this->session->flashdata('error'),
		));
		$this->load->view('admin/templates/header', $data);
		$this->load->view('admin/orders/detail', $data);
		$this->load->view('admin/templates/footer', $data);
	}

	/** Struk digital yang bisa dicetak (dari panel admin). */
	public function receipt($order_number)
	{
		$order = $this->order_model->find_by_number($order_number);
		if ( ! $order)
		{
			show_404();
		}

		$data = array(
			'store_name' => $this->config->item('store_name'),
			'store_area' => $this->config->item('store_area'),
			'order'      => $order,
			'items'      => $this->order_model->items($order->id),
			'statuses'   => order_statuses(),
			'back_url'   => site_url('admin/pesanan/' . $order->order_number),
			'back_label' => 'Kembali ke detail pesanan',
		);
		$this->load->view('receipt/print', $data);
	}

	/** POST: transisi status. */
	public function update_status($order_number)
	{
		$order = $this->order_model->find_by_number($order_number);
		if ( ! $order)
		{
			show_404();
		}
		if ( ! is_post())
		{
			redirect('admin/pesanan/' . $order_number);
			return;
		}

		$new_status = (string) $this->input->post('status');
		$result = $this->order_model->transition_status($order->id, $new_status);

		if ($result['ok'])
		{
			$msg = 'Status pesanan ' . $order->order_number . ' diubah ke "' . order_statuses()[$new_status] . '".';
			if ($new_status === 'dibatalkan')
			{
				$msg .= ' Stok produk telah dikembalikan.';
			}
			$this->session->set_flashdata('success', $msg);
		}
		else
		{
			$this->session->set_flashdata('error', $result['error']);
		}
		redirect('admin/pesanan/' . $order_number);
	}
}
