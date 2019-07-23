<?php
include 'common.php';
include 'header.php';
include 'menu.php';

$stat = Typecho_Widget::widget('Widget_Stat');
?>
<style type="text/css">
.am-fr {
    float: right;
    margin-top: -10px;
}
li.am-pagination-prev {
    float: left;
    margin: 0 10px;
    list-style: none;
}
li.am-pagination-next {
    float: left;
    margin: 0 10px;
    list-style: none;
}
</style>
<?php 
	$db = Typecho_Db::get();
	$queryPosts= $db->select()->from('table.teepay_fees'); 
	$page_now = isset($_GET['page_now']) ? intval($_GET['page_now']) : 1;
	if($page_now<1){
		$page_now=1;
	}
	$resultTotal = $db->fetchAll($queryPosts);
	$page_rec=10;
	$totalrec=count($resultTotal);
	$page=ceil($totalrec/$page_rec);
	if($page_now>$page){
		$page_now=$page;
	}
	if($page_now<=1){
		$before_page=1;
		if($page>1){
			$after_page=$page_now+1;
		}else{
			$after_page=1;
		}
	}else{
		$before_page=$page_now-1;
		if($page_now<$page){
			$after_page=$page_now+1;
		}else{
			$after_page=$page;
		}
	}
	$i=($page_now-1)*$page_rec<0?0:($page_now-1)*$page_rec;
	$queryGoods= $db->select()->from('table.teepay_fees')->join('table.contents', 'table.contents.cid = table.teepay_fees.feecid', Typecho_Db::LEFT_JOIN)->order('table.teepay_fees.feeid',Typecho_Db::SORT_DESC)->offset($i)->limit($page_rec); 
	$rowGoods = $db->fetchAll($queryGoods);
?>
<div class="main">
    <div class="body container">
        <?php include 'page-title.php'; ?>
        <div class="row typecho-page-main" role="main">
            <div class="col-mb-12 typecho-list">
            
                <div class="col-mb-12 col-tb-12" role="main"> 
                <form method="post" name="manage_posts" class="operate-form">
                <div class="typecho-table-wrap">
                    <table class="typecho-list-table">
                        <colgroup>
                            <col width="18%"/>
                            <col width="35%"/>
                            <col width="6%"/>
                            <col width="8%"/>
                            <col width="8%"/>
                            <col width="10%"/>
                            <col width="15%"/>
                        </colgroup>
                        <thead>
                            <tr>
                                <th><?php _e('订单号'); ?></th>
                                <th><?php _e('文章标题'); ?></th>
                                <th><?php _e('付费人'); ?></th>
                                <th><?php _e('价格'); ?></th>
                                <th><?php _e('付费方式'); ?></th>
                                <th><?php _e('状态'); ?></th>
                                <th><?php _e('付款时间'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?php
							  foreach($rowGoods as $value){
							?>
                            <tr id="<?=$value["feeid"];?>">
                                <td><?php echo $value["feeid"]; ?></td>
                                <td><?php echo $value["title"]; ?></td>
                                <td><?php echo $value["feeuid"]; ?></td>
                                <td><?php echo $value["feeprice"];?></td>	
                                <td><?php echo $value["feetype"];?></td>	
                                <td><?php if($value["feestatus"] == 0){
									echo "未付款";
								}elseif($value["feestatus"] == 1){
									echo '<span style="color:blue;">付款成功</span>';
								}else{
									echo '<span style="color:red;">付款失败</span>';
								}
								?></td>	
                                <td><?php echo $value["feeinstime"];?></td>		
                            </tr>
                            <?php
							  }
							?>
                        </tbody>
                    </table>
                </div>
                </form><!-- end .operate-form -->

                <div class="typecho-list-operate clearfix">
                    <div class="am-cf">
					  共 <?=$totalrec;?> 条记录
					  <div class="am-fr">
						<ul class="am-pagination blog-pagination">
						  <?php if($page_now!=1){?>
							<li class="am-pagination-prev"><a href="<?=$url;?>?panel=TeePay%2Fmanage-paylist.php&page_now=1" target="_parent">首页</a></li>
						  <?php }?>
						  <?php if($page_now>1){?>
							<li class="am-pagination-prev"><a href="<?=$url;?>?panel=TeePay%2Fmanage-paylist.php&page_now=<?=$before_page;?>" target="_parent">&laquo; 上一页</a></li>
						  <?php }?>
						  <?php if($page_now<$page){?>
							<li class="am-pagination-next"><a href="<?=$url;?>?panel=TeePay%2Fmanage-paylist.php&page_now=<?=$after_page;?>" target="_parent">下一页 &raquo;</a></li>
						  <?php }?>
						  <?php if($page_now!=$page){?>
							<li class="am-pagination-next"><a href="<?=$url;?>?panel=TeePay%2Fmanage-paylist.php&page_now=<?=$page;?>" target="_parent">尾页</a></li>
						  <?php }?>
						</ul>
					  </div>
					</div>
                </div><!-- end .typecho-list-operate -->
				
				</div>
            </div><!-- end .typecho-list -->
        </div><!-- end .typecho-page-main -->
    </div>
</div>

<?php
include 'copyright.php';
include 'common-js.php';
include 'table-js.php';
include 'footer.php';
?>
