function teepayPayPost() {
	if(document.getElementById("feetype1").checked){
		var feetype = "alipay";
	}else{
		var feetype = "wxpay";
	}
	$.ajax({
		type : "POST",
		url : "/usr/plugins/TeePay/pay.php",
		data : {"action":"paysubmit","feetype":feetype,"feecid":$("#feecid").val(),"feeuid":$("#feeuid").val()},
		dataType : "json",
		success : function(data) {
			if(data.status=="ok"){
				if(data.type=="alipay"){				
					layer.open({
					  type: 1,
					  title: '支付宝扫一扫付款',
					  anim: 2,
					  shadeClose: true, //点击遮罩关闭
					  content: '<center><div style="padding:20px;"><img src="https://my.tv.sohu.com/user/a/wvideo/getQRCode.do?text='+data.qrcode+'" width="200" /></div></center>'
					});
				}else if(data.type=="payjs"){				
					layer.open({
					  type: 1,
					  title: '微信扫一扫付款',
					  anim: 2,
					  shadeClose: true, //点击遮罩关闭
					  content: '<center><div style="padding:20px;"><img src="'+data.qrcode+'" width="200" /></div></center>'
					});
				}
				//启动定时器轮询
				function pay_status(){
				   $.ajax({  
					url:'/usr/plugins/TeePay/return_url.php',
					dataType:'json', 
					type:'post',  
					data:{"feeid":data.feeid}, 
					//data:{"feeid":"2019041313445604489155"}, 
					success:function(data){
					  if(data.feestatus == '1'){
						window.clearInterval(int); //销毁定时器
						//alert("付款成功");
						setTimeout(function(){
							layer.msg("付款成功",function(index){
								window.location.reload();
								});
						},500)	
					  }else if(data.feestatus =='2'){
						window.clearInterval(int); //销毁定时器
						setTimeout(function(){
						  //付款失败
						  layer.msg("付款失败");
						},500)
					  }			 
					},  
				 });
				}								
				var int=self.setInterval(function(){pay_status()},500);
			}else{				
				layer.open({
				  type: 1,
				  title: '付款失败',
				  anim: 2,
				  shadeClose: true, //点击遮罩关闭
				  content: '\<\div style="padding:20px;">请求支付过程出了一点小问题，稍后重试一次吧！\<\/div>'
				});
			}
		},error:function(data){
			layer.msg("服务器错误");
			return false;
		}
	});	
}


function enter_down(form, event) { 
	if(event.keyCode== "13") {
		stopDefault(event);
		submitForm(form,'actionDiv');
	}
}
              
function stopDefault(e) {
	if(e && e.preventDefault) {   //如果提供了事件对象，则这是一个非IE浏览器 
		e.preventDefault(); //阻止默认浏览器动作(W3C)
	} else {  //IE中阻止函数器默认动作的方式
		window.event.returnValue = false; 
	}
	return false;
}
