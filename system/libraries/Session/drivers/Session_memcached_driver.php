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
 * @since	Version 3.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Session Memcached Driver
 * CodeIgniter会话Memcached驱动
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author	Andrey Andreev
 * @link	http://codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session_memcached_driver extends CI_Session_driver implements SessionHandlerInterface {

	/**
	 * Memcached instance
	 * Memcached实例
	 * @var	Memcached
	 */
	protected $_memcached;

	/**
	 * Key prefix
	 * 键前缀 
	 * @var	string
	 */
	protected $_key_prefix = 'ci_session:';

	/**
	 * Lock key
	 * 封锁键
	 * @var	string
	 */
	protected $_lock_key;

	// ------------------------------------------------------------------------

	/**
	 * Class constructor
	 * 构造函数 类构造器
	 * @param	array	$params	Configuration parameters  配置参数
	 * @return	void
	 */
	public function __construct(&$params)
	{
		parent::__construct($params);

		if (empty($this->_config['save_path']))
		{
			log_message('error', 'Session: No Memcached save path configured 没有Memcached保存路径配置.');
		}

		if ($this->_config['match_ip'] === TRUE)
		{
			$this->_key_prefix .= $_SERVER['REMOTE_ADDR'].':';
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Open
	 *
	 * Sanitizes save_path and initializes connections.
	 * 清理save_path并初始化连接。
	 * @param	string	$save_path	Server path(s)
	 * @param	string	$name		Session cookie name, unused  会话cookie的名字,未使用的
	 * @return	bool
	 */
	public function open($save_path, $name)
	{
		$this->_memcached = new Memcached();
		$this->_memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, TRUE); // required for touch() usage 需要联系()使用
		$server_list = array();
		foreach ($this->_memcached->getServerList() as $server)
		{
			$server_list[] = $server['host'].':'.$server['port'];
		}

		if ( ! preg_match_all('#,?([^,:]+)\:(\d{1,5})(?:\:(\d+))?#', $this->_config['save_path'], $matches, PREG_SET_ORDER))
		{
			$this->_memcached = NULL;
			log_message('error', 'Session: Invalid Memcached save path format 无效的Memcached格式保存路径: '.$this->_config['save_path']);
			return FALSE;
		}

		foreach ($matches as $match)
		{
			// If Memcached already has this server (or if the port is invalid), skip it 如果Memcached已经该服务器(或者如果端口是无效的),跳过它
			if (in_array($match[1].':'.$match[2], $server_list, TRUE))
			{
				log_message('debug', 'Session: Memcached server pool already has Memcached服务器池已经'.$match[1].':'.$match[2]);
				continue;
			}

			if ( ! $this->_memcached->addServer($match[1], $match[2], isset($match[3]) ? $match[3] : 0))
			{
				log_message('error', 'Could not add '.$match[1].':'.$match[2].' to Memcached server pool服务器池.');
			}
			else
			{
				$server_list[] = $match[1].':'.$match[2];
			}
		}

		if (empty($server_list))
		{
			log_message('error', 'Session: Memcached server pool is empty. Memcached服务器池是空的');
			return FALSE;
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Read
	 *
	 * Reads session data and acquires a lock
	 * 读取会话数据并获得一个锁
	 * @param	string	$session_id	Session ID
	 * @return	string	Serialized session data 序列化的会话数据
	 */
	public function read($session_id)
	{
		if (isset($this->_memcached) && $this->_get_lock($session_id))
		{
			// Needed by write() to detect session_regenerate_id() calls  需要写()来检测session_regenerate_id()调用
			$this->_session_id = $session_id;

			$session_data = (string) $this->_memcached->get($this->_key_prefix.$session_id);
			$this->_fingerprint = md5($session_data);
			return $session_data;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Write
	 *
	 * Writes (create / update) session data
	 * 写(创建/更新)会话数据
	 * @param	string	$session_id	Session ID
	 * @param	string	$session_data	Serialized session data  序列化的会话数据
	 * @return	bool
	 */
	public function write($session_id, $session_data)
	{
		if ( ! isset($this->_memcached))
		{
			return FALSE;
		}
		// Was the ID regenerated?  ID再生?
		elseif ($session_id !== $this->_session_id)
		{
			if ( ! $this->_release_lock() OR ! $this->_get_lock($session_id))
			{
				return FALSE;
			}

			$this->_fingerprint = md5('');
			$this->_session_id = $session_id;
		}

		if (isset($this->_lock_key))
		{
			$this->_memcached->replace($this->_lock_key, time(), 300);
			if ($this->_fingerprint !== ($fingerprint = md5($session_data)))
			{
				if ($this->_memcached->set($this->_key_prefix.$session_id, $session_data, $this->_config['expiration']))
				{
					$this->_fingerprint = $fingerprint;
					return TRUE;
				}

				return FALSE;
			}

			return $this->_memcached->touch($this->_key_prefix.$session_id, $this->_config['expiration']);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Close
	 *
	 * Releases locks and closes connection.
	 * 释放锁和关闭连接。
	 * @return	bool
	 */
	public function close()
	{
		if (isset($this->_memcached))
		{
			isset($this->_lock_key) && $this->_memcached->delete($this->_lock_key);
			if ( ! $this->_memcached->quit())
			{
				return FALSE;
			}

			$this->_memcached = NULL;
			return TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Destroy
	 *
	 * Destroys the current session.
	 * 销毁了当前会话。
	 * @param	string	$session_id	Session ID
	 * @return	bool
	 */
	public function destroy($session_id)
	{
		if (isset($this->_memcached, $this->_lock_key))
		{
			$this->_memcached->delete($this->_key_prefix.$session_id);
			return $this->_cookie_destroy();
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Garbage Collector
	 *
	 * Deletes expired sessions
	 * 删除过期的会话
	 * @param	int 	$maxlifetime	Maximum lifetime of sessions
	 * @return	bool
	 */
	public function gc($maxlifetime)
	{
		// Not necessary, Memcached takes care of that.  没有必要,Memcached负责
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Get lock
	 *
	 * Acquires an (emulated) lock.
	 * 获得一个锁(模拟)。
	 * @param	string	$session_id	Session ID
	 * @return	bool
	 */
	protected function _get_lock($session_id)
	{
		if (isset($this->_lock_key))
		{
			return $this->_memcached->replace($this->_lock_key, time(), 300);
		}

		// 30 attempts to obtain a lock, in case another request already has it  30试图获得一个锁,以防另一个请求已经
		$lock_key = $this->_key_prefix.$session_id.':lock';
		$attempt = 0;
		do
		{
			if ($this->_memcached->get($lock_key))
			{
				sleep(1);
				continue;
			}

			if ( ! $this->_memcached->set($lock_key, time(), 300))
			{
				log_message('error', 'Session: Error while trying to obtain lock for 错误在试图获得锁 '.$this->_key_prefix.$session_id);
				return FALSE;
			}

			$this->_lock_key = $lock_key;
			break;
		}
		while (++$attempt < 30);

		if ($attempt === 30)
		{
			log_message('error', 'Session: Unable to obtain lock for 无法获得锁'.$this->_key_prefix.$session_id.' after 30 attempts在30次, aborting异常终止.');
			return FALSE;
		}

		$this->_lock = TRUE;
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Release lock
	 *
	 * Releases a previously acquired lock
	 * 发布之前获得锁
	 * @return	bool
	 */
	protected function _release_lock()
	{
		if (isset($this->_memcached, $this->_lock_key) && $this->_lock)
		{
			if ( ! $this->_memcached->delete($this->_lock_key) && $this->_memcached->getResultCode() !== Memcached::RES_NOTFOUND)
			{
				log_message('error', 'Session: Error while trying to free lock for 当试图免费锁时发生错误'.$this->_lock_key);
				return FALSE;
			}

			$this->_lock_key = NULL;
			$this->_lock = FALSE;
		}

		return TRUE;
	}

}
