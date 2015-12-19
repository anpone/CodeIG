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
 * CodeIgniter Caching Class
 * CodeIgniter缓存类
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		EllisLab Dev Team
 * @link
 */
class CI_Cache extends CI_Driver_Library {

	/**
	 * Valid cache drivers
	 * 有效的缓存的drivers
	 * @var array
	 */
	protected $valid_drivers = array(
		'apc',
		'dummy',
		'file',
		'memcached',
		'redis',
		'wincache'
	);

	/**
	 * Path of cache files (if file-based cache)
	 * 缓存文件的路径(如果文件的缓存)
	 * @var string
	 */
	protected $_cache_path = NULL;

	/**
	 * Reference to the driver
	 * 参考driver
	 * @var mixed
	 */
	protected $_adapter = 'dummy';

	/**
	 * Fallback driver
	 * 回滚驱动程序
	 * @var string
	 */
	protected $_backup_driver = 'dummy';

	/**
	 * Cache key prefix
	 * 缓存键前缀
	 * @var	string
	 */
	public $key_prefix = '';

	/**
	 * Constructor
	 * 构造函数
	 * Initialize class properties based on the configuration array.
	 * 基于配置初始化类属性数组。
	 * @param	array	$config = array()
	 * @return	void
	 */
	public function __construct($config = array())
	{
		isset($config['adapter']) && $this->_adapter = $config['adapter'];
		isset($config['backup']) && $this->_backup_driver = $config['backup'];
		isset($config['key_prefix']) && $this->key_prefix = $config['key_prefix'];

		// If the specified adapter isn't available, check the backup. 如果指定的适配器并不可用,检查备份。
		if ( ! $this->is_supported($this->_adapter))
		{
			if ( ! $this->is_supported($this->_backup_driver))
			{
				// Backup isn't supported either. Default to 'Dummy' driver. 备份不受支持。默认为“假”的driver。
				log_message('error', 'Cache adapter "'.$this->_adapter.'" and backup "'.$this->_backup_driver.'" are both unavailable. Cache is now using "Dummy" adapter.');
				$this->_adapter = 'dummy';
			}
			else
			{
				// Backup is supported. Set it to primary. 支持备份。将其设置为主要。
				log_message('debug', 'Cache adapter "'.$this->_adapter.'" is unavailable. Falling back to "'.$this->_backup_driver.'" backup adapter.');
				$this->_adapter = $this->_backup_driver;
			}
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Get
	 *
	 * Look for a value in the cache. If it exists, return the data 在缓存中查找一个值。如果它存在,返回数据
	 * if not, return FALSE 如果不,返回假
	 *
	 * @param	string	$id
	 * @return	mixed	value matching $id or FALSE on failure 值匹配id;如果执行失败将返回FALSE
	 */
	public function get($id)
	{
		return $this->{$this->_adapter}->get($this->key_prefix.$id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Save
	 * 缓存保存
	 * @param	string	$id	Cache ID  缓存ID
	 * @param	mixed	$data	Data to store 数据存储
	 * @param	int	$ttl	Cache TTL (in seconds) 隐藏TTL(用秒)
	 * @param	bool	$raw	Whether to store the raw value 是否存储原始值
	 * @return	bool	TRUE on success, FALSE on failure 真为成功,假为失败
	 */
	public function save($id, $data, $ttl = 60, $raw = FALSE)
	{
		return $this->{$this->_adapter}->save($this->key_prefix.$id, $data, $ttl, $raw);
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 * 从缓存中删除
	 * @param	string	$id	Cache ID  缓存ID
	 * @return	bool	TRUE on success, FALSE on failure 真为成功,假为失败
	 */
	public function delete($id)
	{
		return $this->{$this->_adapter}->delete($this->key_prefix.$id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Increment a raw value
	 * 增加原始值
	 * @param	string	$id	Cache ID  缓存ID
	 * @param	int	$offset	Step/value to add  一步/值增加   步进
	 * @return	mixed	New value on success or FALSE on failure 新值成功;如果执行失败将返回FALSE
	 */
	public function increment($id, $offset = 1)
	{
		return $this->{$this->_adapter}->increment($this->key_prefix.$id, $offset);
	}

	// ------------------------------------------------------------------------

	/**
	 * Decrement a raw value
	 * 递减一个原始值
	 * @param	string	$id	Cache ID  缓存ID
	 * 	 * @param	int	$offset	Step/value to reduce by 一步/值减少   步进
	 * @return	mixed	New value on success or FALSE on failure  新值成功;如果执行失败将返回FALSE
	 */
	public function decrement($id, $offset = 1)
	{
		return $this->{$this->_adapter}->decrement($this->key_prefix.$id, $offset);
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the cache
	 * 清理缓存
	 * @return	bool	TRUE on success, FALSE on failure  真为成功,假为失败
	 */
	public function clean()
	{
		return $this->{$this->_adapter}->clean();
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 * 缓存信息
	 * @param	string	$type = 'user'	user/filehits 用户filehits
	 * @return	mixed	array containing cache info on success OR FALSE on failure 数组包含缓存信息成功;如果执行失败将返回FALSE
	 */
	public function cache_info($type = 'user')
	{
		return $this->{$this->_adapter}->cache_info($type);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata
	 * 获取缓存元数据
	 * @param	string	$id	key to get cache metadata on 让缓存元数据的关键
	 * @return	mixed	cache item metadata 缓存项的元数据
	 */
	public function get_metadata($id)
	{
		return $this->{$this->_adapter}->get_metadata($this->key_prefix.$id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Is the requested driver supported in this environment?
	 * 要求driver在这环境中支持吗?
	 * @param	string	$driver	The driver to test 驱动程序测试
	 * @return	array
	 */
	public function is_supported($driver)
	{
		static $support;

		if ( ! isset($support, $support[$driver]))
		{
			$support[$driver] = $this->{$driver}->is_supported();
		}

		return $support[$driver];
	}
}
