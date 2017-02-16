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
	 * 在微信开发时，很多功能（ＡＰＩ）都是通过http请求完成的，
	 *　例如：获取用户信息，获取js_api_token等等
	 *　所以封装http请求函数
	*/

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

	 /**
	  * 构造http请求，get方式
	  * @param string $url
	  * @return http请求结果
	  */
	 private function http_get($url){
	     $oCurl = curl_init();
	     if(stripos($url,"https://")!==FALSE){
	         curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
	         curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
	         curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
	     }
	     curl_setopt($oCurl, CURLOPT_URL, $url);
	     curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
	     $sContent = curl_exec($oCurl);
	     $aStatus = curl_getinfo($oCurl);
	     curl_close($oCurl);
	     if(intval($aStatus["http_code"])==200){
	         return $sContent;
	     }else{
	         return false;
	     }
	 }









｝