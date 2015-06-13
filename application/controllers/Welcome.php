<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {


	public function index()
	{
		//$this->load->view('welcome_message');
		$query=$this->db->get('employees');
		foreach($query->result() as $row){
			echo $row->first_name;
			echo " " ;
		}
	}
}
