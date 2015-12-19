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
 * CodeIgniter Calendar Class
 * CodeIgniter日历类
 * This class enables the creation of calendars
 * 这个类允许创建日历
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/calendar.html
 */
class CI_Calendar {

	/**
	 * Calendar layout template
	 * 日历布局模板
	 * @var mixed 混合的
	 */
	public $template = '';

	/**
	 * Replacements array for template
	 * 替换数组模板
	 * @var array
	 */
	public $replacements = array();

	/**
	 * Day of the week to start the calendar on
	 * 星期开始日历在sunday
	 * @var string
	 */
	public $start_day = 'sunday';

	/**
	 * How to display months
	 * 如何显示几个月
	 * @var string
	 */
	public $month_type = 'long';

	/**
	 * How to display names of days
	 * 如何显示的名字吗
	 * @var string
	 */
	public $day_type = 'abr';

	/**
	 * Whether to show next/prev month links
	 * 是否显示下一个/上一页链接
	 * @var bool
	 */
	public $show_next_prev = FALSE;

	/**
	 * Url base to use for next/prev month links
	 * Url基地使用下/上一页链接
	 * @var bool
	 */
	public $next_prev_url = '';

	/**
	 * Show days of other months
	 * 展示其他月份的天
	 * @var bool
	 */
	public $show_other_days = FALSE;

	// --------------------------------------------------------------------

