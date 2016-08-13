<?php
/**
 * wechat php test
 */

//define your token
//error_reporting(0);
define("TOKEN", "myfiliun");
define("APPID","wx9e5c18915d89d4af");
define("secret","8aef499e8b97d21a373746edf81ed549");

require './WetChat.class.php';
require './function.php';
require './wechatCallbackapiTest.class.php';


//获取access_token  手动设置啊 

cancleFocus();

$a=new WetChat();
$access_token=$a->GetAccessToken();

echo $access_token;

exit;


$auth_url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=https://houhaibo1xiaoyao.applinzi.com/tree.php&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";

$url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
$json=' {
     "button":[
     {	
          "type":"click",
          "name":"今日书籍",
          "key":"V1001_BOOK"
      },
      {
           "name":"菜单",
           "sub_button":[
           {	
               "type":"view",
               "name":"搜索",
               "url":"http://www.soso.com/"
            },
            {
               "type":"view",
               "name":"林荫小筑",
               "url":"'.$auth_url.'"
            },
            {
               "type":"click",
               "name":"今日要闻",
               "key":"V1001_NEWS"
            },
            {
               "type":"click",
               "name":"获取书目",
               "key":"V1001_GOOD"
            }]
       }]
 }';
$out=$a->SendPost($url,$json);
$wechatObj = new wechatCallbackapiTest();
if($wechatObj->checkSignature()){
    $mediaId=$wechatObj->getMediaID();
    if(!empty($mediaId)){
        $url="https://api.weixin.qq.com/cgi-bin/media/get?access_token=".$access_token."&media_id=".$mediaId;
        $wechatObj->responseMsg($url);
    }
}



