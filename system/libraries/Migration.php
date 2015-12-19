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
 * @since	Version 3.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration Class
 * 迁移类
 * All migrations should implement this, forces up() and down() and gives
 * access to the CI super-global.
 * 所有迁移应该实现这个,forces up()和down (),使对CI超全局变量的访问。
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Reactor Engineers
 * @link
 */
class CI_Migration {

	/**
	 * Whether the library is enabled
	 * 类库是否启用
	 * @var bool
	 */
	protected $_migration_enabled = FALSE;

	/**
	 * Migration numbering type
	 * 迁移类型编号
	 * @var	bool
	 */
	protected $_migration_type = 'sequential';

	/**
	 * Path to migration classes
	 * 迁移类路径
	 * @var string
	 */
	protected $_migration_path = NULL;

	/**
	 * Current migration version
	 * 当前迁移版本
	 * @var mixed
	 */
	protected $_migration_version = 0;

	/**
	 * Database table with migration info
	 * 与迁移数据库表信息
	 * @var string
	 */
	protected $_migration_table = 'migrations';

	/**
	 * Whether to automatically run migrations
	 * 是否自动运行迁移
	 * @var	bool
	 */
	protected $_migration_auto_latest = FALSE;

	/**
	 * Migration basename regex
	 * 迁移:正则表达式
	 * @var bool
	 */
	protected $_migration_regex = NULL;

	/**
	 * Error message
	 * 错误信息
	 * @var string
	 */
	protected $_error_string = '';

