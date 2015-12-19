<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *一个开源的PHP应用程序开发框架
 *
 * This content is released under the MIT License (MIT)
 *这些内容是在MIT许可下发布(麻省理工学院)
 *
 * Copyright (c) 2014 - 2015, British Columbia Institute of Technology
 *版权(c)2014 - 2015年,不列颠哥伦比亚理工学院
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *特此授予许可,免费的,任何人获得这个软件和相关的文档文件的副本(“软件”),解决在软件没有限制,包括但不限于权利使用、复制、修改、合并、出版、发行、有偿、和/或销售的软件副本,并允许他们软件摆设,应当具备下列条件:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *上述版权声明和本许可声明应当包含在　　*所有副本或实质性部分的软件。
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *软件提供了“是”,没有任何类型的保证,明示或暗示,包括但不限于适销性的保证,健身为特定目的和无侵犯。在事件不得作者或版权持有人承担任何索赔、损失或其他责任,无论是在一个动作的合同,侵权或否则,引起的,或与软件或使用或其他交易软件。
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
defined('BASEPATH') OR exit('No direct script access allowed不允许直接脚本访问');

/**
 * System Initialization File
 *系统初始化文件
 *
 * Loads the base classes and executes the request.
 *加载基类并执行请求。
 *
 * @package		CodeIgniter
 * @subpackage	CodeIgniter
 * @category	Front-controller
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/
 */

/**
 * CodeIgniter Version 版本
 *
 * @var	string
 *
 */
	define('CI_VERSION', '3.0.2');

/*
 * ------------------------------------------------------
 *  Load the framework constants 加载框架的常量
 * ------------------------------------------------------
 */
	if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/constants.php'))
	{
		require_once(APPPATH.'config/'.ENVIRONMENT.'/constants.php');
	}

	require_once(APPPATH.'config/constants.php');

/*
 * ------------------------------------------------------
 *  Load the global functions 加载全局函数
 * ------------------------------------------------------
 */
	require_once(BASEPATH.'core/Common.php');


/*
 * ------------------------------------------------------
 * Security procedures 安全程序 密保措施
 * ------------------------------------------------------
 */

if ( ! is_php('5.4'))
{
	ini_set('magic_quotes_runtime', 0);

	if ((bool) ini_get('register_globals'))
	{
		$_protected = array(
			'_SERVER',
			'_GET',
			'_POST',
			'_FILES',
			'_REQUEST',
			'_SESSION',
			'_ENV',
			'_COOKIE',
			'GLOBALS',
			'HTTP_RAW_POST_DATA',
			'system_path',
			'application_folder',
			'view_folder',
			'_protected',
			'_registered'
		);

		$_registered = ini_get('variables_order');
		foreach (array('E' => '_ENV', 'G' => '_GET', 'P' => '_POST', 'C' => '_COOKIE', 'S' => '_SERVER') as $key => $superglobal)
		{
			if (strpos($_registered, $key) === FALSE)
			{
				continue;
			}

			foreach (array_keys($$superglobal) as $var)
			{
				if (isset($GLOBALS[$var]) && ! in_array($var, $_protected, TRUE))
				{
					$GLOBALS[$var] = NULL;
				}
			}
		}
	}
}


/*
 * ------------------------------------------------------
 *  Define a custom error handler so we can log PHP errors 定义一个自定义错误处理程序我们可以记录PHP错误日志
 * ------------------------------------------------------
 */
	set_error_handler('_error_handler');
	set_exception_handler('_exception_handler');
	register_shutdown_function('_shutdown_handler');

/*
 * ------------------------------------------------------
 *  Set the subclass_prefix 设置subclass_prefix
 * ------------------------------------------------------
 *
 * Normally the "subclass_prefix" is set in the config file.
 * The subclass prefix allows CI to know if a core class is
 * being extended via a library in the local application
 * "libraries" folder. Since CI allows config items to be
 * overridden via data set in the main index.php file,
 * before proceeding we need to know if a subclass_prefix
 * override exists. If so, we will set this value now,
 * before any classes are loaded
 * 通常“subclass_prefix”是在配置文件中设置。子类前缀允许CI知道核心类是扩展本地应用程序通过一个图书馆“库”文件夹中。由于CI允许配置项目通过数据集被覆盖在主索引index.php文件,在继续之前,我们需要知道subclass_prefix覆盖存在。如果是这样,在任何类装载以后，我们设置这个值,
 * 
 * Note: Since the config file data is cached it doesn't
 * hurt to load it here.
 * 注意:由于配置文件数据缓存加载在这里也无妨。
 */
	if ( ! empty($assign_to_config['subclass_prefix']))
	{
		get_config(array('subclass_prefix' => $assign_to_config['subclass_prefix']));
	}

