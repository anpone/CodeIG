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
 * Form Validation Class
 * 表单验证类
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Validation
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/form_validation.html
 */
class CI_Form_validation {

	/**
	 * Reference to the CodeIgniter instance
	 * CodeIgniter实例的引用
	 * @var object
	 */
	protected $CI;

	/**
	 * Validation data for the current form submission
	 * 验证当前表单提交的数据
	 * @var array
	 */
	protected $_field_data		= array();

	/**
	 * Validation rules for the current form
	 * 当前表单的验证规则
	 * @var array
	 */
	protected $_config_rules	= array();

	/**
	 * Array of validation errors
	 * 一系列的验证错误
	 * @var array
	 */
	protected $_error_array		= array();

	/**
	 * Array of custom error messages
	 * 阵列的自定义错误消息
	 * @var array
	 */
	protected $_error_messages	= array();

	/**
	 * Start tag for error wrapping
	 * 开始标记为错误包装
	 * @var string
	 */
	protected $_error_prefix	= '<p>';

	/**
	 * End tag for error wrapping
	 * 包装结束标记为错误
	 * @var string
	 */
	protected $_error_suffix	= '</p>';

	/**
	 * Custom error message
	 * 定制错误消息 
	 * @var string
	 */
	protected $error_string		= '';

	/**
	 * Whether the form data has been validated as safe
	 * 表单数据是否已被确认为安全
	 * @var bool
	 */
	protected $_safe_form_data	= FALSE;

	/**
	 * Custom data to validate
	 * 自定义数据验证
	 * @var array
	 */
	public $validation_data	= array();

	/**
	 * Initialize Form_Validation class
	 * 初始化Form_Validation类
	 * @param	array	$rules
	 * @return	void
	 */
	public function __construct($rules = array())
	{
		$this->CI =& get_instance();

		// applies delimiters set in config file. 分隔符设置适用于配置文件。
		if (isset($rules['error_prefix']))
		{
			$this->_error_prefix = $rules['error_prefix'];
			unset($rules['error_prefix']);
		}
		if (isset($rules['error_suffix']))
		{
			$this->_error_suffix = $rules['error_suffix'];
			unset($rules['error_suffix']);
		}

		// Validation rules can be stored in a config file. 验证规则可以存储在一个配置文件。
		$this->_config_rules = $rules;

		// Automatically load the form helper 自动加载辅助形式
		$this->CI->load->helper('form');

		log_message('info', 'Form Validation Class Initialized表单验证类初始化');
	}

	// --------------------------------------------------------------------

