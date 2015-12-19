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
 * CodeIgniter Array Helpers
 * CodeIgniter数组助手
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/array_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('element'))
{
	/**
	 * Element
	 * 元素
	 * Lets you determine whether an array index is set and whether it has a value. 让你确定数组索引设置以及是否有价值。
	 * If the element is empty it returns NULL (or whatever you specify as the default value.)
	 * 如果元素为空则返回零(或任何你指定默认值。)
	 * @param	string
	 * @param	array
	 * @param	mixed
	 * @return	mixed	depends on what the array contains 取决于该数组包含什么
	 */
	function element($item, array $array, $default = NULL)
	{
		return array_key_exists($item, $array) ? $array[$item] : $default;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('random_element'))
{
	/**
	 * Random Element - Takes an array as input and returns a random element
	 * 随机元素——以一个数组作为输入,并返回一个随机元素
	 * @param	array
	 * @return	mixed	depends on what the array contains 取决于该数组包含什么
	 */
	function random_element($array)
	{
		return is_array($array) ? $array[array_rand($array)] : $array;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('elements'))
{
	/**
	 * Elements
	 * 原理
	 * Returns only the array items specified. Will return a default value if
	 * it is not set.
	 * 只返回指定的数组项。将返回一个默认值,如果没有设置。
	 * @param	array
	 * @param	array
	 * @param	mixed
	 * @return	mixed	depends on what the array contains 取决于该数组包含什么
	 */
	function elements($items, array $array, $default = NULL)
	{
		$return = array();

		is_array($items) OR $items = array($items);

		foreach ($items as $item)
		{
			$return[$item] = array_key_exists($item, $array) ? $array[$item] : $default;
		}

		return $return;
	}
}
