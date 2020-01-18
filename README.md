# TePay基础版 #
Typecho 个人支付宝、微信收款插件

插件下载地址：https://github.com/mhcyong/TePay.git  
插件介绍：https://pangsuan.com/p/teepay.html  
演示地址：https://pangsuan.com/p/show-how.html  
推荐个人微信收款平台：https://payjs.cn/ref/ZEWYMZ  

## 特别提醒 ##
TePay-Pro 4.0的版本从2020年1月1日开始已在官网使用，计划2月1日正式发布，升级会加价，但在2月1日发布前购买专业版的用户，都可以免费升级，享受永久升级的服务。  


## 效果演示 ##
1、仰泳の猪：https://zbqbk.cn/index.php/archives/128.html  
2、梁晓斌：https://www.liangxiaobin.com/child/99/  
3、猫先生的个人博客：https://www.mr-mao.cn/archives/gen2-thinksystem-server-install-2008r2.html  
4、如烟若梦：https://abcdl.cn/archives/7/   
5、憶の年：https://www.529i.com/archives/801.html   
6、kali博客：https://blog.bbskali.cn/index.php/archives/1504/  
7、AppleID中文网： https://applecn.cc/appleid.html  
8、菜仔卢的不老阁： http://www.iysky.com/say/580.html  
9、木稚隐语： https://wbessy.com/Pay.html   
10、太阳源码： https://www.sunym.top/archives/5/     


## TePay 专业版（[TePay-Pro](https://pangsuan.com/p/tepay-pro.html)）更新记录 ##
2020-01-18：调整付费记录页面到插件里面，减少用户更换主题的操作，同时增加了订单管理的页；付费记录页面地址为：http://your_domain/tepay/fees；订单管理页面地址为：http://your_domain/tepay/order；该功能目前只在TePay-Pro-4.0以后版本，2月1日后购买专业版用户都可以使用。   
2020-01-12：启用插件时创建一个付费记录的页面，方便用户查看自己的付费情况，访问地址格式为：http://your_domain/tepay.html，地址可以在独立页面修改，创建的时候会在当前主题生成一个page_tepay.php文件，换主题的时候记得复制过去。  
2020-01-07：发现iPad上不能唤醒支付宝app付款，更改iPad上的付款方式都为扫码付款。  
2019-12-17: 付款成功后付费阅读区域增加显示付款方式。  
2019-12-12：针对专业版独立页面也增加了收款设置，故在数据表中增加一个字段来区分，同时更新版本号为TePay-Pro-3.1.0更新此版本的老用户要注意一下。  
2019-12-11：移动端的个人收款之前因为一些原因只保留了支付宝唤醒APP付款，现在还是把微信二维码收款也加上吧。  
2019-12-10：听取用户的意见，给 [独立页面也增加了收款功能](https://pangsuan.com/tepay-for-page.html) ，好的建议我都会考虑的，专业版用户放心吧~~  
2019-12-07：完善管理后台更新文章付费，删除付费文章时的提醒通知。  
2019-12-02: 为了方便删除不需要的付费设置，在编辑文章页面和付费文章管理页面都增加了删除付费设置的操作。  
2019-11-29: 管理文章付费时，如果设置为免费，同时又不设置价格，就干脆从付费表中去掉此条记录（适用TePay-Pro-3.0.0以后的版本）。  
2019-11-25：因支付宝当面付不播报，故采用Server酱来推送支付成功消息到微信；为了保护我和专业版用户的利益，对少部分核心代码进行加密，不会影响二次修改，开发等等操作。  
2019-11-24：更新TePay-Pro到3.0.0版本，去掉了一些不需要的功能，也听取了已购买专业版用户的意见，增加一些功能，比如订单标题可以单独修改。  
2019-11-21：在订单表中增加文章标题的字段，方便直接从数据表查看，也减轻了数据查询的压力。  


## 注意事项 ##
1、支付宝支付注意是应用私钥，支付宝公钥。  
2、使用这个插件网站必须只根目录，二级目录无效，如果一定要在二级目录请自行修改引用文件路劲。  
3、只适用个人支付宝（需签约当面付收单，免费）和个人微信（需在 [payjs.cn](https://payjs.cn/ref/zgpnbd) 开通个人微商户，费用300元）。  
4、因违法被封，或服务器在国外不能收到支付宝回调通知的无法使用。



## TePay 基础版修改记录 ##
2019-11-05：更改插件名称为TePay，此插件不再进行大的功能变更，如有需要请了解专业版 [TePay-Pro](https://pangsuan.com/p/tepay-pro.html) 。     
2019-10-16: 将PC与手机端分开，手机端只保留支付宝支付，因为可以直接跳转到支付宝付款；微信在手机端没有找到好的方式，故先去掉。  
2019-10-15: 去掉付费内容里面Parsedown.php及相关代码，减少因主题问题带来的冲突。  
2019-09-27: 调整主站域名为 https://pangsuan.com ，但微信支付体验时的商户简称还是“微发现”。  
2019-07-23：修改菜单位置为顶级菜单，增加一个付费记录页面。