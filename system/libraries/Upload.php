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
 * File Uploading Class
 * 文件上传类
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Uploads
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/file_uploading.html
 */
class CI_Upload {

	/**
	 * Maximum file size
	 * 最大文件大小
	 * @var	int
	 */
	public $max_size = 0;

	/**
	 * Maximum image width
	 * 最大图像宽度
	 * @var	int
	 */
	public $max_width = 0;

	/**
	 * Maximum image height
	 * 最大图像高度
	 * @var	int
	 */
	public $max_height = 0;

	/**
	 * Minimum image width
	 * 最小图像宽度
	 * @var	int
	 */
	public $min_width = 0;

	/**
	 * Minimum image height
	 * 最小图像高度
	 * @var	int
	 */
	public $min_height = 0;

	/**
	 * Maximum filename length
	 * 最大文件名长度
	 * @var	int
	 */
	public $max_filename = 0;

	/**
	 * Maximum duplicate filename increment ID
	 * 最大重复的文件名增量ID
	 * @var	int
	 */
	public $max_filename_increment = 100;

	/**
	 * Allowed file types
	 * 允许的文件类型
	 * @var	string
	 */
	public $allowed_types = '';

	/**
	 * Temporary filename
	 * 临时文件名
	 * @var	string
	 */
	public $file_temp = '';

	/**
	 * Filename
	 * 文件名
	 * @var	string
	 */
	public $file_name = '';

	/**
	 * Original filename
	 * 原始文件名
	 * @var	string
	 */
	public $orig_name = '';

	/**
	 * File type
	 * 文件类型
	 * @var	string
	 */
	public $file_type = '';

	/**
	 * File size
	 * 文件大小
	 * @var	int
	 */
	public $file_size = NULL;

	/**
	 * Filename extension
	 * 文件扩展名
	 * @var	string
	 */
	public $file_ext = '';

	/**
	 * Force filename extension to lowercase
	 * 迫使文件扩展名小写
	 * @var	string
	 */
	public $file_ext_tolower = FALSE;

	/**
	 * Upload path
	 * 上载路径
	 * @var	string
	 */
	public $upload_path = '';

	/**
	 * Overwrite flag
	 * 覆盖标志
	 * @var	bool
	 */
	public $overwrite = FALSE;

	/**
	 * Obfuscate filename flag
	 * 混淆文件名标识
	 * @var	bool
	 */
	public $encrypt_name = FALSE;

	/**
	 * Is image flag
	 * 是否是图片标识
	 * @var	bool
	 */
	public $is_image = FALSE;

	/**
	 * Image width
	 * 图片宽度
	 * @var	int
	 */
	public $image_width = NULL;

	/**
	 * Image height
	 * 图片高度
	 * @var	int
	 */
	public $image_height = NULL;

	/**
	 * Image type
	 * 图片格式类型jpg png gif
	 * @var	string
	 */
	public $image_type = '';

	/**
	 * Image size string
	 * 图片大小 字符串
	 * @var	string
	 */
	public $image_size_str = '';

	/**
	 * Error messages list
	 * 错误信息列表
	 * @var	array
	 */
	public $error_msg = array();

	/**
	 * Remove spaces flag
	 * 移除空格标识
	 * @var	bool
	 */
	public $remove_spaces = TRUE;

	/**
	 * MIME detection flag
	 * MIME是否检测标识
	 * @var	bool
	 */
	public $detect_mime = TRUE;

	/**
	 * XSS filter flag
	 * XSS过滤 标识
	 * @var	bool
	 */
	public $xss_clean = FALSE;

	/**
	 * Apache mod_mime fix flag
	 * Apache mod_mime 是否使用确定标识
	 * @var	bool
	 */
	public $mod_mime_fix = TRUE;

	/**
	 * Temporary filename prefix
	 * 临时文件名前缀
	 * @var	string
	 */
	public $temp_prefix = 'temp_file_';

	/**
	 * Filename sent by the client
	 * 客户端发送的文件名
	 * @var	bool
	 */
	public $client_name = '';

	// --------------------------------------------------------------------

	/**
	 * Filename override
	 * 文件名覆盖
	 * @var	string
	 */
	protected $_file_name_override = '';

	/**
	 * MIME types list
	 * MIME类型列表
	 * @var	array
	 */
	protected $_mimes = array();

