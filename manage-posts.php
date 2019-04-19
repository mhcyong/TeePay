<?php
include 'common.php';
include 'header.php';
include 'menu.php';

$stat = Typecho_Widget::widget('Widget_Stat');
$posts = Typecho_Widget::widget('Widget_Contents_Post_Admin');
$isAllPosts = ('on' == $request->get('__typecho_all_posts') || 'on' == Typecho_Cookie::get('__typecho_all_posts'));
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
                                <th><?php _e('状态'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        	<?php if($posts->have()): ?>
                            <?php while($posts->next()): ?>
                            <tr id="<?php $posts->theId(); ?>">
                                <td><a href="<?php echo $request->makeUriByRequest('cid='.$posts->cid); ?>" title="点击编辑"><?php $posts->title(); ?></a>
                                <td>
								<?php if($posts->vpay_isFee=="y"){
									echo '元付费中';
								}else{
									echo '免费中';
								}
								?>
								</td>		
                            </tr>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <tr>
                            	<td colspan="6"><h6 class="typecho-list-table-title"><?php _e('没有任何文章'); ?></h6></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                </form><!-- end .operate-form -->

                <div class="typecho-list-operate clearfix">
                    <form method="get">
                        <?php if($posts->have()): ?>
                        <ul class="typecho-pager">
                            <?php $posts->pageNav(); ?>
                        </ul>
                        <?php endif; ?>
                    </form>
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
include 'common-js.php';
include 'table-js.php';
include 'footer.php';
?>
