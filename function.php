<?php

function dir_writeable($dir)
{
    if (!is_dir($dir)) {
        @mkdir($dir, 0777);
    }
    if (is_dir($dir)) {
        if ($fp = @fopen("$dir/test.txt", 'w')) {
            @fclose($fp);
            @unlink("$dir/test.txt");
            $writeable = 1;
        } else {
            $writeable = 0;
        }
    }
    return $writeable;
}

function SendGet($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    // $output=json_decode($output,true);
    // return $output;
}

function sock_get($url)
{

    $info = parse_url($url);
    $fp = fsockopen($info["host"], 80, $errno, $errstr, 3);
    $head = "GET " . $info['path'] . "?" . $info["query"] . " HTTP/1.0\r\n";
    $head .= "Host: " . $info['host'] . "\r\n";
    $head .= "\r\n";
    $write = fputs($fp, $head);
    while (!feof($fp)) {

        $line = fgets($fp);

        echo $line . "<br>";

    }

}

function cancleFocus()
{
    $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
    if (!empty($postStr)) {

        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $event = $postObj->Event;

        $a = new WetChat();
        $time = time();
        $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
        if ($event == "subscribe") {

            $access_token = $a->GetAccessToken();
            $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$fromUsername."&lang=zh_CN ";
            $userinfo=$a->SendGet($url);
            if($userinfo['sex'] == 1){
                $contentStr = "欢迎".$userinfo['nickname']."先生订阅逍遥侯之文学阁。习文以明智，相交而益识！";
            }
            if($userinfo['sex'] == 2){
                $contentStr = "欢迎".$userinfo['nickname']."女士订阅逍遥侯之文学阁。习文以明智，相交而益识！";
            }
            if($userinfo['sex'] == 0){
                $contentStr = "欢迎".$userinfo['nickname']."订阅逍遥侯之文学阁。习文以明智，相交而益识！";
            }
            $msgType = "text";
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            echo $resultStr;
        }
        if ($event == "CLICK") {
            $eventKey = $postObj->EventKey;
            if ($eventKey == "V1001_GOOD") {
                $a = new WetChat();
                $json1 = ' {
           "touser":"' . $fromUsername . '",
           "template_id":"0HNIJ0c9xGsMOj_HNyLQYLw58btBomnAcaDQqITssAY",
           "url":"http://weixin.qq.com/download",            
           "data":{
                   "first": {
                       "value":"恭喜你购买成功！",
                       "color":"#173177"
                   },
                   "keyword1":{
                       "value":"巧克力",
                       "color":"#173177"
                   },
                   "keyword2": {
                       "value":"39.8元",
                       "color":"#173177"
                   },
                   "keyword3": {
                       "value":"2014年9月22日",
                       "color":"#173177"
                   },
                   "keyword4": {
                       "value":"2014年9月22日",
                       "color":"#173177"
                   },
                   "remark":{
                       "value":"欢迎再次购买！",
                       "color":"#173177"
                   }
           }
       }';

                $access_token = $a->GetAccessToken();
                //wechatCallbackapiTest::responseMsg($fromUsername);
                $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token;
                $out = $a->SendPost($url, $json1);


            }

            if ($eventKey == "V1001_BOOK") {

                $access_token = $a->GetAccessToken();
                $pic = "./22.jpg";
                $data = array(
                    'media' => '@' . realpath($pic) . ";type=image/png;filename=./22.jpg"
                );
                $out = $a->SendPost("https://api.weixin.qq.com/cgi-bin/media/upload?access_token=" . $access_token . "&type=image", $data);
                $media_id = $out["media_id"];

                if (!empty($media_id)) {
                    $msgType = "image";
                    $imageTpl = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Image>
                                <MediaId><![CDATA[%s]]></MediaId>
                                </Image>
                                </xml>";

                    $resultStr = sprintf($imageTpl, $fromUsername, $toUsername, $time, $msgType, $media_id);
                    echo $resultStr;
                }
            }
            if($eventKey == "V1001_NEWS"){
                $a = new WetChat();
                $access_token = $a->GetAccessToken();
                $news[]=array(
                    "Title" =>"大学英语四六级成绩查询",
                    "Description" =>"点击图片进入",
                    "PicUrl" =>"http://houhaibo1xiaoyao.applinzi.com/22.jpg",
                    "Url" =>"http://www.weixinla.com/document/36066320.html"
                );
                $res=transmitNews($fromUsername,$toUsername,$news);
                echo $res;
            }
        }

    }
}

function transmitNews($fromuser,$touser, $newsArray)
{
    if(!is_array($newsArray)){
        return;
    }
    $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
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
<Articles>
$item_str</Articles>
</xml>";

    $result = sprintf($xmlTpl, $fromuser, $touser, time(), count($newsArray));
    return $result;
}
