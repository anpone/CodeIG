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
 * CodeIgniter String Helpers
 * CodeIgniter字符串助手
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/string_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('trim_slashes'))
{
	/**
	 * Trim Slashes
	 * 修剪斜杠
	 * Removes any leading/trailing slashes from a string:
	 * 删除任何领导/斜杠从一个字符串
	 * /this/that/theother/ 
	 *
	 * becomes:
	 *
	 * this/that/theother
	 *
	 * @todo	Remove in version 3.1+. 删除在版本3.1 +。
	 * @deprecated	3.0.0	This is just an alias for PHP's native trim()  这只是一个别名为PHP的本机修剪trim()
	 *
	 * @param	string
	 * @return	string
	 */
	function trim_slashes($str)
	{
		return trim($str, '/');
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('strip_slashes'))
{
	/**
	 * Strip Slashes
	 * 带斜杠
	 * Removes slashes contained in a string or in an array
	 * 删除斜杠包含在一个字符串或一个数组
	 * @param	mixed	string or array  字符串或数组
	 * @return	mixed	string or array
	 */
	function strip_slashes($str)
	{
		if ( ! is_array($str))
		{
			return stripslashes($str);
		}

		foreach ($str as $key => $val)
		{
			$str[$key] = strip_slashes($val);
		}

		return $str;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('strip_quotes'))
{
	/**
	 * Strip Quotes
	 * 带引号
	 * Removes single and double quotes from a string
	 * 删除单和双引号的字符串
	 * @param	string
	 * @return	string
	 */
	function strip_quotes($str)
	{
		return str_replace(array('"', "'"), '', $str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('quotes_to_entities'))
{
	/**
	 * Quotes to Entities
	 * 实体的引用
	 * Converts single and double quotes to entities
	 * 将单和双引号转换为实体
	 * @param	string
	 * @return	string
	 */
	function quotes_to_entities($str)
	{
		return str_replace(array("\'","\"","'",'"'), array("&#39;","&quot;","&#39;","&quot;"), $str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('reduce_double_slashes'))
{
	/**
	 * Reduce Double Slashes
	 * 减少双斜杠
	 * Converts double slashes in a string to a single slash, 双斜杠字符串转换为一个斜线,除中发现
	 * except those found in http://
	 *
	 * http://www.some-site.com//index.php
	 *
	 * becomes: 成为
	 * 
	 * http://www.some-site.com/index.php
	 *
	 * @param	string
	 * @return	string
	 */
	function reduce_double_slashes($str)
	{
		return preg_replace('#(^|[^:])//+#', '\\1/', $str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('reduce_multiples'))
{
	/**
	 * Reduce Multiples
	 * 减少的倍数
	 * Reduces multiple instances of a particular character.  Example:
	 * 得出的多个实例一个特殊字符。例子:
	 * Fred, Bill,, Joe, Jimmy
	 *
	 * becomes:变成
	 *
	 * Fred, Bill, Joe, Jimmy
	 *
	 * @param	string
	 * @param	string	the character you wish to reduce  你希望减少的特点
	 * @param	bool	TRUE/FALSE - whether to trim the character from the beginning/end 是否开始/结束的字符
	 * @return	string
	 */
	function reduce_multiples($str, $character = ',', $trim = FALSE)
	{
		$str = preg_replace('#'.preg_quote($character, '#').'{2,}#', $character, $str);
		return ($trim === TRUE) ? trim($str, $character) : $str;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('random_string'))
{
	/**
	 * Create a Random String
	 * 创建一个随机的字符串
	 * Useful for generating passwords or hashes.
	 * 用于生成密码或散列。
	 * @param	string	type of random string.  basic, alpha, alnum, numeric, nozero, unique, md5, encrypt and sha1 类型的随机字符串。alnum基本α,数字、nozero独特,md5加密和sha1
	 * @param	int	number of characters 字符数
	 * @return	string
	 */
	function random_string($type = 'alnum', $len = 8)
	{
		switch ($type)
		{
			case 'basic':
				return mt_rand();
			case 'alnum':
			case 'numeric':
			case 'nozero':
			case 'alpha':
				switch ($type)
				{
					case 'alpha':
						$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'alnum':
						$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
						break;
					case 'numeric':
						$pool = '0123456789';
						break;
					case 'nozero':
						$pool = '123456789';
						break;
				}
				return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
			case 'unique': // todo: remove in 3.1+
			case 'md5':
				return md5(uniqid(mt_rand()));
			case 'encrypt': // todo: remove in 3.1+
			case 'sha1':
				return sha1(uniqid(mt_rand(), TRUE));
		}
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('increment_string'))
{
	/**
	 * Add's _1 to a string or increment the ending number to allow _2, _3, etc
	 * 添加的_1字符串或增加结束号码允许_2、等等
	 * @param	string	required 要求的 
	 * @param	string	What should the duplicate number be appended with 重复的数量应该附加什么
	 * @param	string	Which number should be used for the first dupe increment 第一欺骗增量应该使用哪个号码
	 * @return	string
	 */
	function increment_string($str, $separator = '_', $first = 1)
	{
		preg_match('/(.+)'.preg_quote($separator, '/').'([0-9]+)$/', $str, $match);
		return isset($match[2]) ? $match[1].$separator.($match[2] + 1) : $str.$separator.$first;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('alternator'))
{
	/**
	 * Alternator
	 * 交流发电机 
	 * Allows strings to be alternated. See docs...
	 * 允许将字符串交替。看到文档…
	 * @param	string (as many parameters as needed) (所需的任意数量的参数)
	 * @return	string
	 */
	function alternator($args)
	{
		static $i;

		if (func_num_args() === 0)
		{
			$i = 0;
			return '';
		}
		$args = func_get_args();
		return $args[($i++ % count($args))];
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('repeater'))
{
	/**
	 * Repeater function
	 * 中继器的功能
	 * @todo	Remove in version 3.1+. 删除在版本3.1 +。
	 * @deprecated	3.0.0	This is just an alias for PHP's native str_repeat() 这只是一个别名为PHP的本机函数str_repeat()
	 *
	 * @param	string	$data	String to repeat 字符串重复
	 * @param	int	$num	Number of repeats  重复的数量
	 * @return	string
	 */
	function repeater($data, $num = 1)
	{
		return ($num > 0) ? str_repeat($data, $num) : '';
	}
}
