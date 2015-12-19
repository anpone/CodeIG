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
 * CodeIgniter Driver Library Class
 * CodeIgniter驱动程序库类
 * This class enables you to create "Driver" libraries that add runtime ability
 * to extend the capabilities of a class via additional driver objects
 * 这个类允许您创建“driver”库添加运行时能够扩展一个类的功能通过额外的驱动程序对象
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link
 */
class CI_Driver_Library {

	/**
	 * Array of drivers that are available to use with the driver class
	 * drivers数组可以使用驱动程序类
	 * @var array
	 */
	protected $valid_drivers = array();

	/**
	 * Name of the current class - usually the driver class
	 * 当前类的名称——通常驱动程序类
	 * @var string
	 */
	protected $lib_name;

	/**
	 * Get magic method
	 * 得到魔术方法
	 * The first time a child is used it won't exist, so we instantiate it
	 * subsequents calls will go straight to the proper child.
	 * 第一次使用它不会存在,所以我们直接实例化它随后的调用将会适当的孩子。
	 * @param	string	Child class name 子类名称
	 * @return	object	Child class
	 */
	public function __get($child)
	{
		// Try to load the driver 试图加载驱动程序
		return $this->load_driver($child);
	}

	/**
	 * Load driver
	 * 加载驱动
	 * Separate load_driver call to support explicit driver load by library or user
	 * 单独load_driver调用显式驱动程序加载的库或用户的支持
	 * @param	string	Driver name (w/o parent prefix父母的前缀)
	 * @return	object	Child class
	 */
	public function load_driver($child)
	{
		// Get CodeIgniter instance and subclass prefix 得到CodeIgniter实例和子类前缀
		$prefix = config_item('subclass_prefix');

		if ( ! isset($this->lib_name))
		{
			// Get library name without any prefix 库名称没有任何前缀
			$this->lib_name = str_replace(array('CI_', $prefix), '', get_class($this));
		}

		// The child will be prefixed with the parent lib 子类会以父母为前缀的自由
		$child_name = $this->lib_name.'_'.$child;

		// See if requested child is a valid driver 要求子类是否有效的driver
		if ( ! in_array($child, $this->valid_drivers))
		{
			// The requested driver isn't valid!  要求driver不是有效!
			$msg = 'Invalid driver requested: '.$child_name;
			log_message('error', $msg);
			show_error($msg);
		}

		// Get package paths and filename case variations to search 得到包路径和文件名搜索情况变化
		$CI = get_instance();
		$paths = $CI->load->get_package_paths(TRUE);

		// Is there an extension? 有一个扩展吗?
		$class_name = $prefix.$child_name;
		$found = class_exists($class_name, FALSE);
		if ( ! $found)
		{
			// Check for subclass file 检查文件子类
			foreach ($paths as $path)
			{
				// Does the file exist? 文件存在吗?
				$file = $path.'libraries/'.$this->lib_name.'/drivers/'.$prefix.$child_name.'.php';
				if (file_exists($file))
				{
					// Yes - require base class from BASEPATH 是的,需要从BASEPATH基类
					$basepath = BASEPATH.'libraries/'.$this->lib_name.'/drivers/'.$child_name.'.php';
					if ( ! file_exists($basepath))
					{
						$msg = 'Unable to load the requested class: CI_'.$child_name;
						log_message('error', $msg);
						show_error($msg);
					}

					// Include both sources and mark found 包括来源和被发现的标记
					include_once($basepath);
					include_once($file);
					$found = TRUE;
					break;
				}
			}
		}

		// Do we need to search for the class? 我们需要搜索的类?
		if ( ! $found)
		{
			// Use standard class name 使用标准的类名
			$class_name = 'CI_'.$child_name;
			if ( ! class_exists($class_name, FALSE))
			{
				// Check package paths 检查包的路径
				foreach ($paths as $path)
				{
					// Does the file exist? 文件存在吗?
					$file = $path.'libraries/'.$this->lib_name.'/drivers/'.$child_name.'.php';
					if (file_exists($file))
					{
						// Include source 包括源
						include_once($file);
						break;
					}
				}
			}
		}

		// Did we finally find the class? 我们终于找到类吗?
		if ( ! class_exists($class_name, FALSE))
		{
			if (class_exists($child_name, FALSE))
			{
				$class_name = $child_name;
			}
			else
			{
				$msg = 'Unable to load the requested driver: '.$class_name;
				log_message('error', $msg);
				show_error($msg);
			}
		}
 
		// Instantiate, decorate and add child  实例化,装修和添加的子类
		$obj = new $class_name();
		$obj->decorate($this);
		$this->$child = $obj;
		return $this->$child;
	}

}

