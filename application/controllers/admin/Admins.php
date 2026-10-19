<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Kelola Admin:
 *  - nomor WhatsApp tujuan konfirmasi pesanan (disimpan di tabel `settings`)
 *  - ubah username & password akun yang sedang login
 *  - daftar akun admin (tambah / hapus)
 *
 * Semua aksi pengubah data hanya lewat POST + CSRF (form_open).
 */
class Admins extends Admin_Controller {

	const USERNAME_PATTERN = '/^[A-Za-z0-9._-]+$/';
	const USERNAME_MIN     = 3;
	const USERNAME_MAX     = 60;
	const PASSWORD_MIN     = 8;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('admin_model');
		$this->load->model('setting_model');
	}

	/** Halaman kelola admin. */
	public function index()
	{
		$data = $this->admin_page(array(
			'title'      => 'Kelola Admin',
			'admins'     => $this->admin_model->all(),
			'total'      => $this->admin_model->count_all(),
			'wa_number'  => store_whatsapp(),
			'wa_display' => wa_display(store_whatsapp()),
		));

		$this->load->view('admin/templates/header', $data);
		$this->load->view('admin/admins/index', $data);
		$this->load->view('admin/templates/footer', $data);
	}

	// ------------------------------------------------------------------
	// Nomor WhatsApp konfirmasi
	// ------------------------------------------------------------------

	/** POST: simpan nomor WhatsApp tujuan konfirmasi pesanan. */
	public function update_whatsapp()
	{
		if ( ! is_post())
		{
			redirect('admin/kelola-admin');
			return;
		}

		$number = wa_international($this->input->post('store_whatsapp', TRUE));
		if ($number === NULL)
		{
			$this->session->set_flashdata('error',
				'Nomor WhatsApp tidak valid. Gunakan nomor seluler Indonesia, contoh 08123456789 atau 6281234567890.');
			redirect('admin/kelola-admin');
			return;
		}

		$this->setting_model->set('store_whatsapp', $number);
		$this->session->set_flashdata('success', 'Nomor WhatsApp konfirmasi disimpan: ' . wa_display($number) . '.');
		redirect('admin/kelola-admin');
	}

	// ------------------------------------------------------------------
	// Kredensial akun yang sedang login
	// ------------------------------------------------------------------

	/** POST: ubah username dan/atau password akun sendiri (wajib password saat ini). */
	public function update_credentials()
	{
		if ( ! is_post())
		{
			redirect('admin/kelola-admin');
			return;
		}

		$admin = $this->admin_model->find($this->admin['id']);
		if ( ! $admin)
		{
			$this->session->sess_destroy();
			redirect('admin/login');
			return;
		}

		// wajib konfirmasi dengan password saat ini
		if ( ! password_verify((string) $this->input->post('current_password'), $admin->password))
		{
			$this->session->set_flashdata('error', 'Password saat ini salah. Perubahan dibatalkan.');
			redirect('admin/kelola-admin');
			return;
		}

		$username = trim((string) $this->input->post('username', TRUE));
		$new      = (string) $this->input->post('new_password');
		$confirm  = (string) $this->input->post('new_password_confirm');

		$errors = $this->_validate_username($username, $admin->id);

		$change_password = ($new !== '' || $confirm !== '');
		if ($change_password)
		{
			if (strlen($new) < self::PASSWORD_MIN)
			{
				$errors[] = 'Password baru minimal ' . self::PASSWORD_MIN . ' karakter.';
			}
			if ($new !== $confirm)
			{
				$errors[] = 'Konfirmasi password baru tidak cocok.';
			}
		}

		if ($errors)
		{
			$this->session->set_flashdata('error', implode(' ', $errors));
			redirect('admin/kelola-admin');
			return;
		}

		$fields = array('username' => $username);
		if ($change_password)
		{
			$fields['password'] = password_hash($new, PASSWORD_DEFAULT);
		}
		$this->admin_model->update_admin($admin->id, $fields);

		// jaga session tetap sinkron dengan data terbaru
		$this->session->set_userdata('admin_username', $username);
		if ($change_password)
		{
			$this->session->sess_regenerate(TRUE); // password berubah -> ganti session id
		}

		$this->session->set_flashdata('success', 'Kredensial akun berhasil diperbarui.');
		redirect('admin/kelola-admin');
	}

	// ------------------------------------------------------------------
	// Daftar akun admin
	// ------------------------------------------------------------------

	/** POST: tambah akun admin baru. */
	public function create()
	{
		if ( ! is_post())
		{
			redirect('admin/kelola-admin');
			return;
		}

		$username = trim((string) $this->input->post('username', TRUE));
		$password = (string) $this->input->post('password');
		$confirm  = (string) $this->input->post('password_confirm');

		$errors = $this->_validate_username($username, 0);
		if (strlen($password) < self::PASSWORD_MIN)
		{
			$errors[] = 'Password minimal ' . self::PASSWORD_MIN . ' karakter.';
		}
		if ($password !== $confirm)
		{
			$errors[] = 'Konfirmasi password tidak cocok.';
		}

		if ($errors)
		{
			$this->session->set_flashdata('error', implode(' ', $errors));
			redirect('admin/kelola-admin');
			return;
		}

		$this->admin_model->create($username, $password);
		$this->session->set_flashdata('success', 'Akun admin "' . $username . '" berhasil ditambahkan.');
		redirect('admin/kelola-admin');
	}

	/** POST: hapus akun admin. */
	public function delete($id)
	{
		if ( ! is_post())
		{
			redirect('admin/kelola-admin');
			return;
		}

		$id     = (int) $id;
		$target = $this->admin_model->find($id);

		if ( ! $target)
		{
			$this->session->set_flashdata('error', 'Akun admin tidak ditemukan.');
		}
		elseif ($id === (int) $this->admin['id'])
		{
			$this->session->set_flashdata('error', 'Tidak bisa menghapus akun yang sedang Anda pakai login.');
		}
		elseif ($this->admin_model->count_all() <= 1)
		{
			$this->session->set_flashdata('error', 'Minimal harus ada satu akun admin.');
		}
		else
		{
			$this->admin_model->delete_admin($id);
			$this->session->set_flashdata('success', 'Akun admin "' . $target->username . '" dihapus.');
		}

		redirect('admin/kelola-admin');
	}

	// ------------------------------------------------------------------

	/**
	 * Validasi username; kembalikan array pesan error (kosong kalau valid).
	 */
	protected function _validate_username($username, $ignore_id = 0)
	{
		$errors = array();

		if ($username === '' || strlen($username) < self::USERNAME_MIN)
		{
			$errors[] = 'Username minimal ' . self::USERNAME_MIN . ' karakter.';
		}
		elseif (strlen($username) > self::USERNAME_MAX)
		{
			$errors[] = 'Username maksimal ' . self::USERNAME_MAX . ' karakter.';
		}
		elseif ( ! preg_match(self::USERNAME_PATTERN, $username))
		{
			$errors[] = 'Username hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda hubung.';
		}
		elseif ($this->admin_model->username_taken($username, $ignore_id))
		{
			$errors[] = 'Username sudah dipakai akun lain.';
		}

		return $errors;
	}
}