	/**
	 * Initialize Migration Class
	 * 初始化迁移类
	 * @param	array	$config
	 * @return	void
	 */
	public function __construct($config = array())
	{
		// Only run this constructor on main library load 只在主库加载运行这个构造函数
		if ( ! in_array(get_class($this), array('CI_Migration', config_item('subclass_prefix').'Migration'), TRUE))
		{
			return;
		}

		foreach ($config as $key => $val)
		{
			$this->{'_'.$key} = $val;
		}

		log_message('info', 'Migrations Class Initialized迁移类初始化');

		// Are they trying to use migrations while it is disabled? 他们试图使用迁移虽然是残疾?
		if ($this->_migration_enabled !== TRUE)
		{
			show_error('Migrations has been loaded but is disabled or set up incorrectly 迁移已经加载但禁用或设置不正确.');
		}

		// If not set, set it
		$this->_migration_path !== '' OR $this->_migration_path = APPPATH.'migrations/';

		// Add trailing slash if not set 如果没有设置添加末尾斜杠
		$this->_migration_path = rtrim($this->_migration_path, '/').'/';

		// Load migration language 加载迁移的语言
		$this->lang->load('migration');

		// They'll probably be using dbforge 他们可能会使用dbforge
		$this->load->dbforge();

		// Make sure the migration table name was set. 确保迁移表名称集。
		if (empty($this->_migration_table))
		{
			show_error('Migrations configuration file (migration.php) must have "migration_table" set.');
		}

		// Migration basename regex 迁移:正则表达式
		$this->_migration_regex = ($this->_migration_type === 'timestamp')
			? '/^\d{14}_(\w+)$/'
			: '/^\d{3}_(\w+)$/';

		// Make sure a valid migration numbering type was set. 确保一个有效的迁移编号类型设置。
		if ( ! in_array($this->_migration_type, array('sequential', 'timestamp')))
		{
			show_error('An invalid migration numbering type was specified: '.$this->_migration_type);
		}

		// If the migrations table is missing, make it 如果迁移表丢失,使它
		if ( ! $this->db->table_exists($this->_migration_table))
		{
			$this->dbforge->add_field(array(
				'version' => array('type' => 'BIGINT', 'constraint' => 20),
			));

			$this->dbforge->create_table($this->_migration_table, TRUE);

			$this->db->insert($this->_migration_table, array('version' => 0));
		}

		// Do we auto migrate to the latest migration? 我们自动迁移到最新的移民吗?
		if ($this->_migration_auto_latest === TRUE && ! $this->latest())
		{
			show_error($this->error_string());
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Migrate to a schema version
	 * 迁移到一个模式的版本
	 * Calls each migration step required to get to the schema version of
	 * choice 调用每个迁移步骤需要的模式版本的选择
	 *
	 * @param	string	$target_version	Target schema version 目标模式版本
	 * @return	mixed	TRUE if no migrations are found, current version string on success, FALSE on failure 如果没有发现,迁移成功,当前版本字符串;如果执行失败将返回FALSE
	 */
	public function version($target_version)
	{
		// Note: We use strings, so that timestamp versions work on 32-bit systems 注意:我们使用字符串,所以时间戳版本在32位系统上工作
		$current_version = $this->_get_version();

		if ($this->_migration_type === 'sequential')
		{
			$target_version = sprintf('%03d', $target_version);
		}
		else
		{
			$target_version = (string) $target_version;
		}

		$migrations = $this->find_migrations();

		if ($target_version > 0 && ! isset($migrations[$target_version]))
		{
			$this->_error_string = sprintf($this->lang->line('migration_not_found'), $target_version);
			return FALSE;
		}

		if ($target_version > $current_version)
		{
			// Moving Up 向上移转
			$method = 'up';
		}
		else
		{
			// Moving Down, apply in reverse order  向下移动,适用于相反的顺序
			$method = 'down';
			krsort($migrations);
		}

		if (empty($migrations))
		{
			return TRUE;
		}

		$previous = FALSE;

		// Validate all available migrations, and run the ones within our target range 验证所有可用的迁移,并运行在我们的目标范围
		foreach ($migrations as $number => $file)
		{
			// Check for sequence gaps 检查序列的差距
			if ($this->_migration_type === 'sequential' && $previous !== FALSE && abs($number - $previous) > 1)
			{
				$this->_error_string = sprintf($this->lang->line('migration_sequence_gap'), $number);
				return FALSE;
			}

			include_once($file);
			$class = 'Migration_'.ucfirst(strtolower($this->_get_migration_name(basename($file, '.php'))));

			// Validate the migration file structure 验证迁移文件结构
			if ( ! class_exists($class, FALSE))
			{
				$this->_error_string = sprintf($this->lang->line('migration_class_doesnt_exist'), $class);
				return FALSE;
			}

			$previous = $number;

			// Run migrations that are inside the target range 目标区间内运行迁移
			if (
				($method === 'up'   && $number > $current_version && $number <= $target_version) OR
				($method === 'down' && $number <= $current_version && $number > $target_version)
			)
			{
				$instance = new $class();
				if ( ! is_callable(array($instance, $method)))
				{
					$this->_error_string = sprintf($this->lang->line('migration_missing_'.$method.'_method'), $class);
					return FALSE;
				}

				log_message('debug', 'Migrating '.$method.' from version '.$current_version.' to version '.$number);
				call_user_func(array($instance, $method));
				$current_version = $number;
				$this->_update_version($current_version);
			}
		}

		// This is necessary when moving down, since the the last migration applied
		// will be the down() method for the next migration up from the target
		// 在向下运动时这是必需的,因为过去的迁移应用将是未来迁移下来()方法的目标
		if ($current_version <> $target_version)
		{
			$current_version = $target_version;
			$this->_update_version($current_version);
		}

		log_message('debug', 'Finished migrating to '.$current_version);

		return $current_version;
	}

	// --------------------------------------------------------------------

	/**
	 * Sets the schema to the latest migration 设置模式的最新迁移
	 *
	 * @return	mixed	Current version string on success, FALSE on failure 当前版本字符串成功,;如果执行失败将返回FALSE
	 */
	public function latest()
	{
		$migrations = $this->find_migrations();

		if (empty($migrations))
		{
			$this->_error_string = $this->lang->line('migration_none_found');
			return FALSE;
		}

		$last_migration = basename(end($migrations));

		// Calculate the last migration step from existing migration
		// filenames and proceed to the standard version migration
		// 计算最后迁移步骤从现有迁移文件名和标准版迁移
		return $this->version($this->_get_migration_number($last_migration));
	}

	// --------------------------------------------------------------------

	/**
	 * Sets the schema to the migration version set in config
	 * 设置模式迁移版本配置
	 * @return	mixed	TRUE if no migrations are found, current version string on success, FALSE on failure 如果没有发现,迁移成功,当前版本字符串;如果执行失败将返回FALSE
	 */
	public function current()
	{
		return $this->version($this->_migration_version);
	}

	// --------------------------------------------------------------------

	/**
	 * Error string 错误字串
	 *
	 * @return	string	Error message returned as a string 错误消息作为字符串返回
	 */
	public function error_string()
	{
		return $this->_error_string;
	}

	// --------------------------------------------------------------------

	/**
	 * Retrieves list of available migration scripts
	 * 检索可用迁移脚本列表
	 * @return	array	list of migration file paths sorted by version 迁移文件路径列表,按版本
	 */
	public function find_migrations()
	{
		$migrations = array();

		// Load all *_*.php files in the migrations path 加载所有* _ *。php文件的迁移路径
		foreach (glob($this->_migration_path.'*_*.php') as $file)
		{
			$name = basename($file, '.php');

			// Filter out non-migration files  过滤掉non-migration文件
			if (preg_match($this->_migration_regex, $name))
			{
				$number = $this->_get_migration_number($name);

				// There cannot be duplicate migration numbers  不能有重复迁移数据
				if (isset($migrations[$number]))
				{
					$this->_error_string = sprintf($this->lang->line('migration_multiple_version'), $number);
					show_error($this->_error_string);
				}

				$migrations[$number] = $file;
			}
		}

		ksort($migrations);
		return $migrations;
	}

	// --------------------------------------------------------------------

	/**
	 * Extracts the migration number from a filename
	 * 提取移民数量从一个文件名
	 * @param	string	$migration
	 * @return	string	Numeric portion of a migration filename 数字迁移文件名的一部分
	 */
	protected function _get_migration_number($migration)
	{
		return sscanf($migration, '%[0-9]+', $number)
			? $number : '0';
	}

	// --------------------------------------------------------------------

	/**
	 * Extracts the migration class name from a filename
	 * 从文件名提取迁移类名
	 * @param	string	$migration
	 * @return	string	text portion of a migration filename 迁移文件名的一部分
	 */
	protected function _get_migration_name($migration)
	{
		$parts = explode('_', $migration);
		array_shift($parts);
		return implode('_', $parts);
	}

	// --------------------------------------------------------------------

	/**
	 * Retrieves current schema version
	 * 检索当前模式版本
	 * @return	string	Current migration version 当前迁移版本
	 */
	protected function _get_version()
	{
		$row = $this->db->select('version')->get($this->_migration_table)->row();
		return $row ? $row->version : '0';
	}

	// --------------------------------------------------------------------

	/**
	 * Stores the current schema version
	 * 存储当前模式的版本
	 * @param	string	$migration	Migration reached 迁移到
	 * @return	void
	 */
	protected function _update_version($migration)
	{
		$this->db->update($this->_migration_table, array(
			'version' => $migration
		));
	}

	// --------------------------------------------------------------------

	/**
	 * Enable the use of CI super-global
	 * 使CI超全局变量的使用
	 * @param	string	$var
	 * @return	mixed
	 */
	public function __get($var)
	{
		return get_instance()->$var;
	}

}
