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
 * CodeIgniter Dummy Caching Class
 * CodeIgniter虚拟缓存类
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		EllisLab Dev Team
 * @link
 */
class CI_Cache_dummy extends CI_Driver {

	/**
	 * Get
	 * 获得
	 * Since this is the dummy class, it's always going to return FALSE.
	 * 因为这是虚拟类,它总是返回假。
	 * @param	string
	 * @return	bool	FALSE
	 */
	public function get($id)
	{
		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Save
	 * 缓存保存
	 * @param	string	Unique Key  唯一键
	 * @param	mixed	Data to store 要存储的数据
	 * @param	int	Length of time (in seconds) to cache the data 时间长度(以秒为单位)缓存数据
	 * @param	bool	Whether to store the raw value 是否存储的原始值
	 * @return	bool	TRUE, Simulating success 真的,模拟成功
	 */
	public function save($id, $data, $ttl = 60, $raw = FALSE)
	{
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 * 从缓存中删除
	 * @param	mixed	unique identifier of the item in the cache 在缓存中项目的唯一标识符
	 * @return	bool	TRUE, simulating success 真的,模拟成功
	 */
	public function delete($id)
	{
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Increment a raw value
	 * 增加一个原始值
	 * @param	string	$id	Cache ID  缓存ID
	 * @param	int	$offset	Step/value to add  一步/值增加
	 * @return	mixed	New value on success or FALSE on failure  新值成功;如果执行失败将返回FALSE
	 */
	public function increment($id, $offset = 1)
	{
		return TRUE;
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
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the cache
	 * 清理缓存
	 * @return	bool	TRUE, simulating success 真的,模拟成功
	 */
	public function clean()
	{
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 * 缓存信息
	 * @param	string	user/filehits
	 * @return	bool	FALSE
	 */
	 public function cache_info($type = NULL)
	 {
		 return FALSE;
	 }

	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata
	 * 获取缓存元数据
	 * @param	mixed	key to get cache metadata on 让缓存元数据的关键
	 * @return	bool	FALSE
	 */
	public function get_metadata($id)
	{
		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Is this caching driver supported on the system? 这是缓存系统上的驱动程序支持吗?
	 * Of course this one is. 当然这个是。
	 *
	 * @return	bool	TRUE
	 */
	public function is_supported()
	{
		return TRUE;
	}

}
