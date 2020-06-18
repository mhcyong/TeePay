<?php
/**
 * TeePayForTypecho<a href="http://forum.typecho.org/viewtopic.php?f=6&t=11998">自媒体付费阅读插件免费版</a>
 * @package TeePay For Typecho
 * @author 胖蒜网
 * @version 1.5.4
 * @link https://pangsuan.com/p/teepay.html
 * @date 2019-04-07
 */
class TeePay_Plugin implements Typecho_Plugin_Interface{
    // 激活插件
    public static function activate(){
        $index = Helper::addMenu('文章付费');
		Helper::addAction('teepay-post-free', 'TeePay_Action');
        Helper::addPanel($index, 'TeePay/manage/posts.php', '文章付费', '管理付费文章', 'administrator');
        Helper::addPanel($index, 'TeePay/manage/paylist.php', '付费记录', '付费情况记录', 'administrator');
		Typecho_Plugin::factory('Widget_Archive')->footer = array('TeePay_Plugin', 'footer');
		
		Typecho_Plugin::factory('admin/write-post.php')->option = array(__CLASS__, 'setFeeContent');
		Typecho_Plugin::factory('Widget_Contents_Post_Edit')->finishPublish = array(__CLASS__, "updateFeeContent");
		Typecho_Plugin::factory('Widget_Archive')->select = array(__CLASS__, 'selectHandle');
		
		//后台增加字段
		$db = Typecho_Db::get();
		$prefix = $db->getPrefix();
		self::alterColumn($db,$prefix.'contents','teepay_isFee','enum("y","n") DEFAULT "n"');
		self::alterColumn($db,$prefix.'contents','teepay_price','double(10,2) DEFAULT 0');
		self::alterColumn($db,$prefix.'contents','teepay_content','text');	
		self::createTableTeePayFee($db);
		
        return _t('插件已经激活，需先配置插件信息！');
    }
	
