<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model {

	protected $table = 'products';

	/**
	 * Katalog publik: hanya produk aktif + kategori aktif.
	 * $category_slug dan $q bersifat opsional. Pagination hand-rolled.
	 */
	public function catalog(array $filters = array(), $limit = 12, $offset = 0)
	{
		$this->db->select('products.*, categories.name AS category_name, categories.slug AS category_slug')
			->from('products')
			->join('categories', 'categories.id = products.category_id')
			->where('products.is_active', 1)
			->where('categories.is_active', 1);

		if ( ! empty($filters['category_slug']))
		{
			$this->db->where('categories.slug', $filters['category_slug']);
		}
		if ( ! empty($filters['q']))
		{
			// escape karakter wildcard agar pencarian tidak jadi pola regex/like liar
			$this->db->like('products.name', $this->db->escape_like_str($filters['q']));
		}

		return $this->db->order_by('products.name', 'ASC')
			->limit((int) $limit, (int) $offset)
			->get()
			->result();
	}

	public function catalog_count(array $filters = array())
	{
		$this->db->from('products')
			->join('categories', 'categories.id = products.category_id')
			->where('products.is_active', 1)
			->where('categories.is_active', 1);

		if ( ! empty($filters['category_slug']))
		{
			$this->db->where('categories.slug', $filters['category_slug']);
		}
		if ( ! empty($filters['q']))
		{
			$this->db->like('products.name', $this->db->escape_like_str($filters['q']));
		}

		return $this->db->count_all_results();
	}

	/** Detail publik: produk aktif + kategori aktif, by slug. */
	public function find_active_by_slug($slug)
	{
		return $this->db->select('products.*, categories.name AS category_name, categories.slug AS category_slug')
			->from('products')
			->join('categories', 'categories.id = products.category_id')
			->where('products.slug', $slug)
			->where('products.is_active', 1)
			->where('categories.is_active', 1)
			->get()
			->row();
	}

	/** Produk terbaru untuk beranda. */
	public function latest_active($limit = 8)
	{
		return $this->db->where('is_active', 1)
			->order_by('id', 'DESC')
			->limit((int) $limit)
			->get($this->table)
			->result();
	}

	/** Ambil banyak produk by id — dipakai checkout untuk hitung ulang dari DB. */
	public function find_many(array $ids)
	{
		$ids = array_map('intval', $ids);
		if (empty($ids))
		{
			return array();
		}
		return $this->db->where_in('id', $ids)
			->get($this->table)
			->result();
	}

	/**
	 * Jumlah terjual per produk: total kuantitas di order_items dari pesanan
	 * yang TIDAK dibatalkan (pesanan batal = stok dikembalikan, bukan penjualan).
	 *
	 * @param  int[] $ids
	 * @return array [product_id => qty_terjual]
	 */
	public function sold_counts(array $ids)
	{
		$ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
		if (empty($ids))
		{
			return array();
		}

		$rows = $this->db->select('oi.product_id, SUM(oi.quantity) AS sold', FALSE)
			->from('order_items oi')
			->join('orders o', 'o.id = oi.order_id')
			->where_in('oi.product_id', $ids)
			->where('o.status !=', 'dibatalkan')
			->group_by('oi.product_id')
			->get()
			->result();

		$out = array();
		foreach ($rows as $row)
		{
			$out[(int) $row->product_id] = (int) $row->sold;
		}
		return $out;
	}

	/**
	 * Tempelkan properti ->sold ke tiap produk (0 kalau belum pernah terjual).
	 * Objek stdClass bersifat by-reference, jadi array yang sama ikut terubah.
	 */
	public function attach_sold(array $products)
	{
		$ids = array();
		foreach ($products as $p)
		{
			if (isset($p->id))
			{
				$ids[] = (int) $p->id;
			}
		}
		$map = $this->sold_counts($ids);
		foreach ($products as $p)
		{
			$p->sold = isset($map[(int) $p->id]) ? $map[(int) $p->id] : 0;
		}
		return $products;
	}

	/** Hanya untuk satu produk (detail). */
	public function attach_sold_one($product)
	{
		if ($product && isset($product->id))
		{
			$map = $this->sold_counts(array($product->id));
			$product->sold = isset($map[(int) $product->id]) ? $map[(int) $product->id] : 0;
		}
		return $product;
	}

	// ------------------------------------------------------------------
	// Admin
	// ------------------------------------------------------------------

	public function admin_list($limit = 20, $offset = 0, $q = '')
	{
		$this->db->select('products.*, categories.name AS category_name')
			->from('products')
			->join('categories', 'categories.id = products.category_id', 'left');
		if ($q !== '')
		{
			$this->db->like('products.name', $this->db->escape_like_str($q));
		}
		return $this->db->order_by('products.id', 'DESC')
			->limit((int) $limit, (int) $offset)
			->get()
			->result();
	}

	public function admin_count($q = '')
	{
		$this->db->from('products');
		if ($q !== '')
		{
			$this->db->like('name', $this->db->escape_like_str($q));
		}
		return $this->db->count_all_results();
	}

	public function find($id)
	{
		return $this->db->where('id', (int) $id)
			->get($this->table)
			->row();
	}

	public function create(array $data)
	{
		$data['slug'] = $this->_unique_slug(isset($data['slug']) ? $data['slug'] : $data['name']);
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update_product($id, array $data)
	{
		if (isset($data['name']) && ! isset($data['slug']))
		{
			$data['slug'] = $this->_unique_slug($data['name'], (int) $id);
		}
		$this->db->where('id', (int) $id)->update($this->table, $data);
	}

	/**
	 * Kurangi stok atomik hanya jika stok cukup.
	 * Return TRUE jika stok berkurang, FALSE jika tidak cukup/tidak ada.
	 * Dipakai di dalam transaksi checkout.
	 */
	public function decrement_stock($id, $qty)
	{
		$this->db->where('id', (int) $id)
			->where('stock >=', (int) $qty)
			->set('stock', 'stock - ' . (int) $qty, FALSE)
			->update($this->table);
		return $this->db->affected_rows() > 0;
	}

	/** Tambah stok kembali saat pesanan dibatalkan. */
	public function restore_stock($id, $qty)
	{
		$this->db->where('id', (int) $id)
			->set('stock', 'stock + ' . (int) $qty, FALSE)
			->update($this->table);
	}

	public function delete_product($id)
	{
		// order_items.product_id -> ON DELETE SET NULL, riwayat pesanan tetap utuh
		$this->db->where('id', (int) $id)->delete($this->table);
	}

	protected function _unique_slug($source, $ignore_id = 0)
	{
		$slug = strtolower(trim($source));
		$slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
		$slug = trim($slug, '-');
		if ($slug === '')
		{
			$slug = 'produk-' . time();
		}
		$base = $slug;
		$i = 2;
		while (TRUE)
		{
			$this->db->where('slug', $slug);
			if ($ignore_id > 0)
			{
				$this->db->where('id !=', $ignore_id);
			}
			if ($this->db->count_all_results($this->table) === 0)
			{
				break;
			}
			$slug = $base . '-' . $i++;
		}
		return $slug;
	}
}
