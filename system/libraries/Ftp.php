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
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * FTP Class
 * FTP 类
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/ftp.html
 */
class CI_FTP {

	/**
	 * FTP Server hostname
	 * FTP服务器主机名
	 * @var	string
	 */
	public $hostname = '';

	/**
	 * FTP Username
	 * FTP用户名
	 * @var	string
	 */
	public $username = '';

	/**
	 * FTP Password
	 * FTP密码
	 * @var	string
	 */
	public $password = '';

	/**
	 * FTP Server port
	 * FTP服务器端口
	 * @var	int
	 */
	public $port = 21;

	/**
	 * Passive mode flag
	 * 被动模式标识
	 * @var	bool
	 */
	public $passive = TRUE;

	/**
	 * Debug flag
	 * 调试标记
	 * Specifies whether to display error messages.
	 * 指定是否显示错误消息。
	 * @var	bool
	 */
	public $debug = FALSE;

	// --------------------------------------------------------------------

	/**
	 * Connection ID
	 * 标识符
	 * @var	resource 资源
	 */
	protected $conn_id;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 * 构造函数
	 * @param	array	$config
	 * @return	void
	 */
	public function __construct($config = array())
	{
		empty($config) OR $this->initialize($config);
		log_message('info', 'FTP Class Initialized初始化');
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize preferences
	 * 初始化参数
	 * @param	array	$config
	 * @return	void
	 */
	public function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}

