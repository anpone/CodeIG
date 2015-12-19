<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS 数据库连接设置
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
| 这个文件将包含设置需要访问数据库。
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
| 为完整的说明请查阅数据库连接的页面的用户指南。
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES 解释变量
| -------------------------------------------------------------------
|
|	['dsn']      The full DSN string describe a connection to the database. 完整的DSN描述数据库连接字符串。
|	['hostname'] The hostname of your database server. 数据库服务器的主机名
|	['username'] The username used to connect to the database 用于连接到数据库的用户名
|	['password'] The password used to connect to the database 用于连接到数据库的密码
|	['database'] The name of the database you want to connect to 你想连接到数据库的名称
|	['dbdriver'] The database driver. e.g.: mysqli. 数据库驱动程序。如。:mysqli。
|			Currently supported: 目前支持:
|				 cubrid, ibase, mssql, mysql, mysqli, oci8,
|				 odbc, pdo, postgre, sqlite, sqlite3, sqlsrv
|	['dbprefix'] You can add an optional prefix, which will be added 您可以添加一个可选的前缀,这将被添加到表名在使用查询构建器类
|				 to the table name when using the  Query Builder class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection 是否使用持久连接
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed. 是否应该显示数据库错误
|	['cache_on'] TRUE/FALSE - Enables/disables query caching 启用/禁用查询缓存
|	['cachedir'] The path to the folder where cache files should be stored 文件夹的路径应该存储缓存文件
|	['char_set'] The character set used in communicating with the database 与数据库通信中使用的字符集
|	['dbcollat'] The character collation used in communicating with the database 使用的字符排序与数据库通信
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7 注意:对于MySQL和MySQLi数据库,这个设置是只作为备份如果您的服务器是运行PHP
|				 (and in table creation queries made with DB Forge). (在表创建查询用DB伪造)。
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
|                在PHP中有一种不相容与mysql_real_escape_string()可以让你的网站容易受到SQL注入如果使用多字节字符集和运行版本低于这些。
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected. 网站使用latin - 1或utf - 8数据库字符集和校对不受影响。
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix 一个默认的表前缀应该与dbprefix交换
|	['encrypt']  Whether or not to use an encrypted connection. 是否使用一个加密连接。
|
|			'mysql' (deprecated弃用), 'sqlsrv' and 'pdo/sqlsrv' drivers accept TRUE/FALSE 司机接受真/假
|			'mysqli' and 'pdo/mysql' drivers accept an array with the following options: 司机接受数组使用以下选项:
|
|				'ssl_key'    - Path to the private key file  路径私钥文件
|				'ssl_cert'   - Path to the public key certificate file 公钥证书路径文件
|				'ssl_ca'     - Path to the certificate authority file 路径文件证书颁发机构
|				'ssl_capath' - Path to a directory containing trusted CA certificats in PEM format 路径的目录包含受信任的CA证书在PEM格式
|				'ssl_cipher' - List of *allowed* ciphers to be used for the encryption, separated by colons (':') 列表允许的* *密码用于加密,用冒号分开(“:”)
|				'ssl_verify' - TRUE/FALSE; Whether verify the server certificate or not ('mysqli' only) 真/假;是否验证服务器证书(仅“mysqli”)
|
|	['compress'] Whether or not to use client compression (MySQL only) 是否使用客户端压缩(MySQL)
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections 弹性元件的严格模式连接
|							- good for ensuring strict SQL while developing 有利于保证严格的SQL在开发
|	['ssl_options']	Used to set various SSL options that can be used when making SSL connections. 用于设置各种SSL选项时,可以使用SSL连接。
|	['failover'] array - A array with 0 or more data for connections if the main should fail. 与0或多个数据数组,数组连接如果主要的失败。
|	['save_queries'] TRUE/FALSE - Whether to "save" all executed queries. 是否“拯救”所有执行查询。
| 				NOTE: Disabling this will also effectively disable both
| 				$this->db->last_query() and profiling of DB queries.
|				注意:禁用这也将禁用$ this - > db - >有效last_query()和分析数据库的查询。
| 				When you run a query, with this setting set to TRUE (default),
| 				CodeIgniter will store the SQL statement for debugging purposes.
|				当您运行一个查询,该设置设置为TRUE(缺省值),codeigniter SQL语句将存储用于调试目的。
| 				However, this may cause high memory usage, especially if you run
| 				a lot of SQL queries ... disable this to avoid that problem.
|				然而,这可能会导致内存使用率高,特别是如果你运行的SQL查询…禁用这个来避免这个问题。
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
| $ active_group变量允许您选择连接组活跃。默认情况下,只有一个集团(“违约”组)。
| The $query_builder variables lets you determine whether or not to load the query builder class.
| $query_builder变量允许您确定是否加载查询构建器类。
*/
$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost',
	'username' => '',
	'password' => '',
	'database' => '',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
