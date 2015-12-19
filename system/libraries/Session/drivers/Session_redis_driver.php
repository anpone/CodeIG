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
 * CodeIgniter Session Redis Driver
 * CodeIgniter Redis   session驱动
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author	Andrey Andreev
 * @link	http://codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session_redis_driver extends CI_Session_driver implements SessionHandlerInterface {

	/**
	 * phpRedis instance
	 * phpRedis实例
	 * @var	resource
	 */
	protected $_redis;

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
	 * @param	array	$params	Configuration parameters 配置参数
	 * @return	void
	 */
	public function __construct(&$params)
	{
		parent::__construct($params);

		if (empty($this->_config['save_path']))
		{
			log_message('error', 'Session: No Redis save path configured.');
		}
		elseif (preg_match('#(?:tcp://)?([^:?]+)(?:\:(\d+))?(\?.+)?#', $this->_config['save_path'], $matches))
		{
			isset($matches[3]) OR $matches[3] = ''; // Just to avoid undefined index notices below 为了避免未定义的指数通知如下
			$this->_config['save_path'] = array(
				'host' => $matches[1],
				'port' => empty($matches[2]) ? NULL : $matches[2],
				'password' => preg_match('#auth=([^\s&]+)#', $matches[3], $match) ? $match[1] : NULL,
				'database' => preg_match('#database=(\d+)#', $matches[3], $match) ? (int) $match[1] : NULL,
				'timeout' => preg_match('#timeout=(\d+\.\d+)#', $matches[3], $match) ? (float) $match[1] : NULL
			);

			preg_match('#prefix=([^\s&]+)#', $matches[3], $match) && $this->_key_prefix = $match[1];
		}
		else
		{
			log_message('error', 'Session: Invalid Redis save path format: '.$this->_config['save_path']);
		}

		if ($this->_config['match_ip'] === TRUE)
		{
			$this->_key_prefix .= $_SERVER['REMOTE_ADDR'].':';
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Open
	 * 打开
	 * Sanitizes save_path and initializes connection.
	 * 清理save_path并初始化连接
	 * @param	string	$save_path	Server path  服务器路径
	 * @param	string	$name		Session cookie name, unused
	 * @return	bool
	 */
	public function open($save_path, $name)
	{
		if (empty($this->_config['save_path']))
		{
			return FALSE;
		}

		$redis = new Redis();
		if ( ! $redis->connect($this->_config['save_path']['host'], $this->_config['save_path']['port'], $this->_config['save_path']['timeout']))
		{
			log_message('error', 'Session: Unable to connect to Redis with the configured settings 无法连接到复述,用配置的设置.');
		}
		elseif (isset($this->_config['save_path']['password']) && ! $redis->auth($this->_config['save_path']['password']))
		{
			log_message('error', 'Session: Unable to authenticate to Redis instance 无法验证复述实例.');
		}
		elseif (isset($this->_config['save_path']['database']) && ! $redis->select($this->_config['save_path']['database']))
		{
			log_message('error', 'Session: Unable to select Redis database with index 无法选择复述,数据库和索引'.$this->_config['save_path']['database']);
		}
		else
		{
			$this->_redis = $redis;
			return TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Read
	 *
	 * Reads session data and acquires a lock
	 * 读取会话数据并获得一个锁
	 * @param	string	$session_id	Session ID
	 * @return	string	Serialized session data
	 */
	public function read($session_id)
	{
		if (isset($this->_redis) && $this->_get_lock($session_id))
		{
			// Needed by write() to detect session_regenerate_id() calls  需要写()来检测session_regenerate_id()调用
			$this->_session_id = $session_id;

			$session_data = (string) $this->_redis->get($this->_key_prefix.$session_id);
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
		if ( ! isset($this->_redis))
		{
			return FALSE;
		}
		// Was the ID regenerated? ID再生?
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
			$this->_redis->setTimeout($this->_lock_key, 300);
			if ($this->_fingerprint !== ($fingerprint = md5($session_data)))
			{
				if ($this->_redis->set($this->_key_prefix.$session_id, $session_data, $this->_config['expiration']))
				{
					$this->_fingerprint = $fingerprint;
					return TRUE;
				}

				return FALSE;
			}

			return $this->_redis->setTimeout($this->_key_prefix.$session_id, $this->_config['expiration']);
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
		if (isset($this->_redis))
		{
			try {
				if ($this->_redis->ping() === '+PONG')
				{
					isset($this->_lock_key) && $this->_redis->delete($this->_lock_key);
					if ( ! $this->_redis->close())
					{
						return FALSE;
					}
				}
			}
			catch (RedisException $e)
			{
				log_message('error', 'Session: Got RedisException on close(): '.$e->getMessage());
			}

			$this->_redis = NULL;
			return TRUE;
		}

		return TRUE;
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
		if (isset($this->_redis, $this->_lock_key))
		{
			if (($result = $this->_redis->delete($this->_key_prefix.$session_id)) !== 1)
			{
				log_message('debug', 'Session: Redis::delete() expected to return 1, got '.var_export($result, TRUE).' instead.');
			}

			return $this->_cookie_destroy();
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Garbage Collector
	 * 垃圾回收器
	 * Deletes expired sessions
	 * 删除过期的会话
	 * @param	int 	$maxlifetime	Maximum lifetime of sessions 最大的生命周期
	 * @return	bool
	 */
	public function gc($maxlifetime)
	{
		// Not necessary, Redis takes care of that. 没有必要,复述,照顾。
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
			return $this->_redis->setTimeout($this->_lock_key, 300);
		}

		// 30 attempts to obtain a lock, in case another request already has it 试图获得一个锁,以防另一个请求已经
		$lock_key = $this->_key_prefix.$session_id.':lock';
		$attempt = 0;
		do
		{
			if (($ttl = $this->_redis->ttl($lock_key)) > 0)
			{
				sleep(1);
				continue;
			}

			if ( ! $this->_redis->setex($lock_key, 300, time()))
			{
				log_message('error', 'Session: Error while trying to obtain lock for 误差在试图获得锁'.$this->_key_prefix.$session_id);
				return FALSE;
			}

			$this->_lock_key = $lock_key;
			break;
		}
		while (++$attempt < 30);

		if ($attempt === 30)
		{
			log_message('error', 'Session: Unable to obtain lock for 无法获得锁'.$this->_key_prefix.$session_id.' after 30 attempts, aborting.');
			return FALSE;
		}
		elseif ($ttl === -1)
		{
			log_message('debug', 'Session: Lock for '.$this->_key_prefix.$session_id.' had no TTL, overriding.');
		}

		$this->_lock = TRUE;
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Release lock
	 * 解除锁定
	 * Releases a previously acquired lock
	 * 发布之前获得锁
	 * @return	bool
	 */
	protected function _release_lock()
	{
		if (isset($this->_redis, $this->_lock_key) && $this->_lock)
		{
			if ( ! $this->_redis->delete($this->_lock_key))
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
