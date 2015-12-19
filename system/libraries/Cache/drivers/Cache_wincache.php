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
 * CodeIgniter Wincache Caching Class
 * CodeIgniter Wincache缓存类
 * Read more about Wincache functions here: 阅读更多关于Wincache函数:
 * http://www.php.net/manual/en/ref.wincache.php
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		Mike Murkovic
 * @link
 */
class CI_Cache_wincache extends CI_Driver {

	/**
	 * Get
	 * 获取
	 * Look for a value in the cache. If it exists, return the data, 在缓存中查找一个值。如果它存在,返回数据,
	 * if not, return FALSE
	 *
	 * @param	string	$id	Cache Ide 缓存IED
	 * @return	mixed	Value that is stored/FALSE on failure 存储的值;如果执行失败将返回FALSE
	 */
	public function get($id)
	{
		$success = FALSE;
		$data = wincache_ucache_get($id, $success);

		// Success returned by reference from wincache_ucache_get() 成功返回的引用wincache_ucache_get()
		return ($success) ? $data : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Save
	 * 缓存保存
	 * @param	string	$id	Cache ID 缓存ID
	 * @param	mixed	$data	Data to store  数据存储
	 * @param	int	$ttl	Time to live (in seconds)  生存时间(以秒为单位)
	 * @param	bool	$raw	Whether to store the raw value (unused) 是否存储原始值(未使用)
	 * @return	bool	true on success/false on failure  真正的成功/失败将返回false
	 */
	public function save($id, $data, $ttl = 60, $raw = FALSE)
	{
		return wincache_ucache_set($id, $data, $ttl);
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 * 从缓存中删除
	 * @param	mixed	unique identifier of the item in the cache  在缓存中项目的唯一标识符
	 * @return	bool	true on success/false on failure  真正的成功/失败将返回false
	 */
	public function delete($id)
	{
		return wincache_ucache_delete($id);
	}

	// ------------------------------------------------------------------------

	/**
	 * Increment a raw value
	 * 增加一个原始值
	 * @param	string	$id	Cache ID 缓存ID
	 * @param	int	$offset	Step/value to add 一步/值增加
	 * @return	mixed	New value on success or FALSE on failure 新值成功;如果执行失败将返回FALSE
	 */
	public function increment($id, $offset = 1)
	{
		$success = FALSE;
		$value = wincache_ucache_inc($id, $offset, $success);

		return ($success === TRUE) ? $value : FALSE;
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
		$success = FALSE;
		$value = wincache_ucache_dec($id, $offset, $success);

		return ($success === TRUE) ? $value : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the cache
	 * 清理缓存
	 * @return	bool	false on failure/true on success  false失败/真 成功
	 */
	public function clean()
	{
		return wincache_ucache_clear();
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 * 缓存信息
	 * @return	mixed	array on success, false on failure 数组成功;如果执行失败将返回false
	 */
	 public function cache_info()
	 {
		 return wincache_ucache_info(TRUE);
	 }

	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata
	 * 获取缓存元数据
	 * @param	mixed	key to get cache metadata on 让缓存元数据的关键
	 * @return	mixed	array on success/false on failure 数组成功/;如果执行失败将返回false
	 */
	public function get_metadata($id)
	{
		if ($stored = wincache_ucache_info(FALSE, $id))
		{
			$age = $stored['ucache_entries'][1]['age_seconds'];
			$ttl = $stored['ucache_entries'][1]['ttl_seconds'];
			$hitcount = $stored['ucache_entries'][1]['hitcount'];

			return array(
				'expire'	=> $ttl - $age,
				'hitcount'	=> $hitcount,
				'age'		=> $age,
				'ttl'		=> $ttl
			);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * is_supported()
	 * 是否被支持
	 * Check to see if WinCache is available on this system, bail if it isn't.
	 * 检查WinCache可用在这个系统上,如果不是保释。
	 * @return	bool
	 */
	public function is_supported()
	{
		if ( ! extension_loaded('wincache') OR ! ini_get('wincache.ucenabled'))
		{
			log_message('debug', 'The Wincache PHP extension must be loaded to use Wincache Cache.必须加载Wincache PHP扩展使用Wincache缓存');
			return FALSE;
		}

		return TRUE;
	}

}
