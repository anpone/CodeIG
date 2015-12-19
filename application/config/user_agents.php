<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| USER AGENT TYPES 用户代理类型
| -------------------------------------------------------------------
| This file contains four arrays of user agent data. It is used by the
| User Agent Class to help identify browser, platform, robot, and
| mobile device data. The array keys are used to identify the device
| and the array values are used to set the actual name of the item.
| 这个文件包含四个用户代理数据的数组。这是用户代理类来帮助识别所使用的浏览器,平台,
| 机器人,和移动设备数据。数组的键是用来识别设备和数组值用于设置实际的项目的名称。
*/
$platforms = array(
	'windows nt 10.0'	=> 'Windows 10',
	'windows nt 6.3'	=> 'Windows 8.1',
	'windows nt 6.2'	=> 'Windows 8',
	'windows nt 6.1'	=> 'Windows 7',
	'windows nt 6.0'	=> 'Windows Vista',
	'windows nt 5.2'	=> 'Windows 2003',
	'windows nt 5.1'	=> 'Windows XP',
	'windows nt 5.0'	=> 'Windows 2000',
	'windows nt 4.0'	=> 'Windows NT 4.0',
	'winnt4.0'			=> 'Windows NT 4.0',
	'winnt 4.0'			=> 'Windows NT',
	'winnt'				=> 'Windows NT',
	'windows 98'		=> 'Windows 98',
	'win98'				=> 'Windows 98',
	'windows 95'		=> 'Windows 95',
	'win95'				=> 'Windows 95',
	'windows phone'			=> 'Windows Phone',
	'windows'			=> 'Unknown Windows OS',
	'android'			=> 'Android',
	'blackberry'		=> 'BlackBerry',
	'iphone'			=> 'iOS',
	'ipad'				=> 'iOS',
	'ipod'				=> 'iOS',
	'os x'				=> 'Mac OS X',
	'ppc mac'			=> 'Power PC Mac',
	'freebsd'			=> 'FreeBSD',
	'ppc'				=> 'Macintosh',
	'linux'				=> 'Linux',
	'debian'			=> 'Debian',
	'sunos'				=> 'Sun Solaris',
	'beos'				=> 'BeOS',
	'apachebench'		=> 'ApacheBench',
	'aix'				=> 'AIX',
	'irix'				=> 'Irix',
	'osf'				=> 'DEC OSF',
	'hp-ux'				=> 'HP-UX',
	'netbsd'			=> 'NetBSD',
	'bsdi'				=> 'BSDi',
	'openbsd'			=> 'OpenBSD',
	'gnu'				=> 'GNU/Linux',
	'unix'				=> 'Unknown Unix OS',
	'symbian' 			=> 'Symbian OS'
);


// The order of this array should NOT be changed. Many browsers return
// multiple browser types so we want to identify the sub-type first.
// 这个数组的顺序不应改变。许多浏览器返回多个浏览器类型所以我们要首先确定子类型。
$browsers = array(
	'OPR'			=> 'Opera',
	'Flock'			=> 'Flock',
	'Edge'			=> 'Spartan',
	'Chrome'		=> 'Chrome',
	// Opera 10+ always reports Opera/9.80 and appends Version/<real version> to the user agent string
	// Opera 10 +总报告Opera/ 9.80和附加版本/ <真实版本>用户代理字符串
	'Opera.*?Version'	=> 'Opera',
	'Opera'			=> 'Opera',
	'MSIE'			=> 'Internet Explorer',
	'Internet Explorer'	=> 'Internet Explorer',
	'Trident.* rv'	=> 'Internet Explorer',
	'Shiira'		=> 'Shiira',
	'Firefox'		=> 'Firefox',
	'Chimera'		=> 'Chimera',
	'Phoenix'		=> 'Phoenix',
	'Firebird'		=> 'Firebird',
	'Camino'		=> 'Camino',
	'Netscape'		=> 'Netscape',
	'OmniWeb'		=> 'OmniWeb',
	'Safari'		=> 'Safari',
	'Mozilla'		=> 'Mozilla',
	'Konqueror'		=> 'Konqueror',
	'icab'			=> 'iCab',
	'Lynx'			=> 'Lynx',
	'Links'			=> 'Links',
	'hotjava'		=> 'HotJava',
	'amaya'			=> 'Amaya',
	'IBrowse'		=> 'IBrowse',
	'Maxthon'		=> 'Maxthon',
	'Ubuntu'		=> 'Ubuntu Web Browser'
);

