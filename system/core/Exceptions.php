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
 * Exceptions Class 异常类
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Exceptions
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/exceptions.html
 */
class CI_Exceptions {

	/**
	 * Nesting level of the output buffering mechanism
	 * 嵌套级的输出缓冲机制
	 * @var	int
	 */
	public $ob_level;

	/**
	 * List of available error levels
	 * 可用的错误列表的水平
	 * @var	array
	 */
	public $levels = array(
		E_ERROR			=>	'Error',
		E_WARNING		=>	'Warning',
		E_PARSE			=>	'Parsing Error',
		E_NOTICE		=>	'Notice',
		E_CORE_ERROR		=>	'Core Error',
		E_CORE_WARNING		=>	'Core Warning',
		E_COMPILE_ERROR		=>	'Compile Error',
		E_COMPILE_WARNING	=>	'Compile Warning',
		E_USER_ERROR		=>	'User Error',
		E_USER_WARNING		=>	'User Warning',
		E_USER_NOTICE		=>	'User Notice',
		E_STRICT		=>	'Runtime Notice'
	);

	/**
	 * Class constructor
	 * 构造函数 类构造器 
	 * @return	void
	 */
	public function __construct()
	{
		$this->ob_level = ob_get_level();
		// Note: Do not log messages from this constructor. 注意:不要记录消息从这个构造函数。
	}

	// --------------------------------------------------------------------

	/**
	 * Exception Logger
	 * 异常日志记录器
	 * Logs PHP generated error messages
	 * 日志PHP生成的错误消息
	 * @param	int	$severity	Log level 日志级别
	 * @param	string	$message	Error message 出错信息
	 * @param	string	$filepath	File path 文件路径
	 * @param	int	$line		Line number 行号
	 * @return	void
	 */
	public function log_exception($severity, $message, $filepath, $line)
	{
		$severity = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;
		log_message('error', 'Severity: '.$severity.' --> '.$message.' '.$filepath.' '.$line);
	}

	// --------------------------------------------------------------------

	/**
	 * 404 Error Handler
	 * 404错误处理程序
	 * @uses	CI_Exceptions::show_error()
	 *
	 * @param	string	$page		Page URI 页面URI
	 * @param 	bool	$log_error	Whether to log the error 是否记录错误
	 * @return	void
	 */
	public function show_404($page = '', $log_error = TRUE)
	{
		if (is_cli())
		{
			$heading = 'Not Found';
			$message = 'The controller/method pair you requested was not found控制器/方法对你请求的文件不存在.';
		}
		else
		{
			$heading = '404 Page Not Found';
			$message = 'The page you requested was not found你请求的页面不存在.';
		}

		// By default we log this, but allow a dev to skip it 默认情况下我们记录这些,但允许开发过程跳过它
		if ($log_error)
		{
			log_message('error', $heading.': '.$page);
		}

		echo $this->show_error($heading, $message, 'error_404', 404);
		exit(4); // EXIT_UNKNOWN_FILE
	}

	// --------------------------------------------------------------------

	/**
	 * General Error Page
	 * 一般错误页面
	 * Takes an error message as input (either as a string or an array)
	 * and displays it using the specified template.
	 * 以一个错误消息作为输入(作为字符串或一个数组)和显示它使用指定的模板。
	 *
	 * @param	string		$heading	Page heading 页标题
	 * @param	string|string[]	$message	Error message 出错信息
	 * @param	string		$template	Template name 模板名称
	 * @param 	int		$status_code	(default: 500)
	 *
	 * @return	string	Error page output 错误页面输出
	 */
	public function show_error($heading, $message, $template = 'error_general', $status_code = 500)
	{
		$templates_path = config_item('error_views_path');
		if (empty($templates_path))
		{
			$templates_path = VIEWPATH.'errors'.DIRECTORY_SEPARATOR;
		}

		if (is_cli())
		{
			$message = "\t".(is_array($message) ? implode("\n\t", $message) : $message);
			$template = 'cli'.DIRECTORY_SEPARATOR.$template;
		}
		else
		{
			set_status_header($status_code);
			$message = '<p>'.(is_array($message) ? implode('</p><p>', $message) : $message).'</p>';
			$template = 'html'.DIRECTORY_SEPARATOR.$template;
		}

		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}
		ob_start();
		include($templates_path.$template.'.php');
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}

	// --------------------------------------------------------------------
	/**
	 * show_exception 显示异常(供旧版使用)
	 * 字符串,这将返回输出缓冲区的内容或假,如果输出缓冲不活跃。
	 * @param string $exception
	 * @return	string	Error page output 错误页面输出
	 */

	public function show_exception($exception)
	{
		$templates_path = config_item('error_views_path');
		if (empty($templates_path))
		{
			$templates_path = VIEWPATH.'errors'.DIRECTORY_SEPARATOR;
		}

		$message = $exception->getMessage();
		if (empty($message))
		{
			$message = '(null)';
		}

		if (is_cli())
		{
			$templates_path .= 'cli'.DIRECTORY_SEPARATOR;
		}
		else
		{
			set_status_header(500);
			$templates_path .= 'html'.DIRECTORY_SEPARATOR;
		}

		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}

		ob_start();
		include($templates_path.'error_exception.php');
		$buffer = ob_get_contents();
		ob_end_clean();
		echo $buffer;
	}

	// --------------------------------------------------------------------

	/**
	 * Native PHP error handler
	 * 原生PHP错误处理程序
	 * @param	int	$severity	Error level 错误级别 
	 * @param	string	$message	Error message 出错信息
	 * @param	string	$filepath	File path 文件路径
	 * @param	int	$line		Line number 行号
	 * @return	string	Error page output 错误页面输出
	 */
	public function show_php_error($severity, $message, $filepath, $line)
	{
		$templates_path = config_item('error_views_path');
		if (empty($templates_path))
		{
			$templates_path = VIEWPATH.'errors'.DIRECTORY_SEPARATOR;
		}

		$severity = isset($this->levels[$severity]) ? $this->levels[$severity] : $severity;

		// For safety reasons we don't show the full file path in non-CLI requests
		if ( ! is_cli())
		{
			$filepath = str_replace('\\', '/', $filepath);
			if (FALSE !== strpos($filepath, '/'))
			{
				$x = explode('/', $filepath);
				$filepath = $x[count($x)-2].'/'.end($x);
			}

			$template = 'html'.DIRECTORY_SEPARATOR.'error_php';
		}
		else
		{
			$template = 'cli'.DIRECTORY_SEPARATOR.'error_php';
		}

		if (ob_get_level() > $this->ob_level + 1)
		{
			ob_end_flush();
		}
		ob_start();
		include($templates_path.$template.'.php');
		$buffer = ob_get_contents();
		ob_end_clean();
		echo $buffer;
	}

}
