<?php
namespace app\wechat\controller;

include 'D:\PHP\wx\application\wechat\controller\Wechat.class.php';
include 'D:\PHP\wx\application\wechat\controller\Api.class.php';

/**
 */
class WxController
{
    public function valid()
    {

        //检查消息是否来自微信
        if (!$this->checkSignature()) {
            //非法
            echo "非法操作";
        }

        $wechat = new Wechat(array(
            'appId' => '',
            'token' => '',
            'encodingAESKey' => ''
        ));

        //获取微信消息
        $msg = $wechat->serve();

        //回复微信消息
        if ($msg->MsgType == 'text' && $msg->Content == '你好') {
            $wechat->reply("你也好！");
        } else {
            $wechat->reply("听不懂");
        }
        // api模块 - 包含各种系统主动发起的功能
        $api = new Api(
            array(
                'appId' => '',
                'appSecret' => '',
//                'get_access_token' => function () {
//                    // 用户需要自己实现access_token的返回
//                    return S('weixin');
//                },
//                'save_access_token' => function ($token) {
//                    // 用户需要自己实现access_token的保存
//                    S('wechat_token', $token);
//                }
            )
        );

        $api->create_menu('{
    "button":[
        {
          "type":"click",
          "name":"在线申请",
          "sub_button":[
            {
                "type":"view",
                "name":"ETC申请",
                "key":"www.baidu.com"
            },
             {
                "type":"view",
                "name":"查询进度",
                "key":"www.baidu.com"
            }
          ]
        },
        {
            "name":"预约激活",
            "sub_button":[
                {
                    "type":"click",
                    "name":"点击推事件",
                    "key":"click_event1"
                },
                {
                    "type":"view",
                    "name":"wechat",
                    "url":"http://210812d6.nat123.net/wechat/application/wechat/controller/Demo.html"
                },
                {
                    "type":"scancode_push",
                    "name":"扫码推事件",
                    "key":"scancode_push_event1"
                },
                {
                    "type":"scancode_waitmsg",
                    "name":"扫码带提示",
                    "key":"scancode_waitmsg_event1"
                }
            ]
       },
       {
            "name":"主菜单3",
            "sub_button":[
                {
                    "type":"pic_sysphoto",
                    "name":"系统拍照发图",
                    "key":"pic_sysphoto_event1"
                },
                {
                    "type":"pic_photo_or_album",
                    "name":"拍照或者相册发图",
                    "key":"pic_photo_or_album_event1"
                },
                {
                    "type":"pic_weixin",
                    "name":"微信相册发图",
                    "key":"pic_weixin_event1"
                },
                {
                    "type":"location_select",
                    "name":"发送位置",
                    "key":"location_select_event1"
                }
            ]
       }
    ]}');

        // 获取微信消息
        $msg = $wechat->serve();

        // 回复微信消息
        if ($msg->MsgType == 'text' && $msg->Content == '你好') {
            $wechat->reply("你也好！");
        } else {
            $wechat->reply("听不懂！");
        }

//        //检查消息是否来自微信
//        if (!$this->checkSignature()) {
//            //非法
//            echo "非法操作";
//        }
//        //获取POST参数
//        $param = file_get_contents("php://input");
//        if (!empty($postStr)) {
//            //获取信息
//            $postObj = simplexml_load_string($param, 'SimpleXMLElement', LIBXML_NOCDATA);
//            $to_user = $postObj->ToUserName;
//            $from_user = $postObj->FromUserName;
//            //回复的内容
//            $content = $postObj->Content;
////        $content = 'http://xczx.com/';
//            $response_text = "<xml>
//                                <ToUserName><![CDATA[%s]]></ToUserName>
//                                <FromUserName><![CDATA[%s]]></FromUserName>
//                                <CreateTime>%s</CreateTime>
//                                <MsgType><![CDATA[text]]></MsgType>
//                                <Content><![CDATA[你好]]></Content>
//                                </xml>";
//            $response_text = sprintf($response_text, $to_user, $from_user, time());
//            echo $response_text;
//        } else {
//            echo "";
//            exit;
//        }
    }

    public function responseMsg()
    {
        log(13232);
        $postStr = file_get_contents("php://input");
        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <FuncFlag>0<FuncFlag>
            </xml>";

            if (!empty($keyword)) {
                $msgType = "text";
                $contentStr = "success";
                $resultStr = sprintf($textTpl, $toUsername, $fromUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            } else {
                echo "说点什么吧";
            }
        } else {
            echo "没有获取到数据";
            exit;
        }
    }

    function checkSignature()
    {
        //获取参数
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = "weixin";
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        //验证
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
}

?>