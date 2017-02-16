<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Userinfo extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->library('wechat');
	}

	public function index()
	{

		$user_info = $this -> wechat -> get_user_info();
		$arr = array(
			"userinfo" => $user_info
		);
		$this->load->view('userinfo',$arr);
	}	
}