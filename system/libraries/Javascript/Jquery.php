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
 * Jquery Class
 * Jquery类
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Loader
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/javascript.html
 */
class CI_Jquery extends CI_Javascript {

	/**
	 * JavaScript directory location
	 * JavaScript目录位置
	 * @var	string
	 */
	protected $_javascript_folder = 'js';

	/**
	 * JQuery code for load
	 * JQuery代码加载
	 * @var	array
	 */
	public $jquery_code_for_load = array();

	/**
	 * JQuery code for compile
	 * JQuery代码编译
	 * @var	array
	 */
	public $jquery_code_for_compile = array();

	/**
	 * JQuery corner active flag
	 * JQuery角落里活跃的标识
	 * @var	bool
	 */
	public $jquery_corner_active = FALSE;

	/**
	 * JQuery table sorter active flag
	 * JQuery表分类器活跃的标志
	 * @var	bool
	 */
	public $jquery_table_sorter_active = FALSE;

	/**
	 * JQuery table sorter pager active
	 * JQuery表分选机寻呼机活跃
	 * @var	bool
	 */
	public $jquery_table_sorter_pager_active = FALSE;

	/**
	 * JQuery AJAX image
	 * JQuery AJAX图像
	 * @var	string
	 */
	public $jquery_ajax_img = '';

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 * 构造函数
	 * @param	array	$params
	 * @return	void
	 */
	public function __construct($params)
	{
		$this->CI =& get_instance();
		extract($params);

		if ($autoload === TRUE)
		{
			$this->script();
		}

		log_message('info', 'Jquery Class Initialized Jquery类初始化');
	}

	// --------------------------------------------------------------------
	// Event Code 事件代码
	// --------------------------------------------------------------------

	/**
	 * Blur
	 * 模糊事件
	 * Outputs a jQuery blur event
	 * 输出一个jQuery模糊事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	protected function _blur($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'blur');
	}

	// --------------------------------------------------------------------

	/**
	 * Change
	 * 改变
	 * Outputs a jQuery change event
	 * 输出一个jQuery更改事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	protected function _change($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'change');
	}

	// --------------------------------------------------------------------

	/**
	 * Click
	 * 点击
	 * Outputs a jQuery click event
	 * 输出一个jQuery单击事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @param	bool	whether or not to return false 是否要返回false
	 * @return	string
	 */
	protected function _click($element = 'this', $js = '', $ret_false = TRUE)
	{
		is_array($js) OR $js = array($js);

		if ($ret_false)
		{
			$js[] = 'return false;';
		}

		return $this->_add_event($element, $js, 'click');
	}

	// --------------------------------------------------------------------

	/**
	 * Double Click
	 * 双击
	 * Outputs a jQuery dblclick event
	 * 输出一个jQuery双击鼠标事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	protected function _dblclick($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'dblclick');
	}

	// --------------------------------------------------------------------

	/**
	 * Error
	 * 错误
	 * Outputs a jQuery error event
	 * 输出一个jQuery错误事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	protected function _error($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'error');
	}

	// --------------------------------------------------------------------

	/**
	 * Focus
	 * 聚焦 焦点
	 * Outputs a jQuery focus event
	 * 输出一个jQuery的焦点事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	protected function _focus($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'focus');
	}

	// --------------------------------------------------------------------

	/**
	 * Hover
	 * 悬停
	 * Outputs a jQuery hover event
	 * 输出一个jQuery悬停事件
	 * @param	string	- element 元素
	 * @param	string	- Javascript code for mouse over 鼠标越过的Javascript代码
	 * @param	string	- Javascript code for mouse out  鼠标离开的Javascript代码
	 * @return	string
	 */
	protected function _hover($element = 'this', $over = '', $out = '')
	{
		$event = "\n\t$(".$this->_prep_element($element).").hover(\n\t\tfunction()\n\t\t{\n\t\t\t{$over}\n\t\t}, \n\t\tfunction()\n\t\t{\n\t\t\t{$out}\n\t\t});\n";

		$this->jquery_code_for_compile[] = $event;

		return $event;
	}

	// --------------------------------------------------------------------

