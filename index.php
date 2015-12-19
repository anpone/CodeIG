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
 *特此授予许可,免费的,任何人获得copyof这个软件和相关的文档文件(“软件”),dealin软件没有限制,包括但不限于rightsto使用、复制、修改、合并、出版、发行、有偿、和/或sellcopies的软件,并允许他们软件isfurnished,应当具备下列条件:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *上述版权声明和本许可声明应当包含在所有副本或实质性部分的软件。
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *软件提供了“是”,没有任何形式的保证,表达ORIMPLIED,包括但不限于适销性的保证,健身为特定目的和无侵犯。在任何事件作者或版权持有人应当承担任何索赔、损失或OTHERLIABILITY,无论是在一个动作的合同,侵权或否则,引起的,或与软件或使用或其他交易在软件。
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

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT 应用环境
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *你可以加载不同的配置取决于你当前环境。设置环境影响日志和错误报告之类的东西。
 *
 * This can be set to anything, but default usage is:
 *这个可以设置为任何东西,但是默认用法是:
 *
 *     development开发
 *     testing测试
 *     production生产
 *
 * NOTE: If you change these, also change the error_reporting() code below 
 * 注意:如果你改变这些,也改变error_reporting下面()代码
 */
	define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

/*
 *---------------------------------------------------------------
 * ERROR REPORTING 错误报告
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 * 不同的环境需要不同级别的错误报告。　　*默认情况下开发将显示错误但测试和生活将隐藏它们。
 */
switch (ENVIRONMENT)
{
	case 'development':
		error_reporting(-1);
		ini_set('display_errors', 1);
	break;

	case 'testing':
	case 'production':
		ini_set('display_errors', 0);
		if (version_compare(PHP_VERSION, '5.3', '>='))
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		}
		else
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
		}
	break;

	default:
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'The application environment is not set correctly.';
		exit(1); // EXIT_ERROR 退出错误
}

/*
 *---------------------------------------------------------------
 * SYSTEM FOLDER NAME 框架文件目录
 *---------------------------------------------------------------
 *
 * This variable must contain the name of your "system" folder.
 * Include the path if the folder is not in the same directory
 * as this file.
 * 该变量必须包含你的“系统”文件夹的名称。包括如果文件夹的路径并不像这个文件在同一个目录下。
 */
	$system_path = 'system';

/*
 *---------------------------------------------------------------
 * APPLICATION FOLDER NAME 应用路径 
 *---------------------------------------------------------------
 *
 * If you want this front controller to use a different "application"
 * folder than the default one you can set its name here. The folder
 * can also be renamed or relocated anywhere on your server. If
 * you do, use a full server path. For more info please see the user guide:
 * http://codeigniter.com/user_guide/general/managing_apps.html
 *如果你想要这前端控制器使用一个不同于默认的“应用程序”文件夹中你可以设置它的名字。文件夹也可以重命名或迁移服务器上的任何地方。如果你这样做,使用一个完整的服务器路径。更多信息,请参阅用户指南:
 *
 * NO TRAILING SLASH! 没有尾随的！
 */
	$application_folder = 'application';

/*
 *---------------------------------------------------------------
 * VIEW FOLDER NAME 视图文件夹名称
 *---------------------------------------------------------------
 *
 * If you want to move the view folder out of the application
 * folder set the path to the folder here. The folder can be renamed
 * and relocated anywhere on your server. If blank, it will default
 * to the standard location inside your application folder. If you
 * do move this, use the full server path to this folder.
 *如果你想移动视图文件夹从应用程序文件夹设置文件夹的路径。文件夹可以被重新命名,搬迁服务器上的任何地方。如果空白,它将默认为标准的位置在您的应用程序文件夹。如果你移动,使用完整的服务器路径为这个文件夹。
 *
 * NO TRAILING SLASH!
 */
	$view_folder = '';


/*
 * --------------------------------------------------------------------
 * DEFAULT CONTROLLER 默认控制器 
 * --------------------------------------------------------------------
 *
 * Normally you will set your default controller in the routes.php file.
 * You can, however, force a custom routing by hard-coding a
 * specific controller class/function here. For most applications, you
 * WILL NOT set your routing here, but it's an option for those
 * special instances where you might want to override the standard
 * routing in a specific front controller that shares a common CI installation.
 *通常你会设置默认的控制器的 路由routes.php文件。但是,您可以自定义路由使用硬编码一个特定的控制器类/函数。对于大多数应用程序,您将不设置路由,但这是一个选择的特殊情况下,您可能想要覆盖标准路由在一个特定的前端控制器,共有一致的CI装置。
 *
 * IMPORTANT: If you set the routing here, NO OTHER controller will be
 * callable. In essence, this preference limits your application to ONE
 * specific controller. Leave the function name blank if you need
 * to call functions dynamically via the URI.
 *重要:如果你设置路由,没有其他控制器将可调用的。从本质上讲,这种偏好限制应用程序的一个特定的控制器。把函数名空白如果需要调用函数动态通过URI。
 *
 * Un-comment the $routing array below to use this feature 
 * 取消下面的$路由数组使用此功能注释
 */
	// The directory name, relative to the "controllers" folder.  Leave blank 目录名称,相对于“控制器”文件夹中。留下空白
	// if your controller is not in a sub-folder within the "controllers" folder 如果你的控制器没有“控制器”文件夹中的子文件夹
	// $routing['directory'] = '';

	// The controller class file name.  Example:  mycontroller 控制器类文件名。例如:mycontroller
	// $routing['controller'] = '';

	// The controller function you wish to be called. 你想要被称为的控制器功能。
	// $routing['function']	= '';