/*
 * ------------------------------------------------------
 *  Should we use a Composer autoloader? 我们应该使用一个设计者自动加载器吗?
 * ------------------------------------------------------
 */
	if ($composer_autoload = config_item('composer_autoload'))
	{
		if ($composer_autoload === TRUE)
		{
			file_exists(APPPATH.'vendor/autoload.php')
				? require_once(APPPATH.'vendor/autoload.php')
				: log_message('error', '$config[\'composer_autoload\'] is set to TRUE but '.APPPATH.'vendor/autoload.php was not found.');
		}
		elseif (file_exists($composer_autoload))
		{
			require_once($composer_autoload);
		}
		else
		{
			log_message('error', 'Could not find the specified找不到指定的 $config[\'composer_autoload\'] path: '.$composer_autoload);
		}
	}

/*
 * ------------------------------------------------------
 *  Start the timer... tick tock tick tock... 启动计时器……滴滴答答滴滴答答……
 * ------------------------------------------------------
 */
	$BM =& load_class('Benchmark', 'core');
	$BM->mark('total_execution_time_start');
	$BM->mark('loading_time:_base_classes_start');

/*
 * ------------------------------------------------------
 *  Instantiate the hooks class 实例化钩类
 * ------------------------------------------------------
 */
	$EXT =& load_class('Hooks', 'core'); 

/*
 * ------------------------------------------------------
 *  Is there a "pre_system" hook? 有“pre_system钩吗?
 * ------------------------------------------------------
 */
	$EXT->call_hook('pre_system');

/*
 * ------------------------------------------------------
 *  Instantiate the config class 实例化配置类
 * ------------------------------------------------------
 *
 * Note: It is important that Config is loaded first as
 * most other classes depend on it either directly or by
 * depending on another class that uses it.
 * 注意:重要的是,像大多数其他类配置加载第一个依靠它直接或通过取决于使用它的另一个类。
 *
 */
	$CFG =& load_class('Config', 'core');

	// Do we have any manually set config items in the index.php file? 我们有手动设置配置项的索引index.php文件吗?
	if (isset($assign_to_config) && is_array($assign_to_config))
	{
		foreach ($assign_to_config as $key => $value)
		{
			$CFG->set_item($key, $value);
		}
	}

/*
 * ------------------------------------------------------
 * Important charset-related stuff 重要charset-related东西
 * ------------------------------------------------------
 *
 * Configure mbstring and/or iconv if they are enabled
 * and set MB_ENABLED and ICONV_ENABLED constants, so
 * that we don't repeatedly do extension_loaded() or
 * function_exists() calls.
 * 配置mbstring和/或iconv如果启用和设置MB_ENABLED ICONV_ENABLED常数,这样我们就不会反复做extension_loaded()或function_exists()调用。
 *
 * Note: UTF-8 class depends on this. It used to be done
 * in it's constructor, but it's _not_ class-specific.
 *注意:utf - 8类取决于此。以前做的在它的构造函数,但它是非职业专用。
 */
	$charset = strtoupper(config_item('charset'));
	ini_set('default_charset', $charset);

	if (extension_loaded('mbstring'))
	{
		define('MB_ENABLED', TRUE);
		// mbstring.internal_encoding is deprecated starting with PHP 5.6
		// and it's usage triggers E_DEPRECATED messages.
		//mbstring.internal_encoding被弃用在PHP 5.6 开始使用触发器E_DEPRECATED消息。
		@ini_set('mbstring.internal_encoding', $charset);
		// This is required for mb_convert_encoding() to strip invalid characters.这是所需mb_convert_encoding()条无效字符。
		// That's utilized by CI_Utf8, but it's also done for consistency with iconv. 利用CI_Utf8,但也做了与iconv一致性。
		mb_substitute_character('none');
	}
	else
	{
		define('MB_ENABLED', FALSE);
	}

	// There's an ICONV_IMPL constant, but the PHP manual says that using
	// iconv's predefined constants is "strongly discouraged".
	//有一个ICONV_IMPL常数,但PHP手册说,使用iconv预定义常量是"强烈的打击"
	if (extension_loaded('iconv'))
	{
		define('ICONV_ENABLED', TRUE);
		// iconv.internal_encoding is deprecated starting with PHP 5.6
		// and it's usage triggers E_DEPRECATED messages.
		//iconv.internal_encoding弃用PHP 5.6开始这是使用触发器E_DEPRECATED消息。
		@ini_set('iconv.internal_encoding', $charset);
	}
	else
	{
		define('ICONV_ENABLED', FALSE);
	}

	if (is_php('5.6'))
	{
		ini_set('php.internal_encoding', $charset);
	}