$mobiles = array(
	// legacy array, old values commented out 遗留的数组,旧值注释掉了
	'mobileexplorer'	=> 'Mobile Explorer',
//  'openwave'			=> 'Open Wave',
//	'opera mini'		=> 'Opera Mini',
//	'operamini'			=> 'Opera Mini',
//	'elaine'			=> 'Palm',
	'palmsource'		=> 'Palm',
//	'digital paths'		=> 'Palm',
//	'avantgo'			=> 'Avantgo',
//	'xiino'				=> 'Xiino',
	'palmscape'			=> 'Palmscape',
//	'nokia'				=> 'Nokia',
//	'ericsson'			=> 'Ericsson',
//	'blackberry'		=> 'BlackBerry',
//	'motorola'			=> 'Motorola'

	// Phones and Manufacturers 手机和制造商
	'motorola'		=> 'Motorola',
	'nokia'			=> 'Nokia',
	'palm'			=> 'Palm',
	'iphone'		=> 'Apple iPhone',
	'ipad'			=> 'iPad',
	'ipod'			=> 'Apple iPod Touch',
	'sony'			=> 'Sony Ericsson',
	'ericsson'		=> 'Sony Ericsson',
	'blackberry'	=> 'BlackBerry',
	'cocoon'		=> 'O2 Cocoon',
	'blazer'		=> 'Treo',
	'lg'			=> 'LG',
	'amoi'			=> 'Amoi',
	'xda'			=> 'XDA',
	'mda'			=> 'MDA',
	'vario'			=> 'Vario',
	'htc'			=> 'HTC',
	'samsung'		=> 'Samsung',
	'sharp'			=> 'Sharp',
	'sie-'			=> 'Siemens',
	'alcatel'		=> 'Alcatel',
	'benq'			=> 'BenQ',
	'ipaq'			=> 'HP iPaq',
	'mot-'			=> 'Motorola',
	'playstation portable'	=> 'PlayStation Portable',
	'playstation 3'		=> 'PlayStation 3',
	'playstation vita'  	=> 'PlayStation Vita',
	'hiptop'		=> 'Danger Hiptop',
	'nec-'			=> 'NEC',
	'panasonic'		=> 'Panasonic',
	'philips'		=> 'Philips',
	'sagem'			=> 'Sagem',
	'sanyo'			=> 'Sanyo',
	'spv'			=> 'SPV',
	'zte'			=> 'ZTE',
	'sendo'			=> 'Sendo',
	'nintendo dsi'	=> 'Nintendo DSi',
	'nintendo ds'	=> 'Nintendo DS',
	'nintendo 3ds'	=> 'Nintendo 3DS',
	'wii'			=> 'Nintendo Wii',
	'open web'		=> 'Open Web',
	'openweb'		=> 'OpenWeb',

	// Operating Systems 操作系统
	'android'		=> 'Android',
	'symbian'		=> 'Symbian',
	'SymbianOS'		=> 'SymbianOS',
	'elaine'		=> 'Palm',
	'series60'		=> 'Symbian S60',
	'windows ce'	=> 'Windows CE',

	// Browsers 浏览器
	'obigo'			=> 'Obigo',
	'netfront'		=> 'Netfront Browser',
	'openwave'		=> 'Openwave Browser',
	'mobilexplorer'	=> 'Mobile Explorer',
	'operamini'		=> 'Opera Mini',
	'opera mini'	=> 'Opera Mini',
	'opera mobi'	=> 'Opera Mobile',
	'fennec'		=> 'Firefox Mobile',

	// Other 其他
	'digital paths'	=> 'Digital Paths',
	'avantgo'		=> 'AvantGo',
	'xiino'			=> 'Xiino',
	'novarra'		=> 'Novarra Transcoder',
	'vodafone'		=> 'Vodafone',
	'docomo'		=> 'NTT DoCoMo',
	'o2'			=> 'O2',

	// Fallback  撤退 后退 可依靠的东西 
	'mobile'		=> 'Generic Mobile',
	'wireless'		=> 'Generic Mobile',
	'j2me'			=> 'Generic Mobile',
	'midp'			=> 'Generic Mobile',
	'cldc'			=> 'Generic Mobile',
	'up.link'		=> 'Generic Mobile',
	'up.browser'	=> 'Generic Mobile',
	'smartphone'	=> 'Generic Mobile',
	'cellphone'		=> 'Generic Mobile'
);

// There are hundreds of bots but these are the most common.
// 有成百上千的机器人,但这些是最常见的。
$robots = array(
	'googlebot'		=> 'Googlebot',
	'msnbot'		=> 'MSNBot',
	'baiduspider'		=> 'Baiduspider',
	'bingbot'		=> 'Bing',
	'slurp'			=> 'Inktomi Slurp',
	'yahoo'			=> 'Yahoo',
	'ask jeeves'		=> 'Ask Jeeves',
	'fastcrawler'		=> 'FastCrawler',
	'infoseek'		=> 'InfoSeek Robot 1.0',
	'lycos'			=> 'Lycos',
	'yandex'		=> 'YandexBot',
	'mediapartners-google'	=> 'MediaPartners Google',
	'CRAZYWEBCRAWLER'	=> 'Crazy Webcrawler',
	'adsbot-google'		=> 'AdsBot Google',
	'feedfetcher-google'	=> 'Feedfetcher Google',
	'curious george'	=> 'Curious George'
);
