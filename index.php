<?php
/*
设置
*/
//二维码切换阈值
$switchlimit = 240;
//二维码生成API
$qrcodeAPI = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=https://weixin.qq.com/g/';
/*

*/
$link = $_GET['link'];
//判断是否为微信客户端
if(strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
    //微信客户端
    //判断链接是否存在
    if ($linkdata = json_decode(file_get_contents("qrcodedata/$link.json"))) {
        //点击次数统计
        //判断是否需要换码
        if (($linkdata->count >= $switchlimit) or $linkdata->expire <= time()) {
            //切换群ID
            $newgroupdata = array('1', '1');
            while ($newgroupdata[1] and ($newgroupdata[1] <= time())) $newgroupdata = preg_split('/&/', array_shift($linkdata->flow));
            if ($newgroupdata[1]) {
                $linkdata->id = $newgroupdata[0];
                $linkdata->expire = $newgroupdata[1];
                //重置计数器
                $linkdata->count = 0;
                $linkdata->totalcount++;
            } else {
                $linkdata->count++;
                $linkdata->totalcount++;
            }
        } else {
            $linkdata->count++;
            $linkdata->totalcount++;
        }
        //更新数据文件
        $file = fopen("qrcodedata/$link.json", "w");
        fwrite($file, json_encode($linkdata));
        fclose($file);
        //读取群编号
        $groupid = $linkdata->id;
        //生成二维码并提示扫码
        echo
"<code style=\"font-family:Menlo, Monaco, Consolas, 'Courier New', monospace;display:block;line-height:18px;border:none !important;\">
<script type=\"text/javascript\">
var ua = navigator.userAgent.toLowerCase();
var isWeixin = ua.indexOf('micromessenger') != -1;
var isAndroid = ua.indexOf('android') != -1;
var isIos = (ua.indexOf('iphone') != -1) || (ua.indexOf('ipad') != -1);
document.head.innerHTML = '<title>扫码加群</title><meta charset=\"utf-8\"><meta name=\"viewport\" content=\"width=device-width, initial-scale=1, user-scalable=0\"><link rel=\"stylesheet\" type=\"text/css\" href=\"https://res.wx.qq.com/open/libs/weui/0.4.1/weui.css\">';
document.body.innerHTML = '<div class=\"weui_msg\"><div class=\"weui_text_area\"><h4 class=\"weui_msg_title\">请长按下方二维码入群</h4></div><div class=\"weui_icon_area\"><img src=\"".$qrcodeAPI.$groupid."\"/></div></div>';
</script>
</code>";
    } else {
        //链接不存在
        echo
"<code style=\"font-family:Menlo, Monaco, Consolas, 'Courier New', monospace;display:block;line-height:18px;border:none !important;\">
<script type=\"text/javascript\">
var ua = navigator.userAgent.toLowerCase();
var isWeixin = ua.indexOf('micromessenger') != -1;
var isAndroid = ua.indexOf('android') != -1;
var isIos = (ua.indexOf('iphone') != -1) || (ua.indexOf('ipad') != -1);
document.head.innerHTML = '<title>抱歉，出错了</title><meta charset=\"utf-8\"><meta name=\"viewport\" content=\"width=device-width, initial-scale=1, user-scalable=0\"><link rel=\"stylesheet\" type=\"text/css\" href=\"https://res.wx.qq.com/open/libs/weui/0.4.1/weui.css\">';
document.body.innerHTML = '<div class=\"weui_msg\"><div class=\"weui_icon_area\"><i class=\"weui_icon_info weui_icon_msg\"></i></div><div class=\"weui_text_area\"><h4 class=\"weui_msg_title\">二维码不存在</h4></div></div>';
</script>
</code>";
    }
} else {
    //非微信客户端
    echo
"<code style=\"font-family:Menlo, Monaco, Consolas, 'Courier New', monospace;display:block;line-height:18px;border:none !important;\">
<script type=\"text/javascript\">
var ua = navigator.userAgent.toLowerCase();
var isWeixin = ua.indexOf('micromessenger') != -1;
var isAndroid = ua.indexOf('android') != -1;
var isIos = (ua.indexOf('iphone') != -1) || (ua.indexOf('ipad') != -1);
document.head.innerHTML = '<title>抱歉，出错了</title><meta charset=\"utf-8\"><meta name=\"viewport\" content=\"width=device-width, initial-scale=1, user-scalable=0\"><link rel=\"stylesheet\" type=\"text/css\" href=\"https://res.wx.qq.com/open/libs/weui/0.4.1/weui.css\">';
document.body.innerHTML = '<div class=\"weui_msg\"><div class=\"weui_icon_area\"><i class=\"weui_icon_info weui_icon_msg\"></i></div><div class=\"weui_text_area\"><h4 class=\"weui_msg_title\">请在微信客户端打开链接</h4></div></div>';
</script>
</code>";
}