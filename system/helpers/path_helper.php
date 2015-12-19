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
 * CodeIgniter Path Helpers
 * CodeIgniter路径助手
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/path_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('set_realpath'))
{
	/**
	 * Set Realpath
	 * 设置Realpath
	 * @param	string
	 * @param	bool	checks to see if the path exists 检查路径是否存在
	 * @return	string
	 */
	function set_realpath($path, $check_existance = FALSE)
	{
		// Security check to make sure the path is NOT a URL. No remote file inclusion! 安全检查,以确保不是一个URL的路径。没有远程文件包含!
		if (preg_match('#^(http:\/\/|https:\/\/|www\.|ftp)#i', $path) OR filter_var($path, FILTER_VALIDATE_IP) === $path )
		{
			show_error('The path you submitted must be a local server path, not a URL你提交的路径必须是本地服务器的路径,而不是一个URL');
		}

		// Resolve the path 解决 分解路径
		if (realpath($path) !== FALSE)
		{
			$path = realpath($path);
		}
		elseif ($check_existance && ! is_dir($path) && ! is_file($path))
		{
			show_error('Not a valid path: '.$path);
		}

		// Add a trailing slash, if this is a directory 末尾添加一个斜杠,如果这是一个目录
		return is_dir($path) ? rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR : $path;
	}
}
