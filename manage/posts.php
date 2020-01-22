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
	$queryGoods= $db->select()->from('table.contents')->where('teepay_isFee = ?', "y")->order('table.contents.modified',Typecho_Db::SORT_DESC)->limit(5); 
	$rowGoods = $db->fetchAll($queryGoods);
?>
<div class="main">
    <div class="body container">
        <?php include 'page-title.php'; ?>
        <div class="row typecho-page-main" role="main">
            <div class="col-mb-12 typecho-list">
            
                <div class="col-mb-12 col-tb-8" role="main"> 
                <form method="post" name="manage_posts" class="operate-form">
                <div class="typecho-table-wrap">
                    <table class="typecho-list-table">
                        <colgroup>
                            <col width="85%"/>
                            <col width="15%"/>
                        </colgroup>
                        <thead>
                            <tr>
                                <th><?php _e('标题'); ?></th>
                                <th><?php _e('价格'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?php
							  foreach($rowGoods as $value){
							?>
                            <tr id="<?=$value["cid"];?>">
                                <td><a href="<?php echo $request->makeUriByRequest('cid='.$value["cid"]); ?>" title="点击编辑"><?php echo $value["title"]; ?></a>
                                <td>
								<?php 
								echo $value["teepay_price"];
								?>
								</td>		
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
					  基础版仅能显示5条记录，专业版可显示全部。
					</div>
                </div><!-- end .typecho-list-operate -->
				
				</div>
                <div class="col-mb-12 col-tb-4" role="form">
                    <?php TeePay_Plugin::form()->render(); ?>
                </div>
            </div><!-- end .typecho-list -->
        </div><!-- end .typecho-page-main -->
    </div>
</div>

<?php
include 'copyright.php';
?>
