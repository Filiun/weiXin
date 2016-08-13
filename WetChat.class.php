<?php

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

}