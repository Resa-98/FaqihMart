<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

	protected $table = 'admins';

	public function find_by_username($username)
	{
		return $this->db->where('username', $username)
			->get($this->table)
			->row();
	}

	public function verify($username, $password)
	{
		$admin = $this->find_by_username($username);
		if ($admin && password_verify($password, $admin->password))
		{
			return $admin;
		}
		return NULL;
	}

	/** Semua akun admin, urut lama ke baru. */
	public function all()
	{
		return $this->db->order_by('id', 'ASC')->get($this->table)->result();
	}

	public function find($id)
	{
		return $this->db->where('id', (int) $id)->get($this->table)->row();
	}

	public function count_all()
	{
		return (int) $this->db->count_all($this->table);
	}

	/** TRUE kalau username sudah dipakai akun lain (boleh mengabaikan satu id). */
	public function username_taken($username, $ignore_id = 0)
	{
		$this->db->where('username', $username);
		if ((int) $ignore_id > 0)
		{
			$this->db->where('id !=', (int) $ignore_id);
		}
		return $this->db->count_all_results($this->table) > 0;
	}

	/** Tambah akun admin; password di-hash di sini. */
	public function create($username, $password)
	{
		return $this->db->insert($this->table, array(
			'username'   => $username,
			'password'   => password_hash($password, PASSWORD_DEFAULT),
			'created_at' => date('Y-m-d H:i:s'),
		));
	}

	/** Update kolom tertentu (mis. username dan/atau password yang sudah di-hash). */
	public function update_admin($id, array $fields)
	{
		return $this->db->where('id', (int) $id)->update($this->table, $fields);
	}

	public function delete_admin($id)
	{
		return $this->db->where('id', (int) $id)->delete($this->table);
	}
}
