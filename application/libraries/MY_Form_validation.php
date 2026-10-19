<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Form Validation dengan pesan error default Bahasa Indonesia.
 * Pesan per rule bisa dioverride di controller via $this->form_validation->set_message().
 * {field} akan diganti dengan label field oleh CI3.
 */
class MY_Form_validation extends CI_Form_validation {

	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->set_message('required', '{field} wajib diisi.');
		$this->set_message('matches', '{field} tidak sama dengan {param}.');
		$this->set_message('min_length', '{field} minimal {param} karakter.');
		$this->set_message('max_length', '{field} maksimal {param} karakter.');
		$this->set_message('valid_email', '{field} bukan alamat email yang valid.');
		$this->set_message('valid_url', '{field} bukan URL yang valid.');
		$this->set_message('integer', '{field} harus berupa bilangan bulat.');
		$this->set_message('numeric', '{field} harus berupa angka.');
		$this->set_message('regex_match', 'Format {field} tidak sesuai.');
		$this->set_message('is_unique', '{field} sudah digunakan, silakan pilih yang lain.');
		$this->set_message('in_list', 'Pilihan {field} tidak valid.');
		$this->set_message('greater_than', '{field} harus lebih besar dari {param}.');
		$this->set_message('greater_than_equal_to', '{field} minimal {param}.');
		$this->set_message('less_than_equal_to', '{field} maksimal {param}.');
	}
}
