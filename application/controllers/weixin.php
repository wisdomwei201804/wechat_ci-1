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

    //验证
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    //回复消息
    public function responseMsg()
        {
            $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
            if (!empty($postStr)){
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $RX_TYPE = trim($postObj->MsgType);

                switch ($RX_TYPE)
                {
                    case "text"://文本
                        $resultStr = $this->receiveText($postObj);
                        break;
                    case "image"://图片
                        $resultStr = $this->receiveImage($postObj);
                        break;
                    case "location"://位置
                        $resultStr = $this->receiveLocation($postObj);
                        break;
                    case "voice"://声音
                        $resultStr = $this->receiveVoice($postObj);
                        break;
                    case "video"://视频
                        $resultStr = $this->receiveVideo($postObj);
                        break;
                    case "link"://链接
                        $resultStr = $this->receiveLink($postObj);
                        break;
                    case "event"://事件
                        $resultStr = $this->receiveEvent($postObj);
                        break;
                    default:
                        $resultStr = "unknow msg type: ".$RX_TYPE;
                        break;
                }
                echo $resultStr;
            }else {
                echo "";
                exit;
            }
        }
        //文本
        private function receiveText($object)
        {
            $funcFlag = 0;
            $contentStr = "你发送的是文本，内容为：".$object->Content;
            $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
            return $resultStr;
        }
        //图片
        private function receiveImage($object)
        {
            $funcFlag = 0;
            $contentStr = "你发送的是图片，地址为：".$object->PicUrl;
            $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
            return $resultStr;
        }
        //位置
        private function receiveLocation($object)
        {
            $funcFlag = 0;
            $contentStr = "你发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
            $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
            return $resultStr;
        }
        //声音
        private function receiveVoice($object)
        {
            $funcFlag = 0;
            $contentStr = "你发送的是语音，媒体ID为：".$object->MediaId;
            $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
            return $resultStr;
        }
        //视频
        private function receiveVideo($object)
        {
            $funcFlag = 0;
            $contentStr = "你发送的是视频，媒体ID为：".$object->MediaId;
            $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
            return $resultStr;
        }
        //链接
        private function receiveLink($object)
        {
            $funcFlag = 0;
            $contentStr = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
            $resultStr = $this->transmitText($object, $contentStr, $funcFlag);
            return $resultStr;
        }
        //事件
        private function receiveEvent($object)
        {
            $contentStr = "";

            switch ($object->Event)
            {
                case "subscribe":
                    $content = array();
                    $content[] = array(
                        "Title" =>"大学英语四六级成绩查询", //图文标题
                        "Description" =>"点击图片进入", //图文叙述
                        "PicUrl" =>"https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/bd_logo1_31bdc765.png", //图片地址
                        "Url" =>"http://israel.sinaapp.com/cet/index.php?openid=".$object->FromUserName
                        //跳转链接
                    );
                    break;
                case "unsubscribe":
                    $contentStr = "";
                    break;
                case "CLICK":
                    switch ($object->EventKey)
                    {
                        case "COMPANY":
                            $content = array();
                            $content[] = array(
                                "Title"=>"多图文1标题", 
                                "Description"=>"", 
                                "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", 
                                "Url" =>"http://m.cnblogs.com/?u=txw1958"
                            );
                            break;
                        default:
                            $contentStr = "你点击了: ".$object->EventKey;
                            break;
                    }
                    break;
                default:
                    $contentStr = "receive a new event: ".$object->Event;
                    break;
            }
            $resultStr = $this->transmitNews($object,$content);
            return $resultStr;
        }
        //回复信息的函数，文本
        private function transmitText($object, $content, $flag = 0)
        {
            $textTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>%d</FuncFlag>
                        </xml>";
            $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
            return $resultStr;
        }



        //回复信息的函数，图文消息
        private function transmitNews($object, $newsArray)
            {
                if(!is_array($newsArray)){
                    return;
                }
                $itemTpl = "<item>
                                <Title><![CDATA[%s]]></Title>
                                <Description><![CDATA[%s]]></Description>
                                <PicUrl><![CDATA[%s]]></PicUrl>
                                <Url><![CDATA[%s]]></Url>
                            </item>";
                $item_str = "";
                foreach ($newsArray as $item){
                    $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
                }
                $xmlTpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[news]]></MsgType>
                                <ArticleCount>%s</ArticleCount>
                                <Articles>$item_str</Articles>
                            </xml>";

                $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
                return $result;
            }





    
}