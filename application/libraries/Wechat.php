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

｝