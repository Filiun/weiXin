<?php
class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
			//$this->responseMsg();
        	echo $echoStr;
        	//exit;
        }
    }

    public function responseMsg($cont)
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){

              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;

			    $MsgType=$postObj->MsgType;
			    if($MsgType == "voice"){
					$MediaId=$postObj->MediaId;
				}

                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";


				if(!empty( $keyword ))
                {
              		$msgType = "text";
                	//$contentStr = "Welcome to wechat world!";
                	$contentStr = "Welcome to wechat world!";
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }else{

					$msgType = "text";
					//$contentStr = "Welcome to wechat world!";
					$contentStr = $cont;
					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
					echo $resultStr;


                	//echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }

	public function  getMediaID(){

		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		if(!empty($postStr)){
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
//			$fromUsername = $postObj->FromUserName;
//			$toUsername = $postObj->ToUserName;
			$MsgType=$postObj->MsgType;
			if($MsgType == "voice"){
				$MediaId=$postObj->MediaId;
			}

			return $MediaId;


		}
		return ;
	}

		
	public function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}