	/**
	 * CI Singleton
	 * CI单例
	 * @var object对象
	 */
	protected $CI;

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 * 构造函数 类构造器 
	 * Loads the calendar language file and sets the default time reference.
	 * 加载日历语言文件和设置默认时间参考。
	 * @uses	CI_Lang::$is_loaded
	 *
	 * @param	array	$config	Calendar options 日历设置 日历选项
	 * @return	void
	 */
	public function __construct($config = array())
	{
		$this->CI =& get_instance();
		$this->CI->lang->load('calendar');

		empty($config) OR $this->initialize($config);

		log_message('info', 'Calendar Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize the user preferences
	 * 初始化用户首选项
	 * Accepts an associative array as input, containing display preferences
	 * 接受一个关联数组作为输入,包含显示偏好
	 * @param	array	config preferences 配置首选项
	 * @return	CI_Calendar CI日历
	 */
	public function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}

		// Set the next_prev_url to the controller if required but not defined 设置next_prev_url控制器如果需要但没有定义
		if ($this->show_next_prev === TRUE && empty($this->next_prev_url))
		{
			$this->next_prev_url = $this->CI->config->site_url($this->CI->router->class.'/'.$this->CI->router->method);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Generate the calendar
	 * 生成的日历
	 * @param	int	the year 年
	 * @param	int	the month 月
	 * @param	array	the data to be shown in the calendar cells 数据显示在日历调用
	 * @return	string
	 */
	public function generate($year = '', $month = '', $data = array())
	{
		$local_time = time();

		// Set and validate the supplied month/year 设置和验证提供的月/年
		if (empty($year))
		{
			$year = date('Y', $local_time);
		}
		elseif (strlen($year) === 1)
		{
			$year = '200'.$year;
		}
		elseif (strlen($year) === 2)
		{
			$year = '20'.$year;
		}

		if (empty($month))
		{
			$month = date('m', $local_time);
		}
		elseif (strlen($month) === 1)
		{
			$month = '0'.$month;
		}

		$adjusted_date = $this->adjust_date($month, $year);

		$month	= $adjusted_date['month'];
		$year	= $adjusted_date['year'];

		// Determine the total days in the month 确定总天月
		$total_days = $this->get_total_days($month, $year);

		// Set the starting day of the week 确定本周开始的一天
		$start_days	= array('sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6);
		$start_day	= isset($start_days[$this->start_day]) ? $start_days[$this->start_day] : 0;

		// Set the starting day number 设置数量开始的一天
		$local_date = mktime(12, 0, 0, $month, 1, $year);
		$date = getdate($local_date);
		$day  = $start_day + 1 - $date['wday'];

		while ($day > 1)
		{
			$day -= 7;
		}

		// Set the current month/year/day 设置当前月/年/天
		// We use this to determine the "today" date 我们用这个来确定“今天”日期
		$cur_year	= date('Y', $local_time);
		$cur_month	= date('m', $local_time);
		$cur_day	= date('j', $local_time);

		$is_current_month = ($cur_year == $year && $cur_month == $month);

		// Generate the template data array 生成模板数据数组
		$this->parse_template();

		// Begin building the calendar output 开始建立日历输出
		$out = $this->replacements['table_open']."\n\n".$this->replacements['heading_row_start']."\n";

		// "previous" month link “以前”链接
		if ($this->show_next_prev === TRUE)
		{
			// Add a trailing slash to the URL if needed 如果需要使用斜杠添加到URL
			$this->next_prev_url = preg_replace('/(.+?)\/*$/', '\\1/', $this->next_prev_url);

			$adjusted_date = $this->adjust_date($month - 1, $year);
			$out .= str_replace('{previous_url}', $this->next_prev_url.$adjusted_date['year'].'/'.$adjusted_date['month'], $this->replacements['heading_previous_cell'])."\n";
		}

		// Heading containing the month/year 标题包含月/年
		$colspan = ($this->show_next_prev === TRUE) ? 5 : 7;

		$this->replacements['heading_title_cell'] = str_replace('{colspan}', $colspan,
								str_replace('{heading}', $this->get_month_name($month).'&nbsp;'.$year, $this->replacements['heading_title_cell']));

		$out .= $this->replacements['heading_title_cell']."\n";

		// "next" month link 下一个月链接
		if ($this->show_next_prev === TRUE)
		{
			$adjusted_date = $this->adjust_date($month + 1, $year);
			$out .= str_replace('{next_url}', $this->next_prev_url.$adjusted_date['year'].'/'.$adjusted_date['month'], $this->replacements['heading_next_cell']);
		}

		$out .= "\n".$this->replacements['heading_row_end']."\n\n"
			// Write the cells containing the days of the week 写的调用包含的每周的days
			.$this->replacements['week_row_start']."\n";

		$day_names = $this->get_day_names();

		for ($i = 0; $i < 7; $i ++)
		{
			$out .= str_replace('{week_day}', $day_names[($start_day + $i) %7], $this->replacements['week_day_cell']);
		}

		$out .= "\n".$this->replacements['week_row_end']."\n";

		// Build the main body of the calendar 构建主体的日历
		while ($day <= $total_days)
		{
			$out .= "\n".$this->replacements['cal_row_start']."\n";

			for ($i = 0; $i < 7; $i++)
			{
				if ($day > 0 && $day <= $total_days)
				{
					$out .= ($is_current_month === TRUE && $day == $cur_day) ? $this->replacements['cal_cell_start_today'] : $this->replacements['cal_cell_start'];

					if (isset($data[$day]))
					{
						// Cells with content 调用内容
						$temp = ($is_current_month === TRUE && $day == $cur_day) ?
								$this->replacements['cal_cell_content_today'] : $this->replacements['cal_cell_content'];
						$out .= str_replace(array('{content}', '{day}'), array($data[$day], $day), $temp);
					}
					else
					{
						// Cells with no content 调用无内容
						$temp = ($is_current_month === TRUE && $day == $cur_day) ?
								$this->replacements['cal_cell_no_content_today'] : $this->replacements['cal_cell_no_content'];
						$out .= str_replace('{day}', $day, $temp);
					}

					$out .= ($is_current_month === TRUE && $day == $cur_day) ? $this->replacements['cal_cell_end_today'] : $this->replacements['cal_cell_end'];
				}
				elseif ($this->show_other_days === TRUE)
				{
					$out .= $this->replacements['cal_cell_start_other'];

					if ($day <= 0)
					{
						// Day of previous month 当前天的前一个月
						$prev_month = $this->adjust_date($month - 1, $year);
						$prev_month_days = $this->get_total_days($prev_month['month'], $prev_month['year']);
						$out .= str_replace('{day}', $prev_month_days + $day, $this->replacements['cal_cell_other']);
					}
					else
					{
						// Day of next month 当前天的下一个月
						$out .= str_replace('{day}', $day - $total_days, $this->replacements['cal_cell_other']);
					}

					$out .= $this->replacements['cal_cell_end_other'];
				}
				else
				{
					// Blank cells 空白调用
					$out .= $this->replacements['cal_cell_start'].$this->replacements['cal_cell_blank'].$this->replacements['cal_cell_end'];
				}

				$day++;
			}

			$out .= "\n".$this->replacements['cal_row_end']."\n";
		}

		return $out .= "\n".$this->replacements['table_close'];
	}

	// --------------------------------------------------------------------

	/**
	 * Get Month Name
	 * 月名字
	 * Generates a textual month name based on the numeric
	 * month provided.
	 * 基于数字生成一个文本月名称月提供。
	 * @param	int	the month
	 * @return	string
	 */
	public function get_month_name($month)
	{
		if ($this->month_type === 'short')
		{
			$month_names = array('01' => 'cal_jan', '02' => 'cal_feb', '03' => 'cal_mar', '04' => 'cal_apr', '05' => 'cal_may', '06' => 'cal_jun', '07' => 'cal_jul', '08' => 'cal_aug', '09' => 'cal_sep', '10' => 'cal_oct', '11' => 'cal_nov', '12' => 'cal_dec');
		}
		else
		{
			$month_names = array('01' => 'cal_january', '02' => 'cal_february', '03' => 'cal_march', '04' => 'cal_april', '05' => 'cal_mayl', '06' => 'cal_june', '07' => 'cal_july', '08' => 'cal_august', '09' => 'cal_september', '10' => 'cal_october', '11' => 'cal_november', '12' => 'cal_december');
		}

		return ($this->CI->lang->line($month_names[$month]) === FALSE)
			? ucfirst(substr($month_names[$month], 4))
			: $this->CI->lang->line($month_names[$month]);
	}

	// --------------------------------------------------------------------

	/**
	 * Get Day Names
	 * 得到一天的名字
	 * Returns an array of day names (Sunday, Monday, etc.) based
	 * on the type. Options: long, short, abr
	 * 返回一个数组的名称(周日,周一等)基于类型。选项:长、短,上
	 * @param	string
	 * @return	array
	 */
	public function get_day_names($day_type = '')
	{
		if ($day_type !== '')
		{
			$this->day_type = $day_type;
		}

		if ($this->day_type === 'long')
		{
			$day_names = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
		}
		elseif ($this->day_type === 'short')
		{
			$day_names = array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat');
		}
		else
		{
			$day_names = array('su', 'mo', 'tu', 'we', 'th', 'fr', 'sa');
		}

		$days = array();
		for ($i = 0, $c = count($day_names); $i < $c; $i++)
		{
			$days[] = ($this->CI->lang->line('cal_'.$day_names[$i]) === FALSE) ? ucfirst($day_names[$i]) : $this->CI->lang->line('cal_'.$day_names[$i]);
		}

		return $days;
	}

	// --------------------------------------------------------------------

	/**
	 * Adjust Date
	 * 调整日期
	 * This function makes sure that we have a valid month/year.
	 * For example, if you submit 13 as the month, the year will
	 * increment and the month will become January.
	 * 该函数确保我们有一个有效的月/年。例如,如果您提交13个月,今年将增加,本月将成为1月。
	 * @param	int	the month
	 * @param	int	the year
	 * @return	array
	 */
	public function adjust_date($month, $year)
	{
		$date = array();

		$date['month']	= $month;
		$date['year']	= $year;

		while ($date['month'] > 12)
		{
			$date['month'] -= 12;
			$date['year']++;
		}

		while ($date['month'] <= 0)
		{
			$date['month'] += 12;
			$date['year']--;
		}

		if (strlen($date['month']) === 1)
		{
			$date['month'] = '0'.$date['month'];
		}

		return $date;
	}

	// --------------------------------------------------------------------

	/**
	 * Total days in a given month
	 * 总在一个给定的月
	 * @param	int	the month
	 * @param	int	the year
	 * @return	int
	 */
	public function get_total_days($month, $year)
	{
		$this->CI->load->helper('date');
		return days_in_month($month, $year);
	}

	// --------------------------------------------------------------------

	/**
	 * Set Default Template Data
	 * 设置默认模板数据
	 * This is used in the event that the user has not created their own template
	 * 这是用于事件,用户没有创建自己的模板
	 * @return	array
	 */
	public function default_template()
	{
		return array(
			'table_open'				=> '<table border="0" cellpadding="4" cellspacing="0">',
			'heading_row_start'			=> '<tr>',
			'heading_previous_cell'		=> '<th><a href="{previous_url}">&lt;&lt;</a></th>',
			'heading_title_cell'		=> '<th colspan="{colspan}">{heading}</th>',
			'heading_next_cell'			=> '<th><a href="{next_url}">&gt;&gt;</a></th>',
			'heading_row_end'			=> '</tr>',
			'week_row_start'			=> '<tr>',
			'week_day_cell'				=> '<td>{week_day}</td>',
			'week_row_end'				=> '</tr>',
			'cal_row_start'				=> '<tr>',
			'cal_cell_start'			=> '<td>',
			'cal_cell_start_today'		=> '<td>',
			'cal_cell_start_other'		=> '<td style="color: #666;">',
			'cal_cell_content'			=> '<a href="{content}">{day}</a>',
			'cal_cell_content_today'	=> '<a href="{content}"><strong>{day}</strong></a>',
			'cal_cell_no_content'		=> '{day}',
			'cal_cell_no_content_today'	=> '<strong>{day}</strong>',
			'cal_cell_blank'			=> '&nbsp;',
			'cal_cell_other'			=> '{day}',
			'cal_cell_end'				=> '</td>',
			'cal_cell_end_today'		=> '</td>',
			'cal_cell_end_other'		=> '</td>',
			'cal_row_end'				=> '</tr>',
			'table_close'				=> '</table>'
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Parse Template
	 * 解析模板文件 
	 * Harvests the data within the template {pseudo-variables}
	 * used to display the calendar
	 * 收成{伪变量}内的数据模板用于显示日历
	 * @return	CI_Calendar CI日历
	 */
	public function parse_template()
	{
		$this->replacements = $this->default_template();

		if (empty($this->template))
		{
			return $this;
		}

		if (is_string($this->template))
		{
			$today = array('cal_cell_start_today', 'cal_cell_content_today', 'cal_cell_no_content_today', 'cal_cell_end_today');

			foreach (array('table_open', 'table_close', 'heading_row_start', 'heading_previous_cell', 'heading_title_cell', 'heading_next_cell', 'heading_row_end', 'week_row_start', 'week_day_cell', 'week_row_end', 'cal_row_start', 'cal_cell_start', 'cal_cell_content', 'cal_cell_no_content', 'cal_cell_blank', 'cal_cell_end', 'cal_row_end', 'cal_cell_start_today', 'cal_cell_content_today', 'cal_cell_no_content_today', 'cal_cell_end_today', 'cal_cell_start_other', 'cal_cell_other', 'cal_cell_end_other') as $val)
			{
				if (preg_match('/\{'.$val.'\}(.*?)\{\/'.$val.'\}/si', $this->template, $match))
				{
					$this->replacements[$val] = $match[1];
				}
				elseif (in_array($val, $today, TRUE))
				{
					$this->replacements[$val] = $this->replacements[substr($val, 0, -6)];
				}
			}
		}
		elseif (is_array($this->template))
		{
			$this->replacements = array_merge($this->replacements, $this->template);
		}

		return $this;
	}

}
