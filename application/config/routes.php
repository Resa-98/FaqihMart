<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
// ------------------------------------------------------------------
// Faqih Mart routes
// ------------------------------------------------------------------
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Halaman publik
$route['katalog']                 = 'catalog/index';
$route['kategori/(:any)']         = 'catalog/index/$1';
$route['produk/(:any)']           = 'catalog/detail/$1';
$route['informasi']               = 'home/about';

// Keranjang (semua POST — link/aksi GET sengaja tidak ada agar tidak bisa dipancing)
$route['keranjang']               = 'cart/index';
$route['cart/add']                = 'cart/add';
$route['cart/update']             = 'cart/update';
$route['cart/remove']             = 'cart/remove';

// Checkout
$route['checkout']                = 'checkout/index';
$route['checkout/proses']         = 'checkout/process';
$route['konfirmasi']              = 'checkout/confirmation';

// Admin
$route['admin/login']             = 'auth/login';
$route['admin/login/proses']      = 'auth/process_login';
$route['admin/logout']            = 'auth/logout';
$route['admin']                   = 'admin/dashboard';
$route['admin/produk']            = 'admin/products/index';
$route['admin/produk/tambah']     = 'admin/products/create';
$route['admin/produk/tambah/proses'] = 'admin/products/process_create';
$route['admin/produk/ubah/(:num)'] = 'admin/products/edit/$1';
$route['admin/produk/ubah/(:num)/proses'] = 'admin/products/process_edit/$1';
$route['admin/produk/hapus/(:num)'] = 'admin/products/delete/$1';
$route['admin/kategori']          = 'admin/categories/index';
$route['admin/kategori/tambah']   = 'admin/categories/create';
$route['admin/kategori/tambah/proses'] = 'admin/categories/process_create';
$route['admin/kategori/ubah/(:num)'] = 'admin/categories/edit/$1';
$route['admin/kategori/ubah/(:num)/proses'] = 'admin/categories/process_edit/$1';
$route['admin/kategori/hapus/(:num)'] = 'admin/categories/delete/$1';
$route['admin/pesanan']           = 'admin/orders/index';
$route['admin/pesanan/(:any)']    = 'admin/orders/detail/$1';
$route['admin/pesanan/(:any)/status'] = 'admin/orders/update_status/$1';
