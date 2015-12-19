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
 * @since	Version 2.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Memcached Caching Class
 * CodeIgniter Memcached缓存类
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		EllisLab Dev Team
 * @link
 */
class CI_Cache_memcached extends CI_Driver {

	/**
	 * Holds the memcached object
	 * 持有memcached对象
	 * @var object
	 */
	protected $_memcached;

	/**
	 * Memcached configuration
	 * Memcached的配置
	 * @var array
	 */
	protected $_memcache_conf = array(
		'default' => array(
			'host'		=> '127.0.0.1',
			'port'		=> 11211,
			'weight'	=> 1
		)
	);

	// ------------------------------------------------------------------------

	/**
	 * Class constructor
	 * 构造函数 类构造器 
	 * Setup Memcache(d)
	 * 设置Memcache
	 * @return	void
	 */
	public function __construct()
	{
		// Try to load memcached server info from the config file. 试图加载memcached服务器配置文件的信息。
		$CI =& get_instance();
		$defaults = $this->_memcache_conf['default'];

		if ($CI->config->load('memcached', TRUE, TRUE))
		{
			if (is_array($CI->config->config['memcached']))
			{
				$this->_memcache_conf = array();

				foreach ($CI->config->config['memcached'] as $name => $conf)
				{
					$this->_memcache_conf[$name] = $conf;
				}
			}
		}

		if (class_exists('Memcached', FALSE))
		{
			$this->_memcached = new Memcached();
		}
		elseif (class_exists('Memcache', FALSE))
		{
			$this->_memcached = new Memcache();
		}
		else
		{
			log_message('error', 'Cache: Failed to create Memcache(d) object未能创建Memcache(d)对象; extension not loaded?扩展不加载?');
		}

		foreach ($this->_memcache_conf as $cache_server)
		{
			isset($cache_server['hostname']) OR $cache_server['hostname'] = $defaults['host'];
			isset($cache_server['port']) OR $cache_server['port'] = $defaults['port'];
			isset($cache_server['weight']) OR $cache_server['weight'] = $defaults['weight'];

			if (get_class($this->_memcached) === 'Memcache')
			{
				// Third parameter is persistance and defaults to TRUE. 第三个参数是持久性和默认值为TRUE。
				$this->_memcached->addServer(
					$cache_server['hostname'],
					$cache_server['port'],
					TRUE,
					$cache_server['weight']
				);
			}
			else
			{
				$this->_memcached->addServer(
					$cache_server['hostname'],
					$cache_server['port'],
					$cache_server['weight']
				);
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Fetch from cache
	 * 从缓存中获取
	 * @param	string	$id	Cache ID 缓存ID
	 * @return	mixed	Data on success, FALSE on failure 数据成功;如果执行失败将返回FALSE
	 */
	public function get($id)
	{
		$data = $this->_memcached->get($id);

		return is_array($data) ? $data[0] : $data;
	}

	// ------------------------------------------------------------------------

	/**
	 * Save
	 * 保存
	 * @param	string	$id	Cache ID  缓存ID
	 * @param	mixed	$data	Data being cached  数据缓存
	 * @param	int	$ttl	Time to live  存活时间
	 * @param	bool	$raw	Whether to store the raw value 是否存储的原始值
	 * @return	bool	TRUE on success, FALSE on failure 真为成功,假为失败
	 */
	public function save($id, $data, $ttl = 60, $raw = FALSE)
	{
		if ($raw !== TRUE)
		{
			$data = array($data, time(), $ttl);
		}

		if (get_class($this->_memcached) === 'Memcached')
		{
			return $this->_memcached->set($id, $data, $ttl);
		}
		elseif (get_class($this->_memcached) === 'Memcache')
		{
			return $this->_memcached->set($id, $data, 0, $ttl);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 * 从缓存中删除
	 * @param	mixed	key to be deleted. 被删除的关键。
	 * @return	bool	true on success, false on failure 真正的成功,;如果执行失败将返回false
	 */
	public function delete($id)
	{
		return $this->_memcached->delete($id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Increment a raw value
	 * 增加一个原始值
	 * @param	string	$id	Cache ID  缓存ID
	 * @param	int	$offset	Step/value to add  一步/值增加
	 * @return	mixed	New value on success or FALSE on failure 新值成功;如果执行失败将返回FALSE
	 */
	public function increment($id, $offset = 1)
	{
		return $this->_memcached->increment($id, $offset);
	}

	// ------------------------------------------------------------------------

	/**
	 * Decrement a raw value
	 * 递减一个原始值
	 * @param	string	$id	Cache ID  缓存ID
	 * @param	int	$offset	Step/value to reduce by  一步/值减少
	 * @return	mixed	New value on success or FALSE on failure  新值成功;如果执行失败将返回FALSE
	 */
	public function decrement($id, $offset = 1)
	{
		return $this->_memcached->decrement($id, $offset);
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the Cache
	 * 清理缓存
	 * @return	bool	false on failure/true on success  false失败/真正的成功
	 */
	public function clean()
	{
		return $this->_memcached->flush();
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 * 缓存信息
	 * @return	mixed	array on success, false on failure 数组成功;如果执行失败将返回false
	 */
	public function cache_info()
	{
		return $this->_memcached->getStats();
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata
	 * 获取缓存元数据
	 * @param	mixed	key to get cache metadata on  让缓存元数据的关键
	 * @return	mixed	FALSE on failure, array on success.  假为失败,数组为成功
	 */
	public function get_metadata($id)
	{
		$stored = $this->_memcached->get($id);

		if (count($stored) !== 3)
		{
			return FALSE;
		}

		list($data, $time, $ttl) = $stored;

		return array(
			'expire'	=> $time + $ttl,
			'mtime'		=> $time,
			'data'		=> $data
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * Is supported
	 * 是否支持
	 * Returns FALSE if memcached is not supported on the system.
	 * If it is, we setup the memcached object & return TRUE
	 * 返回FALSE如果memcached不支持系统  如果是,我们安装memcached对象&返回TRUE
	 * @return	bool 
	 */
	public function is_supported()
	{
		return (extension_loaded('memcached') OR extension_loaded('memcache'));
	}
}
