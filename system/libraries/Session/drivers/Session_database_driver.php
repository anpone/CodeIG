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
 * CodeIgniter Session Database Driver
 * CodeIgniter会话数据库驱动程序
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author	Andrey Andreev
 * @link	http://codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session_database_driver extends CI_Session_driver implements SessionHandlerInterface {

	/**
	 * DB object
	 * DB对象
	 * @var	object
	 */
	protected $_db;

	/**
	 * Row exists flag
	 * 行存在的标志
	 * @var	bool
	 */
	protected $_row_exists = FALSE;

	/**
	 * Lock "driver" flag
	 * 锁“driver”标记
	 * @var	string
	 */
	protected $_platform;

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

		$CI =& get_instance();
		isset($CI->db) OR $CI->load->database();
		$this->_db = $CI->db;

		if ( ! $this->_db instanceof CI_DB_query_builder)
		{
			throw new Exception('Query Builder not enabled for the configured database查询构建器不支持配置的数据库. Aborting异常终止.');
		}
		elseif ($this->_db->pconnect)
		{
			throw new Exception('Configured database connection is persistent配置数据库连接是持久的. Aborting异常终止.');
		}
		elseif ($this->_db->cache_on)
		{
			throw new Exception('Configured database connection has cache enabled配置数据库连接启用了缓存. Aborting异常终止.');
		}

		$db_driver = $this->_db->dbdriver.(empty($this->_db->subdriver) ? '' : '_'.$this->_db->subdriver);
		if (strpos($db_driver, 'mysql') !== FALSE)
		{
			$this->_platform = 'mysql';
		}
		elseif (in_array($db_driver, array('postgre', 'pdo_pgsql'), TRUE))
		{
			$this->_platform = 'postgre';
		}

		// Note: BC work-around for the old 'sess_table_name' setting, should be removed in the future. 注意:公元前老“sess_table_name”方法进行设置,在未来应该删除。
		isset($this->_config['save_path']) OR $this->_config['save_path'] = config_item('sess_table_name');
	}

	// ------------------------------------------------------------------------

	/**
	 * Open
	 * 打开
	 * Initializes the database connection
	 * 初始化数据库连接
	 * @param	string	$save_path	Table name  表单名称
	 * @param	string	$name		Session cookie name, unused 会话cookie的名字,未使用的
	 * @return	bool
	 */
	public function open($save_path, $name)
	{
		return empty($this->_db->conn_id)
			? (bool) $this->_db->db_connect()
			: TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Read
	 * 读取
	 * Reads session data and acquires a lock
	 * 读取会话数据并获得一个锁
	 * @param	string	$session_id	Session ID
	 * @return	string	Serialized session data
	 */
	public function read($session_id)
	{
		if ($this->_get_lock($session_id) !== FALSE)
		{
			// Needed by write() to detect session_regenerate_id() calls 需要写()来检测session_regenerate_id()调用
			$this->_session_id = $session_id;

			$this->_db
				->select('data')
				->from($this->_config['save_path'])
				->where('id', $session_id);

			if ($this->_config['match_ip'])
			{
				$this->_db->where('ip_address', $_SERVER['REMOTE_ADDR']);
			}

			if (($result = $this->_db->get()->row()) === NULL)
			{
				$this->_fingerprint = md5('');
				return '';
			}

			// PostgreSQL's variant of a BLOB datatype is Bytea, which is a
			// PITA to work with, so we use base64-encoded data in a TEXT
			// field instead.
			// PostgreSQL的变体Bytea BLOB数据类型,这是一个皮塔饼,所以我们使用base64编码数据在一个文本字段。
			$result = ($this->_platform === 'postgre')
				? base64_decode(rtrim($result->data))
				: $result->data;

			$this->_fingerprint = md5($result);
			$this->_row_exists = TRUE;
			return $result;
		}

		$this->_fingerprint = md5('');
		return '';
	}

	// ------------------------------------------------------------------------

	/**
	 * Write
	 * 写入
	 * Writes (create / update) session data
	 * 写(创建/更新)会话数据
	 * @param	string	$session_id	Session ID
	 * @param	string	$session_data	Serialized session data  序列化的会话数据
	 * @return	bool
	 */
	public function write($session_id, $session_data)
	{
		// Was the ID regenerated? ID再生?
		if ($session_id !== $this->_session_id)
		{
			if ( ! $this->_release_lock() OR ! $this->_get_lock($session_id))
			{
				return FALSE;
			}

			$this->_row_exists = FALSE;
			$this->_session_id = $session_id;
		}
		elseif ($this->_lock === FALSE)
		{
			return FALSE;
		}

		if ($this->_row_exists === FALSE)
		{
			$insert_data = array(
				'id' => $session_id,
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'timestamp' => time(),
				'data' => ($this->_platform === 'postgre' ? base64_encode($session_data) : $session_data)
			);

			if ($this->_db->insert($this->_config['save_path'], $insert_data))
			{
				$this->_fingerprint = md5($session_data);
				return $this->_row_exists = TRUE;
			}

			return FALSE;
		}

		$this->_db->where('id', $session_id);
		if ($this->_config['match_ip'])
		{
			$this->_db->where('ip_address', $_SERVER['REMOTE_ADDR']);
		}

		$update_data = array('timestamp' => time());
		if ($this->_fingerprint !== md5($session_data))
		{
			$update_data['data'] = ($this->_platform === 'postgre')
				? base64_encode($session_data)
				: $session_data;
		}

		if ($this->_db->update($this->_config['save_path'], $update_data))
		{
			$this->_fingerprint = md5($session_data);
			return TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Close
	 * 结束
	 * Releases locks
	 * 释放锁
	 * @return	bool
	 */
	public function close()
	{
		return ($this->_lock)
			? $this->_release_lock()
			: TRUE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Destroy
	 * 摧毁 
	 * Destroys the current session.
	 * 破坏了当前会话
	 * @param	string	$session_id	Session ID
	 * @return	bool
	 */
	public function destroy($session_id)
	{
		if ($this->_lock)
		{
			$this->_db->where('id', $session_id);
			if ($this->_config['match_ip'])
			{
				$this->_db->where('ip_address', $_SERVER['REMOTE_ADDR']);
			}

			return $this->_db->delete($this->_config['save_path'])
				? ($this->close() && $this->_cookie_destroy())
				: FALSE;
		}

		return ($this->close() && $this->_cookie_destroy());
	}

	// ------------------------------------------------------------------------

	/**
	 * Garbage Collector
	 * 垃圾回收器
	 * Deletes expired sessions
	 * 删除过期的会话
	 * @param	int 	$maxlifetime	Maximum lifetime of sessions  最大的session生命周期
	 * @return	bool
	 */
	public function gc($maxlifetime)
	{
		return $this->_db->delete($this->_config['save_path'], 'timestamp < '.(time() - $maxlifetime));
	}

	// ------------------------------------------------------------------------

	/**
	 * Get lock
	 * 锁定文件
	 * Acquires a lock, depending on the underlying platform.
	 * 获得一个锁,取决于底层平台。
	 * @param	string	$session_id	Session ID
	 * @return	bool
	 */
	protected function _get_lock($session_id)
	{
		if ($this->_platform === 'mysql')
		{
			$arg = $session_id.($this->_config['match_ip'] ? '_'.$_SERVER['REMOTE_ADDR'] : '');
			if ($this->_db->query("SELECT GET_LOCK('".$arg."', 300) AS ci_session_lock")->row()->ci_session_lock)
			{
				$this->_lock = $arg;
				return TRUE;
			}

			return FALSE;
		}
		elseif ($this->_platform === 'postgre')
		{
			$arg = "hashtext('".$session_id."')".($this->_config['match_ip'] ? ", hashtext('".$_SERVER['REMOTE_ADDR']."')" : '');
			if ($this->_db->simple_query('SELECT pg_advisory_lock('.$arg.')'))
			{
				$this->_lock = $arg;
				return TRUE;
			}

			return FALSE;
		}

		return parent::_get_lock($session_id);
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
		if ( ! $this->_lock)
		{
			return TRUE;
		}

		if ($this->_platform === 'mysql')
		{
			if ($this->_db->query("SELECT RELEASE_LOCK('".$this->_lock."') AS ci_session_lock")->row()->ci_session_lock)
			{
				$this->_lock = FALSE;
				return TRUE;
			}

			return FALSE;
		}
		elseif ($this->_platform === 'postgre')
		{
			if ($this->_db->simple_query('SELECT pg_advisory_unlock('.$this->_lock.')'))
			{
				$this->_lock = FALSE;
				return TRUE;
			}

			return FALSE;
		}

		return parent::_release_lock();
	}

}
