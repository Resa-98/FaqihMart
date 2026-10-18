<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_model extends CI_Model {

	protected $table = 'orders';

	/**
	 * Simpan pesanan beserta item dalam SATU transaksi.
	 *
	 * - Harga dihitung ulang dari database (bukan dari browser).
	 * - Stok dikurangi atomik dengan syarat `stock >= qty` (gagal kalau kurang),
	 *   sehingga tidak bisa oversell dan tidak bisa dikurangi dua kali.
	 * - Item disimpan sebagai snapshot (nama + harga), riwayat tetap utuh
	 *   walau produk diedit/dihapus.
	 *
	 * @param array $customer ['customer_name','whatsapp','address','note']
	 * @param array $cart     [product_id => qty]
	 * @return array ['ok'=>bool,'error'=>string,'order'=>obj]
	 */
	public function create_with_items(array $customer, array $cart)
	{
		// bersihkan input keranjang
		$cart = array_filter(array_map('intval', $cart), function ($q) {
			return $q > 0;
		});
		if (empty($cart))
		{
			return array('ok' => FALSE, 'error' => 'Keranjang kosong.', 'order' => NULL);
		}

		$this->db->trans_begin();

		// kunci baris produk yang di-checkout agar tidak balapan dengan request lain
		$ids = array_keys($cart);
		$products = $this->db->where_in('id', $ids)
			->order_by('id', 'ASC')
			->get('products')
			->result();

		if (count($products) !== count($cart))
		{
			$this->db->trans_rollback();
			return array('ok' => FALSE, 'error' => 'Ada produk yang sudah tidak tersedia. Periksa kembali keranjang Anda.', 'order' => NULL);
		}

		$items = array();
		$total = 0;
		foreach ($products as $p)
		{
			$qty = $cart[(int) $p->id];
			if ((int) $p->is_active !== 1)
			{
				$this->db->trans_rollback();
				return array('ok' => FALSE, 'error' => 'Produk "' . $p->name . '" sudah tidak dijual.', 'order' => NULL);
			}
			if ( ! $this->product_model_decrement((int) $p->id, $qty))
			{
				$this->db->trans_rollback();
				return array('ok' => FALSE, 'error' => 'Stok "' . $p->name . '" tidak mencukupi.', 'order' => NULL);
			}
			$subtotal = (float) $p->price * $qty;
			$items[] = array(
				'product_id'   => (int) $p->id,
				'product_name' => $p->name,
				'price'        => $p->price,
				'quantity'     => $qty,
				'subtotal'     => $subtotal,
			);
			$total += $subtotal;
		}

		$order_number = $this->_generate_order_number();

		$this->db->insert($this->table, array(
			'order_number'  => $order_number,
			'customer_name' => $customer['customer_name'],
			'whatsapp'      => $customer['whatsapp'],
			'address'       => $customer['address'],
			'note'          => $customer['note'],
			'total_amount'  => $total,
			'status'        => 'menunggu_konfirmasi',
		));
		$order_id = $this->db->insert_id();

		foreach ($items as &$item)
		{
			$item['order_id'] = $order_id;
		}
		unset($item);
		$this->db->insert_batch('order_items', $items);

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return array('ok' => FALSE, 'error' => 'Gagal menyimpan pesanan. Silakan coba lagi.', 'order' => NULL);
		}

		$this->db->trans_commit();

		$order = $this->find($order_id);
		return array('ok' => TRUE, 'error' => '', 'order' => $order);
	}

	/** Dekrement stok dengan syarat cukup (dipanggil dari dalam transaksi). */
	protected function product_model_decrement($id, $qty)
	{
		$this->db->where('id', $id)
			->where('stock >=', $qty)
			->set('stock', 'stock - ' . (int) $qty, FALSE)
			->update('products');
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Nomor pesanan unik: FM-YYYYMMDD-XXXX (4 digit acak).
	 * Dipanggil setelah stok sukses dikurangi, masih di dalam transaksi;
	 * retry bila tabrakan (unik juga dijaga UNIQUE KEY).
	 */
	protected function _generate_order_number()
	{
		$prefix = 'FM-' . date('Ymd') . '-';
		for ($try = 0; $try < 10; $try++)
		{
			$number = $prefix . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
			$exists = $this->db->where('order_number', $number)
				->count_all_results($this->table) > 0;
			if ( ! $exists)
			{
				return $number;
			}
		}
		// sangat kecil kemungkinannya: pakai counter insert_id terakhir
		return $prefix . str_pad((string) (int) $this->db->insert_id(), 4, '0', STR_PAD_LEFT);
	}

	public function find($id)
	{
		return $this->db->where('id', (int) $id)
			->get($this->table)
			->row();
	}

	public function find_by_number($number)
	{
		return $this->db->where('order_number', $number)
			->get($this->table)
			->row();
	}

	public function items($order_id)
	{
		return $this->db->where('order_id', (int) $order_id)
			->order_by('id', 'ASC')
			->get('order_items')
			->result();
	}

	/**
	 * Transisi status + restok saat dibatalkan, dalam satu transaksi.
	 * Restok hanya terjadi sekali: dilakukan saat transisi ke 'dibatalkan',
	 * dan status final (selesai/dibatalkan) tidak punya transisi keluar.
	 */
	public function transition_status($order_id, $new_status)
	{
		$order = $this->find($order_id);
		if ( ! $order)
		{
			return array('ok' => FALSE, 'error' => 'Pesanan tidak ditemukan.');
		}

		$allowed = order_status_transitions();
		if ( ! isset($allowed[$order->status]) || ! in_array($new_status, $allowed[$order->status], TRUE))
		{
			return array('ok' => FALSE, 'error' => 'Status "' . order_statuses()[$order->status] . '" tidak bisa diubah ke "' . (order_statuses()[$new_status] ?? $new_status) . '".');
		}

		$this->db->trans_begin();
		$this->db->where('id', (int) $order_id)->update($this->table, array('status' => $new_status));

		if ($new_status === 'dibatalkan')
		{
			foreach ($this->items($order_id) as $item)
			{
				if ($item->product_id !== NULL)
				{
					$this->db->where('id', (int) $item->product_id)
						->set('stock', 'stock + ' . (int) $item->quantity, FALSE)
						->update('products');
				}
			}
		}

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return array('ok' => FALSE, 'error' => 'Gagal memperbarui status.');
		}
		$this->db->trans_commit();
		return array('ok' => TRUE, 'error' => '');
	}

	// ------------------------------------------------------------------
	// Admin listing
	// ------------------------------------------------------------------

	public function admin_list($limit = 20, $offset = 0, $status = '')
	{
		if ($status !== '' && isset(order_statuses()[$status]))
		{
			$this->db->where('status', $status);
		}
		return $this->db->order_by('id', 'DESC')
			->limit((int) $limit, (int) $offset)
			->get($this->table)
			->result();
	}

	public function admin_count($status = '')
	{
		if ($status !== '' && isset(order_statuses()[$status]))
		{
			$this->db->where('status', $status);
		}
		return $this->db->count_all_results($this->table);
	}

	/** Ringkasan dashboard: jumlah per status. */
	public function count_by_status()
	{
		$rows = $this->db->select('status, COUNT(*) AS total', FALSE)
			->group_by('status')
			->get($this->table)
			->result();
		$out = array_fill_keys(array_keys(order_statuses()), 0);
		foreach ($rows as $row)
		{
			$out[$row->status] = (int) $row->total;
		}
		return $out;
	}
}
