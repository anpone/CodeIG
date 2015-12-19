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
 * User Agent Class
 * 用户代理类 客户端检测
 * Identifies the platform, browser, robot, or mobile device of the browsing agent
 * 标识平台、浏览器、机器人或移动设备浏览的代理
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	User Agent
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/user_agent.html
 */
class CI_User_agent {

	/**
	 * Current user-agent
	 * 当前用户代理
	 * @var string
	 */
	public $agent = NULL;

	/**
	 * Flag for if the user-agent belongs to a browser
	 * 用户代理属于一个浏览器的标识
	 * @var bool
	 */
	public $is_browser = FALSE;

	/**
	 * Flag for if the user-agent is a robot
	 * 如果用户代理是一个机器人的标识
	 * @var bool
	 */
	public $is_robot = FALSE;

	/**
	 * Flag for if the user-agent is a mobile browser
	 * 如果是 移动浏览器 的标识
	 * @var bool
	 */
	public $is_mobile = FALSE;

	/**
	 * Languages accepted by the current user agent
	 * 当前的用户代理接受的语言
	 * @var array
	 */
	public $languages = array();

	/**
	 * Character sets accepted by the current user agent
	 * 字符集接受当前的用户代理
	 * @var array
	 */
	public $charsets = array();

	/**
	 * List of platforms to compare against current user agent
	 * 平台列表 当前用户代理列表
	 * @var array
	 */
	public $platforms = array();

	/**
	 * List of browsers to compare against current user agent
	 * 当前用户的浏览器列表比较代理
	 * @var array
	 */
	public $browsers = array();

	/**
	 * List of mobile browsers to compare against current user agent
	 * 移动浏览器列表比较当前的用户代理
	 * @var array
	 */
	public $mobiles = array();

	/**
	 * List of robots to compare against current user agent
	 * 机器人比较当前用户代理列表
	 * @var array
	 */
	public $robots = array();

	/**
	 * Current user-agent platform
	 * 当前用户代理平台
	 * @var string
	 */
	public $platform = '';

	/**
	 * Current user-agent browser
	 * 当前用户代理浏览器
	 * @var string
	 */
	public $browser = '';

	/**
	 * Current user-agent version
	 * 当前用户代理版本
	 * @var string
	 */
	public $version = '';

	/**
	 * Current user-agent mobile name
	 * 当前用户代理移动的名字
	 * @var string
	 */
	public $mobile = '';

	/**
	 * Current user-agent robot name
	 * 当前用户代理机器人的名字
	 * @var string
	 */
	public $robot = '';

	/**
	 * HTTP Referer
	 * HTTP引用页
	 * @var	mixed
	 */
	public $referer;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 * 构造函数
	 * Sets the User Agent and runs the compilation routine
	 * 设置用户代理和编译程序运行
	 * @return	void
	 */
	public function __construct()
	{
		if (isset($_SERVER['HTTP_USER_AGENT']))
		{
			$this->agent = trim($_SERVER['HTTP_USER_AGENT']);
		}

		if ($this->agent !== NULL && $this->_load_agent_file())
		{
			$this->_compile_data();
		}

		log_message('info', 'User Agent Class Initialized用户代理类初始化');
	}

	// --------------------------------------------------------------------

	/**
	 * Compile the User Agent Data
	 * 编译用户代理数据
	 * @return	bool
	 */
	protected function _load_agent_file()
	{
		if (($found = file_exists(APPPATH.'config/user_agents.php')))
		{
			include(APPPATH.'config/user_agents.php');
		}

		if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/user_agents.php'))
		{
			include(APPPATH.'config/'.ENVIRONMENT.'/user_agents.php');
			$found = TRUE;
		}

		if ($found !== TRUE)
		{
			return FALSE;
		}

		$return = FALSE;

		if (isset($platforms))
		{
			$this->platforms = $platforms;
			unset($platforms);
			$return = TRUE;
		}

		if (isset($browsers))
		{
			$this->browsers = $browsers;
			unset($browsers);
			$return = TRUE;
		}

		if (isset($mobiles))
		{
			$this->mobiles = $mobiles;
			unset($mobiles);
			$return = TRUE;
		}

		if (isset($robots))
		{
			$this->robots = $robots;
			unset($robots);
			$return = TRUE;
		}

		return $return;
	}

