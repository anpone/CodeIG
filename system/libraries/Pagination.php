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
 * Pagination Class
 * 分页类
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Pagination
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/pagination.html
 */
class CI_Pagination {

	/**
	 * Base URL
	 * 基础URL
	 * The page that we're linking to
	 * 我们链接到的页面
	 * @var	string
	 */
	protected $base_url		= '';

	/**
	 * Prefix
	 * 前缀
	 * @var	string
	 */
	protected $prefix = '';

	/**
	 * Suffix
	 * 后缀
	 * @var	string
	 */
	protected $suffix = '';

	/**
	 * Total number of items
	 * 项目总数
	 * @var	int
	 */
	protected $total_rows = 0;

	/**
	 * Number of links to show
	 * 显示的链接数
	 * Relates to "digit" type links shown before/after
	 * the currently viewed page.
	 * 与“数字”类型之前/之后显示当前查看的页面的链接。
	 * @var	int
	 */
	protected $num_links = 2;

	/**
	 * Items per page
	 * 每页呈现笔数
	 * @var	int
	 */
	public $per_page = 10;

	/**
	 * Current page
	 * 当前页 
	 * @var	int
	 */
	public $cur_page = 0;

	/**
	 * Use page numbers flag
	 * 使用页码标志
	 * Whether to use actual page numbers instead of an offset
	 * 是否使用实际的页码,而不是一个偏移量
	 * @var	bool
	 */
	protected $use_page_numbers = FALSE;

	/**
	 * First link
	 * 第一环节
	 * @var	string
	 */
	protected $first_link = '&lsaquo; First';

	/**
	 * Next link
	 * 下一页链接
	 * @var	string
	 */
	protected $next_link = '&gt;';

	/**
	 * Previous link
	 * 前一页链接
	 * @var	string
	 */
	protected $prev_link = '&lt;';

	/**
	 * Last link
	 * 最后一页链接
	 * @var	string
	 */
	protected $last_link = 'Last &rsaquo;';

	/**
	 * URI Segment
	 * URI段
	 * @var	int
	 */
	protected $uri_segment = 0;

	/**
	 * Full tag open
	 * 完整的标签打开
	 * @var	string
	 */
	protected $full_tag_open = '';

	/**
	 * Full tag close
	 * 完整的标签关闭
	 * @var	string
	 */
	protected $full_tag_close = '';

	/**
	 * First tag open
	 * 第一个标签打开
	 * @var	string
	 */
	protected $first_tag_open = '';

	/**
	 * First tag close
	 * 第一个标签关闭
	 * @var	string
	 */
	protected $first_tag_close = '';

	/**
	 * Last tag open
	 * 最后一个标签打开
	 * @var	string
	 */
	protected $last_tag_open = '';

	/**
	 * Last tag close
	 * 最后一个标签关闭
	 * @var	string
	 */
	protected $last_tag_close = '';

	/**
	 * First URL
	 * 第一个网址
	 * An alternative URL for the first page
	 * 第一个页面的另一个URL
	 * @var	string
	 */
	protected $first_url = '';

	/**
	 * Current tag open
	 * 当前标签打开
	 * @var	string
	 */
	protected $cur_tag_open = '<strong>';

	/**
	 * Current tag close
	 * 关闭当前标签
	 * @var	string
	 */
	protected $cur_tag_close = '</strong>';

	/**
	 * Next tag open
	 * 下一个标签打开
	 * @var	string
	 */
	protected $next_tag_open = '';

	/**
	 * Next tag close
	 * 下一个标签关闭
	 * @var	string
	 */
	protected $next_tag_close = '';

	/**
	 * Previous tag open
	 * 以前的标签打开
	 * @var	string
	 */
	protected $prev_tag_open = '';

	/**
	 * Previous tag close
	 * 以前的标签关闭
	 * @var	string
	 */
	protected $prev_tag_close = '';

	/**
	 * Number tag open
	 * 编号标记开放
	 * @var	string
	 */
	protected $num_tag_open = '';

	/**
	 * Number tag close
	 * 编号标记关闭
	 * @var	string
	 */
	protected $num_tag_close = '';

	/**
	 * Page query string flag
	 * 页面查询字符串标记
	 * @var	bool
	 */
	protected $page_query_string = FALSE;

	/**
	 * Query string segment
	 * 查询字符串片段
	 * @var	string
	 */
	protected $query_string_segment = 'per_page';

	/**
	 * Display pages flag
	 * 显示页面标记
	 * @var	bool
	 */
	protected $display_pages = TRUE;

