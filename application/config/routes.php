<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING URI的路由
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
| 该文件允许您重新URI请求特定的控制器功能。
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
| 通常有一个URL字符串之间的一对一的关系及其对应的控制器类/方法。段的URL通常遵循这一模式:
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
| 然而,在某些情况下,您可能想要重新映射关系这样一个不同的类/函数比对应的URL。
| Please see the user guide for complete details:
| 完整的详细信息,请参阅用户指南:
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES 保留的路由
| -------------------------------------------------------------------------
|
| There are three reserved routes:
| 有三个保留路线:
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
| 这条路线指示应该加载哪个控制器类如果URI包含任何数据。在上面的例子中,“欢迎”类将被加载。
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
| 这条路线会告诉路由器使用哪个控制器/方法如果这些提供的URL不能匹配到一个有效的途径。
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| 这不是一个路由,但允许您自动路由控制器包含破折号和方法名称。“——”不是一个有效的类或方法名角色,所以它需要翻译。
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
| 将此选项设置为TRUE时,它将取代所有URI破折号在控制器和方法部分。
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
