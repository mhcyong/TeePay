# TePay基础版 #
Typecho 个人支付宝、微信收款插件

插件下载地址：https://github.com/mhcyong/TePay.git  
演示地址：https://pangsuan.com/p/teepay.html  
推荐个人微信收款平台：https://payjs.cn/ref/ZEWYMZ  


## 效果演示 ##
1、forlove：https://1345708774.top/archives/39/  
2、梁晓斌：https://www.liangxiaobin.com/child/99/  
3、猫先生的个人博客：https://www.mr-mao.cn/archives/gen2-thinksystem-server-install-2008r2.html  
4、如烟若梦：https://abcdl.cn/archives/7/   
5、憶の年：https://www.529i.com/archives/801.html   
6、kali博客：https://blog.bbskali.cn/index.php/archives/1504/  


## TePay 专业版（[TePay-Pro](https://pangsuan.com/p/tepay-pro.html)）更新记录 ##
2019-11-25：因支付宝当面付不播报，故采用Server酱来推送支付成功消息到微信；为了保护我和专业版用户的利益，对少部分核心代码进行加密，不会影响二次修改，开发等等操作。  
2019-11-24：更新TePay-Pro到3.0.0版本，去掉了一些不需要的功能，也听取了已购买专业版用户的意见，增加一些功能，比如订单标题可以单独修改。  
2019-11-21：在订单表中增加文章标题的字段，方便直接从数据表查看，也减轻了数据查询的压力。  


## 注意事项 ##
1、支付宝支付注意是应用私钥，支付宝公钥。  
2、使用这个插件网站必须只根目录，二级目录无效，如果一定要在二级目录请自行修改引用文件路劲。  
3、只适用个人支付宝（需签约当面付收单，免费）和个人微信（需在 [payjs.cn](https://payjs.cn/ref/ZEWYMZ) 开通个人微商户，费用300元）。  
4、因违法被封，或服务器在国外不能收到支付宝回调通知的无法使用。



## TePay 基础版修改记录 ##
2019-11-05：更改插件名称为TePay，此插件不再进行大的功能变更，如有需要请了解专业版 [TePay-Pro](https://pangsuan.com/p/tepay-pro.html) 。     
2019-10-16: 将PC与手机端分开，手机端只保留支付宝支付，因为可以直接跳转到支付宝付款；微信在手机端没有找到好的方式，故先去掉。  
2019-10-15: 去掉付费内容里面Parsedown.php及相关代码，减少因主题问题带来的冲突。  
2019-09-27: 调整主站域名为 https://pangsuan.com ，但微信支付体验时的商户简称还是“微发现”。  
2019-07-23：修改菜单位置为顶级菜单，增加一个付费记录页面。