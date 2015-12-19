<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| AUTO-LOADER 自动加载
| -------------------------------------------------------------------
| This file specifies which systems should be loaded by default.
| 这个文件指定系统应该默认加载。
| In order to keep the framework as light-weight as possible only the
| absolute minimal resources are loaded by default. For example,
| the database is not connected to automatically since no assumption
| is made regarding whether you intend to use it.  This file lets
| you globally define which systems you would like loaded with every
| request.
| 为了保持尽可能轻量级框架只加载默认的绝对最少的资源。例如,数据库没有连接到自动因为没有假设是关于你是否打算使用它。
|  这个文件可以在全局范围内定义的系统你想装载每个请求。
| -------------------------------------------------------------------
| Instructions 使用说明
| -------------------------------------------------------------------
|
| These are the things you can load automatically:
| 这些事情你就可以自动加载:
| 1. Packages 包
| 2. Libraries 库
| 3. Drivers  驱动
| 4. Helper files  辅助文件
| 5. Custom config files 自定义配置文件
| 6. Language files   语言文件
| 7. Models  模型
|
*/

/*
| -------------------------------------------------------------------
|  Auto-load Packages  自动加载包
| -------------------------------------------------------------------
| Prototype:  原型
|
|  $autoload['packages'] = array(APPPATH.'third_party', '/usr/local/shared');
|
*/
$autoload['packages'] = array();

/*
| -------------------------------------------------------------------
|  Auto-load Libraries 自动加载库
| -------------------------------------------------------------------
| These are the classes located in system/libraries/ or your
| application/libraries/ directory, with the addition of the
| 'database' library, which is somewhat of a special case.
| 这些类位于系统/库/或应用程序/图书馆/目录,添加的数据库的库,这是有点特殊情况。
| Prototype:  原型
|
|	$autoload['libraries'] = array('database', 'email', 'session');
|
| You can also supply an alternative library name to be assigned
| in the controller:
| 你也可以提供一个替代库名称被分配在控制器:
|	$autoload['libraries'] = array('user_agent' => 'ua');
*/
$autoload['libraries'] = array();

/*
| -------------------------------------------------------------------
|  Auto-load Drivers 自动加载驱动
| -------------------------------------------------------------------
| These classes are located in system/libraries/ or in your
| application/libraries/ directory, but are also placed inside their
| own subdirectory and they extend the CI_Driver_Library class. They
| offer multiple interchangeable driver options.
| 这些类都位于系统/图书馆/或在您的应用程序/图书馆/目录,但也放置在自己的子目录,他们扩展CI_Driver_Library类。他们提供了多个可互换的司机选择。
| Prototype:  原型
|
|	$autoload['drivers'] = array('cache');
*/
$autoload['drivers'] = array();

/*
| -------------------------------------------------------------------
|  Auto-load Helper Files 自动加载辅助文件
| -------------------------------------------------------------------
| Prototype:  原型
|
|	$autoload['helper'] = array('url', 'file');
*/
$autoload['helper'] = array();

/*
| -------------------------------------------------------------------
|  Auto-load Config files 自动加载配置文件
| -------------------------------------------------------------------
| Prototype:  原型
|
|	$autoload['config'] = array('config1', 'config2');
|
| NOTE: This item is intended for use ONLY if you have created custom
| config files.  Otherwise, leave it blank.
| 注意:本产品适用于只使用如果您已经创建了自定义配置文件。否则,让它空白。
*/
$autoload['config'] = array();

/*
| -------------------------------------------------------------------
|  Auto-load Language files 自动加载语言文件
| -------------------------------------------------------------------
| Prototype:  原型
|
|	$autoload['language'] = array('lang1', 'lang2');
|
| NOTE: Do not include the "_lang" part of your file.  For example
| "codeigniter_lang.php" would be referenced as array('codeigniter');
| 注:不包括“_lang”文件的一部分。例如“codeigniter_lang.php”将被引用的数组(codeigniter);
*/
$autoload['language'] = array();

/*
| -------------------------------------------------------------------
|  Auto-load Models 自动加载模型
| -------------------------------------------------------------------
| Prototype:  原型
|
|	$autoload['model'] = array('first_model', 'second_model');
|
| You can also supply an alternative model name to be assigned
| in the controller:
| 你也可以提供一个替代模型名称指定的控制器:
|	$autoload['model'] = array('first_model' => 'first');
*/
$autoload['model'] = array();
