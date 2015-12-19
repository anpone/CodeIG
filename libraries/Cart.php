<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (http://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Shopping Cart Class
 * 购物车类
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Shopping Cart
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/cart.html
 * @deprecated	3.0.0	This class is too specific for CI.
 */
class CI_Cart {

	/**
	 * These are the regular expression rules that we use to validate the product ID and product name
	 * alpha-numeric, dashes, underscores, or periods
	 * 这些都是我们使用的正则表达式规则来验证产品ID和产品名称字母数字,破折号,下划线或时期
	 * @var string
	 */
	public $product_id_rules = '\.a-z0-9_-';

	/**
	 * These are the regular expression rules that we use to validate the product ID and product name
	 * alpha-numeric, dashes, underscores, colons or periods
	 * 这些都是我们使用的正则表达式规则来验证产品ID和产品名称字母数字,破折号,突显出,冒号或时期
	 * @var string
	 */
	public $product_name_rules = '\w \-\.\:';

	/**
	 * only allow safe product names
	 *
	 * @var bool
	 */
	public $product_name_safe = TRUE;

	// --------------------------------------------------------------------------

	/**
	 * Reference to CodeIgniter instance
	 * 参考CodeIgniter实例
	 * @var object
	 */
	protected $CI;

	/**
	 * Contents of the cart
	 * 购物车的内容
	 * @var array
	 */
	protected $_cart_contents = array();

	/**
	 * Shopping Class Constructor
	 * 购物类构造函数
	 * The constructor loads the Session class, used to store the shopping cart contents.
	 * 构造函数加载会话类,用于存储购物车的内容。
	 * @param	array
	 * @return	void
	 */
	public function __construct($params = array())
	{
		// Set the super object to a local variable for use later 超级对象设置为一个局部变量之后使用
		$this->CI =& get_instance();

		// Are any config settings being passed manually?  If so, set them 任何配置设置是通过手动吗?如果是这样,设置它们
		$config = is_array($params) ? $params : array();

		// Load the Sessions class 加载会话类
		$this->CI->load->driver('session', $config);

		// Grab the shopping cart array from the session table 抓住购物车从会话表数组
		$this->_cart_contents = $this->CI->session->userdata('cart_contents');
		if ($this->_cart_contents === NULL)
		{
			// No cart exists so we'll set some base values 没有车我们会设置一些基本的价值观
			$this->_cart_contents = array('cart_total' => 0, 'total_items' => 0);
		}

		log_message('info', 'Cart Class Initialized车类初始化');
	}

	// --------------------------------------------------------------------

	/**
	 * Insert items into the cart and save it to the session table
	 * 购物车条目插入并将其保存到会话表
	 * @param	array
	 * @return	bool
	 */
	public function insert($items = array())
	{
		// Was any cart data passed? No? Bah... 任何车数据通过吗?没有?呸……
		if ( ! is_array($items) OR count($items) === 0)
		{
			log_message('error', 'The insert method must be passed an array containing data.');
			return FALSE;
		}

		// You can either insert a single product using a one-dimensional array,
		// or multiple products using a multi-dimensional one. The way we
		// determine the array type is by looking for a required array key named "id"
		// at the top level. If it's not found, we will assume it's a multi-dimensional array.
 		// 你可以插入一个单一产品使用一维数组,使用多维的一个或多个产品。我们确定所需的数组类型是通过寻找一个数组键命名为“id”在顶层。如果没有找到,我们将假定它是一个多维数组。
		$save_cart = FALSE;
		if (isset($items['id']))
		{
			if (($rowid = $this->_insert($items)))
			{
				$save_cart = TRUE;
			}
		}
		else
		{
			foreach ($items as $val)
			{
				if (is_array($val) && isset($val['id']))
				{
					if ($this->_insert($val))
					{
						$save_cart = TRUE;
					}
				}
			}
		}

		// Save the cart data if the insert was successful 如果插入成功保存购物车数据
		if ($save_cart === TRUE)
		{
			$this->_save_cart();
			return isset($rowid) ? $rowid : TRUE;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Insert
	 * 插入
	 * @param	array
	 * @return	bool
	 */
	protected function _insert($items = array())
	{
		// Was any cart data passed? No? Bah... 任何车数据通过吗?没有?呸……
		if ( ! is_array($items) OR count($items) === 0)
		{
			log_message('error', 'The insert method must be passed an array containing data插入方法必须通过一个数组,其中包含的数据.');
			return FALSE;
		}

		// --------------------------------------------------------------------

		// Does the $items array contain an id, quantity, price, and name?  These are required
		// ￥items数组包含一个id,数量,价格,和名字吗?这些都是需要的
		if ( ! isset($items['id'], $items['qty'], $items['price'], $items['name']))
		{
			log_message('error', 'The cart array must contain a product ID, quantity, price, and name购物车必须包含产品ID数组,数量,价格,和名称.');
			return FALSE;
		}

		// --------------------------------------------------------------------

		// Prep the quantity. It can only be a number.  Duh... also trim any leading zeros
		// 准备的数量。它只能被一个数字。嗯…也削减任何前导零
		$items['qty'] = (float) $items['qty'];

		// If the quantity is zero or blank there's nothing for us to do 如果数量是零或空白没有什么要我们去做
		if ($items['qty'] == 0)
		{
			return FALSE;
		}

		// --------------------------------------------------------------------

		// Validate the product ID. It can only be alpha-numeric, dashes, underscores or periods
		// Not totally sure we should impose this rule, but it seems prudent to standardize IDs.
		// Note: These can be user-specified by setting the $this->product_id_rules variable.
		// 验证产品ID。它只能是字母数字,破折号,下划线或时间不完全确定我们应该实施这一规则,但似乎谨慎标准化ID。注意:可以指定这些设置$this->product_id_rules变量。
		if ( ! preg_match('/^['.$this->product_id_rules.']+$/i', $items['id']))
		{
			log_message('error', 'Invalid product ID.  The product ID can only contain alpha-numeric characters, dashes, and underscores无效的产品ID。产品ID只能包含字母数字字符,破折号,下划线');
			return FALSE;
		}

		// --------------------------------------------------------------------

		// Validate the product name. It can only be alpha-numeric, dashes, underscores, colons or periods.
		// 验证产品名称。破折号,这只能是字母数字下划线,冒号或时期。
		// Note: These can be user-specified by setting the $this->product_name_rules variable.
		// 注意:可以指定这些设置$this->product_name_rules变量。
		if ($this->product_name_safe && ! preg_match('/^['.$this->product_name_rules.']+$/i'.(UTF8_ENABLED ? 'u' : ''), $items['name']))
		{
			log_message('error', 'An invalid name was submitted as the product name提交一个无效的名字作为产品名称: '.$items['name'].' The name can only contain alpha-numeric characters, dashes, underscores, colons, and spaces名只能包含字母数字字符,破折号,突显出,冒号、空格');
			return FALSE;
		}

		// --------------------------------------------------------------------

		// Prep the price. Remove leading zeros and anything that isn't a number or decimal point.
		// 准备价格。删除前导零和的东西不是一个号码或小数点。
		$items['price'] = (float) $items['price'];

		// We now need to create a unique identifier for the item being inserted into the cart.
		// 现在我们需要创建一个惟一的标识符的项目被插入到购物车。
		// Every time something is added to the cart it is stored in the master cart array. 每次添加到购物车的东西存储在主车数组。
		// Each row in the cart array, however, must have a unique index that identifies not only
		// a particular product, but makes it possible to store identical products with different options.
		// 购物车中的每一行数组,但是,必须有一个唯一索引标识不仅特定产品,但可以存储相同的产品,不同的选项。
		// For example, what if someone buys two identical t-shirts (same product ID), but in
		// different sizes?  The product ID (and other attributes, like the name) will be identical for
		// both sizes because it's the same shirt. The only difference will be the size.
		// 例如,如果有人买两个相同的t恤(同一产品ID),但在不同的尺寸吗?产品ID(和其他属性,如名称)将是相同的大小,因为它是相同的衬衫。唯一的区别是大小。
		// Internally, we need to treat identical submissions, but with different options, as a unique product.
		// 在内部,我们需要把相同的提交,但由于不同的选项,作为一种独特的产品。
		// Our solution is to convert the options array to a string and MD5 it along with the product ID.
		// 我们的解决方案是将选项数组转换成字符串和MD5连同产品ID。
		// This becomes the unique "row ID"  这就变成了独特的“row ID”
		if (isset($items['options']) && count($items['options']) > 0)
		{
			$rowid = md5($items['id'].serialize($items['options']));
		}
		else
		{
			// No options were submitted so we simply MD5 the product ID. 没有提交,所以我们选择简单的MD5产品ID。
			// Technically, we don't need to MD5 the ID in this case, but it makes
			// sense to standardize the format of array indexes for both conditions
			// 从技术上讲,我们不需要MD5的ID在这种情况下,但它是有意义的标准化的格式数组索引为条件
			$rowid = md5($items['id']);
		}

		// --------------------------------------------------------------------

		// Now that we have our unique "row ID", we'll add our cart items to the master array
		// grab quantity if it's already there and add it on
		// 现在,我们有了自己的独特的“row ID”,我们将添加购物车条目到主数组抓数量如果它已经和添加
		$old_quantity = isset($this->_cart_contents[$rowid]['qty']) ? (int) $this->_cart_contents[$rowid]['qty'] : 0;

		// Re-create the entry, just to make sure our index contains only the data from this submission
		// 重新创建的条目,只是为了确保我们的索引只包含这个提交的数据
		$items['rowid'] = $rowid;
		$items['qty'] += $old_quantity;
		$this->_cart_contents[$rowid] = $items;

		return $rowid;
	}

	// --------------------------------------------------------------------

	/**
	 * Update the cart
	 * 更新购物车
	 * This function permits the quantity of a given item to be changed. 这个函数允许给定的项的数量被改变。
	 * Typically it is called from the "view cart" page if a user makes
	 * changes to the quantity before checkout. That array must contain the
	 * product ID and quantity for each item.
	 * 通常被称为的“查看购物车”页面如果用户更改结账前的数量。这个数组必须包含产品ID为每个项目和数量。
	 * @param	array
	 * @return	bool
	 */
	public function update($items = array())
	{
		// Was any cart data passed? 任何车数据通过吗?
		if ( ! is_array($items) OR count($items) === 0)
		{
			return FALSE;
		}

		// You can either update a single product using a one-dimensional array,
		// or multiple products using a multi-dimensional one.  The way we
		// determine the array type is by looking for a required array key named "rowid".
		// If it's not found we assume it's a multi-dimensional array
		// 你可以更新一个产品用一个一维数组,使用多维的一个或多个产品。我们确定所需的数组类型是通过寻找一个数组键命名为“rowid。”如果不是发现我们假设它是一个多维数组
		$save_cart = FALSE;
		if (isset($items['rowid']))
		{
			if ($this->_update($items) === TRUE)
			{
				$save_cart = TRUE;
			}
		}
		else
		{
			foreach ($items as $val)
			{
				if (is_array($val) && isset($val['rowid']))
				{
					if ($this->_update($val) === TRUE)
					{
						$save_cart = TRUE;
					}
				}
			}
		}

		// Save the cart data if the insert was successful 如果插入成功保存购物车数据
		if ($save_cart === TRUE)
		{
			$this->_save_cart();
			return TRUE;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update the cart
	 * 更新购物车
	 * This function permits changing item properties. 这个函数允许改变条目属性。
	 * Typically it is called from the "view cart" page if a user makes
	 * changes to the quantity before checkout. That array must contain the
	 * rowid and quantity for each item.
	 * 通常被称为的“查看购物车”页面如果用户更改结账前的数量。这个数组必须包含rowid为每个项目和数量。
	 * @param	array
	 * @return	bool
	 */
	protected function _update($items = array())
	{
		// Without these array indexes there is nothing we can do 没有这些数组索引没有什么我们可以做的
		if ( ! isset($items['rowid'], $this->_cart_contents[$items['rowid']]))
		{
			return FALSE;
		}

		// Prep the quantity 准备的数量
		if (isset($items['qty']))
		{
			$items['qty'] = (float) $items['qty'];
			// Is the quantity zero?  If so we will remove the item from the cart. 数量为零?如果是这样我们将从购物车中移除项。
			// If the quantity is greater than zero we are updating  如果我们更新的数量大于零
			if ($items['qty'] == 0)
			{
				unset($this->_cart_contents[$items['rowid']]);
				return TRUE;
			}
		}

		// find updatable keys  找到更新的钥匙
		$keys = array_intersect(array_keys($this->_cart_contents[$items['rowid']]), array_keys($items));
		// if a price was passed, make sure it contains valid data  如果通过了一项价格,确保它包含有效数据
		if (isset($items['price']))
		{
			$items['price'] = (float) $items['price'];
		}

		// product id & name shouldn't be changed  产品id和名称不应该改变
		foreach (array_diff($keys, array('id', 'name')) as $key)
		{
			$this->_cart_contents[$items['rowid']][$key] = $items[$key];
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Save the cart array to the session DB
	 * 购物车数组保存到会话DB
	 * @return	bool
	 */
	protected function _save_cart()
	{
		// Let's add up the individual prices and set the cart sub-total 让我们把个别价格并设置车小计
		$this->_cart_contents['total_items'] = $this->_cart_contents['cart_total'] = 0;
		foreach ($this->_cart_contents as $key => $val)
		{
			// We make sure the array contains the proper indexes 我们要确保该数组包含适当的索引
			if ( ! is_array($val) OR ! isset($val['price'], $val['qty']))
			{
				continue;
			}

			$this->_cart_contents['cart_total'] += ($val['price'] * $val['qty']);
			$this->_cart_contents['total_items'] += $val['qty'];
			$this->_cart_contents[$key]['subtotal'] = ($this->_cart_contents[$key]['price'] * $this->_cart_contents[$key]['qty']);
		}

		// Is our cart empty? If so we delete it from the session 我们的车是空的吗?如果我们删除它从会话
		if (count($this->_cart_contents) <= 2)
		{
			$this->CI->session->unset_userdata('cart_contents');

			// Nothing more to do... coffee time! 没有更多的事情要做……喝咖啡的时间!
			return FALSE;
		}

		// If we made it this far it means that our cart has data. 如果我们做到这一步就意味着我们的购物车数据。
		// Let's pass it to the Session class so it can be stored 让我们把它所以它可以存储会话类
		$this->CI->session->set_userdata(array('cart_contents' => $this->_cart_contents));

		// Woot!
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Cart Total
	 * 购物车总计
	 * @return	int
	 */
	public function total()
	{
		return $this->_cart_contents['cart_total'];
	}

	// --------------------------------------------------------------------

	/**
	 * Remove Item
	 * 删除项
	 * Removes an item from the cart
	 * 从购物车中删除一个条目
	 * @param	int
	 * @return	bool
	 */
	 public function remove($rowid)
	 {
		// unset & save 清除设置和保存
		unset($this->_cart_contents[$rowid]);
		$this->_save_cart();
		return TRUE;
	 }

	// --------------------------------------------------------------------

	/**
	 * Total Items
	 * 类目总计
	 * Returns the total item count
	 * 返回项的总数
	 * @return	int
	 */
	public function total_items()
	{
		return $this->_cart_contents['total_items'];
	}

	// --------------------------------------------------------------------

	/**
	 * Cart Contents
	 * 购物车的内容
	 * Returns the entire cart array
	 * 返回整个购物车数组
	 * @param	bool
	 * @return	array
	 */
	public function contents($newest_first = FALSE)
	{
		// do we want the newest first?  我们想要最新的吗?
		$cart = ($newest_first) ? array_reverse($this->_cart_contents) : $this->_cart_contents;

		// Remove these so they don't create a problem when showing the cart table 移除这些所以他们不显示购物车表时创建一个问题
		unset($cart['total_items']);
		unset($cart['cart_total']);

		return $cart;
	}

	// --------------------------------------------------------------------

	/**
	 * Get cart item
	 * 获得购物车类目
	 * Returns the details of a specific item in the cart
	 * 购物车中返回一个特定项目的细节
	 * @param	string	$row_id
	 * @return	array
	 */
	public function get_item($row_id)
	{
		return (in_array($row_id, array('total_items', 'cart_total'), TRUE) OR ! isset($this->_cart_contents[$row_id]))
			? FALSE
			: $this->_cart_contents[$row_id];
	}

	// --------------------------------------------------------------------

	/**
	 * Has options
	 * 有选项
	 * Returns TRUE if the rowid passed to this function correlates to an item 传递给这个函数返回TRUE,如果rowid关联一个项目
	 * that has options associated with it.  与之关联的选项。
	 *
	 * @param	string	$row_id = ''
	 * @return	bool
	 */
	public function has_options($row_id = '')
	{
		return (isset($this->_cart_contents[$row_id]['options']) && count($this->_cart_contents[$row_id]['options']) !== 0);
	}

	// --------------------------------------------------------------------

	/**
	 * Product options
	 * 产品选项
	 * Returns the an array of options, for a particular product row ID
	 * 返回一个数组的选项,为一个特定的产品行ID
	 * @param	string	$row_id = ''
	 * @return	array
	 */
	public function product_options($row_id = '')
	{
		return isset($this->_cart_contents[$row_id]['options']) ? $this->_cart_contents[$row_id]['options'] : array();
	}

	// --------------------------------------------------------------------

	/**
	 * Format Number
	 * 数字格式
	 * Returns the supplied number with commas and a decimal point.
	 * 返回逗号和小数点的数提供。
	 * @param	float
	 * @return	string
	 */
	public function format_number($n = '')
	{
		return ($n === '') ? '' : number_format( (float) $n, 2, '.', ',');
	}

	// --------------------------------------------------------------------

	/**
	 * Destroy the cart
	 * 销毁购物车
	 * Empties the cart and kills the session
	 * 清空购物车并杀死会话
	 * @return	void
	 */
	public function destroy()
	{
		$this->_cart_contents = array('cart_total' => 0, 'total_items' => 0);
		$this->CI->session->unset_userdata('cart_contents');
	}

}
