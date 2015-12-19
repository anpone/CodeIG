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
 * CodeIgniter APC Caching Class
 * CodeIgniter APC缓存类
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		EllisLab Dev Team
 * @link
 */
class CI_Cache_apc extends CI_Driver {

	/**
	 * Get
	 * 获得
	 * Look for a value in the cache. If it exists, return the data 在缓存中查找一个值。如果它存在,返回数据
	 * if not, return FALSE 如果没有,则返回FALSE
	 *
	 * @param	string
	 * @return	mixed	value that is stored存储的值/FALSE on failure如果执行失败将返回FALSE
	 */
	public function get($id)
	{
		$success = FALSE;
		$data = apc_fetch($id, $success);

		if ($success === TRUE)
		{
			return is_array($data)
				? unserialize($data[0])
				: $data;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Save
	 * 缓存保存
	 * @param	string	$id	Cache ID  缓存ID
	 * @param	mixed	$data	Data to store 数据存储
	 * @param	int	$ttol	Length of time (in seconds) to cache the data 时间长度(以秒为单位)缓存数据
	 * @param	bool	$raw	Whether to store the raw value 是否存储的原始值
	 * @return	bool	TRUE on success, FALSE on failure 真为成功,假为失败
	 */
	public function save($id, $data, $ttl = 60, $raw = FALSE)
	{
		$ttl = (int) $ttl;

		return apc_store(
			$id,
			($raw === TRUE ? $data : array(serialize($data), time(), $ttl)),
			$ttl
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 * 从缓存中删除
	 * @param	mixed	unique identifier of the item in the cache 在缓存中项目的唯一标识符
	 * @return	bool	true on success真正的成功/false on failure 如果执行失败将返回false
	 */
	public function delete($id)
	{
		return apc_delete($id);
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
		return apc_inc($id, $offset);
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
		return apc_dec($id, $offset);
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the cache
	 * 清理缓存
	 * @return	bool	false on failure如果执行失败将返回false/true on success真为成功
	 */
	public function clean()
	{
		return apc_clear_cache('user');
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 * 缓存信息
	 * @param	string	user/filehits
	 * @return	mixed	array on success, false on failure 数组成功;如果执行失败将返回false
	 */
	 public function cache_info($type = NULL)
	 {
		 return apc_cache_info($type);
	 }

	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata
	 * 获取缓存元数据
	 * @param	mixed	key to get cache metadata on 让缓存元数据的关键
	 * @return	mixed	array on success数组成功/false on failure  如果执行失败将返回false
	 */
	public function get_metadata($id)
	{
		$success = FALSE;
		$stored = apc_fetch($id, $success);

		if ($success === FALSE OR count($stored) !== 3)
		{
			return FALSE;
		}

		list($data, $time, $ttl) = $stored;

		return array(
			'expire'	=> $time + $ttl,
			'mtime'		=> $time,
			'data'		=> unserialize($data)
		);
	}

	// ------------------------------------------------------------------------

	/**
	 * is_supported()
	 * 是否支持
	 * Check to see if APC is available on this system, bail if it isn't.
	 * 检查APC可以在这个系统,如果它不是保释。
	 * @return	bool
	 */
	public function is_supported()
	{
		if ( ! extension_loaded('apc') OR ! ini_get('apc.enabled'))
		{
			log_message('debug', 'The APC PHP extension must be loaded to use APC Cache.APC PHP扩展必须加载使用APC缓存');
			return FALSE;
		}

		return TRUE;
	}

}