		// Prep the hostname 预科的主机名
		$this->hostname = preg_replace('|.+?://|', '', $this->hostname);
	}

	// --------------------------------------------------------------------

	/**
	 * FTP Connect
	 * FTP连接
	 * @param	array	 $config	Connection values 连接值
	 * @return	bool
	 */
	public function connect($config = array())
	{
		if (count($config) > 0)
		{
			$this->initialize($config);
		}

		if (FALSE === ($this->conn_id = @ftp_connect($this->hostname, $this->port)))
		{
			if ($this->debug === TRUE)
			{
				$this->_error('ftp_unable_to_connect');
			}

			return FALSE;
		}

		if ( ! $this->_login())
		{
			if ($this->debug === TRUE)
			{
				$this->_error('ftp_unable_to_login');
			}

			return FALSE;
		}

		// Set passive mode if needed 如果需要设置被动模式
		if ($this->passive === TRUE)
		{
			ftp_pasv($this->conn_id, TRUE);
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * FTP Login
	 * FTP登录 
	 * @return	bool
	 */
	protected function _login()
	{
		return @ftp_login($this->conn_id, $this->username, $this->password);
	}

	// --------------------------------------------------------------------

	/**
	 * Validates the connection ID
	 * 验证连接ID
	 * @return	bool
	 */
	protected function _is_conn()
	{
		if ( ! is_resource($this->conn_id))
		{
			if ($this->debug === TRUE)
			{
				$this->_error('ftp_no_connection');
			}

			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Change directory
	 * 改变当前工作目录
	 * The second parameter lets us momentarily turn off debugging so that
	 * this function can be used to test for the existence of a folder
	 * without throwing an error. There's no FTP equivalent to is_dir()
	 * so we do it by trying to change to a particular directory.
	 * 第二个参数让我们暂时关闭调试,所以这个函数可以用来测试存在的一个文件夹没有抛出错误。所以没有FTP等价的作用是:判断给定文件名是否是我们试图改变一个特定的目录中。
	 * Internally, this parameter is only used by the "mirror" function below.
	 * 在内部,该参数只有使用下面的“镜像”功能。
	 * @param	string	$path
	 * @param	bool	$suppress_debug 废止排错
	 * @return	bool
	 */
	public function changedir($path, $suppress_debug = FALSE)
	{
		if ( ! $this->_is_conn())
		{
			return FALSE;
		}

		$result = @ftp_chdir($this->conn_id, $path);

		if ($result === FALSE)
		{
			if ($this->debug === TRUE && $suppress_debug === FALSE)
			{
				$this->_error('ftp_unable_to_changedir');
			}

			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Create a directory
	 * 创建一个目录
	 * @param	string	$path
	 * @param	int	$permissions 许可权限
	 * @return	bool
	 */
	public function mkdir($path, $permissions = NULL)
	{
		if ($path === '' OR ! $this->_is_conn())
		{
			return FALSE;
		}

		$result = @ftp_mkdir($this->conn_id, $path);

		if ($result === FALSE)
		{
			if ($this->debug === TRUE)
			{
				$this->_error('ftp_unable_to_mkdir');
			}

			return FALSE;
		}

		// Set file permissions if needed 如果需要设置文件权限
		if ($permissions !== NULL)
		{
			$this->chmod($path, (int) $permissions);
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Upload a file to the server
	 * 上传文件到服务器
	 * @param	string	$locpath
	 * @param	string	$rempath
	 * @param	string	$mode
	 * @param	int	$permissions 许可权限
	 * @return	bool
	 */
	public function upload($locpath, $rempath, $mode = 'auto', $permissions = NULL)
	{
		if ( ! $this->_is_conn())
		{
			return FALSE;
		}

		if ( ! file_exists($locpath))
		{
			$this->_error('ftp_no_source_file');
			return FALSE;
		}

		// Set the mode if not specified 如果未指定设置模式
		if ($mode === 'auto')
		{
			// Get the file extension so we can set the upload type 获取文件扩展名我们可以设置上传类型
			$ext = $this->_getext($locpath);
			$mode = $this->_settype($ext);
		}

		$mode = ($mode === 'ascii') ? FTP_ASCII : FTP_BINARY;

		$result = @ftp_put($this->conn_id, $rempath, $locpath, $mode);

		if ($result === FALSE)
		{
			if ($this->debug === TRUE)
			{
				$this->_error('ftp_unable_to_upload');
			}

			return FALSE;
		}

		// Set file permissions if needed 如果需要设置文件权限
		if ($permissions !== NULL)
		{
			$this->chmod($rempath, (int) $permissions);
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Download a file from a remote server to the local server
	 * 从远程服务器下载文件到本地服务器
	 * @param	string	$rempath
	 * @param	string	$locpath
	 * @param	string	$mode
	 * @return	bool
	 */
	public function download($rempath, $locpath, $mode = 'auto')
	{
		if ( ! $this->_is_conn())
		{
			return FALSE;
		}

		// Set the mode if not specified  如果未指定设置模式
		if ($mode === 'auto')
		{
			// Get the file extension so we can set the upload type 获取文件扩展名我们可以设置上传类型
			$ext = $this->_getext($rempath);
			$mode = $this->_settype($ext);
		}

		$mode = ($mode === 'ascii') ? FTP_ASCII : FTP_BINARY;

		$result = @ftp_get($this->conn_id, $locpath, $rempath, $mode);

		if ($result === FALSE)
		{
			if ($this->debug === TRUE)
			{
				$this->_error('ftp_unable_to_download');
			}

			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Rename (or move) a file
	 *
	 * @param	string	$old_file
	 * @param	string	$new_file
	 * @param	bool	$move
	 * @return	bool
	 */
	public function rename($old_file, $new_file, $move = FALSE)
	{
		if ( ! $this->_is_conn())
		{
			return FALSE;
		}

		$result = @ftp_rename($this->conn_id, $old_file, $new_file);

		if ($result === FALSE)
		{
			if ($this->debug === TRUE)
			{
				$this->_error('ftp_unable_to_'.($move === FALSE ? 'rename' : 'move'));
			}

			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Move a file
	 * 移动一个文件
	 * @param	string	$old_file
	 * @param	string	$new_file
	 * @return	bool
	 */
	public function move($old_file, $new_file)
	{
		return $this->rename($old_file, $new_file, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Rename (or move) a file
	 * 重命名一个文件(或移动)
	 * @param	string	$filepath
	 * @return	bool
	 */
	public function delete_file($filepath)
	{
		if ( ! $this->_is_conn())
		{
			return FALSE;
		}

		$result = @ftp_delete($this->conn_id, $filepath);

		if ($result === FALSE)
		{
			if ($this->debug === TRUE)
			{
				$this->_error('ftp_unable_to_delete');
			}

			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Delete a folder and recursively delete everything (including sub-folders)
	 * contained within it.
	 * 删除一个文件夹和递归删除一切(包括子文件夹)包含在它
	 * @param	string	$filepath
	 * @return	bool
	 */
	public function delete_dir($filepath)
	{
		if ( ! $this->_is_conn())
		{
			return FALSE;
		}

		// Add a trailing slash to the file path if needed 如果需要使用斜杠添加到文件路径
		$filepath = preg_replace('/(.+?)\/*$/', '\\1/', $filepath);

		$list = $this->list_files($filepath);
		if ( ! empty($list))
		{
			for ($i = 0, $c = count($list); $i < $c; $i++)
			{
				// If we can't delete the item it's probaly a directory, 如果我们不能删除的项目提供了一个目录,
				// so we'll recursively call delete_dir() 所以我们会递归地调用delete_dir()
				if ( ! preg_match('#/\.\.?$#', $list[$i]) && ! @ftp_delete($this->conn_id, $list[$i]))
				{
					$this->delete_dir($filepath.$list[$i]);
				}
			}
		}

		if (@ftp_rmdir($this->conn_id, $filepath) === FALSE)
		{
			if ($this->debug === TRUE)
			{
				$this->_error('ftp_unable_to_delete');
			}

			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set file permissions
	 * 设置文件权限
	 * @param	string	$path	File path 文件路径
	 * @param	int	$perm	Permissions 权限 许可权
	 * @return	bool
	 */
	public function chmod($path, $perm)
	{
		if ( ! $this->_is_conn())
		{
			return FALSE;
		}

		if (@ftp_chmod($this->conn_id, $perm, $path) === FALSE)
		{
			if ($this->debug === TRUE)
			{
				$this->_error('ftp_unable_to_chmod');
			}

			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * FTP List files in the specified directory
	 * FTP文件在指定的目录列表
	 * @param	string	$path
	 * @return	array
	 */
	public function list_files($path = '.')
	{
		return $this->_is_conn()
			? ftp_nlist($this->conn_id, $path)
			: FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Read a directory and recreate it remotely
	 * 读远程目录并重新创建它
	 * This function recursively reads a folder and everything it contains
	 * (including sub-folders) and creates a mirror via FTP based on it.
	 * Whatever the directory structure of the original file path will be
	 * recreated on the server.
	 * 这个函数递归地读取一个文件夹,其中包含的所有内容(包括子文件夹)并创建一个镜子通过FTP基于它。
	 * 无论原来的文件路径的目录结构将在服务器上重新创建。
	 * @param	string	$locpath	Path to source with trailing slash 源路径末尾斜杠
	 * @param	string	$rempath	Path to destination - include the base folder with trailing slash 通往目的地——包括基础文件夹末尾斜杠
	 * @return	bool
	 */
	public function mirror($locpath, $rempath)
	{
		if ( ! $this->_is_conn())
		{
			return FALSE;
		}

		// Open the local file path 打开本地文件路径
		if ($fp = @opendir($locpath))
		{
			// Attempt to open the remote file path and try to create it, if it doesn't exist 试图打开远程文件路径,试图创建它,如果它不存在
			if ( ! $this->changedir($rempath, TRUE) && ( ! $this->mkdir($rempath) OR ! $this->changedir($rempath)))
			{
				return FALSE;
			}

			// Recursively read the local directory 递归地读取本地目录
			while (FALSE !== ($file = readdir($fp)))
			{
				if (is_dir($locpath.$file) && $file[0] !== '.')
				{
					$this->mirror($locpath.$file.'/', $rempath.$file.'/');
				}
				elseif ($file[0] !== '.')
				{
					// Get the file extension so we can se the upload type 获取文件扩展名我们可以se上传类型
					$ext = $this->_getext($file);
					$mode = $this->_settype($ext);

					$this->upload($locpath.$file, $rempath.$file, $mode);
				}
			}

			return TRUE;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Extract the file extension
	 * 提取文件扩展名
	 * @param	string	$filename
	 * @return	string
	 */
	protected function _getext($filename)
	{
		return (($dot = strrpos($filename, '.')) === FALSE)
			? 'txt'
			: substr($filename, $dot + 1);
	}

	// --------------------------------------------------------------------

	/**
	 * Set the upload type
	 * 设置上传类型
	 * @param	string	$ext	Filename extension 文件扩展名
	 * @return	string
	 */
	protected function _settype($ext)
	{
		return in_array($ext, array('txt', 'text', 'php', 'phps', 'php4', 'js', 'css', 'htm', 'html', 'phtml', 'shtml', 'log', 'xml'), TRUE)
			? 'ascii'
			: 'binary';
	}

	// ------------------------------------------------------------------------

	/**
	 * Close the connection
	 * 关闭连接
	 * @return	bool
	 */
	public function close()
	{
		return $this->_is_conn()
			? @ftp_close($this->conn_id)
			: FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Display error message
	 * 显示错误消息
	 * @param	string	$line
	 * @return	void
	 */
	protected function _error($line)
	{
		$CI =& get_instance();
		$CI->lang->load('ftp');
		show_error($CI->lang->line($line));
	}

}