/*
 * ------------------------------------------------------
 *  Load compatibility features 加载兼容性特性
 * ------------------------------------------------------
 */

	require_once(BASEPATH.'core/compat/mbstring.php');
	require_once(BASEPATH.'core/compat/hash.php');
	require_once(BASEPATH.'core/compat/password.php');
	require_once(BASEPATH.'core/compat/standard.php');

/*
 * ------------------------------------------------------
 *  Instantiate the UTF-8 class 实例化utf - 8类
 * ------------------------------------------------------
 */
	$UNI =& load_class('Utf8', 'core');

/*
 * ------------------------------------------------------
 *  Instantiate the URI class 实例化URI类
 * ------------------------------------------------------
 */
	$URI =& load_class('URI', 'core');

/*
 * ------------------------------------------------------
 *  Instantiate the routing class and set the routing 实例化路由类并设置路由
 * ------------------------------------------------------
 */
	$RTR =& load_class('Router', 'core', isset($routing) ? $routing : NULL);

/*
 * ------------------------------------------------------
 *  Instantiate the output class 实例化输出类
 * ------------------------------------------------------
 */
	$OUT =& load_class('Output', 'core');

/*
 * ------------------------------------------------------
 *	Is there a valid cache file? If so, we're done... 有一个有效的缓存文件吗?如果是这样,做完了…
 * ------------------------------------------------------
 */
	if ($EXT->call_hook('cache_override') === FALSE && $OUT->_display_cache($CFG, $URI) === TRUE)
	{
		exit;
	}

/*
 * -----------------------------------------------------
 * Load the security class for xss and csrf support 加载的安全类xss和csrf的支持
 * -----------------------------------------------------
 */
	$SEC =& load_class('Security', 'core');

/*
 * ------------------------------------------------------
 *  Load the Input class and sanitize globals 加载的输入类和清洁全局
 * ------------------------------------------------------
 */
	$IN	=& load_class('Input', 'core');

/*
 * ------------------------------------------------------
 *  Load the Language class 加载语言类
 * ------------------------------------------------------
 */
	$LANG =& load_class('Lang', 'core');

/*
 * ------------------------------------------------------
 *  Load the app controller and local controller 加载应用程序控制器和本地控制器
 * ------------------------------------------------------
 *
 */
	// Load the base controller class 加载基本控制器类
	require_once BASEPATH.'core/Controller.php';

	/**
	 * Reference to the CI_Controller method. CI_Controller方法的引用
	 *
	 * Returns current CI instance object 返回当前CI实例对象
	 *
	 * @return object 对象
	 */
	function &get_instance()
	{
		return CI_Controller::get_instance();
	}

	if (file_exists(APPPATH.'core/'.$CFG->config['subclass_prefix'].'Controller.php'))
	{
		require_once APPPATH.'core/'.$CFG->config['subclass_prefix'].'Controller.php';
	}

	// Set a mark point for benchmarking 设置一个标记点为基准
	$BM->mark('loading_time:_base_classes_end');