	/**
	 * CI Singleton
	 * CI单例
	 * @var	object
	 */
	protected $_CI;

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *  构造函数
	 * @param	array	$props
	 * @return	void
	 */
	public function __construct($config = array())
	{
		empty($config) OR $this->initialize($config, FALSE);

		$this->_mimes =& get_mimes();
		$this->_CI =& get_instance();

		log_message('info', 'Upload Class Initialized上传类初始化');
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize preferences
	 * 初始化首选项
	 * @param	array	$config
	 * @param	bool	$reset 复位 重新设定
	 * @return	CI_Upload
	 */
	public function initialize(array $config = array(), $reset = TRUE)
	{
		$reflection = new ReflectionClass($this);

		if ($reset === TRUE)
		{
			$defaults = $reflection->getDefaultProperties();
			foreach (array_keys($defaults) as $key)
			{
				if ($key[0] === '_')
				{
					continue;
				}

				if (isset($config[$key]))
				{
					if ($reflection->hasMethod('set_'.$key))
					{
						$this->{'set_'.$key}($config[$key]);
					}
					else
					{
						$this->$key = $config[$key];
					}
				}
				else
				{
					$this->$key = $defaults[$key];
				}
			}
		}
		else
		{
			foreach ($config as $key => &$value)
			{
				if ($key[0] !== '_' && $reflection->hasProperty($key))
				{
					if ($reflection->hasMethod('set_'.$key))
					{
						$this->{'set_'.$key}($value);
					}
					else
					{
						$this->$key = $value;
					}
				}
			}
		}

		// if a file_name was provided in the config, use it instead of the user input
		// supplied file name for all uploads until initialized again
		// 如果file_name提供的配置,使用它,而不是提供用户输入文件名上传,直到再次初始化
		$this->_file_name_override = $this->file_name;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Perform the file upload
	 * 执行文件上传
	 * @param	string	$field
	 * @return	bool
	 */
	public function do_upload($field = 'userfile')
	{
		// Is $_FILES[$field] set? If not, no reason to continue. 查看$_FILES是否设置.如果没有,没有理由继续。
		if (isset($_FILES[$field]))
		{
			$_file = $_FILES[$field];
		}
		// Does the field name contain array notation? 字段名称包含数组表示法吗?
		elseif (($c = preg_match_all('/(?:^[^\[]+)|\[[^]]*\]/', $field, $matches)) > 1)
		{
			$_file = $_FILES;
			for ($i = 0; $i < $c; $i++)
			{
				// We can't track numeric iterations, only full field names are accepted 我们不能追踪数字迭代,只接受完整的字段名称
				if (($field = trim($matches[0][$i], '[]')) === '' OR ! isset($_file[$field]))
				{
					$_file = NULL;
					break;
				}

				$_file = $_file[$field];
			}
		}

		if ( ! isset($_file))
		{
			$this->set_error('upload_no_file_selected', 'debug');
			return FALSE;
		}

		// Is the upload path valid? 上传路径有效吗?
		if ( ! $this->validate_upload_path())
		{
			// errors will already be set by validate_upload_path() so just return FALSE  错误已经被设定的validate_upload_path()就返回假
			return FALSE;
		}

		// Was the file able to be uploaded? If not, determine the reason why. 文件可以上传?如果没有,确定的原因。
		if ( ! is_uploaded_file($_file['tmp_name']))
		{
			$error = isset($_file['error']) ? $_file['error'] : 4;

			switch ($error)
			{
				case UPLOAD_ERR_INI_SIZE:
					$this->set_error('upload_file_exceeds_limit', 'info');
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$this->set_error('upload_file_exceeds_form_limit', 'info');
					break;
				case UPLOAD_ERR_PARTIAL:
					$this->set_error('upload_file_partial', 'debug');
					break;
				case UPLOAD_ERR_NO_FILE:
					$this->set_error('upload_no_file_selected', 'debug');
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$this->set_error('upload_no_temp_directory', 'error');
					break;
				case UPLOAD_ERR_CANT_WRITE:
					$this->set_error('upload_unable_to_write_file', 'error');
					break;
				case UPLOAD_ERR_EXTENSION:
					$this->set_error('upload_stopped_by_extension', 'debug');
					break;
				default:
					$this->set_error('upload_no_file_selected', 'debug');
					break;
			}

			return FALSE;
		}

		// Set the uploaded data as class variables 上传的数据设置为类变量
		$this->file_temp = $_file['tmp_name'];
		$this->file_size = $_file['size'];

		// Skip MIME type detection? 跳过MIME类型检测?
		if ($this->detect_mime !== FALSE)
		{
			$this->_file_mime_type($_file);
		}

		$this->file_type = preg_replace('/^(.+?);.*$/', '\\1', $this->file_type);
		$this->file_type = strtolower(trim(stripslashes($this->file_type), '"'));
		$this->file_name = $this->_prep_filename($_file['name']);
		$this->file_ext	 = $this->get_extension($this->file_name);
		$this->client_name = $this->file_name;

		// Is the file type allowed to be uploaded? 允许上传的文件类型吗?
		if ( ! $this->is_allowed_filetype())
		{
			$this->set_error('upload_invalid_filetype', 'debug');
			return FALSE;
		}

		// if we're overriding, let's now make sure the new name and type is allowed 如果我们覆盖,现在让我们确保新的名称和类型是被允许的
		if ($this->_file_name_override !== '')
		{
			$this->file_name = $this->_prep_filename($this->_file_name_override);

			// If no extension was provided in the file_name config item, use the uploaded one 如果没有提供的扩展file_name配置项,使用上传
			if (strpos($this->_file_name_override, '.') === FALSE)
			{
				$this->file_name .= $this->file_ext;
			}
			else
			{
				// An extension was provided, let's have it! 提供了一个扩展,让我们拥有它!
				$this->file_ext	= $this->get_extension($this->_file_name_override);
			}

			if ( ! $this->is_allowed_filetype(TRUE))
			{
				$this->set_error('upload_invalid_filetype', 'debug');
				return FALSE;
			}
		}

		// Convert the file size to kilobytes 将文件大小转换为千字节
		if ($this->file_size > 0)
		{
			$this->file_size = round($this->file_size/1024, 2);
		}

		// Is the file size within the allowed maximum? 是在允许的最大文件大小?
		if ( ! $this->is_allowed_filesize())
		{
			$this->set_error('upload_invalid_filesize', 'info');
			return FALSE;
		}

		// Are the image dimensions within the allowed size? 图像尺寸在允许的尺寸吗?
		// Note: This can fail if the server has an open_basedir restriction. 注意:这个可以失败如果服务器有一个open_basedir限制。
		if ( ! $this->is_allowed_dimensions())
		{
			$this->set_error('upload_invalid_dimensions', 'info');
			return FALSE;
		}

		// Sanitize the file name for security  清洁安全的文件名称
		$this->file_name = $this->_CI->security->sanitize_filename($this->file_name);

		// Truncate the file name if it's too long  截断文件名,如果太长了
		if ($this->max_filename > 0)
		{
			$this->file_name = $this->limit_filename_length($this->file_name, $this->max_filename);
		}

		// Remove white spaces in the name  删除空白的名字
		if ($this->remove_spaces === TRUE)
		{
			$this->file_name = preg_replace('/\s+/', '_', $this->file_name);
		}

		/*
		 * Validate the file name  验证文件名称
		 * This function appends an number onto the end of
		 * the file if one with the same name already exists.
		 * If it returns false there was a problem.
		 * 这个函数添加一个号码到文件的末尾,如果有相同的名称已经存在。 如果返回错误的有问题。
		 */ 
		$this->orig_name = $this->file_name;
		if (FALSE === ($this->file_name = $this->set_filename($this->upload_path, $this->file_name)))
		{
			return FALSE;
		}

		/*
		 * Run the file through the XSS hacking filter
		 * This helps prevent malicious code from being
		 * embedded within a file. Scripts can easily
		 * be disguised as images or other file types.
		 */
		if ($this->xss_clean && $this->do_xss_clean() === FALSE)
		{
			$this->set_error('upload_unable_to_write_file', 'error');
			return FALSE;
		}

		/*
		 * Move the file to the final destination
		 * To deal with different server configurations
		 * 将文件移动到最终目的地来处理不同的服务器配置
		 * we'll attempt to use copy() first. If that fails 我们将尝试使用()的第一个副本。如果失败
		 * we'll use move_uploaded_file(). One of the two should
		 * reliably work in most environments
		 * 我们将使用函数()。其中一个应该在大多数环境中可靠地工作
		 */
		if ( ! @copy($this->file_temp, $this->upload_path.$this->file_name))
		{
			if ( ! @move_uploaded_file($this->file_temp, $this->upload_path.$this->file_name))
			{
				$this->set_error('upload_destination_error', 'error');
				return FALSE;
			}
		}

		/*
		 * Set the finalized image dimensions 设置完成图像尺寸
		 * This sets the image width/height (assuming the
		 * file was an image). We use this information
		 * in the "data" function.
		 * 这组图片宽度/高度(假设是一个图像)的文件。我们使用这些信息在“数据”功能。
		 */
		$this->set_image_properties($this->upload_path.$this->file_name);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Finalized Data Array
	 * 完成数据数组
	 * Returns an associative array containing all of the information
	 * related to the upload, allowing the developer easy access in one array.
	 * 返回一个关联数组,其中包含所有的相关信息上传,允许开发者轻松访问在一个数组中。
	 * @param	string	$index
	 * @return	mixed
	 */
	public function data($index = NULL)
	{
		$data = array(
				'file_name'		=> $this->file_name,
				'file_type'		=> $this->file_type,
				'file_path'		=> $this->upload_path,
				'full_path'		=> $this->upload_path.$this->file_name,
				'raw_name'		=> str_replace($this->file_ext, '', $this->file_name),
				'orig_name'		=> $this->orig_name,
				'client_name'		=> $this->client_name,
				'file_ext'		=> $this->file_ext,
				'file_size'		=> $this->file_size,
				'is_image'		=> $this->is_image(),
				'image_width'		=> $this->image_width,
				'image_height'		=> $this->image_height,
				'image_type'		=> $this->image_type,
				'image_size_str'	=> $this->image_size_str,
			);

		if ( ! empty($index))
		{
			return isset($data[$index]) ? $data[$index] : NULL;
		}

		return $data;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Upload Path
	 * 设置上传路径
	 * @param	string	$path
	 * @return	CI_Upload
	 */
	public function set_upload_path($path)
	{
		// Make sure it has a trailing slash 确保使用斜杠
		$this->upload_path = rtrim($path, '/').'/';
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the file name
	 * 设置文件名称
	 * This function takes a filename/path as input and looks for the
	 * existence of a file with the same name. If found, it will append a
	 * number to the end of the filename to avoid overwriting a pre-existing file.
	 * 这个函数接受一个文件名/路径作为输入,并寻找具有相同名称的文件的存在。如果发现,将添加一个数字的文件名,以避免覆盖已存在的文件。
	 * @param	string	$path
	 * @param	string	$filename
	 * @return	string
	 */
	public function set_filename($path, $filename)
	{
		if ($this->encrypt_name === TRUE)
		{
			$filename = md5(uniqid(mt_rand())).$this->file_ext;
		}

		if ($this->overwrite === TRUE OR ! file_exists($path.$filename))
		{
			return $filename;
		}

		$filename = str_replace($this->file_ext, '', $filename);

		$new_filename = '';
		for ($i = 1; $i < $this->max_filename_increment; $i++)
		{
			if ( ! file_exists($path.$filename.$i.$this->file_ext))
			{
				$new_filename = $filename.$i.$this->file_ext;
				break;
			}
		}

		if ($new_filename === '')
		{
			$this->set_error('upload_bad_filename', 'debug');
			return FALSE;
		}
		else
		{
			return $new_filename;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set Maximum File Size
	 * 设置最大文件大小
	 * @param	int	$n
	 * @return	CI_Upload
	 */
	public function set_max_filesize($n)
	{
		$this->max_size = ($n < 0) ? 0 : (int) $n;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Maximum File Size
	 * 设置最大文件大小
	 * An internal alias to set_max_filesize() to help with configuration
	 * as initialize() will look for a set_<property_name>() method ...
	 * 内部别名set_max_filesize()帮助配置初始化()将寻找一个set_ < property_name >()方法……
	 * @param	int	$n
	 * @return	CI_Upload
	 */
	protected function set_max_size($n)
	{
		return $this->set_max_filesize($n);
	}

	// --------------------------------------------------------------------

	/**
	 * Set Maximum File Name Length
	 * 设置最大文件名称长度
	 * @param	int	$n
	 * @return	CI_Upload
	 */
	public function set_max_filename($n)
	{
		$this->max_filename = ($n < 0) ? 0 : (int) $n;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Maximum Image Width
	 * 设置最大图像宽度
	 * @param	int	$n
	 * @return	CI_Upload
	 */
	public function set_max_width($n)
	{
		$this->max_width = ($n < 0) ? 0 : (int) $n;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Maximum Image Height
	 * 设置最大图像高度
	 * @param	int	$n
	 * @return	CI_Upload
	 */
	public function set_max_height($n)
	{
		$this->max_height = ($n < 0) ? 0 : (int) $n;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set minimum image width
	 * 设置最小图像宽度
	 * @param	int	$n
	 * @return	CI_Upload
	 */
	public function set_min_width($n)
	{
		$this->min_width = ($n < 0) ? 0 : (int) $n;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set minimum image height
	 * 设置最小图像高度
	 * @param	int	$n
	 * @return	CI_Upload
	 */
	public function set_min_height($n)
	{
		$this->min_height = ($n < 0) ? 0 : (int) $n;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Allowed File Types
	 * 设置允许的文件类型
	 * @param	mixed	$types
	 * @return	CI_Upload
	 */
	public function set_allowed_types($types)
	{
		$this->allowed_types = (is_array($types) OR $types === '*')
			? $types
			: explode('|', $types);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Image Properties
	 * 设置图像属性
	 * Uses GD to determine the width/height/type of image
	 * 使用GD来确定图像的宽度/高度/类型
	 * @param	string	$path
	 * @return	CI_Upload
	 */
	public function set_image_properties($path = '')
	{
		if ($this->is_image() && function_exists('getimagesize'))
		{
			if (FALSE !== ($D = @getimagesize($path)))
			{
				$types = array(1 => 'gif', 2 => 'jpeg', 3 => 'png');

				$this->image_width	= $D[0];
				$this->image_height	= $D[1];
				$this->image_type	= isset($types[$D[2]]) ? $types[$D[2]] : 'unknown';
				$this->image_size_str	= $D[3]; // string containing height and width 字符串包含高度和宽度
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set XSS Clean
	 * 设置XSS清洁
	 * Enables the XSS flag so that the file that was uploaded
	 * will be run through the XSS filter.
	 * 使XSS国旗,这样上传的文件将贯穿XSS过滤器。
	 * @param	bool	$flag
	 * @return	CI_Upload
	 */
	public function set_xss_clean($flag = FALSE)
	{
		$this->xss_clean = ($flag === TRUE);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Validate the image
	 *
	 * @return	bool
	 */
	public function is_image()
	{
		// IE will sometimes return odd mime-types during upload, so here we just standardize all
		// jpegs or pngs to the same file type.
		// 即有时会返回奇怪的mime类型在上传,这里我们只是规范所有jpeg或png文件类型相同。

		$png_mimes  = array('image/x-png');
		$jpeg_mimes = array('image/jpg', 'image/jpe', 'image/jpeg', 'image/pjpeg');

		if (in_array($this->file_type, $png_mimes))
		{
			$this->file_type = 'image/png';
		}
		elseif (in_array($this->file_type, $jpeg_mimes))
		{
			$this->file_type = 'image/jpeg';
		}

		$img_mimes = array('image/gif',	'image/jpeg', 'image/png');

		return in_array($this->file_type, $img_mimes, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Verify that the filetype is allowed
	 * 验证文件类型是被允许的
	 * @param	bool	$ignore_mime
	 * @return	bool
	 */
	public function is_allowed_filetype($ignore_mime = FALSE)
	{
		if ($this->allowed_types === '*')
		{
			return TRUE;
		}

		if (empty($this->allowed_types) OR ! is_array($this->allowed_types))
		{
			$this->set_error('upload_no_file_types', 'debug');
			return FALSE;
		}

		$ext = strtolower(ltrim($this->file_ext, '.'));

		if ( ! in_array($ext, $this->allowed_types, TRUE))
		{
			return FALSE;
		}

		// Images get some additional checks 图像得到一些额外的检查
		if (in_array($ext, array('gif', 'jpg', 'jpeg', 'jpe', 'png'), TRUE) && @getimagesize($this->file_temp) === FALSE)
		{
			return FALSE;
		}

		if ($ignore_mime === TRUE)
		{
			return TRUE;
		}

		if (isset($this->_mimes[$ext]))
		{
			return is_array($this->_mimes[$ext])
				? in_array($this->file_type, $this->_mimes[$ext], TRUE)
				: ($this->_mimes[$ext] === $this->file_type);
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Verify that the file is within the allowed size
	 * 验证文件是在允许的大小
	 * @return	bool
	 */
	public function is_allowed_filesize()
	{
		return ($this->max_size === 0 OR $this->max_size > $this->file_size);
	}

	// --------------------------------------------------------------------

	/**
	 * Verify that the image is within the allowed width/height
	 * 验证图像内的允许宽度/高度
	 * @return	bool
	 */
	public function is_allowed_dimensions()
	{
		if ( ! $this->is_image())
		{
			return TRUE;
		}

		if (function_exists('getimagesize'))
		{
			$D = @getimagesize($this->file_temp);

			if ($this->max_width > 0 && $D[0] > $this->max_width)
			{
				return FALSE;
			}

			if ($this->max_height > 0 && $D[1] > $this->max_height)
			{
				return FALSE;
			}

			if ($this->min_width > 0 && $D[0] < $this->min_width)
			{
				return FALSE;
			}

			if ($this->min_height > 0 && $D[1] < $this->min_height)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Validate Upload Path
	 * 验证上传路径
	 * Verifies that it is a valid upload path with proper permissions.
	 * 验证它是一个有效的上传路径通过适当的权限。
	 * @return	bool
	 */
	public function validate_upload_path()
	{
		if ($this->upload_path === '')
		{
			$this->set_error('upload_no_filepath', 'error');
			return FALSE;
		}

		if (realpath($this->upload_path) !== FALSE)
		{
			$this->upload_path = str_replace('\\', '/', realpath($this->upload_path));
		}

		if ( ! is_dir($this->upload_path))
		{
			$this->set_error('upload_no_filepath', 'error');
			return FALSE;
		}

		if ( ! is_really_writable($this->upload_path))
		{
			$this->set_error('upload_not_writable', 'error');
			return FALSE;
		}

		$this->upload_path = preg_replace('/(.+?)\/*$/', '\\1/',  $this->upload_path);
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Extract the file extension
	 * 提取文件扩展名
	 * @param	string	$filename
	 * @return	string
	 */
	public function get_extension($filename)
	{
		$x = explode('.', $filename);

		if (count($x) === 1)
		{
			return '';
		}

		$ext = ($this->file_ext_tolower) ? strtolower(end($x)) : end($x);
		return '.'.$ext;
	}

	// --------------------------------------------------------------------

	/**
	 * Limit the File Name Length
	 * 限制文件名长度
	 * @param	string	$filename
	 * @param	int	$length
	 * @return	string
	 */
	public function limit_filename_length($filename, $length)
	{
		if (strlen($filename) < $length)
		{
			return $filename;
		}

		$ext = '';
		if (strpos($filename, '.') !== FALSE)
		{
			$parts		= explode('.', $filename);
			$ext		= '.'.array_pop($parts);
			$filename	= implode('.', $parts);
		}

		return substr($filename, 0, ($length - strlen($ext))).$ext;
	}

	// --------------------------------------------------------------------

	/**
	 * Runs the file through the XSS clean function
	 * 运行文件通过XSS清洁功能
	 * This prevents people from embedding malicious code in their files. 这可以防止人们在他们的文件中嵌入恶意代码。
	 * I'm not sure that it won't negatively affect certain files in unexpected ways, 我不确定它不会影响某些文件以意想不到的方式,
	 * but so far I haven't found that it causes trouble. 但是到目前为止我还没有发现它会引起麻烦。
	 *
	 * @return	string
	 */
	public function do_xss_clean()
	{
		$file = $this->file_temp;

		if (filesize($file) == 0)
		{
			return FALSE;
		}

		if (memory_get_usage() && ($memory_limit = ini_get('memory_limit')))
		{
			$memory_limit *= 1024 * 1024;

			// There was a bug/behavioural change in PHP 5.2, where numbers over one million get output 
			// 有一个bug /行为的改变在PHP 5.2中,数量超过一百万得到输出
			// into scientific notation. number_format() ensures this number is an integer
			// 科学记数法。number_format()确保这个数字是整数
			// http://bugs.php.net/bug.php?id=43053

			$memory_limit = number_format(ceil(filesize($file) + $memory_limit), 0, '.', '');

			ini_set('memory_limit', $memory_limit); // When an integer is used, the value is measured in bytes. - PHP.net 当使用一个整数时,以字节的值。——
		}

		// If the file being uploaded is an image, then we should have no problem with XSS attacks (in theory), but
		// IE can be fooled into mime-type detecting a malformed image as an html file, thus executing an XSS attack on anyone
		// using IE who looks at the image. It does this by inspecting the first 255 bytes of an image. To get around this
		// CI will itself look at the first 255 bytes of an image to determine its relative safety. This can save a lot of
		// processor power and time if it is actually a clean image, as it will be in nearly all instances _except_ an
		// attempted XSS attack.
		// 如果文件被上传是一个图像,然后我们应该没有问题,XSS攻击(理论上),但IE可以骗到mime类型检测畸形的形象作为一个html文件,因此执行使用IE XSS攻击谁看了图片。
		// 它通过检查第一个255字节的图像。为了解决这个词本身看第一个255字节的图像来确定其相对安全。这可以节省许多时间和处理器能力如果它实际上是一个干净的形象,
		// 因为它将在几乎所有情况下_except_未遂XSS攻击。

		if (function_exists('getimagesize') && @getimagesize($file) !== FALSE)
		{
			if (($file = @fopen($file, 'rb')) === FALSE) // "b" to force binary force二进制
			{
				return FALSE; // Couldn't open the file, return FALSE  不能打开文件,返回FALSE
			}

			$opening_bytes = fread($file, 256);
			fclose($file);

			// These are known to throw IE into mime-type detection chaos 这些已知IE扔进mime类型检测混乱
			// <a, <body, <head, <html, <img, <plaintext, <pre, <script, <table, <title
			// title is basically just in SVG, but we filter it anyhow 标题只是在SVG,但是我们无论如何过滤它

			// if it's an image or no "triggers" detected in the first 256 bytes - we're good 如果它是一个图像或没有“触发”在第一个256字节——我们发现好
			return ! preg_match('/<(a|body|head|html|img|plaintext|pre|script|table|title)[\s>]/i', $opening_bytes);
		}

		if (($data = @file_get_contents($file)) === FALSE)
		{
			return FALSE;
		}

		return $this->_CI->security->xss_clean($data, TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Set an error message
	 * 设置一个错误消息
	 * @param	string	$msg
	 * @return	CI_Upload
	 */
	public function set_error($msg, $log_level = 'error')
	{
		$this->_CI->lang->load('upload');

		is_array($msg) OR $msg = array($msg);
		foreach ($msg as $val)
		{
			$msg = ($this->_CI->lang->line($val) === FALSE) ? $val : $this->_CI->lang->line($val);
			$this->error_msg[] = $msg;
			log_message($log_level, $msg);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Display the error message
	 * 显示错误消息
	 * @param	string	$open
	 * @param	string	$close
	 * @return	string
	 */
	public function display_errors($open = '<p>', $close = '</p>')
	{
		return (count($this->error_msg) > 0) ? $open.implode($close.$open, $this->error_msg).$close : '';
	}

	// --------------------------------------------------------------------

	/**
	 * Prep Filename
	 * 预科文件名
	 * Prevents possible script execution from Apache's handling
	 * of files' multiple extensions.
	 * 防止可能的脚本执行Apache处理文件的多个扩展。
	 * @link	http://httpd.apache.org/docs/1.3/mod/mod_mime.html#multipleext
	 *
	 * @param	string	$filename
	 * @return	string
	 */
	protected function _prep_filename($filename)
	{
		if ($this->mod_mime_fix === FALSE OR $this->allowed_types === '*' OR ($ext_pos = strrpos($filename, '.')) === FALSE)
		{
			return $filename;
		}

		$ext = substr($filename, $ext_pos);
		$filename = substr($filename, 0, $ext_pos);
		return str_replace('.', '_', $filename).$ext;
	}

	// --------------------------------------------------------------------

	/**
	 * File MIME type
	 * 文件的MIME类型
	 * Detects the (actual) MIME type of the uploaded file, if possible. 检测(实际)上传文件的MIME类型,如果可能的话。
	 * The input array is expected to be $_FILES[$field] 输入数组将带有$_FILES($字段)
	 *
	 * @param	array	$file
	 * @return	void
	 */
	protected function _file_mime_type($file)
	{
		// We'll need this to validate the MIME info string (e.g. text/plain; charset=us-ascii) 我们需要这个来验证MIME信息字符串(例如文本/平原;charset = us - ascii)
		$regexp = '/^([a-z\-]+\/[a-z0-9\-\.\+]+)(;\s.+)?$/';

		/* Fileinfo extension - most reliable method
		 * Fileinfo扩展,最可靠的方法
		 * Unfortunately, prior to PHP 5.3 - it's only available as a PECL extension and the
		 * more convenient FILEINFO_MIME_TYPE flag doesn't exist.
		 * 不幸的是,PHP 5.3之前——这是只有PECL扩展和更方便FILEINFO_MIME_TYPE标识不存在。
		 */
		if (function_exists('finfo_file'))
		{
			$finfo = @finfo_open(FILEINFO_MIME);
			if (is_resource($finfo)) // It is possible that a FALSE value is returned, if there is no magic MIME database file found on the system
			{                            //有可能是一个错误的返回值,如果没有魔法MIME数据库文件系统上发现的
				$mime = @finfo_file($finfo, $file['tmp_name']);
				finfo_close($finfo);

				/* According to the comments section of the PHP manual page, 根据PHP手册页的评论部分,
				 * it is possible that this function returns an empty string 有可能是这个函数返回一个空字符串
				 * for some files (e.g. if they don't exist in the magic MIME database) 对一些文件(例如,如果他们不存在于魔法MIME数据库)
				 */
				if (is_string($mime) && preg_match($regexp, $mime, $matches))
				{
					$this->file_type = $matches[1];
					return;
				}
			}
		}

		/* This is an ugly hack, but UNIX-type systems provide a "native" way to detect the file type,
		 * which is still more secure than depending on the value of $_FILES[$field]['type'], and as it
		 * was reported in issue #750 (https://github.com/EllisLab/CodeIgniter/issues/750) - it's better
		 * than mime_content_type() as well, hence the attempts to try calling the command line with
		 * three different functions.
		 * 这是一个丑陋的黑客,但类unix系统提供一种“本地”的方式来检测文件类型,仍比的值取决于安全带有$_file($场)(“类型”),
		 * 据报道在问题# 750(https://github.com/EllisLab/CodeIgniter/issues/750)——这比mime_content_type(),
		 * 因此,试图尝试调用命令行,有三个不同的功能。
		 * Notes: 注释:
		 *	- the DIRECTORY_SEPARATOR comparison ensures that we're not on a Windows system       DIRECTORY_SEPARATOR比较确保我们不是在Windows系统上
		 *	- many system admins would disable the exec(), shell_exec(), popen() and similar functions 许多系统管理员可以禁用exec(),shell_exec(),popen()和类似的功能
		 *	  due to security concerns, hence the function_usable() checks   由于安全问题,因此function_usable()检查
		 */
		if (DIRECTORY_SEPARATOR !== '\\')
		{
			$cmd = function_exists('escapeshellarg')
				? 'file --brief --mime '.escapeshellarg($file['tmp_name']).' 2>&1'
				: 'file --brief --mime '.$file['tmp_name'].' 2>&1';

			if (function_usable('exec'))
			{
				/* This might look confusing, as $mime is being populated with all of the output when set in the second parameter.
				 * 这看起来令人困惑,因为美元mime正在填充所有的输出时,设置在第二个参数。
				 * However, we only need the last line, which is the actual return value of exec(), and as such - it overwrites
				 * anything that could already be set for $mime previously. This effectively makes the second parameter a dummy
				 * value, which is only put to allow us to get the return status code.
				 * 然而,我们只需要最后一行,这是实际的exec()的返回值,因此,它覆盖任何可能已经被设置为mime之前。
				 * 这有效地使一个假值,第二个参数是只允许我们返回状态代码。
				 */
				$mime = @exec($cmd, $mime, $return_status);
				if ($return_status === 0 && is_string($mime) && preg_match($regexp, $mime, $matches))
				{
					$this->file_type = $matches[1];
					return;
				}
			}

			if ( ! ini_get('safe_mode') && function_usable('shell_exec'))
			{
				$mime = @shell_exec($cmd);
				if (strlen($mime) > 0)
				{
					$mime = explode("\n", trim($mime));
					if (preg_match($regexp, $mime[(count($mime) - 1)], $matches))
					{
						$this->file_type = $matches[1];
						return;
					}
				}
			}

			if (function_usable('popen'))
			{
				$proc = @popen($cmd, 'r');
				if (is_resource($proc))
				{
					$mime = @fread($proc, 512);
					@pclose($proc);
					if ($mime !== FALSE)
					{
						$mime = explode("\n", trim($mime));
						if (preg_match($regexp, $mime[(count($mime) - 1)], $matches))
						{
							$this->file_type = $matches[1];
							return;
						}
					}
				}
			}
		}

		// Fall back to the deprecated mime_content_type(), if available (still better than $_FILES[$field]['type']) 回落到弃用mime_content_type(),如果可用
		if (function_exists('mime_content_type'))
		{
			$this->file_type = @mime_content_type($file['tmp_name']);
			if (strlen($this->file_type) > 0) // It's possible that mime_content_type() returns FALSE or an empty string 有可能mime_content_type()返回FALSE或一个空字符串
			{
				return;
			}
		}

		$this->file_type = $file['type'];
	}

}
