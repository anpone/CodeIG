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
 * Javascript Class
 * Javascript类
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Javascript
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/javascript.html
 * @deprecated	3.0.0	This was never a good idea in the first place.
 */
class CI_Javascript {

	/**
	 * JavaScript location
	 * 地址对象
	 * @var	string
	 */
	protected $_javascript_location = 'js';

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 * 构造函数 
	 * @param	array	$params
	 * @return	void
	 */
	public function __construct($params = array())
	{
		$defaults = array('js_library_driver' => 'jquery', 'autoload' => TRUE);

		foreach ($defaults as $key => $val)
		{
			if (isset($params[$key]) && $params[$key] !== '')
			{
				$defaults[$key] = $params[$key];
			}
		}

		extract($defaults);

		$this->CI =& get_instance();

		// load the requested js library 加载请求的js库
		$this->CI->load->library('Javascript/'.$js_library_driver, array('autoload' => $autoload));
		// make js to refer to current library js引用当前的库
		$this->js =& $this->CI->$js_library_driver;

		log_message('info', 'Javascript Class Initialized and loaded. Driver used: '.$js_library_driver);
	}

	// --------------------------------------------------------------------
	// Event Code 事件代码
	// --------------------------------------------------------------------

	/**
	 * Blur 模糊
	 *
	 * Outputs a javascript library blur event
	 * 输出一个javascript库模糊事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 他的代码执行
	 * @return	string
	 */
	public function blur($element = 'this', $js = '')
	{
		return $this->js->_blur($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Change
	 * 改变 更改
	 * Outputs a javascript library change event
	 * 输出一个javascript库更改事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	public function change($element = 'this', $js = '')
	{
		return $this->js->_change($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Click
	 * 单击
	 * Outputs a javascript library click event
	 * 输出一个javascript库单击事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @param	bool	whether or not to return false 是否要返回false
	 * @return	string
	 */
	public function click($element = 'this', $js = '', $ret_false = TRUE)
	{
		return $this->js->_click($element, $js, $ret_false);
	}

	// --------------------------------------------------------------------

	/**
	 * Double Click
	 * 双击
	 * Outputs a javascript library dblclick event
	 * 输出一个javascript库双击鼠标事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	public function dblclick($element = 'this', $js = '')
	{
		return $this->js->_dblclick($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Error
	 * 错误
	 * Outputs a javascript library error event
	 * 输出一个javascript库错误事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	public function error($element = 'this', $js = '')
	{
		return $this->js->_error($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Focus
	 * 焦点
	 * Outputs a javascript library focus event
	 * 输出一个javascript库的焦点事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	public function focus($element = 'this', $js = '')
	{
		return $this->js->_focus($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Hover
	 * 盘旋
	 * Outputs a javascript library hover event
	 * 输出一个javascript库悬停事件
	 * @param	string	- element  元素
	 * @param	string	- Javascript code for mouse over 鼠标越过的Javascript代码
	 * @param	string	- Javascript code for mouse out  鼠标移开的Javascript代码
	 * @return	string
	 */
	public function hover($element = 'this', $over = '', $out = '')
	{
		return $this->js->_hover($element, $over, $out);
	}

	// --------------------------------------------------------------------

	/**
	 * Keydown
	 * 键盘按下
	 * Outputs a javascript library keydown event
	 * 输出javascript库keydown事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	public function keydown($element = 'this', $js = '')
	{
		return $this->js->_keydown($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Keyup
	 * 键盘弹起
	 * Outputs a javascript library keyup event
	 * 输出javascript库keyup事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	public function keyup($element = 'this', $js = '')
	{
		return $this->js->_keyup($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Load
	 * 加载
	 * Outputs a javascript library load event
	 * 输出一个javascript库加载事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	public function load($element = 'this', $js = '')
	{
		return $this->js->_load($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Mousedown
	 * 鼠标按下
	 * Outputs a javascript library mousedown event
	 * 输出一个javascript库mousedown事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	public function mousedown($element = 'this', $js = '')
	{
		return $this->js->_mousedown($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Mouse Out
	 * 鼠标离开
	 * Outputs a javascript library mouseout event
	 * 输出一个javascript库mouseout事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	public function mouseout($element = 'this', $js = '')
	{
		return $this->js->_mouseout($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Mouse Over
	 * 鼠标越过
	 * Outputs a javascript library mouseover event
	 * 输出一个javascript库mouseover事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	public function mouseover($element = 'this', $js = '')
	{
		return $this->js->_mouseover($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Mouseup
	 * 鼠标弹起
	 * Outputs a javascript library mouseup event
	 * 输出一个javascript库mouseup事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	public function mouseup($element = 'this', $js = '')
	{
		return $this->js->_mouseup($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Output
	 * 输出
	 * Outputs the called javascript to the screen
	 * 在屏幕上输出被调用的javascript
	 * @param	string	The code to output 代码的输出
	 * @return	string
	 */
	public function output($js)
	{
		return $this->js->_output($js);
	}

	// --------------------------------------------------------------------

	/**
	 * Ready
	 * 准备
	 * Outputs a javascript library ready event
	 * 输出一个javascript库document ready事件
	 * @param	string	$js	Code to execute 要执行的代码
	 * @return	string
	 */
	public function ready($js)
	{
		return $this->js->_document_ready($js);
	}

	// --------------------------------------------------------------------

	/**
	 * Resize
	 * 调整大小
	 * Outputs a javascript library resize event
	 * 输出一个javascript库大小事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	public function resize($element = 'this', $js = '')
	{
		return $this->js->_resize($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Scroll
	 * 滚动
	 * Outputs a javascript library scroll event
	 * 输出一个javascript库滚动事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	public function scroll($element = 'this', $js = '')
	{
		return $this->js->_scroll($element, $js);
	}

	// --------------------------------------------------------------------

	/**
	 * Unload
	 * 卸载
	 * Outputs a javascript library unload event
	 * 输出一个javascript库卸载事件
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	string	The code to execute 要执行的代码
	 * @return	string
	 */
	public function unload($element = 'this', $js = '')
	{
		return $this->js->_unload($element, $js);
	}

	// --------------------------------------------------------------------
	// Effects
	// --------------------------------------------------------------------

	/**
	 * Add Class
	 * 添加class样式
	 * Outputs a javascript library addClass event
	 * 最好选择用addClass事件替代输出javascript库
	 * @param	string	- element 元素
	 * @param	string	- Class to add 添加样式
	 * @return	string
	 */
	public function addClass($element = 'this', $class = '')
	{
		return $this->js->_addClass($element, $class);
	}

	// --------------------------------------------------------------------

	/**
	 * Animate
	 * 动画
	 * Outputs a javascript library animate event
	 * 输出一个javascript库动画事件
	 * @param	string	$element = 'this'
	 * @param	array	$params = array()
	 * @param	mixed	$speed			'slow慢', 'normal正常', 'fast快', or time in milliseconds 时间以毫秒为单位
	 * @param	string	$extra
	 * @return	string
	 */
	public function animate($element = 'this', $params = array(), $speed = '', $extra = '')
	{
		return $this->js->_animate($element, $params, $speed, $extra);
	}

	// --------------------------------------------------------------------

	/**
	 * Fade In
	 * 渐显 淡入
	 * Outputs a javascript library hide event
	 * 输出一个javascript库隐藏事件
	 * @param	string	- element 元素
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds 时间以毫秒为单位
	 * @param	string	- Javascript callback function Javascript回调函数
	 * @return	string
	 */
	public function fadeIn($element = 'this', $speed = '', $callback = '')
	{
		return $this->js->_fadeIn($element, $speed, $callback);
	}

	// --------------------------------------------------------------------

	/**
	 * Fade Out
	 * 淡出
	 * Outputs a javascript library hide event
	 * 输出一个javascript库隐藏事件
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds 时间以毫秒为单位
	 * @param	string	- Javascript callback function 回调函数
	 * @return	string
	 */
	public function fadeOut($element = 'this', $speed = '', $callback = '')
	{
		return $this->js->_fadeOut($element, $speed, $callback);
	}
	// --------------------------------------------------------------------

	/**
	 * Slide Up
	 * 向上滑动
	 * Outputs a javascript library slideUp event
	 * 输出一个javascript库slideUp事件
	 * @param	string	- element 元素
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds 时间以毫秒为单位
	 * @param	string	- Javascript callback function 回调函数
	 * @return	string
	 */
	public function slideUp($element = 'this', $speed = '', $callback = '')
	{
		return $this->js->_slideUp($element, $speed, $callback);

	}

	// --------------------------------------------------------------------

	/**
	 * Remove Class
	 * 移除样式
	 * Outputs a javascript library removeClass event
	 * 输出一个javascript库removeClass事件
	 * @param	string	- element
	 * @param	string	- Class to add
	 * @return	string
	 */
	public function removeClass($element = 'this', $class = '')
	{
		return $this->js->_removeClass($element, $class);
	}

	// --------------------------------------------------------------------

	/**
	 * Slide Down
	 * 向下滑动
	 * Outputs a javascript library slideDown event
	 * 输出一个javascript库slideDown事件
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds 时间以毫秒为单位
	 * @param	string	- Javascript callback function 回调函数
	 * @return	string
	 */
	public function slideDown($element = 'this', $speed = '', $callback = '')
	{
		return $this->js->_slideDown($element, $speed, $callback);
	}

	// --------------------------------------------------------------------

	/**
	 * Slide Toggle
	 * 滑动开关
	 * Outputs a javascript library slideToggle event
	 * 输出一个javascript库slideToggle事件
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds 时间以毫秒为单位
	 * @param	string	- Javascript callback function 回调函数
	 * @return	string
	 */
	public function slideToggle($element = 'this', $speed = '', $callback = '')
	{
		return $this->js->_slideToggle($element, $speed, $callback);

	}

	// --------------------------------------------------------------------

	/**
	 * Hide
	 * 隐藏
	 * Outputs a javascript library hide action
	 * 输出一个javascript库隐藏行动
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds 时间以毫秒为单位
	 * @param	string	- Javascript callback function 回调函数
	 * @return	string
	 */
	public function hide($element = 'this', $speed = '', $callback = '')
	{
		return $this->js->_hide($element, $speed, $callback);
	}

	// --------------------------------------------------------------------

	/**
	 * Toggle
	 * 开关
	 * Outputs a javascript library toggle event
	 * 输出一个javascript库切换事件
	 * @param	string	- element 元素
	 * @return	string
	 */
	public function toggle($element = 'this')
	{
		return $this->js->_toggle($element);

	}

	// --------------------------------------------------------------------

	/**
	 * Toggle Class
	 * 开关样式
	 * Outputs a javascript library toggle class event
	 * 输出一个javascript库切换类事件
	 * @param	string	$element = 'this'
	 * @param	string	$class = ''
	 * @return	string
	 */
	public function toggleClass($element = 'this', $class = '')
	{
		return $this->js->_toggleClass($element, $class);
	}

	// --------------------------------------------------------------------

	/**
	 * Show
	 * 显示
	 * Outputs a javascript library show event
	 * 输出一个javascript库显示事件
	 * @param	string	- element 元素
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds 时间以毫秒为单位
	 * @param	string	- Javascript callback function 回调函数
	 * @return	string
	 */
	public function show($element = 'this', $speed = '', $callback = '')
	{
		return $this->js->_show($element, $speed, $callback);
	}

	// --------------------------------------------------------------------

	/**
	 * Compile
	 * 编译
	 * gather together all script needing to be output
	 * 聚集所有脚本需要输出
	 * @param	string	$view_var
	 * @param	bool	$script_tags
	 * @return	string
	 */
	public function compile($view_var = 'script_foot', $script_tags = TRUE)
	{
		$this->js->_compile($view_var, $script_tags);
	}

	// --------------------------------------------------------------------

	/**
	 * Clear Compile
	 * 清除编译
	 * Clears any previous javascript collected for output
	 * 清除以前的任何javascript收集输出
	 * @return	void
	 */
	public function clear_compile()
	{
		$this->js->_clear_compile();
	}

	// --------------------------------------------------------------------

	/**
	 * External
	 * 外部
	 * Outputs a <script> tag with the source as an external js file
	 * 输出一个<脚本>标记与源作为外部js文件
	 * @param	string	$external_file
	 * @param	bool	$relative
	 * @return	string
	 */
	public function external($external_file = '', $relative = FALSE)
	{
		if ($external_file !== '')
		{
			$this->_javascript_location = $external_file;
		}
		elseif ($this->CI->config->item('javascript_location') !== '')
		{
			$this->_javascript_location = $this->CI->config->item('javascript_location');
		}

		if ($relative === TRUE OR strpos($external_file, 'http://') === 0 OR strpos($external_file, 'https://') === 0)
		{
			$str = $this->_open_script($external_file);
		}
		elseif (strpos($this->_javascript_location, 'http://') !== FALSE)
		{
			$str = $this->_open_script($this->_javascript_location.$external_file);
		}
		else
		{
			$str = $this->_open_script($this->CI->config->slash_item('base_url').$this->_javascript_location.$external_file);
		}

		return $str.$this->_close_script();
	}

	// --------------------------------------------------------------------

	/**
	 * Inline
	 * 内联
	 * Outputs a <script> tag
	 * 输出<脚本>标记
	 * @param	string	The element to attach the event to 元素附加事件
	 * @param	bool	If a CDATA section should be added 如果一个CDATA区域应补充道
	 * @return	string
	 */
	public function inline($script, $cdata = TRUE)
	{
		return $this->_open_script()
			. ($cdata ? "\n// <![CDATA[\n".$script."\n// ]]>\n" : "\n".$script."\n")
			. $this->_close_script();
	}

	// --------------------------------------------------------------------

	/**
	 * Open Script
	 * 开启脚本
	 * Outputs an opening <script>
	 * 输出一个开启<脚本>
	 * @param	string
	 * @return	string
	 */
	protected function _open_script($src = '')
	{
		return '<script type="text/javascript" charset="'.strtolower($this->CI->config->item('charset')).'"'
			.($src === '' ? '>' : ' src="'.$src.'">');
	}

	// --------------------------------------------------------------------

	/**
	 * Close Script
	 * 结束脚本
	 * Outputs an closing </script>
	 * 输出一个关闭脚本< /script >
	 * @param	string
	 * @return	string
	 */
	protected function _close_script($extra = "\n")
	{
		return '</script>'.$extra;
	}

	// --------------------------------------------------------------------
	// AJAX-Y STUFF - still a testbed
	// --------------------------------------------------------------------

	/**
	 * Update
	 * 更新
	 * Outputs a javascript library update event
	 * 输出一个javascript库updater事件
	 * @param	string	- element
	 * @param	string	- One of 'slow', 'normal', 'fast', or time in milliseconds 时间以毫秒计
	 * @param	string	- Javascript callback function 回调函数
	 * @return	string
	 */
	public function update($element = 'this', $speed = '', $callback = '')
	{
		return $this->js->_updater($element, $speed, $callback);
	}

	// --------------------------------------------------------------------

	/**
	 * Generate JSON
	 * 生成JSON
	 * Can be passed a database result or associative array and returns a JSON formatted string
	 * 可以通过一个数据库的结果或关联数组并返回一个JSON格式化字符串
	 * @param	mixed	result set or array 结果集或数组
	 * @param	bool	match array types (defaults to objects) 匹配数组类型(默认为对象)
	 * @return	string	a json formatted string 一个json格式化字符串
	 */
	public function generate_json($result = NULL, $match_array_type = FALSE)
	{
		// JSON data can optionally be passed to this function JSON数据可以被传递给这个函数
		// either as a database result object or an array, or a user supplied array 结果一个数据库对象或数组,或用户提供的数组
		if ($result !== NULL)
		{
			if (is_object($result))
			{
				$json_result = is_callable(array($result, 'result_array')) ? $result->result_array() : (array) $result;
			}
			elseif (is_array($result))
			{
				$json_result = $result;
			}
			else
			{
				return $this->_prep_args($result);
			}
		}
		else
		{
			return 'null';
		}

		$json = array();
		$_is_assoc = TRUE;

		if ( ! is_array($json_result) && empty($json_result))
		{
			show_error('Generate JSON Failed - Illegal key, value pair.');
		}
		elseif ($match_array_type)
		{
			$_is_assoc = $this->_is_associative_array($json_result);
		}

		foreach ($json_result as $k => $v)
		{
			if ($_is_assoc)
			{
				$json[] = $this->_prep_args($k, TRUE).':'.$this->generate_json($v, $match_array_type);
			}
			else
			{
				$json[] = $this->generate_json($v, $match_array_type);
			}
		}

		$json = implode(',', $json);

		return $_is_assoc ? '{'.$json.'}' : '['.$json.']';

	}

	// --------------------------------------------------------------------

	/**
	 * Is associative array
	 * 是关联数组
	 * Checks for an associative array
	 * 检查一个关联数组中
	 * @param	array
	 * @return	bool
	 */
	protected function _is_associative_array($arr)
	{
		foreach (array_keys($arr) as $key => $val)
		{
			if ($key !== $val)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Prep Args
	 * 准备参数
	 * Ensures a standard json value and escapes values
	 * 确保标准json值和逃值
	 * @param	mixed	$result
	 * @param	bool	$is_key = FALSE
	 * @return	string
	 */
	protected function _prep_args($result, $is_key = FALSE)
	{
		if ($result === NULL)
		{
			return 'null';
		}
		elseif (is_bool($result))
		{
			return ($result === TRUE) ? 'true' : 'false';
		}
		elseif (is_string($result) OR $is_key)
		{
			return '"'.str_replace(array('\\', "\t", "\n", "\r", '"', '/'), array('\\\\', '\\t', '\\n', "\\r", '\"', '\/'), $result).'"';
		}
		elseif (is_scalar($result))
		{
			return $result;
		}
	}

}