/*
 * ------------------------------------------------------
 *  Sanity checks 心智健全的检查
 * ------------------------------------------------------
 *
 *  The Router class has already validated the request,
 *  leaving us with 3 options here:
 *路由器类已经验证请求, 　　*留给我们三个选项:
 *
 *	1) an empty class name, if we reached the default
 *	   controller, but it didn't exist;
 *	2) a query string which doesn't go through a
 *	   file_exists() check
 *	3) a regular request for a non-existing page
 *1)一个空类名称,如果我们达到了默认的控制器,但它不存在; 2)查询字符串不经历file_exists()检查; 3)常规请求不存在的页面
 *
 *  We handle all of these as a 404 error. 我们处理所有这些作为一个404错误
 *
 *  Furthermore, none of the methods in the app controller
 *  or the loader class can be called via the URI, nor can
 *  controller methods that begin with an underscore.
 *  此外,所有的应用程序控制器或装入器类中的方法可以通过URI调用控制器的方法也开始凸显。
 */

	$e404 = FALSE;
	$class = ucfirst($RTR->class);
	$method = $RTR->method;

	if (empty($class) OR ! file_exists(APPPATH.'controllers/'.$RTR->directory.$class.'.php'))  //这个就是调用到application控制器
	{
		$e404 = TRUE;
	}
	else
	{
		require_once(APPPATH.'controllers/'.$RTR->directory.$class.'.php');

		if ( ! class_exists($class, FALSE) OR $method[0] === '_' OR method_exists('CI_Controller', $method))
		{
			$e404 = TRUE;
		}
		elseif (method_exists($class, '_remap'))
		{
			$params = array($method, array_slice($URI->rsegments, 2));
			$method = '_remap';
		}
		// WARNING: It appears that there are issues with is_callable() even in PHP 5.2!
		// Furthermore, there are bug reports and feature/change requests related to it
		// that make it unreliable to use in this context. Please, DO NOT change this
		// work-around until a better alternative is available.
		//警告:似乎有问题is_callable()甚至在PHP 5.2 !此外,有bug报告和特性/变更请求相关,使它在这个背景下可靠使用。请不要改变这个变通办法,直到一个更好的选择。
		elseif ( ! in_array(strtolower($method), array_map('strtolower', get_class_methods($class)), TRUE))
		{
			$e404 = TRUE;
		}
	}

	if ($e404)
	{
		if ( ! empty($RTR->routes['404_override']))
		{
			if (sscanf($RTR->routes['404_override'], '%[^/]/%s', $error_class, $error_method) !== 2)
			{
				$error_method = 'index';
			}

			$error_class = ucfirst($error_class);

			if ( ! class_exists($error_class, FALSE))
			{
				if (file_exists(APPPATH.'controllers/'.$RTR->directory.$error_class.'.php'))
				{
					require_once(APPPATH.'controllers/'.$RTR->directory.$error_class.'.php');
					$e404 = ! class_exists($error_class, FALSE);
				}
				// Were we in a directory? If so, check for a global override我们在一个目录吗?如果是这样的话,检查全局覆盖
				elseif ( ! empty($RTR->directory) && file_exists(APPPATH.'controllers/'.$error_class.'.php'))
				{
					require_once(APPPATH.'controllers/'.$error_class.'.php');
					if (($e404 = ! class_exists($error_class, FALSE)) === FALSE)
					{
						$RTR->directory = '';
					}
				}
			}
			else
			{
				$e404 = FALSE;
			}
		}

		// Did we reset the $e404 flag? If so, set the rsegments, starting from index 1
		//我们重置e404美元标志吗?如果是这样的话,设置rsegments,从指数1
		if ( ! $e404)
		{
			$class = $error_class;
			$method = $error_method;

			$URI->rsegments = array(
				1 => $class,
				2 => $method
			);
		}
		else
		{
			show_404($RTR->directory.$class.'/'.$method);
		}
	}

	if ($method !== '_remap')
	{
		$params = array_slice($URI->rsegments, 2);
	}

/*
 * ------------------------------------------------------
 *  Is there a "pre_controller" hook? 有“pre_controller钩吗?
 * ------------------------------------------------------
 */
	$EXT->call_hook('pre_controller');

/*
 * ------------------------------------------------------
 *  Instantiate the requested controller 实例化所请求的控制器
 * ------------------------------------------------------
 */
	// Mark a start point so we can benchmark the controller 标志着一个起点所以我们可以基准控制器
	$BM->mark('controller_execution_time_( '.$class.' / '.$method.' )_start');

	$CI = new $class();

/*
 * ------------------------------------------------------
 *  Is there a "post_controller_constructor" hook? 有“post_controller_constructor钩吗?
 * ------------------------------------------------------
 */
	$EXT->call_hook('post_controller_constructor');

/*
 * ------------------------------------------------------
 *  Call the requested method 调用请求的方法
 * ------------------------------------------------------
 */
	call_user_func_array(array(&$CI, $method), $params);

	// Mark a benchmark end point 标志着一个基准终点
	$BM->mark('controller_execution_time_( '.$class.' / '.$method.' )_end');

/*
 * ------------------------------------------------------
 *  Is there a "post_controller" hook? 有“post_controller钩吗?
 * ------------------------------------------------------
 */
	$EXT->call_hook('post_controller');

/*
 * ------------------------------------------------------
 *  Send the final rendered output to the browser 最终的渲染输出发送到浏览器
 * ------------------------------------------------------
 */
	if ($EXT->call_hook('display_override') === FALSE)
	{
		$OUT->_display();
	}

/*
 * ------------------------------------------------------
 *  Is there a "post_system" hook? 有“post_system钩吗?
 * ------------------------------------------------------
 */
	$EXT->call_hook('post_system');
