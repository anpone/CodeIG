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
 * CodeIgniter URL Helpers
 * CodeIgniter的URL助手
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/url_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('site_url'))
{
	/**
	 * Site URL
	 * 网站网址
	 * Create a local URL based on your basepath. Segments can be passed via the
	 * first parameter either as a string or an array.
	 * 基于你的basepath创建一个本地URL。段可以通过第一个参数为字符串或一个数组。
	 * @param	string	$uri
	 * @param	string	$protocol
	 * @return	string
	 */
	function site_url($uri = '', $protocol = NULL)
	{
		return get_instance()->config->site_url($uri, $protocol);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('base_url'))
{
	/**
	 * Base URL
	 * 基准网址
	 * Create a local URL based on your basepath. 基于你的basepath创建一个本地URL。
	 * Segments can be passed in as a string or an array, same as site_url
	 * or a URL to a file can be passed in, e.g. to an image file.
	 * 段可以作为一个字符串或一个数组,传递一样site_url或者文件可以通过一个URL,比如一个图像文件。
	 * @param	string	$uri
	 * @param	string	$protocol
	 * @return	string
	 */
	function base_url($uri = '', $protocol = NULL)
	{
		return get_instance()->config->base_url($uri, $protocol);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('current_url'))
{
	/**
	 * Current URL
	 * 目前地址 正在浏览的网页 
	 * Returns the full URL (including segments) of the page where this
	 * function is placed
	 * 返回页面的完整的URL(包括部分)这个函数被放置的地方
	 * @return	string
	 */
	function current_url()
	{
		$CI =& get_instance();
		return $CI->config->site_url($CI->uri->uri_string());
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('uri_string'))
{
	/**
	 * URL String
	 * URL字符串
	 * Returns the URI segments.
	 * 返回URI段。
	 * @return	string
	 */
	function uri_string()
	{
		return get_instance()->uri->uri_string();
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('index_page'))
{
	/**
	 * Index page
	 * 索引页
	 * Returns the "index_page" from your config file
	 * 返回“index_page”从您的配置文件
	 * @return	string
	 */
	function index_page()
	{
		return get_instance()->config->item('index_page');
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('anchor'))
{
	/**
	 * Anchor Link
	 * 锚链环
	 * Creates an anchor based on the local URL.
	 * 创建一个锚基于当地的URL。
	 * @param	string	the URL  
	 * @param	string	the link title  链接标题
	 * @param	mixed	any attributes  任何属性
	 * @return	string
	 */
	function anchor($uri = '', $title = '', $attributes = '')
	{
		$title = (string) $title;

		$site_url = is_array($uri)
			? site_url($uri)
			: (preg_match('#^(\w+:)?//#i', $uri) ? $uri : site_url($uri));

		if ($title === '')
		{
			$title = $site_url;
		}

		if ($attributes !== '')
		{
			$attributes = _stringify_attributes($attributes);
		}

		return '<a href="'.$site_url.'"'.$attributes.'>'.$title.'</a>';
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('anchor_popup'))
{
	/**
	 * Anchor Link - Pop-up version
	 * 锚点链接,弹出的版本
	 * Creates an anchor based on the local URL. The link
	 * opens a new window based on the attributes specified.
	 * 创建一个锚基于当地的URL。链接打开一个新窗口根据指定的属性。
	 * @param	string	the URL
	 * @param	string	the link title  链接标题
	 * @param	mixed	any attributes  任何属性
	 * @return	string
	 */
	function anchor_popup($uri = '', $title = '', $attributes = FALSE)
	{
		$title = (string) $title;
		$site_url = preg_match('#^(\w+:)?//#i', $uri) ? $uri : site_url($uri);

		if ($title === '')
		{
			$title = $site_url;
		}

		if ($attributes === FALSE)
		{
			return '<a href="'.$site_url.'" onclick="window.open(\''.$site_url."', '_blank'); return false;\">".$title.'</a>';
		}

		if ( ! is_array($attributes))
		{
			$attributes = array($attributes);

			// Ref: http://www.w3schools.com/jsref/met_win_open.asp
			$window_name = '_blank';
		}
		elseif ( ! empty($attributes['window_name']))
		{
			$window_name = $attributes['window_name'];
			unset($attributes['window_name']);
		}
		else
		{
			$window_name = '_blank';
		}

		foreach (array('width' => '800', 'height' => '600', 'scrollbars' => 'yes', 'menubar' => 'no', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0') as $key => $val)
		{
			$atts[$key] = isset($attributes[$key]) ? $attributes[$key] : $val;
			unset($attributes[$key]);
		}

		$attributes = _stringify_attributes($attributes);

		return '<a href="'.$site_url
			.'" onclick="window.open(\''.$site_url."', '".$window_name."', '"._stringify_attributes($atts, TRUE)."'); return false;\""
			.$attributes.'>'.$title.'</a>';
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('mailto'))
{
	/**
	 * Mailto Link
	 * Mailto链接
	 * @param	string	the email address 电子邮件地址
	 * @param	string	the link title  链接标题
	 * @param	mixed	any attributes  任何属性
	 * @return	string
	 */
	function mailto($email, $title = '', $attributes = '')
	{
		$title = (string) $title;

		if ($title === '')
		{
			$title = $email;
		}

		return '<a href="mailto:'.$email.'"'._stringify_attributes($attributes).'>'.$title.'</a>';
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('safe_mailto'))
{
	/**
	 * Encoded Mailto Link
	 * 编码的Mailto链接
	 * Create a spam-protected mailto link written in Javascript
	 * 创建一个spam-protected mailto链接用Javascript编写的
	 * @param	string	the email address  电子邮件地址
	 * @param	string	the link title     链接标题
	 * @param	mixed	any attributes     任何属性
	 * @return	string
	 */
	function safe_mailto($email, $title = '', $attributes = '')
	{
		$title = (string) $title;

		if ($title === '')
		{
			$title = $email;
		}

		$x = str_split('<a href="mailto:', 1);

		for ($i = 0, $l = strlen($email); $i < $l; $i++)
		{
			$x[] = '|'.ord($email[$i]);
		}

		$x[] = '"';

		if ($attributes !== '')
		{
			if (is_array($attributes))
			{
				foreach ($attributes as $key => $val)
				{
					$x[] = ' '.$key.'="';
					for ($i = 0, $l = strlen($val); $i < $l; $i++)
					{
						$x[] = '|'.ord($val[$i]);
					}
					$x[] = '"';
				}
			}
			else
			{
				for ($i = 0, $l = strlen($attributes); $i < $l; $i++)
				{
					$x[] = $attributes[$i];
				}
			}
		}

		$x[] = '>';

		$temp = array();
		for ($i = 0, $l = strlen($title); $i < $l; $i++)
		{
			$ordinal = ord($title[$i]);

			if ($ordinal < 128)
			{
				$x[] = '|'.$ordinal;
			}
			else
			{
				if (count($temp) === 0)
				{
					$count = ($ordinal < 224) ? 2 : 3;
				}

				$temp[] = $ordinal;
				if (count($temp) === $count)
				{
					$number = ($count === 3)
							? (($temp[0] % 16) * 4096) + (($temp[1] % 64) * 64) + ($temp[2] % 64)
							: (($temp[0] % 32) * 64) + ($temp[1] % 64);
					$x[] = '|'.$number;
					$count = 1;
					$temp = array();
				}
			}
		}

		$x[] = '<'; $x[] = '/'; $x[] = 'a'; $x[] = '>';

		$x = array_reverse($x);

		$output = "<script type=\"text/javascript\">\n"
			."\t//<![CDATA[\n"
			."\tvar l=new Array();\n";

		for ($i = 0, $c = count($x); $i < $c; $i++)
		{
			$output .= "\tl[".$i."] = '".$x[$i]."';\n";
		}

		$output .= "\n\tfor (var i = l.length-1; i >= 0; i=i-1) {\n"
			."\t\tif (l[i].substring(0, 1) === '|') document.write(\"&#\"+unescape(l[i].substring(1))+\";\");\n"
			."\t\telse document.write(unescape(l[i]));\n"
			."\t}\n"
			."\t//]]>\n"
			.'</script>';

		return $output;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('auto_link'))
{
	/**
	 * Auto-linker
	 * 自动链接器
	 * Automatically links URL and Email addresses.   自动链接网址和电子邮件地址。
	 * Note: There's a bit of extra code here to deal with
	 * URLs or emails that end in a period. We'll strip these
	 * off and add them after the link.
	 * 注意:这里有一些额外的代码来处理网址或电子邮件在一段时间内结束。我们将带这些后,将它们添加链接。
	 * @param	string	the string
	 * @param	string	the type: email, url, or both  类型:电子邮件、网址,或两者兼而有之
	 * @param	bool	whether to create pop-up links  是否要创建弹出链接
	 * @return	string
	 */
	function auto_link($str, $type = 'both', $popup = FALSE)
	{
		// Find and replace any URLs.  查找和替换任何url。
		if ($type !== 'email' && preg_match_all('#(\w*://|www\.)[^\s()<>;]+\w#i', $str, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER))
		{
			// Set our target HTML if using popup links.  设定我们的目标如果使用弹出链接的HTML。
			$target = ($popup) ? ' target="_blank"' : '';

			// We process the links in reverse order (last -> first) so that
			// the returned string offsets from preg_match_all() are not
			// moved as we add more HTML.
			// 我们流程中的链接倒序(去年- >第一),以便返回的字符串从preg_match_all补偿()并不像我们添加了更多的HTML。
			foreach (array_reverse($matches) as $match)
			{
				// $match[0] is the matched string/link  是匹配的字符串/链接吗
				// $match[1] is either a protocol prefix or 'www.' 一个协议前缀或“www”。
				//
				// With PREG_OFFSET_CAPTURE, both of the above is an array,  PREG_OFFSET_CAPTURE,上面是一个数组,
				// where the actual value is held in [0] and its offset at the [1] index.  实际价值在哪里举行的[0][1]指数及其偏移量。
				$a = '<a href="'.(strpos($match[1][0], '/') ? '' : 'http://').$match[0][0].'"'.$target.'>'.$match[0][0].'</a>';
				$str = substr_replace($str, $a, $match[0][1], strlen($match[0][0]));
			}
		}

		// Find and replace any emails.  查找和替换任何电子邮件。
		if ($type !== 'url' && preg_match_all('#([\w\.\-\+]+@[a-z0-9\-]+\.[a-z0-9\-\.]+[^[:punct:]\s])#i', $str, $matches, PREG_OFFSET_CAPTURE))
		{
			foreach (array_reverse($matches[0]) as $match)
			{
				if (filter_var($match[0], FILTER_VALIDATE_EMAIL) !== FALSE)
				{
					$str = substr_replace($str, safe_mailto($match[0]), $match[1], strlen($match[0]));
				}
			}
		}

		return $str;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('prep_url'))
{
	/**
	 * Prep URL
	 * 预科的URL
	 * Simply adds the http:// part if no scheme is included
	 * 简单地添加http://如果没有包括计划一部分
	 * @param	string	the URL
	 * @return	string
	 */
	function prep_url($str = '')
	{
		if ($str === 'http://' OR $str === '')
		{
			return '';
		}

		$url = parse_url($str);

		if ( ! $url OR ! isset($url['scheme']))
		{
			return 'http://'.$str;
		}

		return $str;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('url_title'))
{
	/**
	 * Create URL Title
	 * 创建URL标题
	 * Takes a "title" string as input and creates a
	 * human-friendly URL string with a "separator" string
	 * as the word separator.
	 * 以“标题”字符串作为输入并创建一个友好的URL字符串这个词与“分离器”字符串分隔符。
	 * @todo	Remove old 'dash' and 'underscore' usage in 3.1+.  删除旧的“dsh”和“underscore”使用在3.1 +。
	 * @param	string	$str		Input string  输入字符串
	 * @param	string	$separator	Word separator 字分隔符 
	 *			(usually通常用 '-' or '_')
	 * @param	bool	$lowercase	Whether to transform the output string to lowercase 是否输出字符串转换为小写
	 * @return	string
	 */
	function url_title($str, $separator = '-', $lowercase = FALSE)
	{
		if ($separator === 'dash')
		{
			$separator = '-';
		}
		elseif ($separator === 'underscore')
		{
			$separator = '_';
		}

		$q_separator = preg_quote($separator, '#');

		$trans = array(
			'&.+?;'			=> '',
			'[^\w\d _-]'		=> '',
			'\s+'			=> $separator,
			'('.$q_separator.')+'	=> $separator
		);

		$str = strip_tags($str);
		foreach ($trans as $key => $val)
		{
			$str = preg_replace('#'.$key.'#i'.(UTF8_ENABLED ? 'u' : ''), $val, $str);
		}

		if ($lowercase === TRUE)
		{
			$str = strtolower($str);
		}

		return trim(trim($str, $separator));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('redirect'))
{
	/**
	 * Header Redirect
	 * 头重定向
	 * Header redirect in two flavors 头在两个口味重定向
	 * For very fine grained control over headers, you could use the Output
	 * Library's set_header() function.
	 * 头非常细粒度的控制,您可以使用输出库的set_header()函数。
	 * @param	string	$uri	URL
	 * @param	string	$method	Redirect method 重定向的方法
	 *			'auto', 'location' or 'refresh'   “自动”、“位置”或“刷新”
	 * @param	int	$code	HTTP Response status code  HTTP响应状态码
	 * @return	void
	 */
	function redirect($uri = '', $method = 'auto', $code = NULL)
	{
		if ( ! preg_match('#^(\w+:)?//#i', $uri))
		{
			$uri = site_url($uri);
		}

		// IIS environment likely? Use 'refresh' for better compatibility  IIS环境可能?使用“刷新”更好的兼容性
		if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE)
		{
			$method = 'refresh';
		}
		elseif ($method !== 'refresh' && (empty($code) OR ! is_numeric($code)))
		{
			if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1')
			{
				$code = ($_SERVER['REQUEST_METHOD'] !== 'GET')
					? 303	// reference参考: http://en.wikipedia.org/wiki/Post/Redirect/Get
					: 307;
			}
			else
			{
				$code = 302;
			}
		}

		switch ($method)
		{
			case 'refresh':
				header('Refresh:0;url='.$uri);
				break;
			default:
				header('Location: '.$uri, TRUE, $code);
				break;
		}
		exit;
	}
}
