<?php
include 'common.php';
include 'header.php';
include 'menu.php';
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
	$queryGoods= $db->select()->from('table.teepay_fees')->join('table.contents', 'table.contents.cid = table.teepay_fees.feecid', Typecho_Db::LEFT_JOIN)->join('table.users', 'table.users.uid = table.teepay_fees.feeuid', Typecho_Db::LEFT_JOIN)->order('table.teepay_fees.feeid',Typecho_Db::SORT_DESC)->limit(15); 
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
                            <col width="10%"/>
                            <col width="6%"/>
                            <col width="8%"/>
                            <col width="8%"/>
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
                                <td><?php if($value["feeuid"] == 0){
									echo "游客";
								}else{
									echo $value["name"];
								}
								?></td>	
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
					  基础版仅能查看最近15条记录，专业版可查看全部记录。			  
					</div>
                </div><!-- end .typecho-list-operate -->
				
				</div>
            </div><!-- end .typecho-list -->
        </div><!-- end .typecho-page-main -->
    </div>
</div>

<?php
include 'copyright.php';
?>
