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
 * Trackback Class
 * 引用类
 * Trackback Sending/Receiving Class
 * T引用 发送/接收类
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Trackbacks
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/trackback.html
 */
class CI_Trackback {

	/**
	 * Character set
	 * 字符集
	 * @var	string
	 */
	public $charset = 'UTF-8';

	/**
	 * Trackback data
	 * 引用的数据
	 * @var	array
	 */
	public $data = array(
		'url' => '',
		'title' => '',
		'excerpt' => '',
		'blog_name' => '',
		'charset' => ''
	);

	/**
	 * Convert ASCII flag
	 * 转换为ASCII标志
	 * Whether to convert high-ASCII and MS Word
	 * characters to HTML entities.
	 * 是否high-ASCII和微软的Word字符转换为HTML实体。
	 * @var	bool
	 */
	public $convert_ascii = TRUE;

	/**
	 * Response
	 * 响应
	 * @var	string
	 */
	public $response = '';

	/**
	 * Error messages list
	 * 错误消息列表
	 * @var	string[]
	 */
	public $error_msg = array();

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 * 构造函数
	 * @return	void
	 */
	public function __construct()
	{
		log_message('info', 'Trackback Class Initialized Trackback类初始化');
	}

	// --------------------------------------------------------------------

	/**
	 * Send Trackback
	 * 发送引用 Trackback
	 * @param	array
	 * @return	bool
	 */
	public function send($tb_data)
	{
		if ( ! is_array($tb_data))
		{
			$this->set_error('The send() method must be passed an array 方法必须通过一个数组');
			return FALSE;
		}

		// Pre-process the Trackback Data 引用数据预处理
		foreach (array('url', 'title', 'excerpt', 'blog_name', 'ping_url') as $item)
		{
			if ( ! isset($tb_data[$item]))
			{
				$this->set_error('Required item missing所需项目丢失: '.$item);
				return FALSE;
			}

			switch ($item)
			{
				case 'ping_url':
					$$item = $this->extract_urls($tb_data[$item]);
					break;
				case 'excerpt':
					$$item = $this->limit_characters($this->convert_xml(strip_tags(stripslashes($tb_data[$item]))));
					break;
				case 'url':
					$$item = str_replace('&#45;', '-', $this->convert_xml(strip_tags(stripslashes($tb_data[$item]))));
					break;
				default:
					$$item = $this->convert_xml(strip_tags(stripslashes($tb_data[$item])));
					break;
			}

			// Convert High ASCII Characters 高ASCII字符转换
			if ($this->convert_ascii === TRUE && in_array($item, array('excerpt', 'title', 'blog_name'), TRUE))
			{
				$$item = $this->convert_ascii($$item);
			}
		}

		// Build the Trackback data string 构建引用数据字符串
		$charset = isset($tb_data['charset']) ? $tb_data['charset'] : $this->charset;

		$data = 'url='.rawurlencode($url).'&title='.rawurlencode($title).'&blog_name='.rawurlencode($blog_name)
			.'&excerpt='.rawurlencode($excerpt).'&charset='.rawurlencode($charset);

		// Send Trackback(s)
		$return = TRUE;
		if (count($ping_url) > 0)
		{
			foreach ($ping_url as $url)
			{
				if ($this->process($url, $data) === FALSE)
				{
					$return = FALSE;
				}
			}
		}

		return $return;
	}

	// --------------------------------------------------------------------

