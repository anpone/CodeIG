<?php
/**
 * System messages translation for CodeIgniter(tm)
 *
 * @author	CodeIgniter community
 * @copyright	Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	http://codeigniter.com
 */
defined('BASEPATH') OR exit('No direct script access allowed');
$lang['migration_none_found'] = '没有发现任何迁移';
$lang['migration_not_found'] = '无法根据版本号码%s 找到迁移方法';
$lang['migration_sequence_gap'] = '版本迁移存在间隙：%s';
$lang['migration_multiple_version'] = '有多个迁移对应到同一版本号：%s';
$lang['migration_class_doesnt_exist'] = '无法找到迁移类别 "%s"';
$lang['migration_missing_up_method'] = '无法找到迁移类别 "%s" 中的 "up" 方法';
$lang['migration_missing_down_method'] = '无法找到迁移类别 "%s" 中的 " 方法';
$lang['migration_invalid_filename'] = '无效的迁移档名："%s"';