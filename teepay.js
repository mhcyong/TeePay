function teepayPayPost() {
	var str = "确认要付款购买吗？";
	layer.confirm(str, {
		btn: ["付款","算了"]
	}, function(){
		var ii = layer.load(2, {shade:[0.1,"#fff"]});
		if(document.getElementById("feetype1").checked){
			var feetype = "alipay";
		}else{
			var feetype = "wxpay";
		}
		$.ajax({
			type : "POST",
			url : "/usr/plugins/TeePay/pay.php",
			data : {"action":"paysubmit","feetype":feetype,"feecid":$("#feecid").val(),"feeuid":$("#feeuid").val(),"feecookie":$("#feecookie").val()},
			dataType : "json",
			success : function(data) {
				layer.close(ii);
				if(data.status=="ok"){
					if(data.type=="alipay"){
						str='<center><div>支付宝付款</div><div><img src="'+data.qrcode+'" width="200" /></div></center>';
					}else if(data.type=="payjs"){
						str='<center><div>微信付款</div><div><img src="'+data.qrcode+'" width="200" /></div></center>';
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
									layer.close(index);
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
						error:function(){  
						  alert("error");				  
						},  
					 });
					}								
					var int=self.setInterval(function(){pay_status()},500);
				}else{
					str="<center><div>请求支付过程出了一点小问题，稍后重试一次吧！</div></center>";
				}							
				layer.alert(str, {
					btn: ["算了"]
				},function(index){
					layer.close(index);
				});
			},error:function(data){
				layer.close(ii);
				layer.msg("服务器错误");
				return false;
			}
		});
	});
	return false;
}
