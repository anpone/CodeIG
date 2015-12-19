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
 * Config Class 配置类 
 *
 * This class contains functions that enable config files to be managed
 *这个类包含函数,使配置文件管理
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/config.html
 */
class CI_Config {

	/**
	 * List of all loaded config values
	 *所有加载配置值的列表
	 * @var	array
	 */
	public $config = array();

	/**
	 * List of all loaded config files
	 *所有加载配置文件的列表
	 * @var	array
	 */
	public $is_loaded =	array();

	/**
	 * List of paths to search when trying to load a config file.
	 *搜索路径列表当试图加载配置文件。
	 * @used-by	CI_Loader
	 * @var		array
	 */
	public $_config_paths =	array(APPPATH);

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *构造函数 类构造器 
	 * Sets the $config data from the primary config.php file as a class variable.
	 *美元的配置数据从主配置。php文件作为一个类变量。
	 * @return	void
	 */
	public function __construct()
	{
		$this->config =& get_config();

		// Set the base_url automatically if none was provided 如果没有提供自动设置base_url
		if (empty($this->config['base_url']))
		{
			// The regular expression is only a basic validation for a valid "Host" header.正则表达式只是一个基本验证有效的“主机”头。
			// It's not exhaustive, only checks for valid characters.这不是详尽的,只检查有效字符。
			if (isset($_SERVER['HTTP_HOST']) && preg_match('/^((\[[0-9a-f:]+\])|(\d{1,3}(\.\d{1,3}){3})|[a-z0-9\-\.]+)(:\d+)?$/i', $_SERVER['HTTP_HOST']))
			{
				$base_url = (is_https() ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST']
					.substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], basename($_SERVER['SCRIPT_FILENAME'])));
			}
			else
			{
				$base_url = 'http://localhost/';
			}

