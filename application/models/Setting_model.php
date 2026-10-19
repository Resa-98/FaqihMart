<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pengaturan toko sederhana (key/value) yang bisa diubah dari panel admin.
 * Dipakai antara lain untuk nomor WhatsApp tujuan konfirmasi pesanan.
 */
class Setting_model extends CI_Model {

	protected $table = 'settings';

	/**
	 * Ambil satu nilai pengaturan. $default dipakai kalau belum pernah diset.
	 */
	public function get($name, $default = NULL)
	{
		$row = $this->db->select('value')
			->where('name', $name)
			->get($this->table)
			->row();
		return ($row && $row->value !== NULL && $row->value !== '') ? $row->value : $default;
	}

	/**
	 * Simpan (insert kalau baru, update kalau sudah ada).
	 */
	public function set($name, $value)
	{
		$exists = $this->db->where('name', $name)->count_all_results($this->table) > 0;

		if ($exists)
		{
			return $this->db->where('name', $name)->update($this->table, array(
				'value'      => $value,
				'updated_at' => date('Y-m-d H:i:s'),
			));
		}

		return $this->db->insert($this->table, array(
			'name'       => $name,
			'value'      => $value,
			'updated_at' => date('Y-m-d H:i:s'),
		));
	}

	/**
	 * Ambil semua pengaturan sebagai array [name => value].
	 */
	public function all()
	{
		$out = array();
		foreach ($this->db->get($this->table)->result() as $row)
		{
			$out[$row->name] = $row->value;
		}
		return $out;
	}
}
