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

	/**
	 * 发送模板消息
	 * @param string $openid　用户openid
	 * @param string $tempid　模板id
	 * @param string $url　跳转的路径
	 * @return boolean
	 */
	function sendTemplateMsg($openid,$tempid,$url){
	    $access_token = $this -> checkAuth();
	    $send_url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
	    $array = array(
	        "touser"=>$openid,
	        "template_id"=>$tempid,
	        "url"=>$url, 
	        "data" => array(
	            "name" =>array("value"=>"hello！","color"=>"#173177"),
	            "money" =>array("value"=>"111","color"=>"#173177"),
	            "date" =>array("value"=>date('Y-m-d H:i:s'),"color"=>"#173177")
	        ), 
	    );
	    $postJson = json_encode($array);
	    $res = $this->http_post($send_url,$postJson);
	    return $res;
	}

	/**
	 * 获取open_id
	 * 获取openid必须先进行网页授权，具体看手册
	 * @return openid
	 */
	function get_open_id() {
	    if (isset($_GET['code'])) {
	        $code = $_GET['code'];
	    } else {
	        echo "NO CODE";
	    }
	    // 运行cURL，请求网页
	    $data = $this -> http_post('https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . APPID . '&secret=' . APPSECRET . '&code=' . $code . '&grant_type=authorization_code');
	    // 获取access_token；
	    $reg = "#{.+}#";
	    preg_match_all($reg, $data, $matches);
	    $json = $matches[0][0];
	    $accessArr = json_decode($json, true);
	    $openid = $accessArr['openid'];
	    return $openid;
	}

	/**
	 * 获取用户信息
	 * 可以传递指定opeid,获取指定用户的信息
	 * 不传递参数时，获取当前用户信息，前提必须先进行网页授权
	 * @param string $openid　用户openid
	 * @return array $user_info
	 */
	function get_user_info($openid=NULL) {
	    $access_token = $this -> checkAuth();
	    if(!$openid){
	        $openid = $this -> get_open_id();
	    }
	    $user_info = $this -> http_get('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid);
	    $user_info = json_decode($user_info);
	    return $user_info;
	}

	/**
	 * 获取openid列表
	 * @param string $next_openid　第一个拉取的OPENID，不填默认从头开始拉取
	 * 一次拉取调用最多拉取10000个关注者的OpenID，可以通过设置$next_openid多次拉取的方式来满足需求。
	 * @return array $openid_list
	 */
	function open_id_list($next_openid = null){
	    $access_token = $this -> checkAuth();
	    $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$access_token."&next_openid=".$next_openid;
	    $openid_list = $this -> http_get($url);
	    $openid_list = json_decode($openid_list);
	    return $openid_list -> data->openid;
	}


	
    /**
	 * 生成带参数的二维码票据
	 * @param boolean $bool　tuer：永久，false：为临时，
	 * @param string $scene_id　参数
	 * @return string 二维码票据
	 */
    function getQrcodeTicket($bool,$scene_id){
        if($bool){
            $array = array(
                "action_name"=>"QR_LIMIT_SCENE",
                "action_info" => array(
                    "scene" =>array("scene_id"=>$scene_id)
                )
            );

        }else{
            $array = array(
                "expire_seconds"=>604800,//有效时间
                "action_name"=>"QR_SCENE",//二维码类型
                "action_info" => array(
                    "scene" =>array("scene_id"=>$scene_id)//参数
                ), 
            );
        }
        $access_token = $this -> checkAuth();
        $send_url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
        $postJson = json_encode($array);
        $res = $this->http_post($send_url,$postJson);
        $res = json_decode($res);
        return $res->ticket;
    }

     /**
	 * 生成带参数的二维码
	 * @param boolean $bool　tuer：永久，false：为临时，
	 * @param string $scene_id　参数
	 * @return string 二维码票据的html代码
	 */
    function getQrcode($bool,$scene_id){
        $qrcode_ticket = $this -> getQrcodeTicket($bool,$scene_id);
        $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($qrcode_ticket);
        $res = '<img src="'.$url.'" alt="">';
        return $res;

    }




｝