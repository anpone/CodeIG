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
 * CodeIgniter Session Files Driver
 * CodeIgniter会话文件驱动程序
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author	Andrey Andreev
 * @link	http://codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session_files_driver extends CI_Session_driver implements SessionHandlerInterface {

	/**
	 * Save path
	 * 保存路径 
	 * @var	string
	 */
	protected $_save_path;

	/**
	 * File handle
	 * 文件句柄
	 * @var	resource
	 */
	protected $_file_handle;

	/**
	 * File name
	 * 文件名
	 * @var	resource
	 */
	protected $_file_path;

	/**
	 * File new flag
	 * 文件新标识
	 * @var	bool
	 */
	protected $_file_new;

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

		if (isset($this->_config['save_path']))
		{
			$this->_config['save_path'] = rtrim($this->_config['save_path'], '/\\');
			ini_set('session.save_path', $this->_config['save_path']);
		}
		else
		{
			$this->_config['save_path'] = rtrim(ini_get('session.save_path'), '/\\');
		}
	}

	// ------------------------------------------------------------------------

	/**
	 * Open
	 *
	 * Sanitizes the save_path directory.
	 * 清理save_path目录
	 * @param	string	$save_path	Path to session files' directory 会话文件的目录路径
	 * @param	string	$name		Session cookie name  会话cookie的名称
	 * @return	bool
	 */
	public function open($save_path, $name)
	{
		if ( ! is_dir($save_path))
		{
			if ( ! mkdir($save_path, 0700, TRUE))
			{
				throw new Exception("Session: Configured save path 配置保存路径'".$this->_config['save_path']."' is not a directory, doesn't exist or cannot be created 不是一个目录,不存在或无法创建.");
			}
		}
		elseif ( ! is_writable($save_path))
		{
			throw new Exception("Session: Configured save path 配置保存路径'".$this->_config['save_path']."' is not writable by the PHP process 不是由PHP写的过程.");
		}

		$this->_config['save_path'] = $save_path;
		$this->_file_path = $this->_config['save_path'].DIRECTORY_SEPARATOR
			.$name // we'll use the session cookie name as a prefix to avoid collisions 我们将使用会话cookie的名称作为前缀,以避免碰撞
			.($this->_config['match_ip'] ? md5($_SERVER['REMOTE_ADDR']) : '');

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
		// This might seem weird, but PHP 5.6 introduces session_reset(),  也许这看起来很奇怪,但PHP 5.6引入了session_reset(),
		// which re-reads session data  重新读取会话数据
		if ($this->_file_handle === NULL)
		{
			// Just using fopen() with 'c+b' mode would be perfect, but it is only
			// available since PHP 5.2.6 and we have to set permissions for new files,
			// so we'd have to hack around this ...
			// 只使用fopen()和“c + b”模式将是完美的,但这只是由于PHP . 5.2.6可用,我们必须设置权限为新文件,所以我们必须闲逛…
			if (($this->_file_new = ! file_exists($this->_file_path.$session_id)) === TRUE)
			{
				if (($this->_file_handle = fopen($this->_file_path.$session_id, 'w+b')) === FALSE)
				{
					log_message('error', "Session: File '".$this->_file_path.$session_id."' doesn't exist and cannot be created 不存在,不能被创建.");
					return FALSE;
				}
			}
			elseif (($this->_file_handle = fopen($this->_file_path.$session_id, 'r+b')) === FALSE)
			{
				log_message('error', "Session: Unable to open file 无法打开文件'".$this->_file_path.$session_id."'.");
				return FALSE;
			}

			if (flock($this->_file_handle, LOCK_EX) === FALSE)
			{
				log_message('error', "Session: Unable to obtain lock for file 无法获得文件锁'".$this->_file_path.$session_id."'.");
				fclose($this->_file_handle);
				$this->_file_handle = NULL;
				return FALSE;
			}

			// Needed by write() to detect session_regenerate_id() calls  需要写()来检测session_regenerate_id()调用
			$this->_session_id = $session_id;

			if ($this->_file_new)
			{
				chmod($this->_file_path.$session_id, 0600);
				$this->_fingerprint = md5('');
				return '';
			}
		}
		else
		{
			rewind($this->_file_handle);
		}

		$session_data = '';
		for ($read = 0, $length = filesize($this->_file_path.$session_id); $read < $length; $read += strlen($buffer))
		{
			if (($buffer = fread($this->_file_handle, $length - $read)) === FALSE)
			{
				break;
			}

			$session_data .= $buffer;
		}

		$this->_fingerprint = md5($session_data);
		return $session_data;
	}

	// ------------------------------------------------------------------------

	/**
	 * Write
	 *
	 * Writes (create / update) session data
	 * 写(创建/更新)会话数据
	 * @param	string	$session_id	Session ID
	 * @param	string	$session_data	Serialized session data 序列化的会话数据
	 * @return	bool
	 */
	public function write($session_id, $session_data)
	{
		// If the two IDs don't match, we have a session_regenerate_id() call  如果两个id不匹配,我们有一个session_regenerate_id()调用
		// and we need to close the old handle and open a new one  我们需要关闭旧处理,打开一个新的
		if ($session_id !== $this->_session_id && ( ! $this->close() OR $this->read($session_id) === FALSE))
		{
			return FALSE;
		}

		if ( ! is_resource($this->_file_handle))
		{
			return FALSE;
		}
		elseif ($this->_fingerprint === md5($session_data))
		{
			return ($this->_file_new)
				? TRUE
				: touch($this->_file_path.$session_id);
		}

		if ( ! $this->_file_new)
		{
			ftruncate($this->_file_handle, 0);
			rewind($this->_file_handle);
		}

		if (($length = strlen($session_data)) > 0)
		{
			for ($written = 0; $written < $length; $written += $result)
			{
				if (($result = fwrite($this->_file_handle, substr($session_data, $written))) === FALSE)
				{
					break;
				}
			}

			if ( ! is_int($result))
			{
				$this->_fingerprint = md5(substr($session_data, 0, $written));
				log_message('error', 'Session: Unable to write data 无法写入数据.');
				return FALSE;
			}
		}

		$this->_fingerprint = md5($session_data);
		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Close
	 *
	 * Releases locks and closes file descriptor.
	 * 释放锁和关闭文件描述符
	 * @return	bool
	 */
	public function close()
	{
		if (is_resource($this->_file_handle))
		{
			flock($this->_file_handle, LOCK_UN);
			fclose($this->_file_handle);

			$this->_file_handle = $this->_file_new = $this->_session_id = NULL;
			return TRUE;
		}

		return TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Destroy
	 *
	 * Destroys the current session.
	 * 破坏了当前会话。
	 * @param	string	$session_id	Session ID
	 * @return	bool
	 */
	public function destroy($session_id)
	{
		if ($this->close())
		{
			return file_exists($this->_file_path.$session_id)
				? (unlink($this->_file_path.$session_id) && $this->_cookie_destroy())
				: TRUE;
		}
		elseif ($this->_file_path !== NULL)
		{
			clearstatcache();
			return file_exists($this->_file_path.$session_id)
				? (unlink($this->_file_path.$session_id) && $this->_cookie_destroy())
				: TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Garbage Collector
	 *
	 * Deletes expired sessions
	 * 删除过期的会话
	 * @param	int 	$maxlifetime	Maximum lifetime of sessions 最大的生命周期
	 * @return	bool
	 */
	public function gc($maxlifetime)
	{
		if ( ! is_dir($this->_config['save_path']) OR ($directory = opendir($this->_config['save_path'])) === FALSE)
		{
			log_message('debug', "Session: Garbage collector couldn't list files under directory 垃圾收集器无法列出目录下的文件'".$this->_config['save_path']."'.");
			return FALSE;
		}

		$ts = time() - $maxlifetime;

		$pattern = sprintf(
			'/^%s[0-9a-f]{%d}$/',
			preg_quote($this->_config['cookie_name'], '/'),
			($this->_config['match_ip'] === TRUE ? 72 : 40)
		);

		while (($file = readdir($directory)) !== FALSE)
		{
			// If the filename doesn't match this pattern, it's either not a session file or is not ours 如果文件名不匹配这个模式,要么是一个会话文件或不是我们的
			if ( ! preg_match($pattern, $file)
				OR ! is_file($this->_config['save_path'].DIRECTORY_SEPARATOR.$file)
				OR ($mtime = filemtime($this->_config['save_path'].DIRECTORY_SEPARATOR.$file)) === FALSE
				OR $mtime > $ts)
			{
				continue;
			}

			unlink($this->_config['save_path'].DIRECTORY_SEPARATOR.$file);
		}

		closedir($directory);

		return TRUE;
	}

}
