<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Weixin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //加载微信类库
        $this->load->library('wechat');

        if (isset($_GET['echostr'])) {
            $this->valid();
        }else{
            $this->responseMsg();
        }
    }









    
}