	/**
	 * Attributes
	 * 属性
	 * @var	string
	 */
	protected $_attributes = '';

	/**
	 * Link types
	 * 链接类型
	 * "rel" attribute
	 * rel 的属性
	 * @see	CI_Pagination::_attr_rel()
	 * @var	array
	 */
	protected $_link_types = array();

	/**
	 * Reuse query string flag
	 * 重用查询字符串标记
	 * @var	bool
	 */
	protected $reuse_query_string = FALSE;

	/**
	 * Use global URL suffix flag
	 * 使用全局URL后缀标记
	 * @var	bool
	 */
	protected $use_global_url_suffix = FALSE;

	/**
	 * Data page attribute
	 * 数据页面属性
	 * @var	string
	 */
	protected $data_page_attr = 'data-ci-pagination-page';

	/**
	 * CI Singleton
	 * CI单例
	 * @var	object
	 */
	protected $CI;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 * 构造函数
	 * @param	array	$params	Initialization parameters 初始化参数
	 * @return	void
	 */
	public function __construct($params = array())
	{
		$this->CI =& get_instance();
		$this->CI->load->language('pagination');
		foreach (array('first_link', 'next_link', 'prev_link', 'last_link') as $key)
		{
			if (($val = $this->CI->lang->line('pagination_'.$key)) !== FALSE)
			{
				$this->$key = $val;
			}
		}

		$this->initialize($params);
		log_message('info', 'Pagination Class Initialized分页类初始化');
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize Preferences
	 * 初始化参数
	 * @param	array	$params	Initialization parameters 初始化参数
	 * @return	CI_Pagination
	 */
	public function initialize(array $params = array())
	{
		isset($params['attributes']) OR $params['attributes'] = array();
		if (is_array($params['attributes']))
		{
			$this->_parse_attributes($params['attributes']);
			unset($params['attributes']);
		}

		// Deprecated legacy support for the anchor_class option 弃用遗留支持anchor_class选项
		// Should be removed in CI 3.1+ 在CI 3.1 +应该删除吗
		if (isset($params['anchor_class']))
		{
			empty($params['anchor_class']) OR $attributes['class'] = $params['anchor_class'];
			unset($params['anchor_class']);
		}

		foreach ($params as $key => $val)
		{
			if (property_exists($this, $key))
			{
				$this->$key = $val;
			}
		}

		if ($this->CI->config->item('enable_query_strings') === TRUE)
		{
			$this->page_query_string = TRUE;
		}

		if ($this->use_global_url_suffix === TRUE)
		{
			$this->suffix = $this->CI->config->item('url_suffix');
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Generate the pagination links
	 * 生成分页链接
	 * @return	string
	 */
	public function create_links()
	{
		// If our item count or per-page total is zero there is no need to continue. 如果条目数或者页面的总为零,我们没有必要继续。
		// Note: DO NOT change the operator to === here! 注意:不要改变这里的操作符= = = !
		if ($this->total_rows == 0 OR $this->per_page == 0)
		{
			return '';
		}

		// Calculate the total number of pages 计算出总页数
		$num_pages = (int) ceil($this->total_rows / $this->per_page);

		// Is there only one page? Hm... nothing more to do here then. 只有一页吗?嗯……这里没有更多的事情要做。
		if ($num_pages === 1)
		{
			return '';
		}

		// Check the user defined number of links. 检查用户定义的链接数。
		$this->num_links = (int) $this->num_links;

		if ($this->num_links < 0)
		{
			show_error('Your number of links must be a non-negative number.');
		}

		// Keep any existing query string items. 保持任何现有的查询字符串条目。
		// Note: Has nothing to do with any other query string option. 注意:与其他无关查询字符串的选择。
		if ($this->reuse_query_string === TRUE)
		{
			$get = $this->CI->input->get();

			// Unset the controll, method, old-school routing options 设置控制,方法,老派的路由选择
			unset($get['c'], $get['m'], $get[$this->query_string_segment]);
		}
		else
		{
			$get = array();
		}

		// Put together our base and first URLs. 整理我们的基地和第一url。
		// Note: DO NOT append to the properties as that would break successive calls 注意:不要添加到属性,将打破连续调用
		$base_url = trim($this->base_url);
		$first_url = $this->first_url;

		$query_string = '';
		$query_string_sep = (strpos($base_url, '?') === FALSE) ? '?' : '&amp;';

		// Are we using query strings? 我们使用查询字符串吗?
		if ($this->page_query_string === TRUE)
		{
			// If a custom first_url hasn't been specified, we'll create one from
			// the base_url, but without the page item.
			// 如果定制first_url没有指定,我们将创建一个从base_url,但是没有项目的页面。
			if ($first_url === '')
			{
				$first_url = $base_url;

				// If we saved any GET items earlier, make sure they're appended. 如果我们保存任何东西之前,确保他们是附加的。
				if ( ! empty($get))
				{
					$first_url .= $query_string_sep.http_build_query($get);
				}
			}

			// Add the page segment to the end of the query string, where the
			// page number will be appended.
			// 添加页面查询字符串的结束部分,页码添加。
			$base_url .= $query_string_sep.http_build_query(array_merge($get, array($this->query_string_segment => '')));
		}
		else
		{
			// Standard segment mode. 标准段模式。
			// Generate our saved query string to append later after the page number. 生成我们保存的查询字符串添加后页码。
			if ( ! empty($get))
			{
				$query_string = $query_string_sep.http_build_query($get);
				$this->suffix .= $query_string;
			}

			// Does the base_url have the query string in it? base_url有查询字符串吗?
			// If we're supposed to save it, remove it so we can append it later. 如果我们应该保存它,消除它我们可以追加。
			if ($this->reuse_query_string === TRUE && ($base_query_pos = strpos($base_url, '?')) !== FALSE)
			{
				$base_url = substr($base_url, 0, $base_query_pos);
			}

			if ($first_url === '')
			{
				$first_url = $base_url.$query_string;
			}

			$base_url = rtrim($base_url, '/').'/';
		}

		// Determine the current page number. 确定当前页码。
		$base_page = ($this->use_page_numbers) ? 1 : 0;

		// Are we using query strings? 我们使用查询字符串吗?
		if ($this->page_query_string === TRUE)
		{
			$this->cur_page = $this->CI->input->get($this->query_string_segment);
		}
		else
		{
			// Default to the last segment number if one hasn't been defined. 默认最后一段数量如果还没有定义。
			if ($this->uri_segment === 0)
			{
				$this->uri_segment = count($this->CI->uri->segment_array());
			}

			$this->cur_page = $this->CI->uri->segment($this->uri_segment);

			// Remove any specified prefix/suffix from the segment. 删除任何指定的前缀/后缀的段。
			if ($this->prefix !== '' OR $this->suffix !== '')
			{
				$this->cur_page = str_replace(array($this->prefix, $this->suffix), '', $this->cur_page);
			}
		}

		// If something isn't quite right, back to the default base page. 如果有些事不太对劲,回到默认的基本页面。
		if ( ! ctype_digit($this->cur_page) OR ($this->use_page_numbers && (int) $this->cur_page === 0))
		{
			$this->cur_page = $base_page;
		}
		else
		{
			// Make sure we're using integers for comparisons later. 确保我们以后使用整数进行比较。
			$this->cur_page = (int) $this->cur_page;
		}

		// Is the page number beyond the result range? 结果范围外的页码?
		// If so, we show the last page. 如果是这样,我们将展示最后一页。
		if ($this->use_page_numbers)
		{
			if ($this->cur_page > $num_pages)
			{
				$this->cur_page = $num_pages;
			}
		}
		elseif ($this->cur_page > $this->total_rows)
		{
			$this->cur_page = ($num_pages - 1) * $this->per_page;
		}

		$uri_page_number = $this->cur_page;

		// If we're using offset instead of page numbers, convert it  如果我们使用抵消而不是页码,转换它
		// to a page number, so we can generate the surrounding number links. 页码,所以我们可以生成周围的链接数量。
		if ( ! $this->use_page_numbers)
		{
			$this->cur_page = (int) floor(($this->cur_page/$this->per_page) + 1);
		}

		// Calculate the start and end numbers. These determine
		// which number to start and end the digit links with.
		// 开始和结束的数字计算。这些决定开始和结束位链接数
		$start	= (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
		$end	= (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;

		// And here we go... 在这里,我们去……
		$output = '';

		// Render the "First" link. 呈现“第一”链接。
		if ($this->first_link !== FALSE && $this->cur_page > ($this->num_links + 1 + ! $this->num_links))
		{
			// Take the general parameters, and squeeze this pagination-page attr in for JS frameworks.
			// 一般参数,挤压这pagination-page attr JS框架。
			$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, 1);

			$output .= $this->first_tag_open.'<a href="'.$first_url.'"'.$attributes.$this->_attr_rel('start').'>'
				.$this->first_link.'</a>'.$this->first_tag_close;
		}

		// Render the "Previous" link. 呈现“前”链接。
		if ($this->prev_link !== FALSE && $this->cur_page !== 1)
		{
			$i = ($this->use_page_numbers) ? $uri_page_number - 1 : $uri_page_number - $this->per_page;

			$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, ($this->cur_page - 1));

			if ($i === $base_page)
			{
				// First page 首页
				$output .= $this->prev_tag_open.'<a href="'.$first_url.'"'.$attributes.$this->_attr_rel('prev').'>'
					.$this->prev_link.'</a>'.$this->prev_tag_close;
			}
			else
			{
				$append = $this->prefix.$i.$this->suffix;
				$output .= $this->prev_tag_open.'<a href="'.$base_url.$append.'"'.$attributes.$this->_attr_rel('prev').'>'
					.$this->prev_link.'</a>'.$this->prev_tag_close;
			}

		}

		// Render the pages 呈现的页面
		if ($this->display_pages !== FALSE)
		{
			// Write the digit links 写数字链接
			for ($loop = $start - 1; $loop <= $end; $loop++)
			{
				$i = ($this->use_page_numbers) ? $loop : ($loop * $this->per_page) - $this->per_page;

				$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, $loop);

				if ($i >= $base_page)
				{
					if ($this->cur_page === $loop)
					{
						// Current page 当前页
						$output .= $this->cur_tag_open.$loop.$this->cur_tag_close;
					}
					elseif ($i === $base_page)
					{
						// First page 第一页
						$output .= $this->num_tag_open.'<a href="'.$first_url.'"'.$attributes.$this->_attr_rel('start').'>'
							.$loop.'</a>'.$this->num_tag_close;
					}
					else
					{
						$append = $this->prefix.$i.$this->suffix;
						$output .= $this->num_tag_open.'<a href="'.$base_url.$append.'"'.$attributes.'>'
							.$loop.'</a>'.$this->num_tag_close;
					}
				}
			}
		}

		// Render the "next" link 呈现“下一个”链接
		if ($this->next_link !== FALSE && $this->cur_page < $num_pages)
		{
			$i = ($this->use_page_numbers) ? $this->cur_page + 1 : $this->cur_page * $this->per_page;

			$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, $this->cur_page + 1);

			$output .= $this->next_tag_open.'<a href="'.$base_url.$this->prefix.$i.$this->suffix.'"'.$attributes
				.$this->_attr_rel('next').'>'.$this->next_link.'</a>'.$this->next_tag_close;
		}

		// Render the "Last" link 呈现“最后”链接
		if ($this->last_link !== FALSE && ($this->cur_page + $this->num_links + ! $this->num_links) < $num_pages)
		{
			$i = ($this->use_page_numbers) ? $num_pages : ($num_pages * $this->per_page) - $this->per_page;

			$attributes = sprintf('%s %s="%d"', $this->_attributes, $this->data_page_attr, $num_pages);

			$output .= $this->last_tag_open.'<a href="'.$base_url.$this->prefix.$i.$this->suffix.'"'.$attributes.'>'
				.$this->last_link.'</a>'.$this->last_tag_close;
		}

		// Kill double slashes. Note: Sometimes we can end up with a double slash
		// in the penultimate link so we'll kill all double slashes.
		// 杀死双斜杠。注意:有时我们可以得到双斜杠在倒数第二个链接,所以我们将杀死所有双斜杠。
		$output = preg_replace('#([^:"])//+#', '\\1/', $output);

		// Add the wrapper HTML if exists 如果存在添加HTML包装器
		return $this->full_tag_open.$output.$this->full_tag_close;
	}

	// --------------------------------------------------------------------

	/**
	 * Parse attributes
	 * 解析属性
	 * @param	array	$attributes 属性
	 * @return	void
	 */
	protected function _parse_attributes($attributes)
	{
		isset($attributes['rel']) OR $attributes['rel'] = TRUE;
		$this->_link_types = ($attributes['rel'])
			? array('start' => 'start', 'prev' => 'prev', 'next' => 'next')
			: array();
		unset($attributes['rel']);

		$this->_attributes = '';
		foreach ($attributes as $key => $value)
		{
			$this->_attributes .= ' '.$key.'="'.$value.'"';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Add "rel" attribute
	 * 添加“rel属性
	 * @link	http://www.w3.org/TR/html5/links.html#linkTypes
	 * @param	string	$type
	 * @return	string
	 */
	protected function _attr_rel($type)
	{
		if (isset($this->_link_types[$type]))
		{
			unset($this->_link_types[$type]);
			return ' rel="'.$type.'"';
		}

		return '';
	}

}