// --------------------------------------------------------------------------

/**
 * CodeIgniter Driver Class
 * CodeIgniter驱动程序类
 * This class enables you to create drivers for a Library based on the Driver Library.
 * It handles the drivers' access to the parent library
 * 这个类允许您创建驱动程序库基于驱动程序库。它处理drivers的访问父库
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link
 */
class CI_Driver {

	/**
	 * Instance of the parent class
	 * 父类的实例
	 * @var object
	 */
	protected $_parent;

	/**
	 * List of methods in the parent class
	 * 父类中的方法的列表
	 * @var array
	 */
	protected $_methods = array();

	/**
	 * List of properties in the parent class
	 * 在父类的属性列表
	 * @var array
	 */
	protected $_properties = array();

	/**
	 * Array of methods and properties for the parent class(es)
	 * 父类的方法和属性数组(es)
	 * @static 
	 * @var	array
	 */
	protected static $_reflections = array();

	/**
	 * Decorate
	 * 装饰
	 * Decorates the child with the parent driver lib's methods and properties
	 * 装饰子类的父母driver自由的方法和属性
	 * @param	object
	 * @return	void
	 */
	public function decorate($parent)
	{
		$this->_parent = $parent;

		// Lock down attributes to what is defined in the class 锁定属性中定义的类
		// and speed up references in magic methods 和加速魔法方法中的引用

		$class_name = get_class($parent);

		if ( ! isset(self::$_reflections[$class_name]))
		{
			$r = new ReflectionObject($parent);

			foreach ($r->getMethods() as $method)
			{
				if ($method->isPublic())
				{
					$this->_methods[] = $method->getName();
				}
			}

			foreach ($r->getProperties() as $prop)
			{
				if ($prop->isPublic())
				{
					$this->_properties[] = $prop->getName();
				}
			}

			self::$_reflections[$class_name] = array($this->_methods, $this->_properties);
		}
		else
		{
			list($this->_methods, $this->_properties) = self::$_reflections[$class_name];
		}
	}

	// --------------------------------------------------------------------

	/**
	 * __call magic method
	 * __call魔术方法
	 * Handles access to the parent driver library's methods
	 * 处理访问父驱动程序库的方法
	 * @param	string
	 * @param	array
	 * @return	mixed
	 */
	public function __call($method, $args = array())
	{
		if (in_array($method, $this->_methods))
		{
			return call_user_func_array(array($this->_parent, $method), $args);
		}

		throw new BadMethodCallException('No such method: '.$method.'()');
	}

	// --------------------------------------------------------------------

	/**
	 * __get magic method
	 * __get魔术方法
	 * Handles reading of the parent driver library's properties
	 * 父drivers的处理阅读库的属性
	 * @param	string
	 * @return	mixed
	 */
	public function __get($var)
	{
		if (in_array($var, $this->_properties))
		{
			return $this->_parent->$var;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * __set magic method
	 * __set魔术方法
	 * Handles writing to the parent driver library's properties
	 * 处理写入父驱动程序库的属性
	 * @param	string
	 * @param	array
	 * @return	mixed
	 */
	public function __set($var, $val)
	{
		if (in_array($var, $this->_properties))
		{
			$this->_parent->$var = $val;
		}
	}

}
