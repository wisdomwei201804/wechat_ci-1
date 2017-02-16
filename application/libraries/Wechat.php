<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

define("TOKEN", "weixxxxxxin");//用于微信第一次验证
define("APPID", "wx6fxxxxxxx25ccd9");//微信公众号的APPID
define("APPSECRET", "f17c7c6xxxxxxxxxxxxx0ab");//微信的APPSECRET

class CI_Wechat {//定义微信类

	private $_CI;
	private $access_token;
	
	public function __construct() {

	    $this->_CI =& get_instance();//用于自定义的类
	    //加载cache
	    $this->_CI->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));

	}

	/**
	 * 构造http请求，post方式
	 * @param url string 请求路径
	 * @param post_data json格式的字符串
	 * @return http请求结果
	 */
	private function http_post($url, $post_data = '') {
	     $curl = curl_init();
	     curl_setopt($curl, CURLOPT_URL, $url);
	     curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	     curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	     curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	     if ($post_data) {
	         curl_setopt($curl, CURLOPT_POST, 1);
	         curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
	     }
	     $result = curl_exec($curl);
	     if (curl_errno($curl)) {
	         return 'Errno' . curl_error($curl);
	     }
	     curl_close($curl);
	     return $result;
	 }

｝