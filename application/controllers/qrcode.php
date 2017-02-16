<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*

	生成带参数二维码

*/
class Qrcode extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->library('wechat');
	}

	public function index()
	{

		$qrcode = $this -> wechat -> getQrcode(true,2330);
		$arr = array(
			"qrcode" => $qrcode
		);
		$this->load->view('qrcode',$arr);
	}	
}