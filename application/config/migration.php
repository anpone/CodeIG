<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Enable/Disable Migrations 启用/禁用迁移
|--------------------------------------------------------------------------
|
| Migrations are disabled by default for security reasons. 迁移被禁用默认情况下出于安全原因。
| You should enable migrations whenever you intend to do a schema migration and disable it back when you're done.
| 您应该启用迁移每当你打算做一个模式迁移和禁用它的时候你就完成了。
|
*/
$config['migration_enabled'] = FALSE;

/*
|--------------------------------------------------------------------------
| Migration Type 转移类型
|--------------------------------------------------------------------------
|
| Migration file names may be based on a sequential identifier or on
| a timestamp. Options are:
| 迁移文件名可能基于序列标识符或一个时间戳。选项有:
|   'sequential' = Sequential migration naming (001_add_blog.php) 顺序迁移命名(001 _add_blog.php)
|   'timestamp'  = Timestamp migration naming (20121031104401_add_blog.php) 时间戳迁移命名(20121031104401 _add_blog.php)
|                  Use timestamp format使用时间戳格式 YYYYMMDDHHIISS.
|
| Note: If this configuration value is missing the Migration library
|       defaults to 'sequential' for backward compatibility with CI2.
|	如果这个配置值缺失迁移库默认为“顺序”与CI2向后兼容性。
*/
$config['migration_type'] = 'timestamp';

/*
|--------------------------------------------------------------------------
| Migrations table 迁移表
|--------------------------------------------------------------------------
|
| This is the name of the table that will store the current migrations state. 这是表的名称,将存储当前迁移状态。
| When migrations runs it will store in a database table which migration
| level the system is at. It then compares the migration level in this
| table to the $config['migration_version'] if they are not the same it
| will migrate up. This must be set.
| 迁移运行时它将存储在一个数据库表中迁移系统的水平。然后比较了迁移水平表配置美元[' migration_version ']如果它们不是相同的迁移。这必须设置。
*/
$config['migration_table'] = 'migrations';

/*
|--------------------------------------------------------------------------
| Auto Migrate To Latest 自动迁移到最新
|--------------------------------------------------------------------------
|
| If this is set to TRUE when you load the migrations class and have
| $config['migration_enabled'] set to TRUE the system will auto migrate
| to your latest migration (whatever $config['migration_version'] is
| set to). This way you do not have to call migrations anywhere else
| in your code to have the latest migration.
| 如果这是设置为TRUE时负载迁移类和配置$config(“migration_enabled”)设置为TRUE,系统将自动迁移到最新的移民
| (无论配置$[‘migration_version]将)。这样你不需要在代码中调用迁移其他地方有最新的移民。
*/
$config['migration_auto_latest'] = FALSE;

/*
|--------------------------------------------------------------------------
| Migrations version 迁移版本
|--------------------------------------------------------------------------
|
| This is used to set migration version that the file system should be on. 这是用于设置文件系统应该迁移版本。
| If you run $this->migration->current() this is the version that schema will
| be upgraded / downgraded to.
| 如果你运行$ this - >迁移- >当前()将这个版本模式 升级/降级。
*/
$config['migration_version'] = 0;

/*
|--------------------------------------------------------------------------
| Migrations Path 迁移路径
|--------------------------------------------------------------------------
|
| Path to your migrations folder. 迁移文件夹路径。
| Typically, it will be within your application path. 通常情况下,它将在您的应用程序的路径。
| Also, writing permission is required within the migrations path. 同时,需要写权限内的迁移路径。
|
*/
$config['migration_path'] = APPPATH.'migrations/';
