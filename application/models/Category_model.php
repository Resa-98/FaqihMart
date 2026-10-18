<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model {

	protected $table = 'categories';

	/** Kategori aktif untuk navigasi publik (urut nama). */
	public function active_list()
	{
		return $this->db->where('is_active', 1)
			->order_by('name', 'ASC')
			->get($this->table)
			->result();
	}

	/** Semua kategori (admin, termasuk non-aktif). */
	public function all()
	{
		return $this->db->order_by('name', 'ASC')
			->get($this->table)
			->result();
	}

	public function find($id)
	{
		return $this->db->where('id', (int) $id)
			->get($this->table)
			->row();
	}

	public function find_by_slug($slug)
	{
		return $this->db->where('slug', $slug)
			->get($this->table)
			->row();
	}

	public function create(array $data)
	{
		$data['slug'] = $this->_slugify($data['name']);
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update_category($id, array $data)
	{
		if (isset($data['name']))
		{
			// slug hanya diganti jika belum dipakai produk lain
			$new_slug = $this->_slugify($data['name']);
			$in_use = $this->db->where('slug', $new_slug)
				->where('id !=', (int) $id)
				->count_all_results('products') > 0;
			if ( ! $in_use)
			{
				$data['slug'] = $new_slug;
			}
		}
		$this->db->where('id', (int) $id)->update($this->table, $data);
	}

	/** Kategori masih punya produk? (tolak hapus agar FK tidak error) */
	public function has_products($id)
	{
		return $this->db->where('category_id', (int) $id)
			->count_all_results('products') > 0;
	}

	public function delete_category($id)
	{
		$this->db->where('id', (int) $id)->delete($this->table);
	}

	public function product_count($id)
	{
		return $this->db->where('category_id', (int) $id)
			->count_all_results('products');
	}

	protected function _slugify($name)
	{
		$slug = strtolower(trim($name));
		$slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
		$slug = trim($slug, '-');

		// pastikan unik
		$base = $slug;
		$i = 2;
		while ($this->db->where('slug', $slug)->count_all_results($this->table) > 0)
		{
			$slug = $base . '-' . $i++;
		}
		return $slug !== '' ? $slug : 'kategori-' . time();
	}
}
