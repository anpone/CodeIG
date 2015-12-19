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
 * Zip Compression Class
 * Zip压缩类
 * This class is based on a library I found at Zend: 这个类是基于一个在Zend库我发现:
 * http://www.zend.com/codex.php?id=696&single=1
 *
 * The original library is a little rough around the edges so I
 * refactored it and added several additional methods -- Rick Ellis
 * 原库有点粗糙的边缘我重构和添加一些额外的方法——里克·埃利斯
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Encryption
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/zip.html
 */
class CI_Zip {

	/**
	 * Zip data in string form
	 * 压缩数据的字符串形式
	 * @var string
	 */
	public $zipdata = '';

	/**
	 * Zip data for a directory in string form
	 * 字符串格式的压缩数据目录
	 * @var string
	 */
	public $directory = '';

	/**
	 * Number of files/folder in zip file
	 * 数量的文件/文件夹在zip文件中
	 * @var int
	 */
	public $entries = 0;

	/**
	 * Number of files in zip
	 * 在zip文件的数量
	 * @var int
	 */
	public $file_num = 0;

	/**
	 * relative offset of local header
	 * 当地头相对偏移
	 * @var int
	 */
	public $offset = 0;

	/**
	 * Reference to time at init
	 * 在init参考时间
	 * @var int
	 */
	public $now;

	/**
	 * The level of compression
	 * 压缩的程度
	 * Ranges from 0 to 9, with 9 being the highest level.
	 * 范围从0到9,9的最高水平。
	 * @var	int
	 */
	public $compression_level = 2;

