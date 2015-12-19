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
 * CodeIgniter Redis Caching Class
 * CodeIgniter Redis缓存类
 * @package	   CodeIgniter
 * @subpackage Libraries
 * @category   Core
 * @author	   Anton Lindqvist <anton@qvister.se>
 * @link
 */
class CI_Cache_redis extends CI_Driver
{
	/**
	 * Default config
	 * 缺省设置 
	 * @static
	 * @var	array
	 */
	protected static $_default_config = array(
		'socket_type' => 'tcp',
		'host' => '127.0.0.1',
		'password' => NULL,
		'port' => 6379,
		'timeout' => 0
	);

	/**
	 * Redis connection
	 * Redis连接
	 * @var	Redis
	 */
	protected $_redis;

	/**
	 * An internal cache for storing keys of serialized values.
	 * 内部缓存来存储键序列化的值。
	 * @var	array
	 */
	protected $_serialized = array();

	// ------------------------------------------------------------------------

	/**
	 * Class constructor
	 * 构造函数 类构造器 
	 * Setup Redis
	 * 设置 Redis
	 * Loads Redis config file if present. Will halt execution
	 * if a Redis connection can't be established.
	 * 如果存在加载复述,配置文件。将停止执行如果复述,无法建立连接。
	 * @return	void
	 * @see		Redis::connect()
	 */
	public function __construct()
	{
		$config = array();
		$CI =& get_instance();

		if ($CI->config->load('redis', TRUE, TRUE))
		{
			$config = $CI->config->item('redis');
		}

		$config = array_merge(self::$_default_config, $config);
		$this->_redis = new Redis();

		try
		{
			if ($config['socket_type'] === 'unix')
			{
				$success = $this->_redis->connect($config['socket']);
			}
			else // tcp socket
			{
				$success = $this->_redis->connect($config['host'], $config['port'], $config['timeout']);
			}

			if ( ! $success)
			{
				log_message('error', 'Cache: Redis connection failed. Check your configuration.复述,连接失败。检查你的配置');
			}

			if (isset($config['password']) && ! $this->_redis->auth($config['password']))
			{
				log_message('error', 'Cache: Redis authentication failed.复述,身份验证失败');
			}
		}
		catch (RedisException $e)
		{
			log_message('error', 'Cache: Redis connection refused复述,拒绝连接 ('.$e->getMessage().')');
		}

		// Initialize the index of serialized values. 初始化索引序列化的值。
		$serialized = $this->_redis->sMembers('_ci_redis_serialized');
		empty($serialized) OR $this->_serialized = array_flip($serialized);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get cache
	 * 获取缓存
	 * @param	string	Cache ID
	 * @return	mixed
	 */
	public function get($key)
	{
		$value = $this->_redis->get($key);

		if ($value !== FALSE && isset($this->_serialized[$key]))
		{
			return unserialize($value);
		}

		return $value;
	}

	// ------------------------------------------------------------------------

	/**
	 * Save cache
	 * 保存缓存
	 * @param	string	$id	Cache ID  缓存ID
	 * @param	mixed	$data	Data to save数据保存 
	 * @param	int	$ttl	Time to live in seconds  时间在秒
	 * @param	bool	$raw	Whether to store the raw value (unused) 是否存储原始值(未使用)
	 * @return	bool	TRUE on success, FALSE on failure  真为成功,假为失败
	 */
	public function save($id, $data, $ttl = 60, $raw = FALSE)
	{
		if (is_array($data) OR is_object($data))
		{
			if ( ! $this->_redis->sIsMember('_ci_redis_serialized', $id) && ! $this->_redis->sAdd('_ci_redis_serialized', $id))
			{
				return FALSE;
			}

			isset($this->_serialized[$id]) OR $this->_serialized[$id] = TRUE;
			$data = serialize($data);
		}
		elseif (isset($this->_serialized[$id]))
		{
			$this->_serialized[$id] = NULL;
			$this->_redis->sRemove('_ci_redis_serialized', $id);
		}

		return $this->_redis->set($id, $data, $ttl);
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from cache
	 * 从缓存中删除
	 * @param	string	Cache key 缓存键
	 * @return	bool
	 */
	public function delete($key)
	{
		if ($this->_redis->delete($key) !== 1)
		{
			return FALSE;
		}

		if (isset($this->_serialized[$key]))
		{
			$this->_serialized[$key] = NULL;
			$this->_redis->sRemove('_ci_redis_serialized', $key);
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Increment a raw value
	 * 增加一个原始值
	 * @param	string	$id	Cache ID  缓存ID
	 * @param	int	$offset	Step/value to add 一步/值增加
	 * @return	mixed	New value on success or FALSE on failure 新值成功;如果执行失败将返回FALSE
	 */
	public function increment($id, $offset = 1)
	{
		return $this->_redis->incr($id, $offset);
	}

	// ------------------------------------------------------------------------

	/**
	 * Decrement a raw value
	 * 递减一个原始值
	 * @param	string	$id	Cache ID 缓存ID
	 * @param	int	$offset	Step/value to reduce by 一步/值减少
	 * @return	mixed	New value on success or FALSE on failure 新值成功;如果执行失败将返回FALSE
	 */
	public function decrement($id, $offset = 1)
	{
		return $this->_redis->decr($id, $offset);
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean cache
	 * 清空缓存
	 * @return	bool
	 * @see		Redis::flushDB() 冲洗DB
	 */
	public function clean()
	{
		return $this->_redis->flushDB();
	}

	// ------------------------------------------------------------------------

	/**
	 * Get cache driver info
	 * 得到缓存的driver信息
	 * @param	string	Not supported in Redis. 不支持的
	 *			Only included in order to offer a consistent cache API.
	 *			只包括以提供一个一致的缓存API。
	 * @return	array
	 * @see		Redis::info()
	 */
	public function cache_info($type = NULL)
	{
		return $this->_redis->info();
	}

	// ------------------------------------------------------------------------

	/**
	 * Get cache metadata
	 * 获取缓存元数据
	 * @param	string	Cache key 缓存键
	 * @return	array
	 */
	public function get_metadata($key)
	{
		$value = $this->get($key);

		if ($value !== FALSE)
		{
			return array(
				'expire' => time() + $this->_redis->ttl($key),
				'data' => $value
			);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Check if Redis driver is supported
	 * 检查Redis,driver是否支持
	 * @return	bool
	 */
	public function is_supported()
	{
		return extension_loaded('redis');
	}

	// ------------------------------------------------------------------------

	/**
	 * Class destructor
	 * 类的析构函数
	 * Closes the connection to Redis if present.
	 * 关闭连接Redis,如果存在。
	 * @return	void
	 */
	public function __destruct()
	{
		if ($this->_redis)
		{
			$this->_redis->close();
		}
	}
}
