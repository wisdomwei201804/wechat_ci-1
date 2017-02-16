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
	 /*
	
		由于微信的access_token的有效期为７２００秒，而且access_token有次数限制，
		所以为了解决，在access_token有效期不去重新获取access_token，我们使用缓存
		把access_token保存起来并且有效期设置小于7200秒，当我们使用access_token时
		首先判断缓存的access_token是否有效，如果有效直接使用缓存的access_token，如果无效
		就是用http请求再次获取access_token，并且更新缓存。

	 */
	
	/**
	 * 获取access_token
	 * @param string $appid 如在类初始化时已提供，则可为空
	 * @param string $appsecret 如在类初始化时已提供，则可为空
	 * @param string $token 手动指定access_token，非必要情况不建议用
	 */
	private function checkAuth(){
	    $authname = 'wechat_access_token_'.APPID;
	    if ($rs = $this->getCache($authname))  {
	        $this->access_token = $rs;
	        return $rs;
	    }



	    $result = $this->http_get('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.APPID.'&secret='.APPSECRET);
	    if ($result)
	    {
	        $json = json_decode($result,true);
	        if (!$json || isset($json['errcode'])) {
	            $this->errCode = $json['errcode'];
	            $this->errMsg = $json['errmsg'];
	            return false;
	        }
	        $this->access_token = $json['access_token'];
	        $expire = $json['expires_in'] ? intval($json['expires_in'])-100 : 3600;
	        $this->setCache($authname,$this->access_token,$expire);
	        return $this->access_token;
	    }
	    return false;
	}


	/**
	 * 重载设置缓存
	 * @param string $cachename
	 * @param mixed $value
	 * @param int $expired
	 * @return boolean
	 */
	private function setCache($cachename, $value, $expired) {
	    return $this->_CI->cache->save($cachename, $value, $expired);
	}

	/**
	 * 重载获取缓存
	 * @param string $cachename
	 * @return mixed
	 */
	private function getCache($cachename) {
	    return $this->_CI->cache->get($cachename);
	}

	/**
	 * 重载清除缓存
	 * @param string $cachename
	 * @return boolean
	 */
	private function removeCache($cachename) {
	    return $this->_CI->cache->delete($cachename);
	}








｝