	/**
	 * Keydown 键盘按下
	 *
	 * Outputs a jQuery keydown event
	 * 输出一个jQuery 按键按下事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	protected function _keydown($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'keydown');
	}

	// --------------------------------------------------------------------

	/**
	 * Keyup 键盘弹起
	 *
	 * Outputs a jQuery keydown event
	 * 输出一个jQuery keydown事件
	 * @param	string	The element to attach the event to  元素附加事件
	 * @param	string	The code to execute  要执行的代码
	 * @return	string
	 */
	protected function _keyup($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'keyup');
	}

	// --------------------------------------------------------------------

	/**
	 * Load  加载
	 *
	 * Outputs a jQuery load event
	 * 输出一个jQuery加载事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	protected function _load($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'load');
	}

	// --------------------------------------------------------------------

	/**
	 * Mousedown 鼠标按下
	 *
	 * Outputs a jQuery mousedown event
	 * 输出一个jQuery mousedown事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	protected function _mousedown($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'mousedown');
	}

	// --------------------------------------------------------------------

	/**
	 * Mouse Out 鼠标移开
	 *
	 * Outputs a jQuery mouseout event
	 * 输出一个jQuery mouseout事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	protected function _mouseout($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'mouseout');
	}

	// --------------------------------------------------------------------

	/**
	 * Mouse Over 鼠标移过
	 *
	 * Outputs a jQuery mouseover event
	 * 输出一个jQuery mouseover事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	protected function _mouseover($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'mouseover');
	}

	// --------------------------------------------------------------------

	/**
	 * Mouseup 鼠标弹起
	 *
	 * Outputs a jQuery mouseup event
	 * 输出一个jQuery mouseup事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute  要执行的代码
	 * @return	string
	 */
	protected function _mouseup($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'mouseup');
	}

	// --------------------------------------------------------------------

	/**
	 * Output 输出
	 *
	 * Outputs script directly
	 * 直接输出脚本
	 * @param	array	$array_js = array()
	 * @return	void
	 */
	protected function _output($array_js = array())
	{
		if ( ! is_array($array_js))
		{
			$array_js = array($array_js);
		}

		foreach ($array_js as $js)
		{
			$this->jquery_code_for_compile[] = "\t".$js."\n";
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Resize 调整大小
	 *
	 * Outputs a jQuery resize event
	 * 输出一个jQuery调整大小事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	protected function _resize($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'resize');
	}

	// --------------------------------------------------------------------

	/**
	 * Scroll 滚动
	 *
	 * Outputs a jQuery scroll event
	 * 输出一个jQuery滚动事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	protected function _scroll($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'scroll');
	}

	// --------------------------------------------------------------------

	/**
	 * Unload
	 * 卸载
	 * Outputs a jQuery unload event
	 * 输出一个jQuery卸载事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	protected function _unload($element = 'this', $js = '')
	{
		return $this->_add_event($element, $js, 'unload');
	}

	// --------------------------------------------------------------------
	// Effects 效果 特效
	// --------------------------------------------------------------------

	/**
	 * Add Class 添加类
	 *
	 * Outputs a jQuery addClass event
	 * 最好选择用addClass事件替代输出一个jQuery
	 * @param	string	$element 元素
	 * @param	string	$class
	 * @return	string
	 */
	protected function _addClass($element = 'this', $class = '')
	{
		$element = $this->_prep_element($element);
		return '$('.$element.').addClass("'.$class.'");';
	}

	// --------------------------------------------------------------------

	/**
	 * Animate 动画
	 * 
	 * Outputs a jQuery animate event
	 * 输出一个jQuery动画事件
	 * @param	string	$element  元素
	 * @param	array	$params   参数
	 * @param	string	$speed	'slow慢', 'normal正常', 'fast快', or time in milliseconds 时间以毫秒为单位
	 * @param	string	$extra 额外的
	 * @return	string
	 */
	protected function _animate($element = 'this', $params = array(), $speed = '', $extra = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);

		$animations = "\t\t\t";

		foreach ($params as $param => $value)
		{
			$animations .= $param.": '".$value."', ";
		}

		$animations = substr($animations, 0, -2); // remove the last ", "

		if ($speed !== '')
		{
			$speed = ', '.$speed;
		}

		if ($extra !== '')
		{
			$extra = ', '.$extra;
		}

		return "$({$element}).animate({\n$animations\n\t\t}".$speed.$extra.');';
	}

	// --------------------------------------------------------------------

	/**
	 * Fade In 淡入
	 *
	 * Outputs a jQuery hide event
	 * 输出一个jQuery隐藏事件
	 * @param	string	- element 元素
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds 时间以毫秒为单位
	 * @param	string	- Javascript callback function Javascript回调函数
	 * @return	string
	 */
	protected function _fadeIn($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);

		if ($callback !== '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}

		return "$({$element}).fadeIn({$speed}{$callback});";
	}

	// --------------------------------------------------------------------

	/**
	 * Fade Out 淡出
	 *
	 * Outputs a jQuery hide event
	 * 输出一个jQuery隐藏事件
	 * @param	string	- element 元素
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds 时间以毫秒为单位
	 * @param	string	- Javascript callback function Javascript回调函数
	 * @return	string
	 */
	protected function _fadeOut($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);

		if ($callback !== '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}

		return '$('.$element.').fadeOut('.$speed.$callback.');';
	}

	// --------------------------------------------------------------------

	/**
	 * Hide 隐藏
	 *
	 * Outputs a jQuery hide action
	 * 输出一个jQuery隐藏行动
	 * @param	string	- element 元素
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds 时间以毫秒为单位
	 * @param	string	- Javascript callback function  Javascript回调函数
	 * @return	string
	 */
	protected function _hide($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);

		if ($callback !== '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}

		return "$({$element}).hide({$speed}{$callback});";
	}

	// --------------------------------------------------------------------

	/**
	 * Remove Class
	 * 移除类
	 * Outputs a jQuery remove class event
	 * 输出一个jQuery删除类事件
	 * @param	string	$element 元素
	 * @param	string	$class  类
	 * @return	string
	 */
	protected function _removeClass($element = 'this', $class = '')
	{
		$element = $this->_prep_element($element);
		return '$('.$element.').removeClass("'.$class.'");';
	}

	// --------------------------------------------------------------------

	/**
	 * Slide Up  上滑音；滑盖式
	 *
	 * Outputs a jQuery slideUp event
	 * 输出一个jQuery slideUp事件
	 * @param	string	- element  元素
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds 时间以毫秒为单位
	 * @param	string	- Javascript callback function  Javascript回调函数
	 * @return	string
	 */
	protected function _slideUp($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);

		if ($callback !== '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}

		return '$('.$element.').slideUp('.$speed.$callback.');';
	}

	// --------------------------------------------------------------------

	/**
	 * Slide Down 往下滑
	 *
	 * Outputs a jQuery slideDown event
	 * 输出一个jQuery slideDown事件
	 * @param	string	- element  元素
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds 时间以毫秒为单位
	 * @param	string	- Javascript callback function  Javascript回调函数
	 * @return	string
	 */
	protected function _slideDown($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);

		if ($callback !== '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}

		return '$('.$element.').slideDown('.$speed.$callback.');';
	}

	// --------------------------------------------------------------------

	/**
	 * Slide Toggle  滑音控制
	 *
	 * Outputs a jQuery slideToggle event
	 * 输出一个jQuery slideToggle事件
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds 时间以毫秒为单位
	 * @param	string	- Javascript callback function  Javascript回调函数
	 * @return	string
	 */
	protected function _slideToggle($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);

		if ($callback !== '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}

		return '$('.$element.').slideToggle('.$speed.$callback.');';
	}

	// --------------------------------------------------------------------

	/**
	 * Toggle 开关，触发器
	 *
	 * Outputs a jQuery toggle event
	 * 输出一个jQuery触发事件
	 * @param	string	- element 元素
	 * @return	string
	 */
	protected function _toggle($element = 'this')
	{
		$element = $this->_prep_element($element);
		return '$('.$element.').toggle();';
	}

	// --------------------------------------------------------------------

	/**
	 * Toggle Class  支撑类
	 *
	 * Outputs a jQuery toggle class event
	 * 输出jquery开关类事件
	 * @param	string	$element 元素
	 * @param	string	$class
	 * @return	string
	 */
	protected function _toggleClass($element = 'this', $class = '')
	{
		$element = $this->_prep_element($element);
		return '$('.$element.').toggleClass("'.$class.'");';
	}

	// --------------------------------------------------------------------

	/**
	 * Show 显示
	 *
	 * Outputs a jQuery show event
	 * 输出一个jQuery显示事件
	 * @param	string	- element 元素
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds 时间以毫秒为单位
	 * @param	string	- Javascript callback function  Javascript回调函数
	 * @return	string
	 */
	protected function _show($element = 'this', $speed = '', $callback = '')
	{
		$element = $this->_prep_element($element);
		$speed = $this->_validate_speed($speed);

		if ($callback !== '')
		{
			$callback = ", function(){\n{$callback}\n}";
		}

		return '$('.$element.').show('.$speed.$callback.');';
	}

	// --------------------------------------------------------------------

	/**
	 * Updater  更新器
	 *
	 * An Ajax call that populates the designated DOM node with
	 * returned content
	 * 一个Ajax调用与返回填充指定的DOM节点的内容
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	the controller to run the call against 控制器运行调用
	 * @param	string	optional parameters 可选参数
	 * @return	string
	 */

	protected function _updater($container = 'this', $controller = '', $options = '')
	{
		$container = $this->_prep_element($container);
		$controller = (strpos('://', $controller) === FALSE) ? $controller : $this->CI->config->site_url($controller);

		// ajaxStart and ajaxStop are better choices here... but this is a stop gap
		// ajaxStart和ajaxStop是更好的选择……但这是一个权宜之计
		if ($this->CI->config->item('javascript_ajax_img') === '')
		{
			$loading_notifier = 'Loading...';
		}
		else
		{
			$loading_notifier = '<img src="'.$this->CI->config->slash_item('base_url').$this->CI->config->item('javascript_ajax_img').'" alt="Loading" />';
		}

		$updater = '$('.$container.").empty();\n" // anything that was in... get it out 任何东西在…把它弄出来
			."\t\t$(".$container.').prepend("'.$loading_notifier."\");\n"; // to replace with an image 替换为一个形象

		$request_options = '';
		if ($options !== '')
		{
			$request_options .= ', {'
					.(is_array($options) ? "'".implode("', '", $options)."'" : "'".str_replace(':', "':'", $options)."'")
					.'}';
		}

		return $updater."\t\t$($container).load('$controller'$request_options);";
	}

	// --------------------------------------------------------------------
	// Pre-written handy stuff 预先写好的方便的东西
	// --------------------------------------------------------------------

	/**
	 * Zebra tables 斑马表
	 *
	 * @param	string	$class
	 * @param	string	$odd
	 * @param	string	$hover
	 * @return	string
	 */
	protected function _zebraTables($class = '', $odd = 'odd', $hover = '')
	{
		$class = ($class !== '') ? '.'.$class : '';
		$zebra = "\t\$(\"table{$class} tbody tr:nth-child(even)\").addClass(\"{$odd}\");";

		$this->jquery_code_for_compile[] = $zebra;

		if ($hover !== '')
		{
			$hover = $this->hover("table{$class} tbody tr", "$(this).addClass('hover');", "$(this).removeClass('hover');");
		}

		return $zebra;
	}

	// --------------------------------------------------------------------
	// Plugins 插件；
	// --------------------------------------------------------------------

	/**
	 * Corner Plugin 角落里的插件
	 *
	 * @link	http://www.malsup.com/jquery/corner/
	 * @param	string	$element 元素
	 * @param	string	$corner_style
	 * @return	string
	 */
	public function corner($element = '', $corner_style = '')
	{
		// may want to make this configurable down the road 可能想让这一路上可配置
		$corner_location = '/plugins/jquery.corner.js';

		if ($corner_style !== '')
		{
			$corner_style = '"'.$corner_style.'"';
		}

		return '$('.$this->_prep_element($element).').corner('.$corner_style.');';
	}

	// --------------------------------------------------------------------

	/**
	 * Modal window 模式窗口
	 *
	 * Load a thickbox modal window
	 * 加载thickbox模态窗口
	 * @param	string	$src
	 * @param	bool	$relative 相关的
	 * @return	void
	 */
	public function modal($src, $relative = FALSE)
	{
		$this->jquery_code_for_load[] = $this->external($src, $relative);
	}

	// --------------------------------------------------------------------

	/**
	 * Effect 影响 作用
	 *
	 * Load an Effect library
	 * 加载效果库
	 * @param	string	$src
	 * @param	bool	$relative 相关的
	 * @return	void
	 */
	public function effect($src, $relative = FALSE)
	{
		$this->jquery_code_for_load[] = $this->external($src, $relative);
	}

	// --------------------------------------------------------------------

	/**
	 * Plugin 插件
	 *
	 * Load a plugin library
	 * 加载插件库
	 * @param	string	$src
	 * @param	bool	$relative 相关的
	 * @return	void
	 */
	public function plugin($src, $relative = FALSE)
	{
		$this->jquery_code_for_load[] = $this->external($src, $relative);
	}

	// --------------------------------------------------------------------

	/**
	 * UI
	 *
	 * Load a user interface library
	 * 加载用户界面库
	 * @param	string	$src
	 * @param	bool	$relative 相关的
	 * @return	void
	 */
	public function ui($src, $relative = FALSE)
	{
		$this->jquery_code_for_load[] = $this->external($src, $relative);
	}

	// --------------------------------------------------------------------

	/**
	 * Sortable
	 * 可分类的
	 * Creates a jQuery sortable
	 * 创建了一个jQuery可分类的
	 * @param	string	$element 元素
	 * @param	array	$options 可选择
	 * @return	string
	 */
	public function sortable($element, $options = array())
	{
		if (count($options) > 0)
		{
			$sort_options = array();
			foreach ($options as $k=>$v)
			{
				$sort_options[] = "\n\t\t".$k.': '.$v;
			}
			$sort_options = implode(',', $sort_options);
		}
		else
		{
			$sort_options = '';
		}

		return '$('.$this->_prep_element($element).').sortable({'.$sort_options."\n\t});";
	}

	// --------------------------------------------------------------------

	/**
	 * Table Sorter Plugin
	 * 表分类器插件
	 * @param	string	table name
	 * @param	string	plugin location 插件的位置
	 * @return	string
	 */
	public function tablesorter($table = '', $options = '')
	{
		$this->jquery_code_for_compile[] = "\t$(".$this->_prep_element($table).').tablesorter('.$options.");\n";
	}

	// --------------------------------------------------------------------
	// Class functions 类方法
	// --------------------------------------------------------------------

	/**
	 * Add Event 添加事件
	 *  
	 * Constructs the syntax for an event, and adds to into the array for compilation
	 * 构造事件的语法,并添加到数组进行编译
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @param	string	The event to pass 事件经过
	 * @return	string
	 */
	protected function _add_event($element, $js, $event)
	{
		if (is_array($js))
		{
			$js = implode("\n\t\t", $js);
		}

		$event = "\n\t$(".$this->_prep_element($element).').'.$event."(function(){\n\t\t{$js}\n\t});\n";
		$this->jquery_code_for_compile[] = $event;
		return $event;
	}

	// --------------------------------------------------------------------

	/**
	 * Compile 编译
	 *
	 * As events are specified, they are stored in an array
	 * This function compiles them all for output on a page
	 * 作为指定的事件,他们都存储在一个数组函数编译输出页面上
	 * @param	string	$view_var
	 * @param	bool	$script_tags
	 * @return	void
	 */
	protected function _compile($view_var = 'script_foot', $script_tags = TRUE)
	{
		// External references 外部参考 引用
		$external_scripts = implode('', $this->jquery_code_for_load);
		$this->CI->load->vars(array('library_src' => $external_scripts));

		if (count($this->jquery_code_for_compile) === 0)
		{
			// no inline references, let's just return 没有内联引用,我们就回来了
			return;
		}

		// Inline references 内联的引用
		$script = '$(document).ready(function() {'."\n"
			.implode('', $this->jquery_code_for_compile)
			.'});';

		$output = ($script_tags === FALSE) ? $script : $this->inline($script);

		$this->CI->load->vars(array($view_var => $output));
	}

	// --------------------------------------------------------------------

	/**
	 * Clear Compile
	 * 清理编译
	 * Clears the array of script events collected for output
	 * 清理脚本事件收集的数组输出
	 * @return	void
	 */
	protected function _clear_compile()
	{
		$this->jquery_code_for_compile = array();
	}

	// --------------------------------------------------------------------

	/**
	 * Document Ready 
	 * 文档已经准备好 
	 * A wrapper for writing document.ready()
	 * 一个包装器编写document.ready()
	 * @param	array	$js
	 * @return	void
	 */
	protected function _document_ready($js)
	{
		is_array($js) OR $js = array($js);

		foreach ($js as $script)
		{
			$this->jquery_code_for_compile[] = $script;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Script Tag 脚本标签 
	 *
	 * Outputs the script tag that loads the jquery.js file into an HTML document
	 * 输出加载jquery脚本标记。js文件到一个HTML文档
	 * @param	string	$library_src
	 * @param	bool	$relative 相关的
	 * @return	string
	 */
	public function script($library_src = '', $relative = FALSE)
	{
		$library_src = $this->external($library_src, $relative);
		$this->jquery_code_for_load[] = $library_src;
		return $library_src;
	}

	// --------------------------------------------------------------------

	/**
	 * Prep Element
	 * 预科元素
	 * Puts HTML element in quotes for use in jQuery code 使HTML元素引用jQuery代码使用
	 * unless the supplied element is the Javascript 'this'
	 * object, in which case no quotes are added
	 * 除非提供的元素是Javascript的这个对象,在这种情况下不添加引号
	 * @param	string
	 * @return	string
	 */
	protected function _prep_element($element)
	{
		if ($element !== 'this')
		{
			$element = '"'.$element.'"';
		}

		return $element;
	}

	// --------------------------------------------------------------------

	/**
	 * Validate Speed
	 * 验证速度
	 * Ensures the speed parameter is valid for jQuery
	 * 确保jQuery的速度参数是有效的
	 * @param	string
	 * @return	string
	 */
	protected function _validate_speed($speed)
	{
		if (in_array($speed, array('slow', 'normal', 'fast')))
		{
			return '"'.$speed.'"';
		}
		elseif (preg_match('/[^0-9]/', $speed))
		{
			return '';
		}

		return $speed;
	}

}
