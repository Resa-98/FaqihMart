<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Login / logout admin. Rate limit sederhana berbasis session:
 * setelah 5 kali gagal, tunggu 60 detik sebelum boleh mencoba lagi.
 */
class Auth extends CI_Controller {

	const MAX_ATTEMPTS = 5;
	const LOCK_SECONDS = 60;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin_model');
		$this->load->library('form_validation');
	}

	/** Tampilkan form login (atau langsung ke dashboard kalau sudah login). */
	public function login()
	{
		if ($this->session->userdata('admin_id'))
		{
			redirect('admin');
			return;
		}

		$data = array(
			'title' => 'Login Admin',
			'error' => $this->session->flashdata('error'),
		);
		$this->load->view('admin/login', $data);
	}

	/** POST: verifikasi kredensial + rate limit. */
	public function process_login()
	{
		if ( ! is_post())
		{
			redirect('admin/login');
			return;
		}

		if ($this->session->userdata('login_locked_until') && time() < $this->session->userdata('login_locked_until'))
		{
			$wait = (int) ceil(($this->session->userdata('login_locked_until') - time()) / 60);
			$this->session->set_flashdata('error', 'Terlalu banyak percobaan. Coba lagi dalam ±' . $wait . ' menit.');
			redirect('admin/login');
			return;
		}

		$this->form_validation->set_rules('username', 'Username', 'required|trim|max_length[60]');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() === FALSE)
		{
			$this->session->set_flashdata('error', 'Username dan password wajib diisi.');
			redirect('admin/login');
			return;
		}

		$admin = $this->admin_model->verify(
			$this->input->post('username', TRUE),
			(string) $this->input->post('password')
		);

		if ( ! $admin)
		{
			$fails = (int) $this->session->userdata('login_fails') + 1;
			$this->session->set_userdata('login_fails', $fails);
			if ($fails >= self::MAX_ATTEMPTS)
			{
				$this->session->set_userdata('login_locked_until', time() + self::LOCK_SECONDS);
				$this->session->set_userdata('login_fails', 0);
				$this->session->set_flashdata('error', 'Terlalu banyak percobaan gagal. Tunggu 1 menit.');
			}
			else
			{
				$this->session->set_flashdata('error', 'Username atau password salah.');
			}
			redirect('admin/login');
			return;
		}

		// regenerasi session id setelah login sukses (cegah session fixation)
		$this->session->sess_regenerate(TRUE);
		$this->session->set_userdata(array(
			'admin_id'        => $admin->id,
			'admin_username'  => $admin->username,
			'login_fails'     => 0,
			'login_locked_until' => 0,
		));

		redirect('admin');
	}

	/** Logout. */
	public function logout()
	{
		// hanya boleh dari POST agar tidak bisa dipancing lewat link
		if ( ! is_post())
		{
			redirect('admin/login');
			return;
		}
		$this->session->unset_userdata(array('admin_id', 'admin_username', 'login_fails', 'login_locked_until'));
		$this->session->sess_destroy();
		redirect('admin/login');
	}
}
