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
 * CodeIgniter Cookie Helpers
 * CodeIgniter cookie助手
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/cookie_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('set_cookie'))
{
	/**
	 * Set cookie
	 * 设置cookie
	 * Accepts seven parameters, or you can submit an associative
	 * array in the first parameter containing all the values.
	 * 接受七个参数,或者你可以提交一个关联数组中第一个参数包含所有值。
	 * @param	mixed 混合的name
	 * @param	string	the value of the cookie cookie的值
	 * @param	string	the number of seconds until expiration  直到过期的秒数
	 * @param	string	the cookie domain.  Usually:  .yourdomain.com  cookie域。通常是 .domain.com
	 * @param	string	the cookie path cookie的路径
	 * @param	string	the cookie prefix  cookie的前缀
	 * @param	bool	true makes the cookie secure  真正使cookie安全
	 * @param	bool	true makes the cookie accessible via http(s) only (no javascript) 真正使cookie只通过http(s)(没有javascript)
	 * @return	void  返回 无信息
	 */
	function set_cookie($name, $value = '', $expire = '', $domain = '', $path = '/', $prefix = '', $secure = FALSE, $httponly = FALSE)
	{
		// Set the config file options 设置配置文件选项
		get_instance()->input->set_cookie($name, $value, $expire, $domain, $path, $prefix, $secure, $httponly);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('get_cookie'))
{
	/**
	 * Fetch an item from the COOKIE array
	 * 获取一个项目从COOKIE数组
	 * @param	string 字符串
	 * @param	bool   布尔值
	 * @return	mixed  混合的
	 */
	function get_cookie($index, $xss_clean = NULL)
	{
		is_bool($xss_clean) OR $xss_clean = (config_item('global_xss_filtering') === TRUE);
		$prefix = isset($_COOKIE[$index]) ? '' : config_item('cookie_prefix');
		return get_instance()->input->cookie($prefix.$index, $xss_clean);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('delete_cookie'))
{
	/**
	 * Delete a COOKIE
	 * 删除COOKIE
	 * @param	mixed  混合的
	 * @param	string	the cookie domain. Usually: .yourdomain.com  cookie域。通常:.yourdomain.com
	 * @param	string	the cookie path  cookie的路径
	 * @param	string	the cookie prefix  cookie的前缀
	 * @return	void  无信息
	 */
	function delete_cookie($name, $domain = '', $path = '/', $prefix = '')
	{
		set_cookie($name, '', '', $domain, $path, $prefix);
	}
}
