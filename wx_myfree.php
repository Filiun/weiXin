<?php
/**
 * wechat php test
 */

//define your token
//error_reporting(0);
define("TOKEN", "myfiliun");
define("APPID","wxaa0b1580db278673");
define("secret","e88e43ad77cb9c7fdd392ba717a76bab");

require './function.php';

//$a=new WetChat();


$type=$_POST["filename"];
$type1=$_POST["filelength"];

if($type){

    echo  $type;

}else{

    echo  33;
}
exit;

echo $type;
 echo $type1;
exit;




$access_token=$a->GetAccessToken();

echo $access_token;
//$url1="https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=".$access_token;
//$out1=$a->SendGet($url1);
//print_r($out1);
exit;

$url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;

$json=' {
     "button":[
     {	
          "type":"click",
          "name":"今日歌曲",
          "key":"V1001_TODAY_MUSIC"
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
               "name":"视频",
               "url":"http://v.qq.com/"
            },
            {
               "type":"click",
               "name":"赞一下我们",
               "key":"V1001_GOOD"
            }]
       }]
 }';
$arr=json_decode($json,true);
$out=$a->SendPost($url,$arr);

print_r($out);









Class WetChat
{

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

    public function SendPost($url,$param)
    {
//        $url = "http://localhost/web_services.php";
//        $post_data = array("username" => "bob", "key" => "12345");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $output = curl_exec($ch);
        curl_close($ch);
        $output=json_decode($output,true);
        return $output;
    }

    public function GetAccessToken(){
        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxaa0b1580db278673&secret=e88e43ad77cb9c7fdd392ba717a76bab";

        $db = mysqli_connect(SAE_MYSQL_HOST_M,SAE_MYSQL_USER,SAE_MYSQL_PASS,SAE_MYSQL_DB,SAE_MYSQL_PORT);
        if ($db) {

           // mysqli_select_db(SAE_MYSQL_DB, $db);
            $sql="SELECT * FROM wx_access where id = 1";
            $res= mysqli_query($db,$sql,MYSQLI_USE_RESULT);
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
            $sql="INSERT INTO wx_access (access_token, expire_time) VALUES ('$access_token','$start_time')";
            mysqli_query($db,$sql);
            return $access_token;
        }
    }

}
