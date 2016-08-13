<?php
require_once "./JSSDK/jssdk.php";
$jssdk = new JSSDK("wx9e5c18915d89d4af", "8aef499e8b97d21a373746edf81ed549");
$signPackage = $jssdk->GetSignPackage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>Title</title>
    <link rel="stylesheet" href="./weui/dist/style/weui.min.css"/>
    <script src="./js/jquery.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
</head>
<body>
<a href="#" class="weui_btn weui_btn_primary">按钮</a>
<a href="#" class="weui_btn weui_btn_disabled weui_btn_warn">确认</a>
<div class="weui_cells_title">带说明的列表项</div>
<div class="weui_cells">
    <div class="weui_cell">
        <div class="weui_cell_bd weui_cell_primary">
            <p>标题文字</p>
        </div>
        <div class="weui_cell_ft">
            说明文字
        </div>
    </div>
</div>
<div id="mm">
    <div class="choose">
        <a href="#" id="choose" class="weui_btn weui_btn_plain_primary">上传图片</a>
    </div>
    <div class="confirm">
        <a href="#" id="confirm" class="weui_btn weui_btn_plain_primary">确认上传</a>
    </div>
</div>
<script type="application/javascript">
    wx.config({
        debug: true,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
            'onMenuShareTimeline', 'onMenuShareAppMessage','onMenuShareQQ','chooseImage','uploadImage','hideOptionMenu','scanQRCode'
        ]
    });
    wx.ready(function(){
        wx.onMenuShareTimeline({
            title: '我爱文博', // 分享标题
            link: 'http://houhaibo1xiaoyao.applinzi.com/tree.php', // 分享链接
            imgUrl: './peng.jpg', // 分享图标
            success: function () {
            },
            cancel: function () {
            }
        });
        wx.onMenuShareQQ({
            title: '我在恒达文博工作，深爱着文博！', // 分享标题
            desc: '分享', // 分享描述
            link: 'http://houhaibo1xiaoyao.applinzi.com/tree.php', // 分享链接
            imgUrl: 'http://houhaibo1xiaoyao.applinzi.com/peng.jpg', // 分享图标
            trigger: function (res) {
                alert('用户点击分享到朋友圈');
            },
            success: function (res) {
               // alert(6666);
            },
            cancel: function () {
            },
            fail: function (res) {
                alert('wx.onMenuShareTimeline:fail: '+JSON.stringify(res));
            }
        });

//        wx.scanQRCode({
//            needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
//            scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
//            success: function (res) {
//                var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
//                alert(result);
//            }
//        });

        //wx.hideOptionMenu();
        $("#choose").click(function () {
            wx.chooseImage({
                count: 9, // 默认9
                sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
                sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
                success: function (res) {
                    var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                    var img="<img src='"+localIds+"' id='img' name='"+localIds+"' style='width:150px;height:150px;'>";
                    $("#mm").append(img);
                    $(".choose").css("display","none");

                }
            });

        });

        $("#confirm").click(function () {

            var localId=$("#img").attr("name");
            wx.uploadImage({
                localId: localId, // 需要上传的图片的本地ID，由chooseImage接口获得
                isShowProgressTips: 1, // 默认为1，显示进度提示
                success: function (res) {
                    var serverId = res.serverId; // 返回图片的服务器端ID
                    alert(serverId);
                }
            });

        });

    });

</script>
</body>
</html>