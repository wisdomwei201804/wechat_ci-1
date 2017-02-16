<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*

	js_sdk

*/
class Js_sdk extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->library('wechat');
	}

	public function js_sdk()
	{

		$http = (isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!='off')?'https://':'http://';
		$url = $http.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];//获取当前访问的ＵＲＬ
		$res = $this -> wechat -> jsSdk($url);
		$this->load->view('js_sdk',$res);
	}	
}