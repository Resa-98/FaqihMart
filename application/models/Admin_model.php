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
}
