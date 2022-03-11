<?php
/*
设置
*/
//链接字符长度
$linklength = 3;
//业务域名
$domain = '';
//数据存放相对位置
$datapath = '../qrcodedata';
//数据存放绝对位置
$absdatapath = '/qrcodedata';
/*

*/
ini_set("display_errors", 0);
error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ALL ^ E_WARNING);
function getExpireTime()
{
    return time() + 7 * 24 * 3600 - 1800;
}
function getRandomString($len, $chars=null)  
{  
    if (is_null($chars)) {  
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";  
    }  
    mt_srand(10000000*(double)microtime());  
    for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++) {  
        $str .= $chars[mt_rand(0, $lc)];  
    }  
    return $str;  
}
//
$method = $_GET['method'];
$link = $_GET['link'];
$groupid = $_GET['groupid'];
$note = $_GET['note'];
switch ($method) {
    case 'query':
        echo file_get_contents("http://$domain.$absdatapath/linkquery.php");
        break;
    case 'create':
        if ($groupid and $note) {
            $link = getRandomString($linklength);
            while (file_get_contents("$datapath/$link.json")) $link = getRandomString($linklength);
            $linkdata = json_decode('{"id":"","note":"","count":0,"totalcount":0,"expire":"0","flow":[]}');
            $linkdata->id = $groupid;
            $linkdata->note = $note;
            $linkdata->expire = getExpireTime();
            $file = fopen("$datapath/$link.json", "w");
            fwrite($file, json_encode($linkdata));
            fclose($file);
            echo "创建成功，短链接：$domain/$link";
        } else echo "error, missing parameter(s):".($groupid?'':'groupid')." ".($note?'':'noteid').".";
        break;
    case 'add':
        if ($groupid and $link) {
            if ($linkdata = json_decode(file_get_contents("$datapath/$link.json"))) {
                array_push($linkdata->flow, $groupid.'&'.getExpireTime());
                $file = fopen("$datapath/$link.json", "w");
                fwrite($file, json_encode($linkdata));
                fclose($file);
                echo "新增群二维码成功";
            } else echo "error, invalid link.";
        } else echo "error, missing parameter(s):".($groupid?'':'groupid')." ".($link?'':'link').".";
        break;
    case 'remove':
        if ($groupid and $link) {
            if ($linkdata = json_decode(file_get_contents("$datapath/$link.json"))) {
                foreach ($linkdata->flow as $flows) {
                    if (preg_match("/$groupid&/", $flows)) {
                        $removeflow = $flows;
                        break;
                    }
                }
                if ($removeflow) {
                    $linkdata->flow = array_values(array_diff($linkdata->flow, ["$removeflow"]));
                    $file = fopen("$datapath/$link.json", "w");
                    fwrite($file, json_encode($linkdata));
                    fclose($file);
                    echo "移除群二维码成功";
                } else echo "error, invalid groupid";
            } else echo "error, invalid link.";
        } else echo "error, missing parameter(s):".($groupid?'':'groupid')." ".($link?'':'link').".";
        break;
    case 'delete':
        if ($link) {
            if (file_get_contents("$datapath/$link.json")) {
                unlink("$datapath/$link.json");
                echo "删除短链接成功";
            } else echo "error, invalid link.";
        } else echo "error, missing parameter: link.";
        break;
    default:
        echo "error, invalid method.";
        break;
}