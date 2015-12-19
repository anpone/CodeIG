<?php
/**
 * CodeIgniter Benchmark标准检查测试程序
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
 * Benchmark Class 基准测试类
 *
 * This class enables you to mark points and calculate the time difference
 * between them. Memory consumption can also be displayed.
 * 这个类允许您标记点和计算时差在他们之间。内存消耗也可以显示出来。
 * 
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/benchmark.html
 */
class CI_Benchmark {

	/**
	 * List of all benchmark markers 所有基准标记列表
	 *
	 * @var	array
	 */
	public $marker = array();

	/**
	 * Set a benchmark marker 设定一个基准标记
	 *
	 * Multiple calls to this function can be made so that several
	 * execution points can be timed.
	 * 多个调用这个函数可以这样几个可以定时执行点。
	 * 
	 * @param	string	$name	Marker name
	 * @return	void
	 */
	public function mark($name)
	{
		$this->marker[$name] = microtime(TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Elapsed time 经过的时间
	 *
	 * Calculates the time difference between two marked points. 计算两个标记点的时差。
	 *
	 * If the first parameter is empty this function instead returns the
	 * {elapsed_time} pseudo-variable. This permits the full system
	 * execution time to be shown in a template. The output class will
	 * swap the real value for this variable.
	 * 如果这个函数的第一个参数是空的而不是返回{ elapsed_time }伪变量。这允许完整的系统执行时间模板所示。输出类将交换这个变量的真正价值。
	 * 
	 * @param	string	$point1		A particular marked point 一个特定的标记点
	 * @param	string	$point2		A particular marked point 一个特定的标记点
	 * @param	int	$decimals	Number of decimal places 设置小数位数
	 *
	 * @return	string	Calculated elapsed time on success, 计算运行时间成功,
	 *			an '{elapsed_string}' if $point1 is empty
	 *			or an empty string if $point1 is not found. 如果没有找到$point1 一个空字符串
	 */
	public function elapsed_time($point1 = '', $point2 = '', $decimals = 4)
	{
		if ($point1 === '')
		{
			return '{elapsed_time}';
		}

		if ( ! isset($this->marker[$point1]))
		{
			return '';
		}

		if ( ! isset($this->marker[$point2]))
		{
			$this->marker[$point2] = microtime(TRUE);
		}

		return number_format($this->marker[$point2] - $this->marker[$point1], $decimals);
	}

	// --------------------------------------------------------------------

	/**
	 * Memory Usage 内存使用情况 
	 *
	 * Simply returns the {memory_usage} marker. 简单地返回{ memory_usage }标记。
	 *
	 * This permits it to be put it anywhere in a template
	 * without the memory being calculated until the end.
	 * The output class will swap the real value for this variable.
	 * 这允许它把它在一个模板没有内存被计算到最后。输出类将交换这个变量的真正价值。
	 *
	 * @return	string	'{memory_usage}'
	 */
	public function memory_usage()
	{
		return '{memory_usage}';
	}

}
