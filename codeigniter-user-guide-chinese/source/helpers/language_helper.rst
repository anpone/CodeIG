###############
语言辅助函数
###############

语言辅助函数文件包含了用于处理语言文件的一些函数。

.. contents::
  :local:

.. raw:: html

  <div class="custom-index container"></div>

加载辅助函数
===================

该辅助函数通过下面的代码加载::

	$this->load->helper('language');

可用函数
===================

该辅助函数有下列可用函数：

.. php:function:: lang($line[, $for = ''[, $attributes = array()]])

 	:param	string	$line: Language line key
 	:param	string	$for: HTML "for" attribute (ID of the element we're creating a label for)
 	:param	array	$attributes: Any additional HTML attributes
 	:returns:	HTML-formatted language line label
	:rtype:	string

	此函数使用简单的语法从已加载的语言文件中返回一行文本。
	这种简单的写法在视图文件中可能比调用 ``CI_Lang::line()`` 更顺手。

	Example::

		echo lang('language_key', 'form_item_id', array('class' => 'myClass'));
		// Outputs: <label for="form_item_id" class="myClass">Language line</label>