	// --------------------------------------------------------------------

	/**
	 * Compile the User Agent Data
	 * 编译用户代理数据
	 * @return	bool
	 */
	protected function _compile_data()
	{
		$this->_set_platform();

		foreach (array('_set_robot', '_set_browser', '_set_mobile') as $function)
		{
			if ($this->$function() === TRUE)
			{
				break;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Platform
	 * 设置平台
	 * @return	bool
	 */
	protected function _set_platform()
	{
		if (is_array($this->platforms) && count($this->platforms) > 0)
		{
			foreach ($this->platforms as $key => $val)
			{
				if (preg_match('|'.preg_quote($key).'|i', $this->agent))
				{
					$this->platform = $val;
					return TRUE;
				}
			}
		}

		$this->platform = 'Unknown Platform';
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Browser
	 * 设置浏览器
	 * @return	bool
	 */
	protected function _set_browser()
	{
		if (is_array($this->browsers) && count($this->browsers) > 0)
		{
			foreach ($this->browsers as $key => $val)
			{
				if (preg_match('|'.$key.'.*?([0-9\.]+)|i', $this->agent, $match))
				{
					$this->is_browser = TRUE;
					$this->version = $match[1];
					$this->browser = $val;
					$this->_set_mobile();
					return TRUE;
				}
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Robot
	 * 设置机器人
	 * @return	bool
	 */
	protected function _set_robot()
	{
		if (is_array($this->robots) && count($this->robots) > 0)
		{
			foreach ($this->robots as $key => $val)
			{
				if (preg_match('|'.preg_quote($key).'|i', $this->agent))
				{
					$this->is_robot = TRUE;
					$this->robot = $val;
					$this->_set_mobile();
					return TRUE;
				}
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Mobile Device
	 * 设置移动设备
	 * @return	bool
	 */
	protected function _set_mobile()
	{
		if (is_array($this->mobiles) && count($this->mobiles) > 0)
		{
			foreach ($this->mobiles as $key => $val)
			{
				if (FALSE !== (stripos($this->agent, $key)))
				{
					$this->is_mobile = TRUE;
					$this->mobile = $val;
					return TRUE;
				}
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the accepted languages
	 * 设置接受语言
	 * @return	void
	 */
	protected function _set_languages()
	{
		if ((count($this->languages) === 0) && ! empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			$this->languages = explode(',', preg_replace('/(;\s?q=[0-9\.]+)|\s/i', '', strtolower(trim($_SERVER['HTTP_ACCEPT_LANGUAGE']))));
		}

		if (count($this->languages) === 0)
		{
			$this->languages = array('Undefined');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set the accepted character sets
	 * 接受的字符集
	 * @return	void
	 */
	protected function _set_charsets()
	{
		if ((count($this->charsets) === 0) && ! empty($_SERVER['HTTP_ACCEPT_CHARSET']))
		{
			$this->charsets = explode(',', preg_replace('/(;\s?q=.+)|\s/i', '', strtolower(trim($_SERVER['HTTP_ACCEPT_CHARSET']))));
		}

		if (count($this->charsets) === 0)
		{
			$this->charsets = array('Undefined');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Is Browser
	 * 是否是浏览器
	 * @param	string	$key
	 * @return	bool
	 */
	public function is_browser($key = NULL)
	{
		if ( ! $this->is_browser)
		{
			return FALSE;
		}

		// No need to be specific, it's a browser 不需要特定的,它是一个浏览器
		if ($key === NULL)
		{
			return TRUE;
		}

		// Check for a specific browser 检查一个特定的浏览器
		return (isset($this->browsers[$key]) && $this->browser === $this->browsers[$key]);
	}

	// --------------------------------------------------------------------

	/**
	 * Is Robot
	 * 是否是机器人
	 * @param	string	$key
	 * @return	bool
	 */
	public function is_robot($key = NULL)
	{
		if ( ! $this->is_robot)
		{
			return FALSE;
		}

		// No need to be specific, it's a robot 不需要特定的,是一个机器人
		if ($key === NULL)
		{
			return TRUE;
		}

		// Check for a specific robot 检查一个特定的机器人
		return (isset($this->robots[$key]) && $this->robot === $this->robots[$key]);
	}

	// --------------------------------------------------------------------

	/**
	 * Is Mobile
	 * 是否是手机
	 * @param	string	$key
	 * @return	bool
	 */
	public function is_mobile($key = NULL)
	{
		if ( ! $this->is_mobile)
		{
			return FALSE;
		}

		// No need to be specific, it's a mobile 不需要特定的,这是一个手机
		if ($key === NULL)
		{
			return TRUE;
		}

		// Check for a specific robot 检查一个特定的机器人
		return (isset($this->mobiles[$key]) && $this->mobile === $this->mobiles[$key]);
	}

	// --------------------------------------------------------------------

	/**
	 * Is this a referral from another site?
	 * 这是一个从另一个网站推荐吗?
	 * @return	bool
	 */
	public function is_referral()
	{
		if ( ! isset($this->referer))
		{
			if (empty($_SERVER['HTTP_REFERER']))
			{
				$this->referer = FALSE;
			}
			else
			{
				$referer_host = @parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
				$own_host = parse_url(config_item('base_url'), PHP_URL_HOST);

				$this->referer = ($referer_host && $referer_host !== $own_host);
			}
		}

		return $this->referer;
	}

	// --------------------------------------------------------------------

	/**
	 * Agent String
	 * 代理字符串
	 * @return	string
	 */
	public function agent_string()
	{
		return $this->agent;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Platform
	 * 获取平台
	 * @return	string
	 */
	public function platform()
	{
		return $this->platform;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Browser Name
	 * 得到浏览器的名字
	 * @return	string
	 */
	public function browser()
	{
		return $this->browser;
	}

	// --------------------------------------------------------------------

	/**
	 * Get the Browser Version
	 * 浏览器版本
	 * @return	string
	 */
	public function version()
	{
		return $this->version;
	}

	// --------------------------------------------------------------------

	/**
	 * Get The Robot Name
	 * 机器人的名字
	 * @return	string
	 */
	public function robot()
	{
		return $this->robot;
	}
	// --------------------------------------------------------------------

	/**
	 * Get the Mobile Device
	 * 移动设备
	 * @return	string
	 */
	public function mobile()
	{
		return $this->mobile;
	}

	// --------------------------------------------------------------------

	/**
	 * Get the referrer
	 * 推荐人
	 * @return	bool
	 */
	public function referrer()
	{
		return empty($_SERVER['HTTP_REFERER']) ? '' : trim($_SERVER['HTTP_REFERER']);
	}

	// --------------------------------------------------------------------

	/**
	 * Get the accepted languages
	 * 得到公认的语言
	 * @return	array
	 */
	public function languages()
	{
		if (count($this->languages) === 0)
		{
			$this->_set_languages();
		}

		return $this->languages;
	}

	// --------------------------------------------------------------------

	/**
	 * Get the accepted Character Sets
	 * 得到公认的字符集
	 * @return	array
	 */
	public function charsets()
	{
		if (count($this->charsets) === 0)
		{
			$this->_set_charsets();
		}

		return $this->charsets;
	}

	// --------------------------------------------------------------------

	/**
	 * Test for a particular language
	 * 测试一个特定的语言
	 * @param	string	$lang
	 * @return	bool
	 */
	public function accept_lang($lang = 'en')
	{
		return in_array(strtolower($lang), $this->languages(), TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Test for a particular character set
	 * 测试一个特定的字符集
	 * @param	string	$charset
	 * @return	bool
	 */
	public function accept_charset($charset = 'utf-8')
	{
		return in_array(strtolower($charset), $this->charsets(), TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Parse a custom user-agent string
	 * 解析一个定制的用户代理字符串
	 * @param	string	$string
	 * @return	void
	 */
	public function parse($string)
	{
		// Reset values 恢复缺省值
		$this->is_browser = FALSE;
		$this->is_robot = FALSE;
		$this->is_mobile = FALSE;
		$this->browser = '';
		$this->version = '';
		$this->mobile = '';
		$this->robot = '';

		// Set the new user-agent string and parse it, unless empty 设置新的用户代理字符串并解析它,除非是空的
		$this->agent = $string;

		if ( ! empty($string))
		{
			$this->_compile_data();
		}
	}

}
