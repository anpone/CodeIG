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
 * CodeIgniter File Caching Class
 * CodeIgniter文件缓存类
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		EllisLab Dev Team
 * @link
 */
class CI_Cache_file extends CI_Driver {

	/**
	 * Directory in which to save cache files
	 * 目录来保存缓存文件
	 * @var string
	 */
	protected $_cache_path;

	/**
	 * Initialize file-based cache
	 * 初始化文件的缓存
	 * @return	void
	 */
	public function __construct()
	{
		$CI =& get_instance();
		$CI->load->helper('file');
		$path = $CI->config->item('cache_path');
		$this->_cache_path = ($path === '') ? APPPATH.'cache/' : $path;
	}

	// ------------------------------------------------------------------------

	/**
	 * Fetch from cache
	 * 从缓存中获取
	 * @param	string	$id	Cache ID  缓存ID
	 * @return	mixed	Data on success, FALSE on failure  数据成功;如果执行失败将返回FALSE
	 */
	public function get($id)
	{
		$data = $this->_get($id);
		return is_array($data) ? $data['data'] : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Save into cache
	 * 保存到缓存
	 * @param	string	$id	Cache ID  缓存ID
	 * @param	mixed	$data	Data to store  数据存储
	 * @param	int	$ttl	Time to live in seconds 时间住在秒
	 * @param	bool	$raw	Whether to store the raw value (unused) 是否存储原始值(未使用)
	 * @return	bool	TRUE on success, FALSE on failure 真为成功,假为失败
	 */
	public function save($id, $data, $ttl = 60, $raw = FALSE)
	{
		$contents = array(
			'time'		=> time(),
			'ttl'		=> $ttl,
			'data'		=> $data
		);

		if (write_file($this->_cache_path.$id, serialize($contents)))
		{
			chmod($this->_cache_path.$id, 0640);
			return TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Delete from Cache
	 * 从缓存中删除
	 * @param	mixed	unique identifier of item in cache 缓存项的唯一标识符
	 * @return	bool	true on success/false on failure 真正的成功/失败将返回false
	 */
	public function delete($id)
	{
		return file_exists($this->_cache_path.$id) ? unlink($this->_cache_path.$id) : FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Increment a raw value
	 * 增加一个原始值
	 * @param	string	$id	Cache ID  缓存ID
	 * @param	int	$offset	Step/value to add 一步/值增加
	 * @return	New value on success, FALSE on failure  新值成功,;如果执行失败将返回FALSE
	 */
	public function increment($id, $offset = 1)
	{
		$data = $this->_get($id);

		if ($data === FALSE)
		{
			$data = array('data' => 0, 'ttl' => 60);
		}
		elseif ( ! is_int($data['data']))
		{
			return FALSE;
		}

		$new_value = $data['data'] + $offset;
		return $this->save($id, $new_value, $data['ttl'])
			? $new_value
			: FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Decrement a raw value
	 * 递减一个原始值
	 * @param	string	$id	Cache ID 缓存ID
	 * @param	int	$offset	Step/value to reduce by 一步/值减少
	 * @return	New value on success, FALSE on failure   新值成功,;如果执行失败将返回FALSE
	 */
	public function decrement($id, $offset = 1)
	{
		$data = $this->_get($id);

		if ($data === FALSE)
		{
			$data = array('data' => 0, 'ttl' => 60);
		}
		elseif ( ! is_int($data['data']))
		{
			return FALSE;
		}

		$new_value = $data['data'] - $offset;
		return $this->save($id, $new_value, $data['ttl'])
			? $new_value
			: FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Clean the Cache
	 * 清理缓存
	 * @return	bool	false on failure/true on success false失败/真正的成功
	 */
	public function clean()
	{
		return delete_files($this->_cache_path, FALSE, TRUE);
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 * 缓存信息
	 * Not supported by file-based caching
	 * 不支持的文件缓存
	 * @param	string	user/filehits  用户filehits
	 * @return	mixed	FALSE
	 */
	public function cache_info($type = NULL)
	{
		return get_dir_file_info($this->_cache_path);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get Cache Metadata
	 * 获取缓存元数据
	 * @param	mixed	key to get cache metadata on 让缓存元数据的关键
	 * @return	mixed	FALSE on failure, array on success. 假为失败,数组成功
	 */
	public function get_metadata($id)
	{
		if ( ! file_exists($this->_cache_path.$id))
		{
			return FALSE;
		}

		$data = unserialize(file_get_contents($this->_cache_path.$id));

		if (is_array($data))
		{
			$mtime = filemtime($this->_cache_path.$id);

			if ( ! isset($data['ttl']))
			{
				return FALSE;
			}

			return array(
				'expire' => $mtime + $data['ttl'],
				'mtime'	 => $mtime
			);
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Is supported
	 * 是否支持
	 * In the file driver, check to see that the cache directory is indeed writable
	 * 在文件驱动程序,查看缓存目录确实是可写的
	 * @return	bool
	 */
	public function is_supported()
	{
		return is_really_writable($this->_cache_path);
	}

	// ------------------------------------------------------------------------

	/**
	 * Get all data
	 * 得到所有的数据
	 * Internal method to get all the relevant data about a cache item
	 * 内部方法来获取所有相关数据缓存项
	 * @param	string	$id	Cache ID 缓存ID
	 * @return	mixed	Data array on success or FALSE on failure 数据数组成功;如果执行失败将返回FALSE
	 */
	protected function _get($id)
	{
		if ( ! is_file($this->_cache_path.$id))
		{
			return FALSE;
		}

		$data = unserialize(file_get_contents($this->_cache_path.$id));

		if ($data['ttl'] > 0 && time() > $data['time'] + $data['ttl'])
		{
			unlink($this->_cache_path.$id);
			return FALSE;
		}

		return $data;
	}

}
