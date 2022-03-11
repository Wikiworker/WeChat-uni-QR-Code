# WeChat uni QR Code
## 介绍
用于实现一码加入多群的轻量短链接跳转系统（无前端，需二次开发）。完全使用php，无任何依赖或数据库需求。

（注意保障接口安全）
## 设置
#### 参数设置
* <code>index.php</code> 设置跳转次数阈值、二维码生成API。
* <code>qrcodeapi.php</code> 设置链接长度、业务域名、数据存放位置。
#### Nginx Rewrite
参考rewrite.txt
* Apache需自行转换后写入.htaccess文件内。
## API
#### 参数
* <code>method</code> API方法。
* <code>link</code> 短链接。
* <code>groupid</code> 微信群邀请二维码的编号（去掉 "https://weixin.qq.com/g/" 后的剩余部分）。
* <code>note</code> 备注信息。
* <code>count</code> 当前二维码的有效访问次数。
* <code>totalcount</code> 短链接总计有效访问次数。
* <code>expire</code> 当前二维码的过期时间（unix时间戳）。
 
 微信群邀请二维码默认7天有效，通过API添加邀请码时，系统会为其设置6天23小时30分钟的生命周期。
* <code>flow</code> 候补二维码序列，格式：<code>编号&过期时间</code> 。

**以下API中参数缺一不可**
#### 以json列出所有短链接
<code>GET qrcodeapi.php?method=query</code>
#### 创建新短链接
<code>GET qrcodeapi.php?method=create&groupid={groupid}&note={note}</code>
#### 删除短链接
<code>GET qrcodeapi.php?method=delete&link={link}</code>
#### 为短链接增加邀请码
<code>GET qrcodeapi.php?method=add&link={link}&groupid={groupid}</code>
#### 为短链接移除邀请码
<code>GET qrcodeapi.php?method=remove&link={link}&groupid={groupid}</code>
* 达到访问次数阈值和过期邀请码会自动删除，无需额外处理。
