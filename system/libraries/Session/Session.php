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
 * @since	Version 2.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Session Class
 * CodeIgniter会话Session类
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author		Andrey Andreev
 * @link		http://codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session {

	/**
	 * Userdata array
	 * 用户数据的数组
	 * Just a reference to $_SESSION, for BC purposes.
	 * 只是一个参考$ _SESSION, 目的
	 */ 
	public $userdata;

	protected $_driver = 'files';
	protected $_config;

	// ------------------------------------------------------------------------

	/**
	 * Class constructor
	 * 构造函数 类构造器 
	 * @param	array	$params	Configuration parameters 配置参数
	 * @return	void
	 */
	public function __construct(array $params = array())
	{
		// No sessions under CLI 没有会话在CLI
		if (is_cli())
		{
			log_message('debug', 'Session: Initialization under CLI aborted 初始化下CLI失败.');
			return;
		}
		elseif ((bool) ini_get('session.auto_start'))
		{
			log_message('error', 'Session: session.auto_start is enabled in php.ini. Aborting.在php.ini中启用失败');
			return;
		}
		elseif ( ! empty($params['driver']))
		{
			$this->_driver = $params['driver'];
			unset($params['driver']);
		}
		elseif ($driver = config_item('sess_driver'))
		{
			$this->_driver = $driver;
		}
		// Note: BC workaround 变通方案
		elseif (config_item('sess_use_database'))
		{
			$this->_driver = 'database';
		}

		$class = $this->_ci_load_classes($this->_driver);

		// Configuration ... 配置
		$this->_configure($params);

		$class = new $class($this->_config);
		if ($class instanceof SessionHandlerInterface)
		{
			if (is_php('5.4'))
			{
				session_set_save_handler($class, TRUE);
			}
			else
			{
				session_set_save_handler(
					array($class, 'open'),
					array($class, 'close'),
					array($class, 'read'),
					array($class, 'write'),
					array($class, 'destroy'),
					array($class, 'gc')
				);

				register_shutdown_function('session_write_close');
			}
		}
		else
		{
			log_message('error', "Session: Driver '".$this->_driver."' doesn't implement SessionHandlerInterface. Aborting.没有实现会话处理程序接口 失败");
			return;
		}

		// Sanitize the cookie, because apparently PHP doesn't do that for userspace handlers  清洁cookie,因为显然PHP不做,对于用户空间处理程序
		if (isset($_COOKIE[$this->_config['cookie_name']])
			&& (
				! is_string($_COOKIE[$this->_config['cookie_name']])
				OR ! preg_match('/^[0-9a-f]{40}$/', $_COOKIE[$this->_config['cookie_name']])
			)
		)
		{
			unset($_COOKIE[$this->_config['cookie_name']]);
		}

		session_start();

		// Is session ID auto-regeneration configured? (ignoring ajax requests) 会话ID auto-regeneration配置吗?(忽略ajax请求)
		if ((empty($_SERVER['HTTP_X_REQUESTED_WITH']) OR strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')
			&& ($regenerate_time = config_item('sess_time_to_update')) > 0
		)
		{
			if ( ! isset($_SESSION['__ci_last_regenerate']))
			{
				$_SESSION['__ci_last_regenerate'] = time();
			}
			elseif ($_SESSION['__ci_last_regenerate'] < (time() - $regenerate_time))
			{
				$this->sess_regenerate((bool) config_item('sess_regenerate_destroy'));
			}
		}
		// Another work-around ... PHP doesn't seem to send the session cookie 另一个办法解决…PHP似乎没有发送会话cookie
		// unless it is being currently created or regenerated  目前,除非它被创建或再生
		elseif (isset($_COOKIE[$this->_config['cookie_name']]) && $_COOKIE[$this->_config['cookie_name']] === session_id())
		{
			setcookie(
				$this->_config['cookie_name'],
				session_id(),
				(empty($this->_config['cookie_lifetime']) ? 0 : time() + $this->_config['cookie_lifetime']),
				$this->_config['cookie_path'],
				$this->_config['cookie_domain'],
				$this->_config['cookie_secure'],
				TRUE
			);
		}

		$this->_ci_init_vars();

		log_message('info', "Session: Class initialized using 类初始化使用'".$this->_driver."' driver.");
	}

	// ------------------------------------------------------------------------

	/**
	 * CI Load Classes
	 * CI加载类
	 * An internal method to load all possible dependency and extension
	 * classes. It kind of emulates the CI_Driver library, but is
	 * self-sufficient.
	 * 一个内部方法加载所有可能的依赖和扩展类。它模拟CI_Driver库,但自给自足。
	 * @param	string	$driver	Driver name
	 * @return	string	Driver class name
	 */
	protected function _ci_load_classes($driver)
	{
		// PHP 5.4 compatibility PHP 5.4兼容性
		interface_exists('SessionHandlerInterface', FALSE) OR require_once(BASEPATH.'libraries/Session/SessionHandlerInterface.php');

		$prefix = config_item('subclass_prefix');

		if ( ! class_exists('CI_Session_driver', FALSE))
		{
			require_once(
				file_exists(APPPATH.'libraries/Session/Session_driver.php')
					? APPPATH.'libraries/Session/Session_driver.php'
					: BASEPATH.'libraries/Session/Session_driver.php'
			);

			if (file_exists($file_path = APPPATH.'libraries/Session/'.$prefix.'Session_driver.php'))
			{
				require_once($file_path);
			}
		}

		$class = 'Session_'.$driver.'_driver';

		// Allow custom drivers without the CI_ or MY_ prefix 允许定制的drivers没有CI_或MY_前缀
		if ( ! class_exists($class, FALSE) && file_exists($file_path = APPPATH.'libraries/Session/drivers/'.$class.'.php'))
		{
			require_once($file_path);
			if (class_exists($class, FALSE))
			{
				return $class;
			}
		}

		if ( ! class_exists('CI_'.$class, FALSE))
		{
			if (file_exists($file_path = APPPATH.'libraries/Session/drivers/'.$class.'.php') OR file_exists($file_path = BASEPATH.'libraries/Session/drivers/'.$class.'.php'))
			{
				require_once($file_path);
			}

			if ( ! class_exists('CI_'.$class, FALSE) && ! class_exists($class, FALSE))
			{
				throw new UnexpectedValueException("Session: Configured driver '".$driver."' was not found. Aborting失败.");
			}
		}

		if ( ! class_exists($prefix.$class) && file_exists($file_path = APPPATH.'libraries/Session/drivers/'.$prefix.$class.'.php'))
		{
			require_once($file_path);
			if (class_exists($prefix.$class, FALSE))
			{
				return $prefix.$class;
			}
			else
			{
				log_message('debug', 'Session: '.$prefix.$class.".php found but it doesn't declare class 发现php但并不声明类".$prefix.$class.'.');
			}
		}

		return 'CI_'.$class;
	}

	// ------------------------------------------------------------------------

	/**
	 * Configuration
	 * 配置
	 * Handle input parameters and configuration defaults
	 * 处理输入参数和配置缺省值
	 * @param	array	&$params	Input parameters 输入参数
	 * @return	void
	 */
	protected function _configure(&$params)
	{
		$expiration = config_item('sess_expiration');

		if (isset($params['cookie_lifetime']))
		{
			$params['cookie_lifetime'] = (int) $params['cookie_lifetime'];
		}
		else
		{
			$params['cookie_lifetime'] = ( ! isset($expiration) && config_item('sess_expire_on_close'))
				? 0 : (int) $expiration;
		}

		isset($params['cookie_name']) OR $params['cookie_name'] = config_item('sess_cookie_name');
		if (empty($params['cookie_name']))
		{
			$params['cookie_name'] = ini_get('session.name');
		}
		else
		{
			ini_set('session.name', $params['cookie_name']);
		}

		isset($params['cookie_path']) OR $params['cookie_path'] = config_item('cookie_path');
		isset($params['cookie_domain']) OR $params['cookie_domain'] = config_item('cookie_domain');
		isset($params['cookie_secure']) OR $params['cookie_secure'] = (bool) config_item('cookie_secure');

		session_set_cookie_params(
			$params['cookie_lifetime'],
			$params['cookie_path'],
			$params['cookie_domain'],
			$params['cookie_secure'],
			TRUE // HttpOnly; Yes, this is intentional and not configurable for security reasons  HttpOnly,是的,这是故意的,没有可配置,是为了安全起见
		);

		if (empty($expiration))
		{
			$params['expiration'] = (int) ini_get('session.gc_maxlifetime');
		}
		else
		{
			$params['expiration'] = (int) $expiration;
			ini_set('session.gc_maxlifetime', $expiration);
		}

		$params['match_ip'] = (bool) (isset($params['match_ip']) ? $params['match_ip'] : config_item('sess_match_ip'));

		isset($params['save_path']) OR $params['save_path'] = config_item('sess_save_path');

		$this->_config = $params;

		// Security is king  安全是王
		ini_set('session.use_trans_sid', 0);
		ini_set('session.use_strict_mode', 1);
		ini_set('session.use_cookies', 1);
		ini_set('session.use_only_cookies', 1);
		ini_set('session.hash_function', 1);
		ini_set('session.hash_bits_per_character', 4);
	}

	// ------------------------------------------------------------------------

	/**
	 * Handle temporary variables
	 * 处理临时变量
	 * Clears old "flash" data, marks the new one for deletion and handles
	 * "temp" data deletion.
	 * 清除旧的“闪电”数据,标志着新的一个用于删除,处理“临时”数据删除。
	 * @return	void
	 */
	protected function _ci_init_vars()
	{
		if ( ! empty($_SESSION['__ci_vars']))
		{
			$current_time = time();

			foreach ($_SESSION['__ci_vars'] as $key => &$value)
			{
				if ($value === 'new')
				{
					$_SESSION['__ci_vars'][$key] = 'old';
				}
				// Hacky, but 'old' will (implicitly) always be less than time() ;)  但“旧”(隐式地)总是会不到时间(),)
				// DO NOT move this above the 'new' check!  不要移动这个“新”以上检查!
				elseif ($value < $current_time)
				{
					unset($_SESSION[$key], $_SESSION['__ci_vars'][$key]);
				}
			}

			if (empty($_SESSION['__ci_vars']))
			{
				unset($_SESSION['__ci_vars']);
			}
		}

		$this->userdata =& $_SESSION;
	}

	// ------------------------------------------------------------------------

	/**
	 * Mark as flash
	 * 将标记为 flash
	 * @param	mixed	$key	Session data key(s) 会话数据的关键
	 * @return	bool
	 */
	public function mark_as_flash($key)
	{
		if (is_array($key))
		{
			for ($i = 0, $c = count($key); $i < $c; $i++)
			{
				if ( ! isset($_SESSION[$key[$i]]))
				{
					return FALSE;
				}
			}

			$new = array_fill_keys($key, 'new');

			$_SESSION['__ci_vars'] = isset($_SESSION['__ci_vars'])
				? array_merge($_SESSION['__ci_vars'], $new)
				: $new;

			return TRUE;
		}

		if ( ! isset($_SESSION[$key]))
		{
			return FALSE;
		}

		$_SESSION['__ci_vars'][$key] = 'new';
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get flash keys
	 * 获得flash键
	 * @return	array
	 */
	public function get_flash_keys()
	{
		if ( ! isset($_SESSION['__ci_vars']))
		{
			return array();
		}

		$keys = array();
		foreach (array_keys($_SESSION['__ci_vars']) as $key)
		{
			is_int($_SESSION['__ci_vars'][$key]) OR $keys[] = $key;
		}

		return $keys;
	}

	// ------------------------------------------------------------------------

	/**
	 * Unmark flash
	 * 取消标记闪光
	 * @param	mixed	$key	Session data key(s)
	 * @return	void
	 */
	public function unmark_flash($key)
	{
		if (empty($_SESSION['__ci_vars']))
		{
			return;
		}

		is_array($key) OR $key = array($key);

		foreach ($key as $k)
		{
			if (isset($_SESSION['__ci_vars'][$k]) && ! is_int($_SESSION['__ci_vars'][$k]))
			{
				unset($_SESSION['__ci_vars'][$k]);
			}
		}

		if (empty($_SESSION['__ci_vars']))
		{
			unset($_SESSION['__ci_vars']);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Mark as temp
	 * 将标记为temp
	 * @param	mixed	$key	Session data key(s)
	 * @param	int	$ttl	Time-to-live in seconds
	 * @return	bool
	 */
	public function mark_as_temp($key, $ttl = 300)
	{
		$ttl += time();

		if (is_array($key))
		{
			$temp = array();

			foreach ($key as $k => $v)
			{
				// Do we have a key => ttl pair, or just a key?
				if (is_int($k))
				{
					$k = $v;
					$v = $ttl;
				}
				else
				{
					$v += time();
				}

				if ( ! isset($_SESSION[$k]))
				{
					return FALSE;
				}

				$temp[$k] = $v;
			}

			$_SESSION['__ci_vars'] = isset($_SESSION['__ci_vars'])
				? array_merge($_SESSION['__ci_vars'], $temp)
				: $temp;

			return TRUE;
		}

		if ( ! isset($_SESSION[$key]))
		{
			return FALSE;
		}

		$_SESSION['__ci_vars'][$key] = $ttl;
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get temp keys
	 * 获得临时密钥
	 * @return	array
	 */
	public function get_temp_keys()
	{
		if ( ! isset($_SESSION['__ci_vars']))
		{
			return array();
		}

		$keys = array();
		foreach (array_keys($_SESSION['__ci_vars']) as $key)
		{
			is_int($_SESSION['__ci_vars'][$key]) && $keys[] = $key;
		}

		return $keys;
	}

	// ------------------------------------------------------------------------

	/**
	 * Unmark flash
	 * 取消标记flash
	 * @param	mixed	$key	Session data key(s)
	 * @return	void
	 */
	public function unmark_temp($key)
	{
		if (empty($_SESSION['__ci_vars']))
		{
			return;
		}

		is_array($key) OR $key = array($key);

		foreach ($key as $k)
		{
			if (isset($_SESSION['__ci_vars'][$k]) && is_int($_SESSION['__ci_vars'][$k]))
			{
				unset($_SESSION['__ci_vars'][$k]);
			}
		}

		if (empty($_SESSION['__ci_vars']))
		{
			unset($_SESSION['__ci_vars']);
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * __get()
	 *
	 * @param	string	$key	'session_id' or a session data key
	 * @return	mixed
	 */
	public function __get($key)
	{
		// Note: Keep this order the same, just in case somebody wants to 注意:保持这个顺序不变,以防有人想
		//       use 'session_id' as a session data key, for whatever reason 使用“session_id”作为会话数据密钥,不管出于什么原因
		if (isset($_SESSION[$key]))
		{
			return $_SESSION[$key];
		}
		elseif ($key === 'session_id')
		{
			return session_id();
		}

		return NULL;
	}

	// ------------------------------------------------------------------------

	/**
	 * __set()
	 *
	 * @param	string	$key	Session data key   会话数据的关键
	 * @param	mixed	$value	Session data value 会话数据值
	 * @return	void
	 */
	public function __set($key, $value)
	{
		$_SESSION[$key] = $value;
	}

	// ------------------------------------------------------------------------

	/**
	 * Session destroy
	 * 会话摧毁
	 * Legacy CI_Session compatibility method
	 * 遗留CI_Session兼容性的方法
	 * @return	void
	 */
	public function sess_destroy()
	{
		session_destroy();
	}

	// ------------------------------------------------------------------------

	/**
	 * Session regenerate
	 * 会话再生
	 * Legacy CI_Session compatibility method
	 * 遗留CI_Session兼容性的方法
	 * @param	bool	$destroy	Destroy old session data flag 摧毁旧会话数据标志
	 * @return	void
	 */
	public function sess_regenerate($destroy = FALSE)
	{
		$_SESSION['__ci_last_regenerate'] = time();
		session_regenerate_id($destroy);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get userdata reference
	 * 获取用户数据参考
	 * Legacy CI_Session compatibility method
	 * 遗留CI_Session兼容性的方法
	 * @returns	array
	 */
	public function &get_userdata()
	{
		return $_SESSION;
	}

	// ------------------------------------------------------------------------

	/**
	 * Userdata (fetch)
	 * 用户数据(取)
	 * Legacy CI_Session compatibility method
	 * 遗留CI_Session兼容性的方法
	 * @param	string	$key	Session data key  会话数据的关键
	 * @return	mixed	Session data value or NULL if not found  如果没有找到会话数据值或NULL
	 */
	public function userdata($key = NULL)
	{
		if (isset($key))
		{
			return isset($_SESSION[$key]) ? $_SESSION[$key] : NULL;
		}
		elseif (empty($_SESSION))
		{
			return array();
		}

		$userdata = array();
		$_exclude = array_merge(
			array('__ci_vars'),
			$this->get_flash_keys(),
			$this->get_temp_keys()
		);

		foreach (array_keys($_SESSION) as $key)
		{
			if ( ! in_array($key, $_exclude, TRUE))
			{
				$userdata[$key] = $_SESSION[$key];
			}
		}

		return $userdata;
	}

	// ------------------------------------------------------------------------

	/**
	 * Set userdata
	 * 设置用户数据
	 * Legacy CI_Session compatibility method
	 * 遗留CI_Session兼容性的方法
	 * @param	mixed	$data	Session data key or an associative array  会话数据键或一个关联数组中
	 * @param	mixed	$value	Value to store  值储存
	 * @return	void
	 */
	public function set_userdata($data, $value = NULL)
	{
		if (is_array($data))
		{
			foreach ($data as $key => &$value)
			{
				$_SESSION[$key] = $value;
			}

			return;
		}

		$_SESSION[$data] = $value;
	}

	// ------------------------------------------------------------------------

	/**
	 * Unset userdata
	 * 未设置的用户数据
	 * Legacy CI_Session compatibility method
	 * 遗留CI_Session兼容性的方法
	 * @param	mixed	$data	Session data key(s) 会话数据键
	 * @return	void
	 */
	public function unset_userdata($key)
	{
		if (is_array($key))
		{
			foreach ($key as $k)
			{
				unset($_SESSION[$k]);
			}

			return;
		}

		unset($_SESSION[$key]);
	}

	// ------------------------------------------------------------------------

	/**
	 * All userdata (fetch)
	 * 所有用户数据(取)
	 * Legacy CI_Session compatibility method
	 * 遗留CI_Session兼容性的方法
	 * @return	array	$_SESSION, excluding flash data items 不包括闪存数据项
	 */
	public function all_userdata()
	{
		return $this->userdata();
	}

	// ------------------------------------------------------------------------

	/**
	 * Has userdata
	 * 有用户数据
	 * Legacy CI_Session compatibility method
	 * 遗留CI_Session兼容性的方法
	 * @param	string	$key	Session data key 会话数据的关键
	 * @return	bool
	 */
	public function has_userdata($key)
	{
		return isset($_SESSION[$key]);
	}

	// ------------------------------------------------------------------------

	/**
	 * Flashdata (fetch)
	 * 闪存数据(取)
	 * Legacy CI_Session compatibility method
	 * 遗留CI_Session兼容性的方法
	 * @param	string	$key	Session data key  会话数据的关键
	 * @return	mixed	Session data value or NULL if not found  如果没有找到会话数据值或NULL
	 */
	public function flashdata($key = NULL)
	{
		if (isset($key))
		{
			return (isset($_SESSION['__ci_vars'], $_SESSION['__ci_vars'][$key], $_SESSION[$key]) && ! is_int($_SESSION['__ci_vars'][$key]))
				? $_SESSION[$key]
				: NULL;
		}

		$flashdata = array();

		if ( ! empty($_SESSION['__ci_vars']))
		{
			foreach ($_SESSION['__ci_vars'] as $key => &$value)
			{
				is_int($value) OR $flashdata[$key] = $_SESSION[$key];
			}
		}

		return $flashdata;
	}

	// ------------------------------------------------------------------------

	/**
	 * Set flashdata
	 * 闪存数据集
	 * Legacy CI_Session compatibility method
	 * 遗留CI_Session兼容性的方法
	 * @param	mixed	$data	Session data key or an associative array 会话数据键或一个关联数组中
	 * @param	mixed	$value	Value to store  值储存
	 * @return	void
	 */
	public function set_flashdata($data, $value = NULL)
	{
		$this->set_userdata($data, $value);
		$this->mark_as_flash(is_array($data) ? array_keys($data) : $data);
	}

	// ------------------------------------------------------------------------

	/**
	 * Keep flashdata
	 * 保持闪存数据
	 * Legacy CI_Session compatibility method
	 * 遗留CI_Session兼容性的方法
	 * @param	mixed	$key	Session data key(s) 会话数据的关键
	 * @return	void
	 */
	public function keep_flashdata($key)
	{
		$this->mark_as_flash($key);
	}

	// ------------------------------------------------------------------------

	/**
	 * Temp data (fetch)
	 * 临时数据(取)
	 * Legacy CI_Session compatibility method
	 * 遗留CI_Session兼容性的方法
	 * @param	string	$key	Session data key 会话数据的关键
	 * @return	mixed	Session data value or NULL if not found 如果没有找到会话数据值或NULL
	 */
	public function tempdata($key = NULL)
	{
		if (isset($key))
		{
			return (isset($_SESSION['__ci_vars'], $_SESSION['__ci_vars'][$key], $_SESSION[$key]) && is_int($_SESSION['__ci_vars'][$key]))
				? $_SESSION[$key]
				: NULL;
		}

		$tempdata = array();

		if ( ! empty($_SESSION['__ci_vars']))
		{
			foreach ($_SESSION['__ci_vars'] as $key => &$value)
			{
				is_int($value) && $tempdata[$key] = $_SESSION[$key];
			}
		}

		return $tempdata;
	}

	// ------------------------------------------------------------------------

	/**
	 * Set tempdata
	 * 临时数据集
	 * Legacy CI_Session compatibility method
	 * 遗留CI_Session兼容性的方法
	 * @param	mixed	$data	Session data key or an associative array of items 会话数据条目的键或关联数组
	 * @param	mixed	$value	Value to store  值储存
	 * @param	int	$ttl	Time-to-live in seconds  生存时间以秒为单位
	 * @return	void
	 */
	public function set_tempdata($data, $value = NULL, $ttl = 300)
	{
		$this->set_userdata($data, $value);
		$this->mark_as_temp(is_array($data) ? array_keys($data) : $data, $ttl);
	}

	// ------------------------------------------------------------------------

	/**
	 * Unset tempdata
	 * 未设置的临时数据
	 * Legacy CI_Session compatibility method
	 * 遗留CI_Session兼容性的方法
	 * @param	mixed	$data	Session data key(s) 会话数据的关键
	 * @return	void
	 */
	public function unset_tempdata($key)
	{
		$this->unmark_temp($key);
	}

}