/*
 * -------------------------------------------------------------------
 *  CUSTOM CONFIG VALUES 自定义配置值 
 * -------------------------------------------------------------------
 *
 * The $assign_to_config array below will be passed dynamically to the
 * config class when initialized. This allows you to set custom config
 * items or override any default config values found in the config.php file.
 * This can be handy as it permits you to share one application between
 * multiple front controller files, with each file containing different
 * config values.
 *下面的$ assign_to_config数组将被传递时动态地配置类初始化。这允许您设置自定义配置项或覆盖任何默认配置中config.php文件。这可以方便的,因为它允许您共享多个前端控制器文件之间的一个应用程序,每个文件包含不同的配置值。
 *
 * Un-comment the $assign_to_config array below to use this feature  
 * 取消下面的$ assign_to_config数组使用此功能注释
 */
	// $assign_to_config['name_of_config_item'] = 'value of config item';



// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE 用户可配置的设置。不要编辑下面这行
// --------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability 解决系统路径以提高可靠性
 * ---------------------------------------------------------------
 */

	// Set the current directory correctly for CLI requests 正确设置当前目录为CLI请求
	if (defined('STDIN'))
	{
		chdir(dirname(__FILE__));
	}

	if (($_temp = realpath($system_path)) !== FALSE)
	{
		$system_path = $_temp.'/';
	}
	else
	{
		// Ensure there's a trailing slash 确定框架路径是否含有/
		$system_path = rtrim($system_path, '/').'/';
	}

	// Is the system path correct? 系统路径是正确的吗?
	if ( ! is_dir($system_path))
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'Your system folder path does not appear to be set correctly. Please open the following file and correct this:你的系统文件夹路径似乎没有正确设置。请打开以下文件和正确的 '.pathinfo(__FILE__, PATHINFO_BASENAME);
		exit(3); // EXIT_CONFIG
	}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants 现在我们知道的路径,设置主要路径常量
 * -------------------------------------------------------------------
 */
	// The name of THIS file 这个文件的名称(自身)
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

	// Path to the system folder 系统目录的路径
	define('BASEPATH', str_replace('\\', '/', $system_path));

	// Path to the front controller (this file) 路径前端控制器(此文件)
	define('FCPATH', dirname(__FILE__).'/');

	// Name of the "system folder" "系统文件夹"的名称
	define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));

	// The path to the "application" folder "应用程序"文件夹的路径
	if (is_dir($application_folder))
	{
		if (($_temp = realpath($application_folder)) !== FALSE)
		{
			$application_folder = $_temp;
		}

		define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
	}
	else
	{
		if ( ! is_dir(BASEPATH.$application_folder.DIRECTORY_SEPARATOR))
		{
			header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
			echo 'Your application folder path does not appear to be set correctly. Please open the following file and correct this:您的应用程序文件夹路径似乎没有正确设置。请打开以下文件和正确的 '.SELF;
			exit(3); // EXIT_CONFIG
		}

		define('APPPATH', BASEPATH.$application_folder.DIRECTORY_SEPARATOR);
	}

	// The path to the "views" folder “视图”文件夹的路径
	if ( ! is_dir($view_folder))
	{
		if ( ! empty($view_folder) && is_dir(APPPATH.$view_folder.DIRECTORY_SEPARATOR))
		{
			$view_folder = APPPATH.$view_folder;
		}
		elseif ( ! is_dir(APPPATH.'views'.DIRECTORY_SEPARATOR))
		{
			header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
			echo 'Your view folder path does not appear to be set correctly. Please open the following file and correct this: '.SELF;
			exit(3); // EXIT_CONFIG
		}
		else
		{
			$view_folder = APPPATH.'views';
		}
	}

	if (($_temp = realpath($view_folder)) !== FALSE)
	{
		$view_folder = $_temp.DIRECTORY_SEPARATOR;
	}
	else
	{
		$view_folder = rtrim($view_folder, '/\\').DIRECTORY_SEPARATOR;
	}

	define('VIEWPATH', $view_folder);

/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE 载入引导程序文件
 * --------------------------------------------------------------------
 *
 * And away we go... 我们别再傻下去了…
 */
require_once BASEPATH.'core/CodeIgniter.php';