    // 禁用插件
    public static function deactivate(){
		$index = Helper::removeMenu('文章付费');		
		Helper::removeAction('teepay-post-free');
		Helper::removePanel($index, 'TeePay/manage/posts.php');
		Helper::removePanel($index, 'TeePay/manage/paylist.php');
        return _t('插件已被禁用');
    }
	/**
	 * 把付费内容设置装入文章编辑页
	 *
	 * @access public
	 * @return void
	 */
	public static function setFeeContent($post) {
		$db = Typecho_Db::get();
		$row = $db->fetchRow($db->select('teepay_content,teepay_price,teepay_isFee')->from('table.contents')->where('cid = ?', $post->cid));
		$teepay_content = isset($row['teepay_content']) ? $row['teepay_content'] : '';	
		$teepay_price = isset($row['teepay_price']) ? $row['teepay_price'] : '';	
		$teepay_isFee = isset($row['teepay_isFee']) ? $row['teepay_isFee'] : '';	
		if($teepay_isFee == "y"){
		$html = '<section class="typecho-post-option"><label for="teepay_price" class="typecho-label">是否付费</label>
				<p><span><input name="teepay_isFee" type="radio" value="n" id="teepay_isFee-n">
				<label for="teepay_isFee-n">
				免费的</label>
				</span><span>
				<input name="teepay_isFee" type="radio" value="y" id="teepay_isFee-y" checked="true">
				<label for="teepay_isFee-y">
				要付费</label>
				</span></p></section>
				<section class="typecho-post-option"><label for="teepay_price" class="typecho-label">付费价格（元）</label><p><input id="teepay_price" name="teepay_price" type="text" value="'.$teepay_price.'" class="w-100 text"></p></section>
				<section class="typecho-post-option"><label for="teepay_content" class="typecho-label">付费可见内容</label><p><textarea id="teepay_content" name="teepay_content" type="text" value="" class="w-100 text">'.$teepay_content.'</textarea></p></section>';
			
		}else{			
		$html = '<section class="typecho-post-option"><label for="teepay_price" class="typecho-label">是否付费</label>
				<p><span><input name="teepay_isFee" type="radio" value="n" id="teepay_isFee-n" checked="true">
				<label for="teepay_isFee-n">
				免费的</label>
				</span><span>
				<input name="teepay_isFee" type="radio" value="y" id="teepay_isFee-y">
				<label for="teepay_isFee-y">
				要付费</label>
				</span></p></section>
				<section class="typecho-post-option"><label for="teepay_price" class="typecho-label">付费价格（元）</label><p><input id="teepay_price" name="teepay_price" type="text" value="'.$teepay_price.'" class="w-100 text"></p></section>
				<section class="typecho-post-option"><label for="teepay_content" class="typecho-label">付费可见内容</label><p><textarea id="teepay_content" name="teepay_content" type="text" value="" class="w-100 text">'.$teepay_content.'</textarea></p></section>';
		}
		_e($html);
	}
	/**
	 * 发布文章同时更新文章类型
	 *
	 * @access public
	 * @return void
	 */
	public static function updateFeeContent($contents, $post){
		$teepay_isFee = $post->request->get('teepay_isFee', NULL);
		$teepay_price = $post->request->get('teepay_price', NULL);
		$teepay_content = $post->request->get('teepay_content', NULL);
		$db = Typecho_Db::get();
		$sql = $db->update('table.contents')->rows(array('teepay_isFee' => $teepay_isFee,'teepay_price' => $teepay_price,'teepay_content' => $teepay_content))->where('cid = ?', $post->cid);
		$db->query($sql);
	}
    /**
     * 把增加的字段添加到查询中，以便在模版中直接调用
     *
     * @access public
     * @return void
     */
	public static function selectHandle($archive){
		$user = Typecho_Widget::widget('Widget_User');
		if ('post' == $archive->parameter->type || 'page' == $archive->parameter->type) {
			if ($user->hasLogin()) {
				$select = $archive->select()->where('table.contents.status = ? OR table.contents.status = ? OR
						(table.contents.status = ? AND table.contents.authorId = ?)',
						'publish', 'hidden', 'private', $user->uid);
			} else {
				$select = $archive->select()->where('table.contents.status = ? OR table.contents.status = ?',
						'publish', 'hidden');
			}
		} else {
			if ($user->hasLogin()) {
				$select = $archive->select()->where('table.contents.status = ? OR
						(table.contents.status = ? AND table.contents.authorId = ?)', 'publish', 'private', $user->uid);
			} else {
				$select = $archive->select()->where('table.contents.status = ?', 'publish');
			}
		}
		$select->where('table.contents.created < ?', Typecho_Date::gmtTime());
		$select->cleanAttribute('fields');
		return $select;
	}
	
	
	/**
     * 在主题中直接调用
     *
     * @access public
     * @return int
     * @throws
     */
    public static function getTeePay(){
		$db = Typecho_Db::get();
		$options = Typecho_Widget::widget('Widget_Options');
		$option=$options->plugin('TeePay');
        $cid = Typecho_Widget::widget('Widget_Archive')->cid;
		$query= $db->select()->from('table.contents')->where('cid = ?', $cid ); 
		$row = $db->fetchRow($query);
		if($row['teepay_isFee']=='y'){
			if($row['authorId']!=Typecho_Cookie::get('__typecho_uid')){
				$cookietime=$option->teepay_cookietime==""?1:$option->teepay_cookietime;
				if(!isset($_COOKIE["TeePayCookie"])){
					$randomCode = md5(uniqid(microtime(true),true));
					setcookie("TeePayCookie",$randomCode, time()+3600*24*$cookietime);
				}
				$queryItem= $db->select()->from('table.teepay_fees')->where('feecookie = ?', $_COOKIE["TeePayCookie"])->where('feestatus = ?', 1)->where('feecid = ?', $row['cid']); 
				$rowItem = $db->fetchRow($queryItem);
				$rowUserItemNum = 0;
				if(Typecho_Cookie::get('__typecho_uid')){
					$queryUserItem= $db->select()->from('table.teepay_fees')->where('feeuid = ?', Typecho_Cookie::get('__typecho_uid'))->where('feestatus = ?', 1)->where('feecid = ?', $row['cid']); 
					$rowUserItem = $db->fetchRow($queryUserItem);
					if(!empty($rowUserItem)){
						$rowUserItemNum = 1;
					}
				}
				if(count($rowItem) != 0 || $rowUserItemNum){ ?>			
				<div style="background:#f8f8f8;padding:30px 20px;border:1px dashed #ccc;position: relative;z-index:999;margin:15px 0">
					<span><?php echo $row['teepay_content'] ?></span>
					<span style="position: absolute;top:5px;left:15px;font-size:90%;color:#90949c;">订单号：<?php echo $rowItem['feeid'] ?></span>
					<span style="position: absolute;top:5px;right:15px;"><img style="width:22px;" src="/usr/plugins/TeePay/pay.png" alt=""></span>
				</div>
				<?php }else{ ?>
					<div style="background:#f8f8f8;padding:35px 15px 10px;border:1px dashed #ccc;position: relative;text-align:center;margin:15px 0;">
						<form id="teepayPayPost" onsubmit="return false" action="##" method="post" style="margin:10px 0;">
							<?php if($option->show_Alipay_Wxpay == "all"){ ?>
								<input type="radio" id="feetype1" name="feetype" value="alipay">支付宝支付
								<input type="radio" id="feetype2" name="feetype" value="wxpay" checked>微信支付
							<?php }else{
								if($option->show_Alipay_Wxpay == "alipay"){?>					
								<input type="radio" id="feetype1" name="feetype" value="alipay" checked>支付宝支付
								<input type="radio" id="feetype2" name="feetype" value="wxpay" style="display:none;">
								<?php }else{?>	
								<input type="radio" id="feetype1" name="feetype" value="alipay" style="display:none;">
								<input type="radio" id="feetype2" name="feetype" value="wxpay" checked>微信支付
								<?php }
							}?>
							<div style="clear:left;"></div>				
							<div style="height:34px;line-height:34px;border:none;-moz-border-radius: 0px;-webkit-border-radius: 0px;border-radius:0px;">
							价格： <?php echo $row['teepay_price'] ?> 元
							</div>
							<input id="verifybtn" style="border-radius: 4px; border-style: none; width: 80px; height: 34px; line-height: 34px; padding: 0 5px; background-color: #F60; text-align: center; color: #FFF; font-size: 14px;cursor: pointer;-webkit-appearance : none ;" onclick="teepayPayPost();" οnkeydοwn="enter_down(this.form, event);" type="button" value="付款"/>
							<input type="hidden" name="action" value="paysubmit" />
							<input type="hidden" id="feecid" name="feecid" value="<?php echo $row['cid'] ?>" />
							<input type="hidden" id="feeuid" name="feeuid" value="<?php echo Typecho_Cookie::get('__typecho_uid') ?>" />
						</form>
						<div style="clear:left;"></div>
						<span>温馨提示：<span style="color: red">免登录付款后<?php echo $cookietime;?>天内可重复阅读隐藏内容，<a href="<?php $options->adminUrl(); ?>" style="">登录</a></span>用户付款后可永久阅读隐藏的内容。 </span>
						<span style="position: absolute;top:5px;left:15px;font-size:90%;color:#90949c;">付费可读</span>
						<span style="position: absolute;top:5px;right:15px;"><img style="width:22px;" src="/usr/plugins/TeePay/pay.png" alt=""></span>
					</div>
				<?php } 
			}else{ ?>			
			<div style="background:#f8f8f8;padding:35px 15px 10px;border:1px dashed #ccc;position: relative;z-index:999;margin:15px 0">
				<span><?php echo $row['teepay_content'] ?></span>
				<span style="position: absolute;top:5px;left:15px;font-size:90%;color:#90949c;">作者本人可读</span>
				<span style="position: absolute;top:5px;right:15px;"><img style="width:22px;" src="/usr/plugins/TeePay/pay.png" alt=""></span>
			</div>
		<?php } 
		}
    }
	
    // 插件配置面板
    public static function config(Typecho_Widget_Helper_Form $form){
		$db = Typecho_Db::get();
		$prefix = $db->getPrefix();
		$options = Typecho_Widget::widget('Widget_Options');
		$plug_url = $options->pluginUrl;
		$div=new Typecho_Widget_Helper_Layout();
		$div->html('<small>		
			<h6>基础功能</h6>
			<span><p>第一步：配置下方各项参数；</p></span>
			<span>
				<p>
					第二步：在主题post.php文件相应位置添加：<font color="blue">&lt;?php echo TeePay_Plugin::getTeePay(); ?></font>。
				</p>
			</span>
			<span><p>第三步：等待其他用户或游客购买对应付费文章；</p></span>
		</small>');
		$div->render();
		
		//配置信息
		$teepay_cookietime = new Typecho_Widget_Helper_Form_Element_Text('teepay_cookietime', array('value'), 1, _t('免登录Cookie保存时间(天)'), _t('指定使用免登录付费后几天内可以查看隐藏内容，默认为1天，不会记录到买入订单中。'));
        $form->addInput($teepay_cookietime);
		//alipay配置
		$alipay_appid = new Typecho_Widget_Helper_Form_Element_Text('alipay_appid', array('value'), "", _t('支付宝appid'), _t('支付宝的appid号。'));
        $form->addInput($alipay_appid);
		$app_private_key = new Typecho_Widget_Helper_Form_Element_Textarea('app_private_key', array('value'), "", _t('应用私钥'), _t('应用私钥，不是支付宝私钥。'));
        $form->addInput($app_private_key);
		$alipay_public_key = new Typecho_Widget_Helper_Form_Element_Textarea('alipay_public_key', array('value'), "", _t('支付宝公钥'), _t('在支付宝生成的公钥。'));
        $form->addInput($alipay_public_key);
		$alipay_notify_url = new Typecho_Widget_Helper_Form_Element_Text('alipay_notify_url', array('value'), $plug_url.'/TeePay/alipay_notify_url.php', _t('支付宝异步回调接口'), _t('支付完成后异步回调的接口地址。'));
        $form->addInput($alipay_notify_url);
		//payjs配置	
		$payjs_wxpay_mchid = new Typecho_Widget_Helper_Form_Element_Text('payjs_wxpay_mchid', array('value'), "", _t('payjs商户号'), _t('在<a href="https://payjs.cn/ref/ZEWYMZ" target="_blank">payjs官网</a>注册的商户号。'));
        $form->addInput($payjs_wxpay_mchid);
		$payjs_wxpay_key = new Typecho_Widget_Helper_Form_Element_Text('payjs_wxpay_key', array('value'), "", _t('payjs通信密钥'), _t('在<a href="https://payjs.cn/ref/ZEWYMZ" target="_blank">payjs官网</a>注册的通信密钥。'));
        $form->addInput($payjs_wxpay_key);
		$payjs_wxpay_notify_url = new Typecho_Widget_Helper_Form_Element_Text('payjs_wxpay_notify_url', array('value'), $plug_url.'/TeePay/wxpay_notify_url.php', _t('payjs异步回调接口'), _t('支付完成后异步回调的接口地址。'));
        $form->addInput($payjs_wxpay_notify_url);	
		//设置显示微信，支付宝，还是全部都显示
		$show_Alipay_Wxpay= new Typecho_Widget_Helper_Form_Element_Radio('show_Alipay_Wxpay',array('all' => _t('全部方式'),'alipay' => _t('仅支付宝'),'wxpay' => _t('仅微信支付')),'all',_t('支付方式'),_t("选择需要开启的支付方式，默认支付宝和微信两种方式。"));
		$form->addInput($show_Alipay_Wxpay);
    }

    // 个人用户配置面板
    public static function personalConfig(Typecho_Widget_Helper_Form $form){
    }

    // 获得插件配置信息
    public static function getConfig(){
        return Typecho_Widget::widget('Widget_Options')->plugin('TeePay');
    }
	
	public static function footer(){
		echo '
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>	
		<script src="https://cdnjs.cloudflare.com/ajax/libs/layer/2.3/layer.js"></script>
		<script src="/usr/plugins/TeePay/teepay.js"></script>';
	}


	/*修改数据表字段*/
	public static function alterColumn($db,$table,$column,$define){
		$prefix = $db->getPrefix();
		$query= "select * from information_schema.columns WHERE table_name = '".$table."' AND column_name = '".$column."'";
		$row = $db->fetchRow($query);
		if(count($row)==0){
			$db->query('ALTER TABLE `'.$table.'` ADD COLUMN `'.$column.'` '.$define.';');
		}
	}

  	/*创建支付订单数据表*/
	public static function createTableTeePayFee($db){
		$prefix = $db->getPrefix();
		$db->query('CREATE TABLE IF NOT EXISTS `'.$prefix.'teepay_fees` (
		  `feeid` varchar(64) COLLATE utf8_general_ci NOT NULL,
		  `feecid` bigint(20) DEFAULT NULL,
		  `feeuid` bigint(20) DEFAULT NULL,
		  `feeprice` double(10,2) DEFAULT NULL,
		  `feetype` enum("alipay","wxpay","qqpay","tlepay") COLLATE utf8_general_ci DEFAULT "alipay",
		  `feestatus` smallint(2) DEFAULT "0" COMMENT "订单状态：0、未付款；1、付款成功；2、付款失败",
		  `feeinstime` datetime DEFAULT NULL,
		  `feecookie` varchar(255) COLLATE utf8_general_ci DEFAULT NULL,
		  PRIMARY KEY (`feeid`)
		) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;');
	}
	
	public static function form($action = NULL)
	{
		/** 构建表格 */
		$options = Typecho_Widget::widget('Widget_Options');
		$form = new Typecho_Widget_Helper_Form(Typecho_Common::url('/action/teepay-post-free', $options->index),
		Typecho_Widget_Helper_Form::POST_METHOD);
		
		/** 标题 */
		$title = new Typecho_Widget_Helper_Form_Element_Text('title', NULL, NULL, _t('标题*'));
		$form->addInput($title);
		
		/** 是否付费 */
		$teepay_isFee = new Typecho_Widget_Helper_Form_Element_Radio('teepay_isFee', 
						array('n' => _t('免费的'), 'y' => _t('要付费')),
						'n', _t('是否需付费*'));		
		$form->addInput($teepay_isFee);
		
		/** 付费价格 */
		$teepay_price = new Typecho_Widget_Helper_Form_Element_Text('teepay_price', NULL, NULL, _t('付费价格（元）*'));
		$form->addInput($teepay_price);
		
		/** 付费可见内容 */
		$teepay_content = new Typecho_Widget_Helper_Form_Element_Textarea('teepay_content', NULL, NULL, _t('付费可见内容*'));
		$form->addInput($teepay_content);
			
		/** 链接动作 */
		$do = new Typecho_Widget_Helper_Form_Element_Hidden('do');
		$form->addInput($do);
		
		/** 链接主键 */
		$cid = new Typecho_Widget_Helper_Form_Element_Hidden('cid');
		$form->addInput($cid);
		
		/** 提交按钮 */
		$submit = new Typecho_Widget_Helper_Form_Element_Submit();
		$submit->input->setAttribute('class', 'btn primary');
		$form->addItem($submit);
		$request = Typecho_Request::getInstance();

        if (isset($request->cid) && 'insert' != $action) {
            /** 更新模式 */
			$db = Typecho_Db::get();
			$prefix = $db->getPrefix();
            $post = $db->fetchRow($db->select()->from($prefix.'contents')->where('cid = ?', $request->cid));
            if (!$post) {
                throw new Typecho_Widget_Exception(_t('文章不存在'), 404);
            }
            
            $title->value($post['title']);
            $teepay_isFee->value($post['teepay_isFee']);
            $teepay_price->value($post['teepay_price']);
            $teepay_content->value($post['teepay_content']);
            $do->value('update');
            $cid->value($post['cid']);
            $submit->value(_t('确认付费'));
            $_action = 'update';
        } else {
            $submit->value(_t('确认付费'));
        }
        
        if (empty($action)) {
            $action = $_action;
        }
      
        return $form;
	}
}