	/**
	 * Receive Trackback  Data
	 * 接收引用Trackback数据
	 * This function simply validates the incoming TB data. 这个函数只是验证传入的结核病数据
	 * It returns FALSE on failure and TRUE on success. 它返回FALSE失败和真正的成功。
	 * If the data is valid it is set to the $this->data array 如果数据是有效设置为$ this - >数据数组
	 * so that it can be inserted into a database. 这样它就可以被插入到数据库中。
	 *
	 * @return	bool
	 */
	public function receive()
	{
		foreach (array('url', 'title', 'blog_name', 'excerpt') as $val)
		{
			if (empty($_POST[$val]))
			{
				$this->set_error('The following required POST variable is missing以下要求POST变量是失踪: '.$val);
				return FALSE;
			}

			$this->data['charset'] = isset($_POST['charset']) ? strtoupper(trim($_POST['charset'])) : 'auto';

			if ($val !== 'url' && MB_ENABLED === TRUE)
			{
				if (MB_ENABLED === TRUE)
				{
					$_POST[$val] = mb_convert_encoding($_POST[$val], $this->charset, $this->data['charset']);
				}
				elseif (ICONV_ENABLED === TRUE)
				{
					$_POST[$val] = @iconv($this->data['charset'], $this->charset.'//IGNORE', $_POST[$val]);
				}
			}

			$_POST[$val] = ($val !== 'url') ? $this->convert_xml(strip_tags($_POST[$val])) : strip_tags($_POST[$val]);

			if ($val === 'excerpt')
			{
				$_POST['excerpt'] = $this->limit_characters($_POST['excerpt']);
			}

			$this->data[$val] = $_POST[$val];
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Send Trackback Error Message
	 * Trackback发送错误消息
	 * Allows custom errors to be set. By default it
	 * sends the "incomplete information" error, as that's
	 * the most common one.
	 * 允许自定义错误设置。默认情况下它发送“不完全信息”错误,这是最常见的。
	 * @param	string
	 * @return	void
	 */
	public function send_error($message = 'Incomplete Information')
	{
		exit('<?xml version="1.0" encoding="utf-8"?'.">\n<response>\n<error>1</error>\n<message>".$message."</message>\n</response>");
	}

	// --------------------------------------------------------------------

	/**
	 * Send Trackback Success Message
	 * 发送Trackback成功消息
	 * This should be called when a trackback has been
	 * successfully received and inserted.
	 * 这应该叫做当一个trackback已成功接收和插入。
	 * @return	void
	 */
	public function send_success()
	{
		exit('<?xml version="1.0" encoding="utf-8"?'.">\n<response>\n<error>0</error>\n</response>");
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a particular item
	 * 获取一个特定的项目
	 * @param	string
	 * @return	string
	 */
	public function data($item)
	{
		return isset($this->data[$item]) ? $this->data[$item] : '';
	}

	// --------------------------------------------------------------------

	/**
	 * Process Trackback
	 * 过程Trackback
	 * Opens a socket connection and passes the data to
	 * the server. Returns TRUE on success, FALSE on failure
	 * 打开一个套接字连接并将数据传递给服务器。成功执行,将返回TRUE;如果执行失败将返回FALSE
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function process($url, $data)
	{
		$target = parse_url($url);

		// Open the socket 打开套接字
		if ( ! $fp = @fsockopen($target['host'], 80))
		{
			$this->set_error('Invalid Connection: '.$url);
			return FALSE;
		}

		// Build the path 构建路径
		$path = isset($target['path']) ? $target['path'] : $url;
		empty($target['query']) OR $path .= '?'.$target['query'];

		// Add the Trackback ID to the data string   Trackback ID添加到数据字符串
		if ($id = $this->get_id($url))
		{
			$data = 'tb_id='.$id.'&'.$data;
		}

		// Transfer the data 数据调用 
		fputs($fp, 'POST '.$path." HTTP/1.0\r\n");
		fputs($fp, 'Host: '.$target['host']."\r\n");
		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($fp, 'Content-length: '.strlen($data)."\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $data);

		// Was it successful? 这是成功吗?

		$this->response = '';
		while ( ! feof($fp))
		{
			$this->response .= fgets($fp, 128);
		}
		@fclose($fp);

		if (stripos($this->response, '<error>0</error>') === FALSE)
		{
			$message = preg_match('/<message>(.*?)<\/message>/is', $this->response, $match)
				? trim($match[1])
				: 'An unknown error was encountered遇到了一个未知错误';
			$this->set_error($message);
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Extract Trackback URLs
	 * 提取Trackback url
	 * This function lets multiple trackbacks be sent. 这个函数允许多个trackback被发送。
	 * It takes a string of URLs (separated by comma or
	 * space) and puts each URL into an array
	 * 它接受一个字符串URL(由逗号或空格),把每个URL到一个数组中
	 * @param	string
	 * @return	string
	 */
	public function extract_urls($urls)
	{
		// Remove the pesky white space and replace with a comma, then replace doubles. 把讨厌的空白和替换用逗号,然后取代双打。
		$urls = str_replace(',,', ',', preg_replace('/\s*(\S+)\s*/', '\\1,', $urls));

		// Break into an array via commas and remove duplicates 进入一个数组通过逗号和删除重复值
		$urls = array_unique(preg_split('/[,]/', rtrim($urls, ',')));

		array_walk($urls, array($this, 'validate_url'));
		return $urls;
	}

	// --------------------------------------------------------------------

	/**
	 * Validate URL
	 * 验证URL
	 * Simply adds "http://" if missing
	 * 仅仅是增加了“http://”如果失踪
	 * @param	string
	 * @return	void
	 */
	public function validate_url(&$url)
	{
		$url = trim($url);

		if (strpos($url, 'http') !== 0)
		{
			$url = 'http://'.$url;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Find the Trackback URL's ID
	 * 找到Trackback URL的ID
	 * @param	string
	 * @return	string
	 */
	public function get_id($url)
	{
		$tb_id = '';

		if (strpos($url, '?') !== FALSE)
		{
			$tb_array = explode('/', $url);
			$tb_end   = $tb_array[count($tb_array)-1];

			if ( ! is_numeric($tb_end))
			{
				$tb_end  = $tb_array[count($tb_array)-2];
			}

			$tb_array = explode('=', $tb_end);
			$tb_id	= $tb_array[count($tb_array)-1];
		}
		else
		{
			$url = rtrim($url, '/');

			$tb_array = explode('/', $url);
			$tb_id	= $tb_array[count($tb_array)-1];

			if ( ! is_numeric($tb_id))
			{
				$tb_id = $tb_array[count($tb_array)-2];
			}
		}

		return ctype_digit((string) $tb_id) ? $tb_id : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Convert Reserved XML characters to Entities
	 * 保留XML字符转换为实体
	 * @param	string
	 * @return	string
	 */
	public function convert_xml($str)
	{
		$temp = '__TEMP_AMPERSANDS__';

		$str = preg_replace(array('/&#(\d+);/', '/&(\w+);/'), $temp.'\\1;', $str);

		$str = str_replace(array('&', '<', '>', '"', "'", '-'),
					array('&amp;', '&lt;', '&gt;', '&quot;', '&#39;', '&#45;'),
					$str);

		return preg_replace(array('/'.$temp.'(\d+);/', '/'.$temp.'(\w+);/'), array('&#\\1;', '&\\1;'), $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Character limiter
	 * 字符限制器
	 * Limits the string based on the character count. Will preserve complete words.
	 * 限制了基于字符计数的字符串。将保留完整的单词。
	 * @param	string
	 * @param	int
	 * @param	string
	 * @return	string
	 */
	public function limit_characters($str, $n = 500, $end_char = '&#8230;')
	{
		if (strlen($str) < $n)
		{
			return $str;
		}

		$str = preg_replace('/\s+/', ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));

		if (strlen($str) <= $n)
		{
			return $str;
		}

		$out = '';
		foreach (explode(' ', trim($str)) as $val)
		{
			$out .= $val.' ';
			if (strlen($out) >= $n)
			{
				return rtrim($out).$end_char;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * High ASCII to Entities
	 * 高ASCII实体
	 * Converts Hight ascii text and MS Word special chars
	 * to character entities
	 * 将高ascii文本和微软的Word特殊字符转换为字符实体
	 * @param	string
	 * @return	string
	 */
	public function convert_ascii($str)
	{
		$count	= 1;
		$out	= '';
		$temp	= array();

		for ($i = 0, $s = strlen($str); $i < $s; $i++)
		{
			$ordinal = ord($str[$i]);

			if ($ordinal < 128)
			{
				$out .= $str[$i];
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

					$out .= '&#'.$number.';';
					$count = 1;
					$temp = array();
				}
			}
		}

		return $out;
	}

	// --------------------------------------------------------------------

	/**
	 * Set error message
	 * 错误信息设置
	 * @param	string
	 * @return	void
	 */
	public function set_error($msg)
	{
		log_message('error', $msg);
		$this->error_msg[] = $msg;
	}

	// --------------------------------------------------------------------

	/**
	 * Show error messages
	 * 显示错误信息
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function display_errors($open = '<p>', $close = '</p>')
	{
		return (count($this->error_msg) > 0) ? $open.implode($close.$open, $this->error_msg).$close : '';
	}

}
