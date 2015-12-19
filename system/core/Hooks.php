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
 * Hooks Class
 * 钩子类
 * Provides a mechanism to extend the base system without hacking.
 * 提供了一种机制来扩展基本系统没有窃听。
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/general/hooks.html
 */
class CI_Hooks {

	/**
	 * Determines whether hooks are enabled
	 * 决定是否启用钩子
	 * @var	bool
	 */
	public $enabled = FALSE;

	/**
	 * List of all hooks set in config/hooks.php
	 * 所有的钩子在配置/ hooks.php列表
	 * @var	array
	 */
	public $hooks =	array();

	/**
	 * Array with class objects to use hooks methods
	 * 与类对象数组,使用钩子方法
	 * @var array
	 */
	protected $_objects = array();

	/**
	 * In progress flag
	 * 在进步的旗帜
	 * Determines whether hook is in progress, used to prevent infinte loops
	 * 确定是否在进步,钩用于防止infinte循环
	 * @var	bool
	 */
	protected $_in_progress = FALSE;

	/**
	 * Class constructor
	 * 构造函数 类构造器 
	 * @return	void
	 */
	public function __construct()
	{
		$CFG =& load_class('Config', 'core');
		log_message('info', 'Hooks Class Initialized');

		// If hooks are not enabled in the config file 如果钩子不启用配置文件
		// there is nothing else to do 有什么其他的事要做
		if ($CFG->item('enable_hooks') === FALSE)
		{
			return;
		}

		// Grab the "hooks" definition file. 抓住“钩子”定义文件。
		if (file_exists(APPPATH.'config/hooks.php'))
		{
			include(APPPATH.'config/hooks.php');
		}

		if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/hooks.php'))
		{
			include(APPPATH.'config/'.ENVIRONMENT.'/hooks.php');
		}

		// If there are no hooks, we're done. 如果没有钩子,做完了。
		if ( ! isset($hook) OR ! is_array($hook))
		{
			return;
		}

		$this->hooks =& $hook;
		$this->enabled = TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Call Hook 调用钩子
	 *
	 * Calls a particular hook. Called by CodeIgniter.php.
	 * 调用一个特定的钩。叫CodeIgniter.php。
	 * @uses	CI_Hooks::_run_hook()
	 *
	 * @param	string	$which	Hook name 钩子名称
	 * @return	bool	TRUE on success or FALSE on failure 成功为真，失败为假
	 */
	public function call_hook($which = '')
	{
		if ( ! $this->enabled OR ! isset($this->hooks[$which]))
		{
			return FALSE;
		}

		if (is_array($this->hooks[$which]) && ! isset($this->hooks[$which]['function']))
		{
			foreach ($this->hooks[$which] as $val)
			{
				$this->_run_hook($val);
			}
		}
		else
		{
			$this->_run_hook($this->hooks[$which]);
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Run Hook 运行钩子
	 *
	 * Runs a particular hook 运行一个特定的钩
	 *
	 * @param	array	$data	Hook details 钩子细节
	 * @return	bool	TRUE on success or FALSE on failure 成功为真，失败为假
	 */
	protected function _run_hook($data)
	{
		// Closures/lambda functions and array($object, 'method') callables闭包/ lambda函数和数组($对象,“方法”)调用
		if (is_callable($data))
		{
			is_array($data)
				? $data[0]->{$data[1]}()
				: $data();

			return TRUE;
		}
		elseif ( ! is_array($data))
		{
			return FALSE;
		}

		// -----------------------------------
		// Safety - Prevents run-away loops 安全,防止循环失控
		// -----------------------------------

		// If the script being called happens to have the same
		// hook call within it a loop can happen
		// 如果脚本被调用发生在有相同的钩内调用一个循环可以发生
		if ($this->_in_progress === TRUE)
		{
			return;
		}

		// -----------------------------------
		// Set file path 设置文件路径
		// -----------------------------------

		if ( ! isset($data['filepath'], $data['filename']))
		{
			return FALSE;
		}

		$filepath = APPPATH.$data['filepath'].'/'.$data['filename'];

		if ( ! file_exists($filepath))
		{
			return FALSE;
		}

		// Determine and class and/or function names 确定和类和/或函数名
		$class		= empty($data['class']) ? FALSE : $data['class'];
		$function	= empty($data['function']) ? FALSE : $data['function'];
		$params		= isset($data['params']) ? $data['params'] : '';

		if (empty($function))
		{
			return FALSE;
		}

		// Set the _in_progress flag 设置_in_progress国旗
		$this->_in_progress = TRUE;

		// Call the requested class and/or function 调用请求的类和/或功能
		if ($class !== FALSE)
		{
			// The object is stored? 对象存储?
			if (isset($this->_objects[$class]))
			{
				if (method_exists($this->_objects[$class], $function))
				{
					$this->_objects[$class]->$function($params);
				}
				else
				{
					return $this->_in_progress = FALSE;
				}
			}
			else
			{
				class_exists($class, FALSE) OR require_once($filepath);

				if ( ! class_exists($class, FALSE) OR ! method_exists($class, $function))
				{
					return $this->_in_progress = FALSE;
				}

				// Store the object and execute the method 存储对象和执行方法
				$this->_objects[$class] = new $class();
				$this->_objects[$class]->$function($params);
			}
		}
		else
		{
			function_exists($function) OR require_once($filepath);

			if ( ! function_exists($function))
			{
				return $this->_in_progress = FALSE;
			}

			$function($params);
		}

		$this->_in_progress = FALSE;
		return TRUE;
	}

}