	/**
	 * Initialize zip compression class
	 * 初始化zip压缩类
	 * @return	void
	 */
	public function __construct()
	{
		$this->now = time();
		log_message('info', 'Zip Compression Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Add Directory
	 * 添加目录 添加文件夹 
	 * Lets you add a virtual directory into which you can place files.
	 * 允许您添加一个虚拟目录,您可以将文件。
	 * @param	mixed	$directory	the directory name. Can be string or array 目录名称。可以是字符串或数组
	 * @return	void
	 */
	public function add_dir($directory)
	{
		foreach ((array) $directory as $dir)
		{
			if ( ! preg_match('|.+/$|', $dir))
			{
				$dir .= '/';
			}

			$dir_time = $this->_get_mod_time($dir);
			$this->_add_dir($dir, $dir_time['file_mtime'], $dir_time['file_mdate']);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get file/directory modification time
	 * 得到文件/目录的修改时间
	 * If this is a newly created file/dir, we will set the time to 'now'
	 * 如果这是一个新创建的文件/目录,我们将时间'现在'
	 * @param	string	$dir	path to file
	 * @return	array	filemtime/filemdate
	 */
	protected function _get_mod_time($dir)
	{
		// filemtime() may return false, but raises an error for non-existing files  filemtime()会返回false,但提出了一个不存在的文件错误
		$date = file_exists($dir) ? getdate(filemtime($dir)) : getdate($this->now);

		return array(
			'file_mtime' => ($date['hours'] << 11) + ($date['minutes'] << 5) + $date['seconds'] / 2,
			'file_mdate' => (($date['year'] - 1980) << 9) + ($date['mon'] << 5) + $date['mday']
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Add Directory
	 * 添加目录 添加文件夹 
	 * @param	string	$dir	the directory name 目录名称
	 * @param	int	$file_mtime 时间
	 * @param	int	$file_mdate 日期
	 * @return	void
	 */
	protected function _add_dir($dir, $file_mtime, $file_mdate)
	{
		$dir = str_replace('\\', '/', $dir);

		$this->zipdata .=
			"\x50\x4b\x03\x04\x0a\x00\x00\x00\x00\x00"
			.pack('v', $file_mtime)
			.pack('v', $file_mdate)
			.pack('V', 0) // crc32
			.pack('V', 0) // compressed filesize  压缩文件大小
			.pack('V', 0) // uncompressed filesize  未压缩的文件大小
			.pack('v', strlen($dir)) // length of pathname  路径的长度
			.pack('v', 0) // extra field length  额外的字段长度
			.$dir
			// below is "data descriptor" segment  下面是“数据描述符”
			.pack('V', 0) // crc32
			.pack('V', 0) // compressed filesize  压缩文件大小
			.pack('V', 0); // uncompressed filesize  未压缩的文件大小

		$this->directory .=
			"\x50\x4b\x01\x02\x00\x00\x0a\x00\x00\x00\x00\x00"
			.pack('v', $file_mtime)
			.pack('v', $file_mdate)
			.pack('V',0) // crc32
			.pack('V',0) // compressed filesize  压缩文件大小
			.pack('V',0) // uncompressed filesize  未压缩的文件大小
			.pack('v', strlen($dir)) // length of pathname  路径的长度
			.pack('v', 0) // extra field length  额外的字段长度
			.pack('v', 0) // file comment length  文件评论长度
			.pack('v', 0) // disk number start  磁盘数量开始
			.pack('v', 0) // internal file attributes  内部文件属性
			.pack('V', 16) // external file attributes - 'directory' bit set  外部文件属性——“目录”设置
			.pack('V', $this->offset) // relative offset of local header  当地头相对偏移
			.$dir;

		$this->offset = strlen($this->zipdata);
		$this->entries++;
	}

	// --------------------------------------------------------------------

	/**
	 * Add Data to Zip
	 * 数据添加到Zip
	 * Lets you add files to the archive. If the path is included
	 * in the filename it will be placed within a directory. Make
	 * sure you use add_dir() first to create the folder.
	 * 允许您添加文件存档。如果包含在文件名的路径将被放置在一个目录中。首先确保你使用add_dir()创建的文件夹。
	 * @param	mixed	$filepath	A single filepath or an array of file => data pairs 单个filepath或一组文件= >数据对
	 * @param	string	$data		Single file contents  单个文件内容
	 * @return	void
	 */
	public function add_data($filepath, $data = NULL)
	{
		if (is_array($filepath))
		{
			foreach ($filepath as $path => $data)
			{
				$file_data = $this->_get_mod_time($path);
				$this->_add_data($path, $data, $file_data['file_mtime'], $file_data['file_mdate']);
			}
		}
		else
		{
			$file_data = $this->_get_mod_time($filepath);
			$this->_add_data($filepath, $data, $file_data['file_mtime'], $file_data['file_mdate']);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Add Data to Zip
	 * 数据添加到Zip
	 * @param	string	$filepath	the file name/path  文件名称/路径
	 * @param	string	$data	the data to be encoded  编码的数据
	 * @param	int	$file_mtime
	 * @param	int	$file_mdate
	 * @return	void
	 */
	protected function _add_data($filepath, $data, $file_mtime, $file_mdate)
	{
		$filepath = str_replace('\\', '/', $filepath);

		$uncompressed_size = strlen($data);
		$crc32  = crc32($data);
		$gzdata = substr(gzcompress($data, $this->compression_level), 2, -4);
		$compressed_size = strlen($gzdata);

		$this->zipdata .=
			"\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00"
			.pack('v', $file_mtime)
			.pack('v', $file_mdate)
			.pack('V', $crc32)
			.pack('V', $compressed_size)
			.pack('V', $uncompressed_size)
			.pack('v', strlen($filepath)) // length of filename  文件名的长度
			.pack('v', 0) // extra field length  额外的字段长度
			.$filepath
			.$gzdata; // "file data" segment  “文件数据”部分
		
		$this->directory .=
			"\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00"
			.pack('v', $file_mtime)
			.pack('v', $file_mdate)
			.pack('V', $crc32)
			.pack('V', $compressed_size)
			.pack('V', $uncompressed_size)
			.pack('v', strlen($filepath)) // length of filename  文件名的长度
			.pack('v', 0) // extra field length  额外的字段长度
			.pack('v', 0) // file comment length 文件评论长度
			.pack('v', 0) // disk number start 磁盘数量开始
			.pack('v', 0) // internal file attributes 内部文件属性
			.pack('V', 32) // external file attributes - 'archive' bit set  外部文件属性——“存档”设置
			.pack('V', $this->offset) // relative offset of local header  当地头相对偏移
			.$filepath;

		$this->offset = strlen($this->zipdata);
		$this->entries++;
		$this->file_num++;
	}

	// --------------------------------------------------------------------

	/**
	 * Read the contents of a file and add it to the zip
	 * 读取文件的内容,并将其添加到zip
	 * @param	string	$path
	 * @param	bool	$archive_filepath
	 * @return	bool
	 */
	public function read_file($path, $archive_filepath = FALSE)
	{
		if (file_exists($path) && FALSE !== ($data = file_get_contents($path)))
		{
			if (is_string($archive_filepath))
			{
				$name = str_replace('\\', '/', $archive_filepath);
			}
			else
			{
				$name = str_replace('\\', '/', $path);

				if ($archive_filepath === FALSE)
				{
					$name = preg_replace('|.*/(.+)|', '\\1', $name);
				}
			}

			$this->add_data($name, $data);
			return TRUE;
		}

		return FALSE;
	}

	// ------------------------------------------------------------------------

	/**
	 * Read a directory and add it to the zip.
	 * 读一个目录,并将其添加到zip。
	 * This function recursively reads a folder and everything it contains (including
	 * sub-folders) and creates a zip based on it. Whatever directory structure
	 * is in the original file path will be recreated in the zip file.
	 * 这个函数递归地读取一个文件夹,其中包含的所有内容(包括子文件夹)并创建一个基于zip。无论目录结构是在原来的文件路径将重新创建zip文件。
	 * @param	string	$path	path to source directory  源目录路径
	 * @param	bool	$preserve_filepath
	 * @param	string	$root_path
	 * @return	bool
	 */
	public function read_dir($path, $preserve_filepath = TRUE, $root_path = NULL)
	{
		$path = rtrim($path, '/\\').DIRECTORY_SEPARATOR;
		if ( ! $fp = @opendir($path))
		{
			return FALSE;
		}

		// Set the original directory root for child dir's to use as relative  为子目录设置原始目录根dir的使用是相对的
		if ($root_path === NULL)
		{
			$root_path = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, dirname($path)).DIRECTORY_SEPARATOR;
		}

		while (FALSE !== ($file = readdir($fp)))
		{
			if ($file[0] === '.')
			{
				continue;
			}

			if (is_dir($path.$file))
			{
				$this->read_dir($path.$file.DIRECTORY_SEPARATOR, $preserve_filepath, $root_path);
			}
			elseif (FALSE !== ($data = file_get_contents($path.$file)))
			{
				$name = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $path);
				if ($preserve_filepath === FALSE)
				{
					$name = str_replace($root_path, '', $name);
				}

				$this->add_data($name.$file, $data);
			}
		}

		closedir($fp);
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get the Zip file
	 * 得到了压缩文件
	 * @return	string	(binary encoded)
	 */
	public function get_zip()
	{
		// Is there any data to return? 有数据返回吗?
		if ($this->entries === 0)
		{
			return FALSE;
		}

		return $this->zipdata
			.$this->directory."\x50\x4b\x05\x06\x00\x00\x00\x00"
			.pack('v', $this->entries) // total # of entries "on this disk"  总条目#“这个磁盘上”
			.pack('v', $this->entries) // total # of entries overall  总#的条目
			.pack('V', strlen($this->directory)) // size of central dir  主要的dir的大小
			.pack('V', strlen($this->zipdata)) // offset to start of central dir  偏移主要的dir的开始
			."\x00\x00"; // .zip file comment length  zip文件评论长度
	}

	// --------------------------------------------------------------------

	/**
	 * Write File to the specified directory
	 * 写文件到指定目录
	 * Lets you write a file
	 * 让你写一个文件
	 * @param	string	$filepath	the file name
	 * @return	bool
	 */
	public function archive($filepath)
	{
		if ( ! ($fp = @fopen($filepath, 'w+b')))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);

		for ($result = $written = 0, $data = $this->get_zip(), $length = strlen($data); $written < $length; $written += $result)
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

	// --------------------------------------------------------------------

	/**
	 * Download
	 * 下载
	 * @param	string	$filename	the file name
	 * @return	void
	 */
	public function download($filename = 'backup.zip')
	{
		if ( ! preg_match('|.+?\.zip$|', $filename))
		{
			$filename .= '.zip';
		}

		get_instance()->load->helper('download');
		$get_zip = $this->get_zip();
		$zip_content =& $get_zip;

		force_download($filename, $zip_content);
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize Data
	 * 预置数据
	 * Lets you clear current zip data. Useful if you need to create 当前的压缩数据。如果你需要创建有用的
	 * multiple zips with different data. 多个拉链与不同的数据。
	 *
	 * @return	CI_Zip
	 */
	public function clear_data()
	{
		$this->zipdata = '';
		$this->directory = '';
		$this->entries = 0;
		$this->file_num = 0;
		$this->offset = 0;
		return $this;
	}

}
