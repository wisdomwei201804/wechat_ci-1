<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->library('wechat');
	}

	public function open_id()
	{
		$user_info = $this -> wechat -> get_user_info();
		var_dump($user_info);die();
		$this->load->view('index',$user_info);
	}
	public function user_name()
	{
		$user_info = $this -> wechat -> get_user_info();
		$this->load->view('username',$user_info);
	}
	public function user_info()
	{

		$http = (isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!='off')?'https://':'http://';
		$url = $http.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
		$res = $this -> wechat -> jsSdk($url);
		$qrcode = $this -> wechat -> getQrcode(true,2330);
		$arr = array(
			"jsdata" => $res,
			"qrcode" => $qrcode
		);
		$this->load->view('userinfo',$arr);
	}	
}