			$this->set_item('base_url', $base_url);
		}

		log_message('info', 'Config Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Load Config File 加载配置文件
	 *
	 * @param	string	$file			Configuration file name 配置文件名称
	 * @param	bool	$use_sections		Whether configuration values should be loaded into their own section配置值是否应该被加载到自己的部分
	 * @param	bool	$fail_gracefully	Whether to just return FALSE or display an error message是否要返回FALSE或显示一条错误消息
	 * @return	bool	TRUE if the file was loaded correctly or FALSE on failure真的如果文件被加载正确;如果执行失败将返回FALSE
	 */
	public function load($file = '', $use_sections = FALSE, $fail_gracefully = FALSE)
	{
		$file = ($file === '') ? 'config' : str_replace('.php', '', $file);
		$loaded = FALSE;

		foreach ($this->_config_paths as $path)
		{
			foreach (array($file, ENVIRONMENT.DIRECTORY_SEPARATOR.$file) as $location)
			{
				$file_path = $path.'config/'.$location.'.php';
				if (in_array($file_path, $this->is_loaded, TRUE))
				{
					return TRUE;
				}

				if ( ! file_exists($file_path))
				{
					continue;
				}

				include($file_path);

				if ( ! isset($config) OR ! is_array($config))
				{
					if ($fail_gracefully === TRUE)
					{
						return FALSE;
					}

					show_error('Your '.$file_path.' file does not appear to contain a valid configuration array文件似乎并不包含一个有效的配置阵列.');
				}

				if ($use_sections === TRUE)
				{
					$this->config[$file] = isset($this->config[$file])
						? array_merge($this->config[$file], $config)
						: $config;
				}
				else
				{
					$this->config = array_merge($this->config, $config);
				}

				$this->is_loaded[] = $file_path;
				$config = NULL;
				$loaded = TRUE;
				log_message('debug', 'Config file loaded配置文件加载: '.$file_path);
			}
		}

		if ($loaded === TRUE)
		{
			return TRUE;
		}
		elseif ($fail_gracefully === TRUE)
		{
			return FALSE;
		}

		show_error('The configuration file内核编译配置文件 '.$file.'.php does not exist.');
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a config file item 获取配置文件项
	 *
	 * @param	string	$item	Config item name 配置项名称
	 * @param	string	$index	Index name 索引名称
	 * @return	string|null	The configuration item or NULL if the item doesn't exist 配置项或NULL如果条目不存在
	 */
	public function item($item, $index = '')
	{
		if ($index == '')
		{
			return isset($this->config[$item]) ? $this->config[$item] : NULL;
		}

		return isset($this->config[$index], $this->config[$index][$item]) ? $this->config[$index][$item] : NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch a config file item with slash appended (if not empty) 获取配置文件项与削减附加(如果不是空的)
	 *
	 * @param	string		$item	Config item name 配置项名称
	 * @return	string|null	The configuration item or NULL if the item doesn't exist 配置项或NULL如果条目不存在
	 */
	public function slash_item($item)
	{
		if ( ! isset($this->config[$item]))
		{
			return NULL;
		}
		elseif (trim($this->config[$item]) === '')
		{
			return '';
		}

		return rtrim($this->config[$item], '/').'/';
	}

	// --------------------------------------------------------------------

	/**
	 * Site URL 网站网址
	 *
	 * Returns base_url . index_page [. uri_string] 返回base_url.index_page[.uri_string]
	 *
	 * @uses	CI_Config::_uri_string()
	 *
	 * @param	string|string[]	$uri	URI string or an array of segments 段的URI字符串或一个数组
	 * @param	string	$protocol
	 * @return	string
	 */
	public function site_url($uri = '', $protocol = NULL)
	{
		$base_url = $this->slash_item('base_url');

		if (isset($protocol))
		{
			// For protocol-relative links =>protocol-relative链接
			if ($protocol === '')
			{
				$base_url = substr($base_url, strpos($base_url, '//'));
			}
			else
			{
				$base_url = $protocol.substr($base_url, strpos($base_url, '://'));
			}
		}

		if (empty($uri))
		{
			return $base_url.$this->item('index_page');
		}

		$uri = $this->_uri_string($uri);

		if ($this->item('enable_query_strings') === FALSE)
		{
			$suffix = isset($this->config['url_suffix']) ? $this->config['url_suffix'] : '';

			if ($suffix !== '')
			{
				if (($offset = strpos($uri, '?')) !== FALSE)
				{
					$uri = substr($uri, 0, $offset).$suffix.substr($uri, $offset);
				}
				else
				{
					$uri .= $suffix;
				}
			}

			return $base_url.$this->slash_item('index_page').$uri;
		}
		elseif (strpos($uri, '?') === FALSE)
		{
			$uri = '?'.$uri;
		}

		return $base_url.$this->item('index_page').$uri;
	}

	// -------------------------------------------------------------

	/**
	 * Base URL 基准网址
	 *
	 * Returns base_url [. uri_string] 返回base_url
	 *
	 * @uses	CI_Config::_uri_string()
	 *
	 * @param	string|string[]	$uri	URI string or an array of segments段的URI字符串或一个数组
	 * @param	string	$protocol
	 * @return	string
	 */
	public function base_url($uri = '', $protocol = NULL)
	{
		$base_url = $this->slash_item('base_url');

		if (isset($protocol))
		{
			// For protocol-relative links protocol-relative相关协议链接
			if ($protocol === '')
			{
				$base_url = substr($base_url, strpos($base_url, '//'));
			}
			else
			{
				$base_url = $protocol.substr($base_url, strpos($base_url, '://'));
			}
		}

		return $base_url.ltrim($this->_uri_string($uri), '/');
	}

	// -------------------------------------------------------------

	/**
	 * Build URI string 构建URI字符串
	 *
	 * @used-by	CI_Config::site_url()
	 * @used-by	CI_Config::base_url()
	 *
	 * @param	string|string[]	$uri	URI string or an array of segments 段的URI字符串或一个数组
	 * @return	string
	 */
	protected function _uri_string($uri)
	{
		if ($this->item('enable_query_strings') === FALSE)
		{
			if (is_array($uri))
			{
				$uri = implode('/', $uri);
			}
			return trim($uri, '/');
		}
		elseif (is_array($uri))
		{
			return http_build_query($uri);
		}

		return $uri;
	}

	// --------------------------------------------------------------------

	/**
	 * System URL 系统网址
	 *
	 * @deprecated	3.0.0	Encourages insecure practices 鼓励不安全的做法
	 * @return	string
	 */
	public function system_url()
	{
		$x = explode('/', preg_replace('|/*(.+?)/*$|', '\\1', BASEPATH));
		return $this->slash_item('base_url').end($x).'/';
	}

	// --------------------------------------------------------------------

	/**
	 * Set a config file item 设置一个配置文件项
	 *
	 * @param	string	$item	Config item key   配置项的键
	 * @param	string	$value	Config item value 配置项的值
	 * @return	void
	 */
	public function set_item($item, $value)
	{
		$this->config[$item] = $value;
	}

}
