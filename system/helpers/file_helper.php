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
 * CodeIgniter File Helpers
 * CodeIgniter文件助手
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/file_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('read_file'))
{
	/**
	 * Read File
	 * 读取文件
	 * Opens the file specified in the path and returns it as a string.
	 * 打开文件中指定的路径,将其作为字符串返回。
	 * @todo	Remove in version 3.1+. 删除在版本3.1 +。
	 * @deprecated	3.0.0	It is now just an alias for PHP's native file_get_contents(). 现在只是一个别名为PHP的本机file_get_contents()。
	 * @param	string	$file	Path to file  路径文件
	 * @return	string	File contents  文件内容
	 */
	function read_file($file)
	{
		return @file_get_contents($file);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('write_file'))
{
	/**
	 * Write File
	 * 写入文件
	 * Writes data to the file specified in the path. 将数据写入文件中指定的路径。
	 * Creates a new file if non-existent. 创建一个新文件,如果不存在。
	 *
	 * @param	string	$path	File path  文件路径
	 * @param	string	$data	Data to write  写入数据
	 * @param	string	$mode	fopen() mode (default: 'wb') |=>fopen()模式(默认值:'wb')
	 * @return	bool
	 */
	function write_file($path, $data, $mode = 'wb')
	{
		if ( ! $fp = @fopen($path, $mode))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);

		for ($result = $written = 0, $length = strlen($data); $written < $length; $written += $result)
		{
			if (($result = fwrite($fp, substr($data, $written))) === FALSE)
			{
				break;
			}
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return is_int($result);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('delete_files'))
{
	/**
	 * Delete Files
	 * 删除文件
	 * Deletes all files contained in the supplied directory path. 删除所有文件中提供的目录路径。
	 * Files must be writable or owned by the system in order to be deleted. 文件必须可写或由系统以被删除。
	 * If the second parameter is set to TRUE, any directories contained
	 * within the supplied base directory will be nuked as well.
	 * 如果第二个参数设置为TRUE,任何目录包含在供应基地目录将裸露的。
	 * @param	string	$path		File path  文件路径
	 * @param	bool	$del_dir	Whether to delete any directories found in the path  是否要删除目录路径中找到
	 * @param	bool	$htdocs		Whether to skip deleting .htaccess and index page files  是否跳过删除.htaccess文件和索引页面
	 * @param	int	$_level		Current directory depth level (default: 0; internal use only) 当前目录深度级别(默认值:0;仅内部使用)
	 * @return	bool
	 */
	function delete_files($path, $del_dir = FALSE, $htdocs = FALSE, $_level = 0)
	{
		// Trim the trailing slash  修剪尾部的斜杠
		$path = rtrim($path, '/\\');

		if ( ! $current_dir = @opendir($path))
		{
			return FALSE;
		}

		while (FALSE !== ($filename = @readdir($current_dir)))
		{
			if ($filename !== '.' && $filename !== '..')
			{
				if (is_dir($path.DIRECTORY_SEPARATOR.$filename) && $filename[0] !== '.')
				{
					delete_files($path.DIRECTORY_SEPARATOR.$filename, $del_dir, $htdocs, $_level + 1);
				}
				elseif ($htdocs !== TRUE OR ! preg_match('/^(\.htaccess|index\.(html|htm|php)|web\.config)$/i', $filename))
				{
					@unlink($path.DIRECTORY_SEPARATOR.$filename);
				}
			}
		}

		closedir($current_dir);

		return ($del_dir === TRUE && $_level > 0)
			? @rmdir($path)
			: TRUE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_filenames'))
{
	/**
	 * Get Filenames
	 * 获取文件名
	 * Reads the specified directory and builds an array containing the filenames.
	 * Any sub-folders contained within the specified path are read as well.
	 * 读取指定的目录,并构建一个数组包含文件名。 所有子文件夹中包含指定路径读取。
	 * @param	string	path to source  源路径
	 * @param	bool	whether to include the path as part of the filename  是否包括路径的文件名
	 * @param	bool	internal variable to determine recursion status - do not use in calls 内部变量来确定递归状态——不使用调用
	 * @return	array
	 */
	function get_filenames($source_dir, $include_path = FALSE, $_recursion = FALSE)
	{
		static $_filedata = array();

		if ($fp = @opendir($source_dir))
		{
			// reset the array and make sure $source_dir has a trailing slash on the initial call
			// 重置该数组并确保$source_dir末尾有斜杠初始调用
			if ($_recursion === FALSE)
			{
				$_filedata = array();
				$source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
			}

			while (FALSE !== ($file = readdir($fp)))
			{
				if (is_dir($source_dir.$file) && $file[0] !== '.')
				{
					get_filenames($source_dir.$file.DIRECTORY_SEPARATOR, $include_path, TRUE);
				}
				elseif ($file[0] !== '.')
				{
					$_filedata[] = ($include_path === TRUE) ? $source_dir.$file : $file;
				}
			}

			closedir($fp);
			return $_filedata;
		}

		return FALSE;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('get_dir_file_info'))
{
	/**
	 * Get Directory File \Information
	 * 获取目录文件信息
	 * Reads the specified directory and builds an array containing the filenames,
	 * filesize, dates, and permissions
	 * 读取指定的目录包含文件名和建立一个数组 文件大小、日期和权限
	 * Any sub-folders contained within the specified path are read as well.
	 * 所有子文件夹中包含指定路径读取。
	 * @param	string	path to source  源路径
	 * @param	bool	Look only at the top level directory specified?  只看指定的顶级目录中?
	 * @param	bool	internal variable to determine recursion status - do not use in calls  内部变量来确定递归状态——不使用调用
	 * @return	array
	 */
	function get_dir_file_info($source_dir, $top_level_only = TRUE, $_recursion = FALSE)
	{
		static $_filedata = array();
		$relative_path = $source_dir;

		if ($fp = @opendir($source_dir))
		{
			// reset the array and make sure $source_dir has a trailing slash on the initial call
			// 重置该数组并确保$source_dir末尾有斜杠初始调用
			if ($_recursion === FALSE)
			{
				$_filedata = array();
				$source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
			}

			// Used to be foreach (scandir($source_dir, 1) as $file), but scandir() is simply not as fast
			// 使用foreach(scandir($source_dir,1)as $file),但scandir()不一样快
			while (FALSE !== ($file = readdir($fp)))
			{
				if (is_dir($source_dir.$file) && $file[0] !== '.' && $top_level_only === FALSE)
				{
					get_dir_file_info($source_dir.$file.DIRECTORY_SEPARATOR, $top_level_only, TRUE);
				}
				elseif ($file[0] !== '.')
				{
					$_filedata[$file] = get_file_info($source_dir.$file);
					$_filedata[$file]['relative_path'] = $relative_path;
				}
			}

			closedir($fp);
			return $_filedata;
		}

		return FALSE;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('get_file_info'))
{
	/**
	 * Get File Info
	 * 获取文件信息
	 * Given a file and path, returns the name, path, size, date modified
	 * Second parameter allows you to explicitly declare what information you want returned
	 * Options are: name, server_path, size, date, readable, writable, executable, fileperms
	 * Returns FALSE if the file cannot be found.
	 * 给定一个文件和路径,返回名称、路径、大小、日期修改第二个参数允许您显式地声明你想要什么信息返回选项有:名称、server_path,大小,日期,可读,可写,可执行文件,如果文件不能找到函数返回FALSE。
	 * @param	string	path to file  文件路径
	 * @param	mixed	array or comma separated string of information returned  数组或逗号分隔字符串返回的信息
	 * @return	array
	 */
	function get_file_info($file, $returned_values = array('name', 'server_path', 'size', 'date'))
	{
		if ( ! file_exists($file))
		{
			return FALSE;
		}

		if (is_string($returned_values))
		{
			$returned_values = explode(',', $returned_values);
		}

		foreach ($returned_values as $key)
		{
			switch ($key)
			{
				case 'name':
					$fileinfo['name'] = basename($file);
					break;
				case 'server_path':
					$fileinfo['server_path'] = $file;
					break;
				case 'size':
					$fileinfo['size'] = filesize($file);
					break;
				case 'date':
					$fileinfo['date'] = filemtime($file);
					break;
				case 'readable':
					$fileinfo['readable'] = is_readable($file);
					break;
				case 'writable':
					$fileinfo['writable'] = is_really_writable($file);
					break;
				case 'executable':
					$fileinfo['executable'] = is_executable($file);
					break;
				case 'fileperms':
					$fileinfo['fileperms'] = fileperms($file);
					break;
			}
		}

		return $fileinfo;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('get_mime_by_extension'))
{
	/**
	 * Get Mime by Extension
	 * 得到Mime扩展
	 * Translates a file extension into a mime type based on config/mimes.php. 将一个文件扩展名转换为基于配置/ mimes.php mime类型。
	 * Returns FALSE if it can't determine the type, or open the mime config file
	 * 返回FALSE如果不能确定类型,或打开mime配置文件
	 * Note: this is NOT an accurate way of determining file mime types, and is here strictly as a convenience
	 * It should NOT be trusted, and should certainly NOT be used for security
	 * 注意:这不是一个准确的方法确定文件的mime类型,这里是严格方便它不应该被信任,和当然不应该被用于安全
	 * @param	string	$filename	File name  文件名称
	 * @return	string
	 */
	function get_mime_by_extension($filename)
	{
		static $mimes;

		if ( ! is_array($mimes))
		{
			$mimes = get_mimes();

			if (empty($mimes))
			{
				return FALSE;
			}
		}

		$extension = strtolower(substr(strrchr($filename, '.'), 1));

		if (isset($mimes[$extension]))
		{
			return is_array($mimes[$extension])
				? current($mimes[$extension]) // Multiple mime types, just give the first one 多个mime类型,给第一个
				: $mimes[$extension];
		}

		return FALSE;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('symbolic_permissions'))
{
	/**
	 * Symbolic Permissions
	 * 符号权限  象征性的权限  
	 * Takes a numeric value representing a file's permissions and returns
	 * standard symbolic notation representing that value
	 * 需要一个数值,表示一个文件的权限和返回标准符号代表值
	 * @param	int	$perms	Permissions 许可，权限
	 * @return	string
	 */
	function symbolic_permissions($perms)
	{
		if (($perms & 0xC000) === 0xC000)
		{
			$symbolic = 's'; // Socket 套接字 接口
		}
		elseif (($perms & 0xA000) === 0xA000)
		{
			$symbolic = 'l'; // Symbolic Link 符号连接
		}
		elseif (($perms & 0x8000) === 0x8000)
		{
			$symbolic = '-'; // Regular 规则的
		}
		elseif (($perms & 0x6000) === 0x6000)
		{
			$symbolic = 'b'; // Block special 设备文件系统 
		}
		elseif (($perms & 0x4000) === 0x4000)
		{
			$symbolic = 'd'; // Directory 目录
		}
		elseif (($perms & 0x2000) === 0x2000)
		{
			$symbolic = 'c'; // Character special  特殊角色
		}
		elseif (($perms & 0x1000) === 0x1000)
		{
			$symbolic = 'p'; // FIFO pipe  先进先出 管道
		}
		else
		{
			$symbolic = 'u'; // Unknown 未知
		}

		// Owner所有者
		$symbolic .= (($perms & 0x0100) ? 'r' : '-')
			.(($perms & 0x0080) ? 'w' : '-')
			.(($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));

		// Group群组
		$symbolic .= (($perms & 0x0020) ? 'r' : '-')
			.(($perms & 0x0010) ? 'w' : '-')
			.(($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));

		// World全局
		$symbolic .= (($perms & 0x0004) ? 'r' : '-')
			.(($perms & 0x0002) ? 'w' : '-')
			.(($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));

		return $symbolic;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('octal_permissions'))
{
	/**
	 * Octal Permissions
	 * 八进制权限 
	 * Takes a numeric value representing a file's permissions and returns
	 * a three character string representing the file's octal permissions
	 * 需要一个数值,表示文件的权限,并返回一个表示文件的八进制权限三个字符串
	 * @param	int	$perms	Permissions 许可权限
	 * @return	string
	 */
	function octal_permissions($perms)
	{
		return substr(sprintf('%o', $perms), -3);
	}
}