	/**
	 * Set Rules
	 * 设置规则
	 * This function takes an array of field names and validation
	 * rules as input, any custom error messages, validates the info,
	 * and stores it
	 * 这个函数需要一个数组的字段名和验证规则作为输入,任何自定义错误消息,验证信息,并存储它
	 * @param	mixed	$field
	 * @param	string	$label
	 * @param	mixed	$rules
	 * @param	array	$errors
	 * @return	CI_Form_validation
	 */
	public function set_rules($field, $label = '', $rules = array(), $errors = array())
	{
		// No reason to set rules if we have no POST data 没有理由去制定规则,如果我们没有POST数据
		// or a validation array has not been specified  没有指定验证数组
		if ($this->CI->input->method() !== 'post' && empty($this->validation_data))
		{
			return $this;
		}

		// If an array was passed via the first parameter instead of individual string 如果数组是通过第一个参数,而不是单个字符串
		// values we cycle through it and recursively call this function. 值我们循环和递归地调用这个函数
		if (is_array($field))
		{
			foreach ($field as $row)
			{
				// Houston, we have a problem... 休斯顿,我们有一个问题…
				if ( ! isset($row['field'], $row['rules']))
				{
					continue;
				}

				// If the field label wasn't passed we use the field name 如果字段标签不是通过我们使用字段名
				$label = isset($row['label']) ? $row['label'] : $row['field'];

				// Add the custom error message array 添加自定义错误消息数组
				$errors = (isset($row['errors']) && is_array($row['errors'])) ? $row['errors'] : array();

				// Here we go! 准备出发
				$this->set_rules($row['field'], $label, $row['rules'], $errors);
			}

			return $this;
		}

		// No fields or no rules? Nothing to do... 没有字段或没有规则?无事可做…
		if ( ! is_string($field) OR $field === '' OR empty($rules))
		{
			return $this;
		}
		elseif ( ! is_array($rules))
		{
			// BC: Convert pipe-separated rules string to an array 公元前:pipe-separated(被分开的管)规则字符串转换为一个数组
			if ( ! is_string($rules))
			{
				return $this;
			}

			$rules = preg_split('/\|(?![^\[]*\])/', $rules);
		}

		// If the field label wasn't passed we use the field name 如果字段标签不是通过我们使用字段名
		$label = ($label === '') ? $field : $label;

		$indexes = array();

		// Is the field name an array? If it is an array, we break it apart 字段名称数组吗?如果它是一个数组,我们把它分开
		// into its components so that we can fetch the corresponding POST data later 到它的组件,这样以后我们可以获取相应的POST数据
		if (($is_array = (bool) preg_match_all('/\[(.*?)\]/', $field, $matches)) === TRUE)
		{
			sscanf($field, '%[^[][', $indexes[0]);

			for ($i = 0, $c = count($matches[0]); $i < $c; $i++)
			{
				if ($matches[1][$i] !== '')
				{
					$indexes[] = $matches[1][$i];
				}
			}
		}

		// Build our master array 构建我们的主数组
		$this->_field_data[$field] = array(
			'field'		=> $field,
			'label'		=> $label,
			'rules'		=> $rules,
			'errors'	=> $errors,
			'is_array'	=> $is_array,
			'keys'		=> $indexes,
			'postdata'	=> NULL,
			'error'		=> ''
		);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * By default, form validation uses the $_POST array to validate
	 * 默认情况下,表单验证使用$ _POST数组来验证
	 * If an array is set through this method, then this array will
	 * be used instead of the $_POST array
	 * 如果一个数组设置通过这种方法,则将使用这个数组而不是$ _POST数组
	 * Note that if you are validating multiple arrays, then the
	 * reset_validation() function should be called after validating
	 * each array due to the limitations of CI's singleton
	 * 注意,如果您验证多个数组,然后reset_validation()函数应该被称为后验证每个数组将CI的单例模式的局限性
	 * @param	array	$data
	 * @return	CI_Form_validation
	 */
	public function set_data(array $data)
	{
		if ( ! empty($data))
		{
			$this->validation_data = $data;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Error Message
	 * 设置错误消息
	 * Lets users set their own error messages on the fly. Note:
	 * The key name has to match the function name that it corresponds to.
	 * 允许用户设定他们自己的错误消息。注意:关键名称必须匹配它对应的函数名。
	 * @param	array
	 * @param	string
	 * @return	CI_Form_validation
	 */
	public function set_message($lang, $val = '')
	{
		if ( ! is_array($lang))
		{
			$lang = array($lang => $val);
		}

		$this->_error_messages = array_merge($this->_error_messages, $lang);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set The Error Delimiter
	 * 设置错误分隔符
	 * Permits a prefix/suffix to be added to each error message
	 * 允许一个前缀/后缀添加到每一个错误消息
	 * @param	string
	 * @param	string
	 * @return	CI_Form_validation
	 */
	public function set_error_delimiters($prefix = '<p>', $suffix = '</p>')
	{
		$this->_error_prefix = $prefix;
		$this->_error_suffix = $suffix;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Error Message
	 * 得到错误消息
	 * Gets the error message associated with a particular field
	 * 与一个特定的领域相关的错误消息
	 * @param	string	$field	Field name 字段名
	 * @param	string	$prefix	HTML start tag HTML开始标记
	 * @param 	string	$suffix	HTML end tag HTML结束标记
	 * @return	string
	 */
	public function error($field, $prefix = '', $suffix = '')
	{
		if (empty($this->_field_data[$field]['error']))
		{
			return '';
		}

		if ($prefix === '')
		{
			$prefix = $this->_error_prefix;
		}

		if ($suffix === '')
		{
			$suffix = $this->_error_suffix;
		}

		return $prefix.$this->_field_data[$field]['error'].$suffix;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Array of Error Messages
	 * 得到一系列的错误消息
	 * Returns the error messages as an array
	 * 返回错误消息作为一个数组
	 * @return	array
	 */
	public function error_array()
	{
		return $this->_error_array;
	}

	// --------------------------------------------------------------------

	/**
	 * Error String
	 * 错误字符串
	 * Returns the error messages as a string, wrapped in the error delimiters
	 * 返回错误消息作为一个字符串,用分隔符的错误
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function error_string($prefix = '', $suffix = '')
	{
		// No errors, validation passes! 没有错误,验证通过!
		if (count($this->_error_array) === 0)
		{
			return '';
		}

		if ($prefix === '')
		{
			$prefix = $this->_error_prefix;
		}

		if ($suffix === '')
		{
			$suffix = $this->_error_suffix;
		}

		// Generate the error string 生成的错误字符串
		$str = '';
		foreach ($this->_error_array as $val)
		{
			if ($val !== '')
			{
				$str .= $prefix.$val.$suffix."\n";
			}
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Run the Validator
	 * 运行验证器
	 * This function does all the work.
	 * 这个函数做所有的工作。
	 * @param	string	$group
	 * @return	bool
	 */
	public function run($group = '')
	{
		// Do we even have any data to process?  Mm? 我们甚至有任何数据处理吗?毫米吗?
		$validation_array = empty($this->validation_data) ? $_POST : $this->validation_data;
		if (count($validation_array) === 0)
		{
			return FALSE;
		}

		// Does the _field_data array containing the validation rules exist? _field_data数组包含验证规则的存在吗?
		// If not, we look to see if they were assigned via a config file 如果没有,我们希望看到他们是否被分配通过一个配置文件
		if (count($this->_field_data) === 0)
		{
			// No validation rules?  We're done... 没有验证规则?做完了…
			if (count($this->_config_rules) === 0)
			{
				return FALSE;
			}

			if (empty($group))
			{
				// Is there a validation rule for the particular URI being accessed? 有被访问特定URI的验证规则吗?
				$group = trim($this->CI->uri->ruri_string(), '/');
				isset($this->_config_rules[$group]) OR $group = $this->CI->router->class.'/'.$this->CI->router->method;
			}

			$this->set_rules(isset($this->_config_rules[$group]) ? $this->_config_rules[$group] : $this->_config_rules);

			// Were we able to set the rules correctly? 我们能够正确制定规则吗?
			if (count($this->_field_data) === 0)
			{
				log_message('debug', 'Unable to find validation rules');
				return FALSE;
			}
		}

		// Load the language file containing error messages 加载包含错误消息的语言文件
		$this->CI->lang->load('form_validation');

		// Cycle through the rules for each field and match the corresponding $validation_data item 循环每个字段的规则并匹配相应的美元validation_data项目
		foreach ($this->_field_data as $field => $row)
		{
			// Fetch the data from the validation_data array item and cache it in the _field_data array. 获取validation_data数组项的数据和缓存_field_data数组。
			// Depending on whether the field name is an array or a string will determine where we get it from.  取决于字段名称是一个数组或字符串将决定我们得到它。
			if ($row['is_array'] === TRUE)
			{
				$this->_field_data[$field]['postdata'] = $this->_reduce_array($validation_array, $row['keys']);
			}
			elseif (isset($validation_array[$field]))
			{
				$this->_field_data[$field]['postdata'] = $validation_array[$field];
			}
		}

		// Execute validation rules 执行验证规则
		// Note: A second foreach (for now) is required in order to avoid false-positives 注意:第二个foreach(现在)是为了避免假阳性
		//	 for rules like 'matches', which correlate to other validation fields. 匹配”等规则,关联到其他验证字段。
		foreach ($this->_field_data as $field => $row)
		{
			// Don't try to validate if we have no rules set  不要试图验证如果我们没有规则集
			if (empty($row['rules']))
			{
				continue;
			}

			$this->_execute($row, $row['rules'], $this->_field_data[$field]['postdata']);
		}

		// Did we end up with any errors? 我们有任何错误吗?
		$total_errors = count($this->_error_array);
		if ($total_errors > 0)
		{
			$this->_safe_form_data = TRUE;
		}

		// Now we need to re-set the POST data with the new, processed data 现在我们需要重新设定新的POST数据,处理数据
		$this->_reset_post_array();

		return ($total_errors === 0);
	}

	// --------------------------------------------------------------------

	/**
	 * Traverse a multidimensional $_POST array index until the data is found
	 * 遍历一个多维数组$ _POST指数直到找到数据
	 * @param	array
	 * @param	array
	 * @param	int
	 * @return	mixed
	 */
	protected function _reduce_array($array, $keys, $i = 0)
	{
		if (is_array($array) && isset($keys[$i]))
		{
			return isset($array[$keys[$i]]) ? $this->_reduce_array($array[$keys[$i]], $keys, ($i+1)) : NULL;
		}

		// NULL must be returned for empty fields 空为空的字段必须返回
		return ($array === '') ? NULL : $array;
	}

	// --------------------------------------------------------------------

	/**
	 * Re-populate the _POST array with our finalized and processed data
	 * 重新填充_POST数组与我们确定和处理数据
	 * @return	void
	 */
	protected function _reset_post_array()
	{
		foreach ($this->_field_data as $field => $row)
		{
			if ($row['postdata'] !== NULL)
			{
				if ($row['is_array'] === FALSE)
				{
					if (isset($_POST[$row['field']]))
					{
						$_POST[$row['field']] = $row['postdata'];
					}
				}
				else
				{
					// start with a reference 从一个参考
					$post_ref =& $_POST;

					// before we assign values, make a reference to the right POST key 在赋值之前,使右门柱键的引用
					if (count($row['keys']) === 1)
					{
						$post_ref =& $post_ref[current($row['keys'])];
					}
					else
					{
						foreach ($row['keys'] as $val)
						{
							$post_ref =& $post_ref[$val];
						}
					}

					if (is_array($row['postdata']))
					{
						$array = array();
						foreach ($row['postdata'] as $k => $v)
						{
							$array[$k] = $v;
						}

						$post_ref = $array;
					}
					else
					{
						$post_ref = $row['postdata'];
					}
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Executes the Validation routines
	 * 执行验证例程
	 * @param	array
	 * @param	array
	 * @param	mixed
	 * @param	int
	 * @return	mixed
	 */
	protected function _execute($row, $rules, $postdata = NULL, $cycles = 0)
	{
		// If the $_POST data is an array we will run a recursive call 如果数组$ _POST数据我们将运行一个递归调用
		if (is_array($postdata))
		{
			foreach ($postdata as $key => $val)
			{
				$this->_execute($row, $rules, $val, $key);
			}

			return;
		}

		// If the field is blank, but NOT required, no further tests are necessary 如果字段是空白的,但不是必需的,没有进一步的测试是必要的
		$callback = FALSE;
		if ( ! in_array('required', $rules) && ($postdata === NULL OR $postdata === ''))
		{
			// Before we bail out, does the rule contain a callback? 在我们救助之前,规则包含一个回调吗?
			foreach ($rules as &$rule)
			{
				if (is_string($rule))
				{
					if (strncmp($rule, 'callback_', 9) === 0)
					{
						$callback = TRUE;
						$rules = array(1 => $rule);
						break;
					}
				}
				elseif (is_callable($rule))
				{
					$callback = TRUE;
					$rules = array(1 => $rule);
					break;
				}
				elseif (is_array($rule) && isset($rule[0], $rule[1]) && is_callable($rule[1]))
				{
					$callback = TRUE;
					$rules = array(array($rule[0], $rule[1]));
					break;
				}
			}

			if ( ! $callback)
			{
				return;
			}
		}

		// Isset Test. Typically this rule will only apply to checkboxes. 收取测试。通常这条规则只适用于复选框。
		if (($postdata === NULL OR $postdata === '') && ! $callback)
		{
			if (in_array('isset', $rules, TRUE) OR in_array('required', $rules))
			{
				// Set the message type 设置消息类型
				$type = in_array('required', $rules) ? 'required' : 'isset';

				// Check if a custom message is defined 检查是否定义了自定义消息
				if (isset($this->_field_data[$row['field']]['errors'][$type]))
				{
					$line = $this->_field_data[$row['field']]['errors'][$type];
				}
				elseif (isset($this->_error_messages[$type]))
				{
					$line = $this->_error_messages[$type];
				}
				elseif (FALSE === ($line = $this->CI->lang->line('form_validation_'.$type))
					// DEPRECATED support for non-prefixed keys 弃用支持non-prefixed钥匙
					&& FALSE === ($line = $this->CI->lang->line($type, FALSE)))
				{
					$line = 'The field was not set';
				}

				// Build the error message  构建错误消息
				$message = $this->_build_error_msg($line, $this->_translate_fieldname($row['label']));

				// Save the error message  保存错误消息
				$this->_field_data[$row['field']]['error'] = $message;

				if ( ! isset($this->_error_array[$row['field']]))
				{
					$this->_error_array[$row['field']] = $message;
				}
			}

			return;
		}

		// --------------------------------------------------------------------

		// Cycle through each rule and run it 循环每条规则并运行它
		foreach ($rules as $rule)
		{
			$_in_array = FALSE;

			// We set the $postdata variable with the current data in our master array so that
			// each cycle of the loop is dealing with the processed data from the last cycle
			// 我们设置了$ postdata变量与当前数据在我们的主数组/每个周期的循环处理最后一个周期的数据处理
			if ($row['is_array'] === TRUE && is_array($this->_field_data[$row['field']]['postdata']))
			{
				// We shouldn't need this safety, but just in case there isn't an array index
				// associated with this cycle we'll bail out
				// 我们不需要这个安全,但是以防没有数组索引与这个周期我们救助
				if ( ! isset($this->_field_data[$row['field']]['postdata'][$cycles]))
				{
					continue;
				}

				$postdata = $this->_field_data[$row['field']]['postdata'][$cycles];
				$_in_array = TRUE;
			}
			else
			{
				// If we get an array field, but it's not expected - then it is most likely
				// somebody messing with the form on the client side, so we'll just consider
				// it an empty field
				// 如果我们得到一个数组字段,但这并不是预期的,那么它最有可能有人干扰的形式在客户端,所以我们只会考虑一个空字段
				$postdata = is_array($this->_field_data[$row['field']]['postdata'])
					? NULL
					: $this->_field_data[$row['field']]['postdata'];
			}

			// Is the rule a callback?  规则是一个回调?
			$callback = $callable = FALSE;
			if (is_string($rule))
			{
				if (strpos($rule, 'callback_') === 0)
				{
					$rule = substr($rule, 9);
					$callback = TRUE;
				}
			}
			elseif (is_callable($rule))
			{
				$callable = TRUE;
			}
			elseif (is_array($rule) && isset($rule[0], $rule[1]) && is_callable($rule[1]))
			{
				// We have a "named" callable, so save the name  我们有一个“命名”可调用的,所以保存的名字
				$callable = $rule[0];
				$rule = $rule[1];
			}

			// Strip the parameter (if exists) from the rule 从规则中 带参数(如果存在)
			// Rules can contain a parameter: max_length[5]  规则可以包含一个参数:max_length[5]
			$param = FALSE;
			if ( ! $callable && preg_match('/(.*?)\[(.*)\]/', $rule, $match))
			{
				$rule = $match[1];
				$param = $match[2];
			}

			// Call the function that corresponds to the rule 调用的函数对应的规则
			if ($callback OR $callable !== FALSE)
			{
				if ($callback)
				{
					if ( ! method_exists($this->CI, $rule))
					{
						log_message('debug', 'Unable to find callback validation rule: '.$rule);
						$result = FALSE;
					}
					else
					{
						// Run the function and grab the result 运行功能和获取结果
						$result = $this->CI->$rule($postdata, $param);
					}
				}
				else
				{
					$result = is_array($rule)
						? $rule[0]->{$rule[1]}($postdata)
						: $rule($postdata);

					// Is $callable set to a rule name?  $callable可调用设置为一个规则的名字吗?
					if ($callable !== FALSE)
					{
						$rule = $callable;
					}
				}

				// Re-assign the result to the master data array 重新分配结果到主数据数组
				if ($_in_array === TRUE)
				{
					$this->_field_data[$row['field']]['postdata'][$cycles] = is_bool($result) ? $postdata : $result;
				}
				else
				{
					$this->_field_data[$row['field']]['postdata'] = is_bool($result) ? $postdata : $result;
				}

				// If the field isn't required and we just processed a callback we'll move on...
				// 如果字段不是必需的,我们只是一个回调处理我们会继续……
				if ( ! in_array('required', $rules, TRUE) && $result !== FALSE)
				{
					continue;
				}
			}
			elseif ( ! method_exists($this, $rule))
			{
				// If our own wrapper function doesn't exist we see if a native PHP function does.
				// 如果我们自己的包装器函数不存在我们看看一个原生PHP函数。
				// Users can use any native PHP function call that has one param.
				// 用户可以使用任何原生PHP函数调用的参数之一。
				if (function_exists($rule))
				{
					// Native PHP functions issue warnings if you pass them more parameters than they use
					// 原生PHP函数发出警告,如果你传递的参数比他们使用
					$result = ($param !== FALSE) ? $rule($postdata, $param) : $rule($postdata);

					if ($_in_array === TRUE)
					{
						$this->_field_data[$row['field']]['postdata'][$cycles] = is_bool($result) ? $postdata : $result;
					}
					else
					{
						$this->_field_data[$row['field']]['postdata'] = is_bool($result) ? $postdata : $result;
					}
				}
				else
				{
					log_message('debug', 'Unable to find validation rule: '.$rule);
					$result = FALSE;
				}
			}
			else
			{
				$result = $this->$rule($postdata, $param);

				if ($_in_array === TRUE)
				{
					$this->_field_data[$row['field']]['postdata'][$cycles] = is_bool($result) ? $postdata : $result;
				}
				else
				{
					$this->_field_data[$row['field']]['postdata'] = is_bool($result) ? $postdata : $result;
				}
			}

			// Did the rule test negatively? If so, grab the error. 规则测试消极吗?如果是这样的话,抓住这个错误。
			if ($result === FALSE)
			{
				// Callable rules might not have named error messages  可调用的规则可能没有命名的错误消息
				if ( ! is_string($rule))
				{
					$line = $this->CI->lang->line('form_validation_error_message_not_set').'(Anonymous function)';
				}
				// Check if a custom message is defined  检查是否定义了自定义消息
				elseif (isset($this->_field_data[$row['field']]['errors'][$rule]))
				{
					$line = $this->_field_data[$row['field']]['errors'][$rule];
				}
				elseif ( ! isset($this->_error_messages[$rule]))
				{
					if (FALSE === ($line = $this->CI->lang->line('form_validation_'.$rule))
						// DEPRECATED support for non-prefixed keys  弃用支持non-prefixed钥匙
						&& FALSE === ($line = $this->CI->lang->line($rule, FALSE)))
					{
						$line = $this->CI->lang->line('form_validation_error_message_not_set').'('.$rule.')';
					}
				}
				else
				{
					$line = $this->_error_messages[$rule];
				}

				// Is the parameter we are inserting into the error message the name
				// of another field? If so we need to grab its "field label"
				// 是我们的参数插入到错误消息另一个字段的名字吗?如果我们需要抓住“字段标签”
				if (isset($this->_field_data[$param], $this->_field_data[$param]['label']))
				{
					$param = $this->_translate_fieldname($this->_field_data[$param]['label']);
				}

				// Build the error message 构建错误消息
				$message = $this->_build_error_msg($line, $this->_translate_fieldname($row['label']), $param);

				// Save the error message 保存错误消息
				$this->_field_data[$row['field']]['error'] = $message;

				if ( ! isset($this->_error_array[$row['field']]))
				{
					$this->_error_array[$row['field']] = $message;
				}

				return;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Translate a field name
	 * 翻译一个字段名
	 * @param	string	the field name 域名称 
	 * @return	string
	 */
	protected function _translate_fieldname($fieldname)
	{
		// Do we need to translate the field name? We look for the prefix 'lang:' to determine this 我们需要将字段名吗?我们寻找前缀“lang:”来确定这一点
		// If we find one, but there's no translation for the string - just return it 如果我们找到了,但是没有翻译的字符串,就返回它
		if (sscanf($fieldname, 'lang:%s', $line) === 1 && FALSE === ($fieldname = $this->CI->lang->line($line, FALSE)))
		{
			return $line;
		}

		return $fieldname;
	}

	// --------------------------------------------------------------------

	/**
	 * Build an error message using the field and param.
	 * 使用领域和参数构建一条错误消息。
	 * @param	string	The error message line 错误消息行
	 * @param	string	A field's human name 一个领域的人的名字
	 * @param	mixed	A rule's optional parameter 个规则是可选的参数
	 * @return	string
	 */
	protected function _build_error_msg($line, $field = '', $param = '')
	{
		// Check for %s in the string for legacy support. 检查% s为遗留在字符串的支持。
		if (strpos($line, '%s') !== FALSE)
		{
			return sprintf($line, $field, $param);
		}

		return str_replace(array('{field}', '{param}'), array($field, $param), $line);
	}

	// --------------------------------------------------------------------

	/**
	 * Checks if the rule is present within the validator
	 * 在验证器检查规则是否存在
	 * Permits you to check if a rule is present within the validator
	 * 允许您在验证器检查是否存在一个规则
	 * @param	string	the field name 域名称 
	 * @return	bool
	 */
	public function has_rule($field)
	{
		return isset($this->_field_data[$field]);
	}

	// --------------------------------------------------------------------

	/**
	 * Get the value from a form
	 * 从一种形式得到的价值
	 * Permits you to repopulate a form field with the value it was submitted
	 * with, or, if that value doesn't exist, with the default
	 * 允许您重新提交一个表单字段值,或者,如果该值不存在,违约
	 * @param	string	the field name
	 * @param	string
	 * @return	string
	 */
	public function set_value($field = '', $default = '')
	{
		if ( ! isset($this->_field_data[$field], $this->_field_data[$field]['postdata']))
		{
			return $default;
		}

		// If the data is an array output them one at a time. 如果数据是一个数组输出一次。
		//	E.g: form_input('name[]', set_value('name[]');
		if (is_array($this->_field_data[$field]['postdata']))
		{
			return array_shift($this->_field_data[$field]['postdata']);
		}

		return $this->_field_data[$field]['postdata'];
	}

	// --------------------------------------------------------------------

	/**
	 * Set Select
	 * 机组选型
	 * Enables pull-down lists to be set to the value the user
	 * selected in the event of an error
	 * 支持下拉列表被设置为用户选择的值的一个错误
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	public function set_select($field = '', $value = '', $default = FALSE)
	{
		if ( ! isset($this->_field_data[$field], $this->_field_data[$field]['postdata']))
		{
			return ($default === TRUE && count($this->_field_data) === 0) ? ' selected="selected"' : '';
		}

		$field = $this->_field_data[$field]['postdata'];
		$value = (string) $value;
		if (is_array($field))
		{
			// Note: in_array('', array(0)) returns TRUE, do not use it 注意:in_array(“阵列(0))返回TRUE,不使用它
			foreach ($field as &$v)
			{
				if ($value === $v)
				{
					return ' selected="selected"';
				}
			}

			return '';
		}
		elseif (($field === '' OR $value === '') OR ($field !== $value))
		{
			return '';
		}

		return ' selected="selected"';
	}

	// --------------------------------------------------------------------

	/**
	 * Set Radio
	 * 设置单选
	 * Enables radio buttons to be set to the value the user
	 * selected in the event of an error
	 * 允许将单选按钮设置为用户的价值选择的一个错误
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	public function set_radio($field = '', $value = '', $default = FALSE)
	{
		if ( ! isset($this->_field_data[$field], $this->_field_data[$field]['postdata']))
		{
			return ($default === TRUE && count($this->_field_data) === 0) ? ' checked="checked"' : '';
		}

		$field = $this->_field_data[$field]['postdata'];
		$value = (string) $value;
		if (is_array($field))
		{
			// Note: in_array('', array(0)) returns TRUE, do not use it 注意:in_array(“阵列(0))返回TRUE,不使用它
			foreach ($field as &$v)
			{
				if ($value === $v)
				{
					return ' checked="checked"';
				}
			}

			return '';
		}
		elseif (($field === '' OR $value === '') OR ($field !== $value))
		{
			return '';
		}

		return ' checked="checked"';
	}

	// --------------------------------------------------------------------

	/**
	 * Set Checkbox
	 * 设置多选复选框
	 * Enables checkboxes to be set to the value the user
	 * selected in the event of an error
	 * 允许将复选框设置为用户的值 选择的一个错误
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	public function set_checkbox($field = '', $value = '', $default = FALSE)
	{
		// Logic is exactly the same as for radio fields 同单选字段的逻辑是一模一样
		return $this->set_radio($field, $value, $default);
	}

	// --------------------------------------------------------------------

	/**
	 * Required
	 * 必需的
	 * @param	string
	 * @return	bool
	 */
	public function required($str)
	{
		return is_array($str) ? (bool) count($str) : (trim($str) !== '');
	}

	// --------------------------------------------------------------------

	/**
	 * Performs a Regular Expression match test.
	 * 执行一个正则表达式匹配测试。
	 * @param	string
	 * @param	string	regex
	 * @return	bool
	 */
	public function regex_match($str, $regex)
	{
		return (bool) preg_match($regex, $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Match one field to another
	 * 匹配一个领域到另一个地方
	 * @param	string	$str	string to compare against 字符串比较
	 * @param	string	$field
	 * @return	bool
	 */
	public function matches($str, $field)
	{
		return isset($this->_field_data[$field], $this->_field_data[$field]['postdata'])
			? ($str === $this->_field_data[$field]['postdata'])
			: FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Differs from another field
	 * 不同于另一个领域
	 * @param	string
	 * @param	string	field
	 * @return	bool
	 */
	public function differs($str, $field)
	{
		return ! (isset($this->_field_data[$field]) && $this->_field_data[$field]['postdata'] === $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Is Unique
	 * 是独一无二的
	 * Check if the input value doesn't already exist
	 * in the specified database field.
	 * 检查输入的值是否在指定的数据库字段不存在。
	 * @param	string	$str
	 * @param	string	$field
	 * @return	bool
	 */
	public function is_unique($str, $field)
	{
		sscanf($field, '%[^.].%[^.]', $table, $field);
		return isset($this->CI->db)
			? ($this->CI->db->limit(1)->get_where($table, array($field => $str))->num_rows() === 0)
			: FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Minimum Length
	 * 最小长度
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function min_length($str, $val)
	{
		if ( ! is_numeric($val))
		{
			return FALSE;
		}

		return ($val <= mb_strlen($str));
	}

	// --------------------------------------------------------------------

	/**
	 * Max Length
	 * 最大长度
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function max_length($str, $val)
	{
		if ( ! is_numeric($val))
		{
			return FALSE;
		}

		return ($val >= mb_strlen($str));
	}

	// --------------------------------------------------------------------

	/**
	 * Exact Length
	 * 准确的长度
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function exact_length($str, $val)
	{
		if ( ! is_numeric($val))
		{
			return FALSE;
		}

		return (mb_strlen($str) === (int) $val);
	}

	// --------------------------------------------------------------------

	/**
	 * Valid URL
	 * 有效的URL
	 * @param	string	$str
	 * @return	bool
	 */
	public function valid_url($str)
	{
		if (empty($str))
		{
			return FALSE;
		}
		elseif (preg_match('/^(?:([^:]*)\:)?\/\/(.+)$/', $str, $matches))
		{
			if (empty($matches[2]))
			{
				return FALSE;
			}
			elseif ( ! in_array($matches[1], array('http', 'https'), TRUE))
			{
				return FALSE;
			}

			$str = $matches[2];
		}

		$str = 'http://'.$str;

		// There's a bug affecting PHP 5.2.13, 5.3.2 that considers the
		// underscore to be a valid hostname character instead of a dash.
		// 有一个bug影响PHP 5.2.13,5.3.2认为下划线是一个有效的主机名字符而不是短跑。
		// Reference参考: https://bugs.php.net/bug.php?id=51192
		if (version_compare(PHP_VERSION, '5.2.13', '==') OR version_compare(PHP_VERSION, '5.3.2', '=='))
		{
			sscanf($str, 'http://%[^/]', $host);
			$str = substr_replace($str, strtr($host, array('_' => '-', '-' => '_')), 7, strlen($host));
		}

		return (filter_var($str, FILTER_VALIDATE_URL) !== FALSE);
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Email
	 * 信箱 有效的电子邮箱
	 * @param	string
	 * @return	bool
	 */
	public function valid_email($str)
	{
		if (function_exists('idn_to_ascii') && $atpos = strpos($str, '@'))
		{
			$str = substr($str, 0, ++$atpos).idn_to_ascii(substr($str, $atpos));
		}

		return (bool) filter_var($str, FILTER_VALIDATE_EMAIL);
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Emails
	 * 有效的电子邮件
	 * @param	string
	 * @return	bool
	 */
	public function valid_emails($str)
	{
		if (strpos($str, ',') === FALSE)
		{
			return $this->valid_email(trim($str));
		}

		foreach (explode(',', $str) as $email)
		{
			if (trim($email) !== '' && $this->valid_email(trim($email)) === FALSE)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Validate IP Address
	 * 验证的IP地址
	 * @param	string
	 * @param	string	'ipv4' or 'ipv6' to validate a specific IP format  “ipv4”或“ipv6”来验证一个特定IP的格式
	 * @return	bool
	 */
	public function valid_ip($ip, $which = '')
	{
		return $this->CI->input->valid_ip($ip, $which);
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha
	 * 阿尔法 检查字母字符
	 * @param	string
	 * @return	bool
	 */
	public function alpha($str)
	{
		return ctype_alpha($str);
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric
	 * 阿尔法数字 检查字母数字字符
	 * @param	string
	 * @return	bool
	 */
	public function alpha_numeric($str)
	{
		return ctype_alnum((string) $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric w/ spaces
	 * 文字数字式的 执行正则表达式匹配
	 * @param	string
	 * @return	bool
	 */
	public function alpha_numeric_spaces($str)
	{
		return (bool) preg_match('/^[A-Z0-9 ]+$/i', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric with underscores and dashes
	 * 字母数字下划线和破折号
	 * @param	string
	 * @return	bool
	 */
	public function alpha_dash($str)
	{
		return (bool) preg_match('/^[a-z0-9_-]+$/i', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Numeric
	 * 数字
	 * @param	string
	 * @return	bool
	 */
	public function numeric($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $str);

	}

	// --------------------------------------------------------------------

	/**
	 * Integer
	 * 整数
	 * @param	string
	 * @return	bool
	 */
	public function integer($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Decimal number
	 * 十进制数
	 * @param	string
	 * @return	bool
	 */
	public function decimal($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Greater than
	 * 大于
	 * @param	string
	 * @param	int
	 * @return	bool
	 */
	public function greater_than($str, $min)
	{
		return is_numeric($str) ? ($str > $min) : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Equal to or Greater than
	 * 等于或大于
	 * @param	string
	 * @param	int
	 * @return	bool
	 */
	public function greater_than_equal_to($str, $min)
	{
		return is_numeric($str) ? ($str >= $min) : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Less than
	 * 少于
	 * @param	string
	 * @param	int
	 * @return	bool
	 */
	public function less_than($str, $max)
	{
		return is_numeric($str) ? ($str < $max) : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Equal to or Less than
	 * 等于或小于
	 * @param	string
	 * @param	int
	 * @return	bool
	 */
	public function less_than_equal_to($str, $max)
	{
		return is_numeric($str) ? ($str <= $max) : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Value should be within an array of values
	 * 值应该是在一个数组的值
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	public function in_list($value, $list)
	{
		return in_array($value, explode(',', $list), TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * Is a Natural number  (0,1,2,3, etc.)
	 * 是一个自然数
	 * @param	string
	 * @return	bool
	 */
	public function is_natural($str)
	{
		return ctype_digit((string) $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Is a Natural number, but not a zero  (1,2,3, etc.)
	 * 是一个自然数,但不是一个零
	 * @param	string
	 * @return	bool
	 */
	public function is_natural_no_zero($str)
	{
		return ($str != 0 && ctype_digit((string) $str));
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Base64
	 * 有效的Base64
	 * Tests a string for characters outside of the Base64 alphabet
	 * as defined by RFC 2045 http://www.faqs.org/rfcs/rfc2045
	 * 测试一个字符串字符以外的Base64字母表 作为由RFC 2045定义的
	 * @param	string
	 * @return	bool
	 */
	public function valid_base64($str)
	{
		return (base64_encode(base64_decode($str)) === $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Prep data for form
	 * 准备的数据形式
	 * This function allows HTML to be safely shown in a form.
	 * Special characters are converted.
	 * 这个函数允许将HTML安全表格所示。 特殊字符转换。
	 * @param	string
	 * @return	string
	 */
	public function prep_for_form($data = '')
	{
		if ($this->_safe_form_data === FALSE OR empty($data))
		{
			return $data;
		}

		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				$data[$key] = $this->prep_for_form($val);
			}

			return $data;
		}

		return str_replace(array("'", '"', '<', '>'), array('&#39;', '&quot;', '&lt;', '&gt;'), stripslashes($data));
	}

	// --------------------------------------------------------------------

	/**
	 * Prep URL
	 * 预科的URL
	 * @param	string
	 * @return	string
	 */
	public function prep_url($str = '')
	{
		if ($str === 'http://' OR $str === '')
		{
			return '';
		}

		if (strpos($str, 'http://') !== 0 && strpos($str, 'https://') !== 0)
		{
			return 'http://'.$str;
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Strip Image Tags
	 * 带图像标记
	 * @param	string
	 * @return	string
	 */
	public function strip_image_tags($str)
	{
		return $this->CI->security->strip_image_tags($str);
	}

	// --------------------------------------------------------------------

	/**
	 * Convert PHP tags to entities
	 * PHP标记转换为实体
	 * @param	string
	 * @return	string
	 */
	public function encode_php_tags($str)
	{
		return str_replace(array('<?', '?>'), array('&lt;?', '?&gt;'), $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Reset validation vars
	 * 重置验证var
	 * Prevents subsequent validation routines from being affected by the
	 * results of any previous validation routine due to the CI singleton.
	 * 防止后续验证例程被影响的结果,由于以往任何验证例程CI singleton。
	 * @return	CI_Form_validation
	 */
	public function reset_validation()
	{
		$this->_field_data = array();
		$this->_error_array = array();
		$this->_error_messages = array();
		$this->error_string = '';
		return $this;
	}

}
