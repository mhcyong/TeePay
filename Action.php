<?php
class TeePay_Action extends Typecho_Widget implements Widget_Interface_Do
{
	private $db;
	private $options;
	private $prefix;
			

	public function updateTeePay()
	{
		if (TeePay_Plugin::form('update')->validate()) {
			$this->response->goBack();
		}

		/** 取出数据 */
		$post = $this->request->from('cid', 'title', 'teepay_isFee', 'teepay_price', 'teepay_content');

		/** 更新数据 */
		$this->db->query($this->db->update($this->prefix.'contents')->rows($post)->where('cid = ?', $post['cid']));

		/** 设置高亮 */
		$this->widget('Widget_Notice')->highlight('post-'.$post['cid']);

		/** 提示信息 */
		$this->widget('Widget_Notice')->set(_t('文章 %s 费用已经被更新为 %s 元',
		$post['title'], $post['teepay_price']), NULL, 'success');

		/** 转向原页 */
		$this->response->redirect(Typecho_Common::url('extending.php?panel=TeePay%2Fmanage/posts.php', $this->options->adminUrl));
	}


	public function action()
	{
		$user = Typecho_Widget::widget('Widget_User');
        $user->pass('administrator');
		$this->db = Typecho_Db::get();
		$this->prefix = $this->db->getPrefix();
		$this->options = Typecho_Widget::widget('Widget_Options');
		$this->on($this->request->is('do=update'))->updateTeePay();
		$this->response->redirect($this->options->adminUrl);
	}
}
