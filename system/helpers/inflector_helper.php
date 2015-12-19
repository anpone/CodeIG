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
 * CodeIgniter Inflector Helpers
 * CodeIgniter弯曲物助手
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/inflector_helper.html
 */

// --------------------------------------------------------------------

if ( ! function_exists('singular'))
{
	/**
	 * Singular
	 * 单一的
	 * Takes a plural word and makes it singular
	 * 需要一个复数词和奇异
	 * @param	string	$str	Input string 输入字符串
	 * @return	string
	 */
	function singular($str)
	{
		$result = strval($str);

		if ( ! is_countable($result))
		{
			return $result;
		}

		$singular_rules = array(
			'/(matr)ices$/'		=> '\1ix',
			'/(vert|ind)ices$/'	=> '\1ex',
			'/^(ox)en/'		=> '\1',
			'/(alias)es$/'		=> '\1',
			'/([octop|vir])i$/'	=> '\1us',
			'/(cris|ax|test)es$/'	=> '\1is',
			'/(shoe)s$/'		=> '\1',
			'/(o)es$/'		=> '\1',
			'/(bus|campus)es$/'	=> '\1',
			'/([m|l])ice$/'		=> '\1ouse',
			'/(x|ch|ss|sh)es$/'	=> '\1',
			'/(m)ovies$/'		=> '\1\2ovie',
			'/(s)eries$/'		=> '\1\2eries',
			'/([^aeiouy]|qu)ies$/'	=> '\1y',
			'/([lr])ves$/'		=> '\1f',
			'/(tive)s$/'		=> '\1',
			'/(hive)s$/'		=> '\1',
			'/([^f])ves$/'		=> '\1fe',
			'/(^analy)ses$/'	=> '\1sis',
			'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/' => '\1\2sis',
			'/([ti])a$/'		=> '\1um',
			'/(p)eople$/'		=> '\1\2erson',
			'/(m)en$/'		=> '\1an',
			'/(s)tatuses$/'		=> '\1\2tatus',
			'/(c)hildren$/'		=> '\1\2hild',
			'/(n)ews$/'		=> '\1\2ews',
			'/([^us])s$/'		=> '\1'
		);

		foreach ($singular_rules as $rule => $replacement)
		{
			if (preg_match($rule, $result))
			{
				$result = preg_replace($rule, $replacement, $result);
				break;
			}
		}

		return $result;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('plural'))
{
	/**
	 * Plural
	 * 复数
	 * Takes a singular word and makes it plural
	 * 需要一个单一的词和复数
	 * @param	string	$str	Input string 输入字符串
	 * @return	string
	 */
	function plural($str)
	{
		$result = strval($str);

		if ( ! is_countable($result))
		{
			return $result;
		}
        //下面的是英语单词单数 复数转换的规则表
		$plural_rules = array(
			'/(quiz)$/'                => '\1zes',      // quizzes 测验的复数
			'/^(ox)$/'                 => '\1\2en',     // ox
			'/([m|l])ouse$/'           => '\1ice',      // mouse, louse
			'/(matr|vert|ind)ix|ex$/'  => '\1ices',     // matrix, vertex, index
			'/(x|ch|ss|sh)$/'          => '\1es',       // search, switch, fix, box, process, address
			'/([^aeiouy]|qu)y$/'       => '\1ies',      // query, ability, agency
			'/(hive)$/'                => '\1s',        // archive, hive
			'/(?:([^f])fe|([lr])f)$/'  => '\1\2ves',    // half, safe, wife
			'/sis$/'                   => 'ses',        // basis, diagnosis
			'/([ti])um$/'              => '\1a',        // datum, medium
			'/(p)erson$/'              => '\1eople',    // person, salesperson
			'/(m)an$/'                 => '\1en',       // man, woman, spokesman
			'/(c)hild$/'               => '\1hildren',  // child
			'/(buffal|tomat)o$/'       => '\1\2oes',    // buffalo, tomato
			'/(bu|campu)s$/'           => '\1\2ses',    // bus, campus
			'/(alias|status|virus)$/'  => '\1es',       // alias
			'/(octop)us$/'             => '\1i',        // octopus
			'/(ax|cris|test)is$/'      => '\1es',       // axis, crisis
			'/s$/'                     => 's',          // no change (compatibility) 没有变化(兼容性)
			'/$/'                      => 's',
		);

		foreach ($plural_rules as $rule => $replacement)
		{
			if (preg_match($rule, $result))
			{
				$result = preg_replace($rule, $replacement, $result);
				break;
			}
		}

		return $result;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('camelize'))
{
	/**
	 * Camelize
	 * 目标字符串驼峰化 (java用词)
	 * Takes multiple words separated by spaces or underscores and camelizes them
	 * 需要多个词用空格或下划线分隔和驼峰化他们
	 * @param	string	$str	Input string 输入字符串
	 * @return	string
	 */
	function camelize($str)
	{
		return strtolower($str[0]).substr(str_replace(' ', '', ucwords(preg_replace('/[\s_]+/', ' ', $str))), 1);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('underscore'))
{
	/**
	 * Underscore
	 * 下划线
	 * Takes multiple words separated by spaces and underscores them
	 * 需要多个单词隔开空间和下划线
	 * @param	string	$str	Input string 输入字符串
	 * @return	string
	 */
	function underscore($str)
	{
		return preg_replace('/[\s]+/', '_', trim(MB_ENABLED ? mb_strtolower($str) : strtolower($str)));
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('humanize'))
{
	/**
	 * Humanize
	 * 人性化
	 * Takes multiple words separated by the separator and changes them to spaces
	 * 需要多个单词隔开分离器和改变空间
	 * @param	string	$str		Input string  输入串
	 * @param 	string	$separator	Input separator 输入分隔符
	 * @return	string
	 */
	function humanize($str, $separator = '_')
	{
		return ucwords(preg_replace('/['.$separator.']+/', ' ', trim(MB_ENABLED ? mb_strtolower($str) : strtolower($str))));
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('is_countable'))
{
	/**
	 * Checks if the given word has a plural version.
	 * 检查给定单词的复数版本。
	 * @param	string	$word	Word to check 检查的单词
	 * @return	bool
	 */
	function is_countable($word)
	{
		return ! in_array(
			strtolower($word),
			array(
				'equipment', 'information', 'rice', 'money',
				'species', 'series', 'fish', 'meta'
			)
		);
	}
}
