<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
*/
$route['default_controller'] = 'beranda';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// API Routes
$route['api'] = 'api/beranda/index';
$route['api/(:any)'] = 'api/$1';
$route['api/(:any)/(:any)'] = 'api/$1/$2';
$route['api/(:any)/(:any)/(:any)'] = 'api/$1/$2/$3';
