<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
*/
$route['default_controller'] = 'beranda';
$route['404_override'] = '';
$route['translate_uri_dashes'] = TRUE;

// API Routes
// Endpoint dengan dash (mis. /api/peserta/daftar-kelas) dipetakan ke method
// underscore (daftar_kelas) lewat translate_uri_dashes di atas.
$route['api/katalog/program/(:num)'] = 'api/katalog/detail/$1';
$route['api/katalog/kelas/(:num)'] = 'api/katalog/kelas_detail/$1';
$route['api'] = 'api/beranda/index';
$route['api/(:any)'] = 'api/$1';
$route['api/(:any)/(:any)'] = 'api/$1/$2';
$route['api/(:any)/(:any)/(:any)'] = 'api/$1/$2/$3';
