<?php
class JSSDK {
  private $appId;
  private $appSecret;

  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
  }

  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();
    // 注意 URL 一定要动态获取，不能 hardcode.

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage; 
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  public function getJsApiTicket() {
    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例

//    $accessToken=$this->GetAccessToken();
//
//    $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
//
//    $res=$this->SendGet($url);
//
//    echo  $res["ticket"];
//    exit;
//    //return $res["ticket"];

    return "kgt8ON7yVITDhtdwci0qeXnkv_P8ps1MFwh-ZA8AKFxtzTbn03p47OXtvIfqxT7AHjzN5NA9TVKkvlZnt-A4ZQ";

//    $data = json_decode($this->get_php_file("jsapi_ticket.php"));
//    if ($data->expire_time < time()) {
//      $accessToken = $this->getAccessToken();
//      // 如果是企业号用以下 URL 获取 ticket
//      // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
//      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
//      $res = json_decode($this->httpGet($url));
//      $ticket = $res->ticket;
//      if ($ticket) {
//        $data->expire_time = time() + 7000;
//        $data->jsapi_ticket = $ticket;
//        $this->set_php_file("jsapi_ticket.php", json_encode($data));
//      }
//    } else {
//      $ticket = $data->jsapi_ticket;
//    }
//
//    return $ticket;
  }

  public function GetAccessToken(){
    $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx9e5c18915d89d4af&secret=8aef499e8b97d21a373746edf81ed549";


    // $out=$this->SendGet($url);
    // $access_token=$out["access_token"];

    $access_token="OB_kEJ09KjZ1F3f4X92agi67tPFBP9RvYQ420ySjDZCJNzCH-wR3Kgmd5cQOrGwK4RFEbLXOk4xgESXvyMW4rxkqdvgSH-N0Lk4iC38iPg-eG5fPgZ9sZ25vdynElvEARYTbAFALSN";
    return $access_token;


    $db = mysqli_connect(SAE_MYSQL_HOST_M,SAE_MYSQL_USER,SAE_MYSQL_PASS,SAE_MYSQL_DB,SAE_MYSQL_PORT);
    if ($db) {

      $sql="SELECT * FROM wx_access where id = 1";
      $res= mysqli_query($db,$sql);
    }else{
      die("连接错误: " . mysqli_connect_error());
    }

    $row = mysqli_fetch_assoc($res);
    if(!empty($row)){
      $end_time=time();
      $diff=$end_time-$row['expire_time'];
      if($diff > 0){

        $out=$this->SendGet($url);
        $access_token=$out["access_token"];
        $start_time=time()+7000;
        $data=array();
        $data["access_token"]=$access_token;
        $data["expire_time"]=$start_time;
        $sql1="DELETE FROM wx_access WHERE id=1";
        mysqli_query($db,$sql1);
        $sql="INSERT INTO wx_access (access_token, expire_time) VALUES ('$access_token','$start_time')";
        mysqli_query($db,$sql);

        return $access_token;
      }
      return $row['access_token'];
    }else{

      $out=$this->SendGet($url);
      $access_token=$out["access_token"];
      $start_time=time()+7000;
      $data=array();
      $data["access_token"]=$access_token;
      $data["expire_time"]=$start_time;
      $sql1="DELETE  FROM wx_access WHERE  id=1";
      mysqli_query($db,$sql1);
      $sql="INSERT INTO wx_access (access_token, expire_time) VALUES ('$access_token','$start_time')";
      mysqli_query($db,$sql);
      return $access_token;
    }
  }

  public function SendGet($url)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $output = curl_exec($ch);
    curl_close($ch);
    $output=json_decode($output,true);
    return $output;
  }

//  private function getAccessToken() {
//    // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
//    $data = json_decode($this->get_php_file("access_token.php"));
//    if ($data->expire_time < time()) {
//      // 如果是企业号用以下URL获取access_token
//      // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
//      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
//      $res = json_decode($this->httpGet($url));
//      $access_token = $res->access_token;
//      if ($access_token) {
//        $data->expire_time = time() + 7000;
//        $data->access_token = $access_token;
//        $this->set_php_file("access_token.php", json_encode($data));
//      }
//    } else {
//      $access_token = $data->access_token;
//    }
//    return $access_token;
//  }
//
//  private function httpGet($url) {
//    $curl = curl_init();
//    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
//    // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
//    // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
//    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
//    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
//    curl_setopt($curl, CURLOPT_URL, $url);
//
//    $res = curl_exec($curl);
//    curl_close($curl);
//
//    return $res;
//  }


//  private function get_php_file($filename) {
//    return trim(substr(file_get_contents($filename), 15));
//  }
//  private function set_php_file($filename, $content) {
//    $fp = fopen($filename, "w");
//    fwrite($fp, "" . $content);
//    fclose($fp);
//  }
}

