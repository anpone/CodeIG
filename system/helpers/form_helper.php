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
 * CodeIgniter Form Helpers
 * CodeIgniter形式助手
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/form_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('form_open'))
{
	/**
	 * Form Declaration
	 * FORM表单声明
	 * Creates the opening portion of the form.
	 * 创建开放形式的一部分。
	 * @param	string	the URI segments of the form destination  URI的形式的目的地
	 * @param	array	a key/value pair of attributes key=>value 两个属性
	 * @param	array	a key/value pair hidden data   key=>value隐藏数据
	 * @return	string
	 */
	function form_open($action = '', $attributes = array(), $hidden = array())
	{
		$CI =& get_instance();

		// If no action is provided then set to the current url 如果没有提供动作然后设置为当前url
		if ( ! $action)
		{
			$action = $CI->config->site_url($CI->uri->uri_string());
		}
		// If an action is not a full URL then turn it into one 如果一个动作不是一个完整的URL然后把它变成一个
		elseif (strpos($action, '://') === FALSE)
		{
			$action = $CI->config->site_url($action);
		}

		$attributes = _attributes_to_string($attributes);

		if (stripos($attributes, 'method=') === FALSE)
		{
			$attributes .= ' method="post"';
		}

		if (stripos($attributes, 'accept-charset=') === FALSE)
		{
			$attributes .= ' accept-charset="'.strtolower(config_item('charset')).'"';
		}

		$form = '<form action="'.$action.'"'.$attributes.">\n";

		// Add CSRF field if enabled, but leave it out for GET requests and requests to external websites
		// 如果启用添加CSRF字段,但把它的GET请求,请求外部网站
		if ($CI->config->item('csrf_protection') === TRUE && strpos($action, $CI->config->base_url()) !== FALSE && ! stripos($form, 'method="get"'))
		{
			$hidden[$CI->security->get_csrf_token_name()] = $CI->security->get_csrf_hash();
		}

		if (is_array($hidden))
		{
			foreach ($hidden as $name => $value)
			{
				$form .= '<input type="hidden" name="'.$name.'" value="'.html_escape($value).'" style="display:none;" />'."\n";
			}
		}

		return $form;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_open_multipart'))
{
	/**
	 * Form Declaration - Multipart type
	 * 声明形式——多部分类型
	 * Creates the opening portion of the form, but with "multipart/form-data".
	 * 创建开放部分的形式,但“多部分/格式”。
	 * @param	string	the URI segments of the form destination  URI的形式的目的地
	 * @param	array	a key/value pair of attributes   key=>value 两个属性
	 * @param	array	a key/value pair hidden data     key=>value 隐藏数据
	 * @return	string
	 */
	function form_open_multipart($action = '', $attributes = array(), $hidden = array())
	{
		if (is_string($attributes))
		{
			$attributes .= ' enctype="multipart/form-data"';
		}
		else
		{
			$attributes['enctype'] = 'multipart/form-data';
		}

		return form_open($action, $attributes, $hidden);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_hidden'))
{
	/**
	 * Hidden Input Field
	 * 隐藏的输入字段
	 * Generates hidden fields. You can pass a simple key/value string or
	 * an associative array with multiple values.
	 * 生成隐藏字段。您可以通过一个简单的键/值字符串或关联数组与多个值。
	 * @param	mixed	$name		Field name
	 * @param	string	$value		Field value
	 * @param	bool	$recursing
	 * @return	string
	 */
	function form_hidden($name, $value = '', $recursing = FALSE)
	{
		static $form;

		if ($recursing === FALSE)
		{
			$form = "\n";
		}

		if (is_array($name))
		{
			foreach ($name as $key => $val)
			{
				form_hidden($key, $val, TRUE);
			}

			return $form;
		}

		if ( ! is_array($value))
		{
			$form .= '<input type="hidden" name="'.$name.'" value="'.html_escape($value)."\" />\n";
		}
		else
		{
			foreach ($value as $k => $v)
			{
				$k = is_int($k) ? '' : $k;
				form_hidden($name.'['.$k.']', $v, TRUE);
			}
		}

		return $form;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_input'))
{
	/**
	 * Text Input Field
	 * 文本输入框 
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_input($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'type' => 'text',
			'name' => is_array($data) ? '' : $data,
			'value' => $value
		);

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_password'))
{
	/**
	 * Password Field
	 * 密码字段 
	 * Identical to the input function but adds the "password" type
	 * 相同的输入函数,但补充说“密码”类型
	 * @param	mixed 
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_password($data = '', $value = '', $extra = '')
	{
		is_array($data) OR $data = array('name' => $data);
		$data['type'] = 'password';
		return form_input($data, $value, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_upload'))
{
	/**
	 * Upload Field
	 * 上传字段
	 * Identical to the input function but adds the "file" type
	 * 相同的输入函数,但补充说“文件”类型
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_upload($data = '', $value = '', $extra = '')
	{
		$defaults = array('type' => 'file', 'name' => '');
		is_array($data) OR $data = array('name' => $data);
		$data['type'] = 'file';

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_textarea'))
{
	/**
	 * Textarea field
	 * 多行文本字段
	 * @param	mixed	$data
	 * @param	string	$value
	 * @param	mixed	$extra
	 * @return	string
	 */
	function form_textarea($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'name' => is_array($data) ? '' : $data,
			'cols' => '40',
			'rows' => '10'
		);

		if ( ! is_array($data) OR ! isset($data['value']))
		{
			$val = $value;
		}
		else
		{
			$val = $data['value'];
			unset($data['value']); // textareas don't use the value attribute 文本区域不要使用value属性
		}

		return '<textarea '._parse_form_attributes($data, $defaults)._attributes_to_string($extra).'>'
			.html_escape($val)
			."</textarea>\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_multiselect'))
{
	/**
	 * Multi-select menu
	 * 同时选中菜单
	 * @param	string
	 * @param	array
	 * @param	mixed
	 * @param	mixed
	 * @return	string
	 */
	function form_multiselect($name = '', $options = array(), $selected = array(), $extra = '')
	{
		$extra = _attributes_to_string($extra);
		if (stripos($extra, 'multiple') === FALSE)
		{
			$extra .= ' multiple="multiple"';
		}

		return form_dropdown($name, $options, $selected, $extra);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('form_dropdown'))
{
	/**
	 * Drop-down Menu
	 * 下拉式菜单
	 * @param	mixed	$data
	 * @param	mixed	$options
	 * @param	mixed	$selected
	 * @param	mixed	$extra
	 * @return	string
	 */
	function form_dropdown($data = '', $options = array(), $selected = array(), $extra = '')
	{
		$defaults = array();

		if (is_array($data))
		{
			if (isset($data['selected']))
			{
				$selected = $data['selected'];
				unset($data['selected']); // select tags don't have a selected attribute 选择标签没有选择属性
			}

			if (isset($data['options']))
			{
				$options = $data['options'];
				unset($data['options']); // select tags don't use an options attribute 选择标签不使用一个选择属性
			}
		}
		else
		{
			$defaults = array('name' => $data);
		}

		is_array($selected) OR $selected = array($selected);
		is_array($options) OR $options = array($options);

		// If no selected state was submitted we will attempt to set it automatically 如果没有选中的状态我们将尝试设置它自动提交
		if (empty($selected))
		{
			if (is_array($data))
			{
				if (isset($data['name'], $_POST[$data['name']]))
				{
					$selected = array($_POST[$data['name']]);
				}
			}
			elseif (isset($_POST[$data]))
			{
				$selected = array($_POST[$data]);
			}
		}

		$extra = _attributes_to_string($extra);

		$multiple = (count($selected) > 1 && stripos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

		$form = '<select '.rtrim(_parse_form_attributes($data, $defaults)).$extra.$multiple.">\n";

		foreach ($options as $key => $val)
		{
			$key = (string) $key;

			if (is_array($val))
			{
				if (empty($val))
				{
					continue;
				}

				$form .= '<optgroup label="'.$key."\">\n";

				foreach ($val as $optgroup_key => $optgroup_val)
				{
					$sel = in_array($optgroup_key, $selected) ? ' selected="selected"' : '';
					$form .= '<option value="'.html_escape($optgroup_key).'"'.$sel.'>'
						.(string) $optgroup_val."</option>\n";
				}

				$form .= "</optgroup>\n";
			}
			else
			{
				$form .= '<option value="'.html_escape($key).'"'
					.(in_array($key, $selected) ? ' selected="selected"' : '').'>'
					.(string) $val."</option>\n";
			}
		}

		return $form."</select>\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_checkbox'))
{
	/**
	 * Checkbox Field
	 *
	 * @param	mixed
	 * @param	string
	 * @param	bool
	 * @param	mixed
	 * @return	string
	 */
	function form_checkbox($data = '', $value = '', $checked = FALSE, $extra = '')
	{
		$defaults = array('type' => 'checkbox', 'name' => ( ! is_array($data) ? $data : ''), 'value' => $value);

		if (is_array($data) && array_key_exists('checked', $data))
		{
			$checked = $data['checked'];

			if ($checked == FALSE)
			{
				unset($data['checked']);
			}
			else
			{
				$data['checked'] = 'checked';
			}
		}

		if ($checked == TRUE)
		{
			$defaults['checked'] = 'checked';
		}
		else
		{
			unset($defaults['checked']);
		}

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_radio'))
{
	/**
	 * Radio Button
	 *
	 * @param	mixed
	 * @param	string
	 * @param	bool
	 * @param	mixed
	 * @return	string
	 */
	function form_radio($data = '', $value = '', $checked = FALSE, $extra = '')
	{
		is_array($data) OR $data = array('name' => $data);
		$data['type'] = 'radio';

		return form_checkbox($data, $value, $checked, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_submit'))
{
	/**
	 * Submit Button
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_submit($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'type' => 'submit',
			'name' => is_array($data) ? '' : $data,
			'value' => $value
		);

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_reset'))
{
	/**
	 * Reset Button
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_reset($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'type' => 'reset',
			'name' => is_array($data) ? '' : $data,
			'value' => $value
		);

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_button'))
{
	/**
	 * Form Button
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_button($data = '', $content = '', $extra = '')
	{
		$defaults = array(
			'name' => is_array($data) ? '' : $data,
			'type' => 'button'
		);

		if (is_array($data) && isset($data['content']))
		{
			$content = $data['content'];
			unset($data['content']); // content is not an attribute  内容不是一个属性
		}

		return '<button '._parse_form_attributes($data, $defaults)._attributes_to_string($extra).'>'
			.$content
			."</button>\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_label'))
{
	/**
	 * Form Label Tag
	 *
	 * @param	string	The text to appear onscreen  出现在屏幕上的文本
	 * @param	string	The id the label applies to  id标签适用于
	 * @param	string	Additional attributes  附加属性 
	 * @return	string
	 */
	function form_label($label_text = '', $id = '', $attributes = array())
	{

		$label = '<label';

		if ($id !== '')
		{
			$label .= ' for="'.$id.'"';
		}

		if (is_array($attributes) && count($attributes) > 0)
		{
			foreach ($attributes as $key => $val)
			{
				$label .= ' '.$key.'="'.$val.'"';
			}
		}

		return $label.'>'.$label_text.'</label>';
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_fieldset'))
{
	/**
	 * Fieldset Tag
	 * 自定义字段标签
	 * Used to produce <fieldset><legend>text</legend>.  To close fieldset
	 * use form_fieldset_close()
	 * 用于生产<自定义字段> < /传说> <传奇>文本。关闭自定义字段使用form_fieldset_close()
	 * @param	string	The legend text  传说的文本
	 * @param	array	Additional attributes 附加属性 
	 * @return	string
	 */
	function form_fieldset($legend_text = '', $attributes = array())
	{
		$fieldset = '<fieldset'._attributes_to_string($attributes).">\n";
		if ($legend_text !== '')
		{
			return $fieldset.'<legend>'.$legend_text."</legend>\n";
		}

		return $fieldset;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_fieldset_close'))
{
	/**
	 * Fieldset Close Tag
	 * 自定义字段结束标记
	 * @param	string
	 * @return	string
	 */
	function form_fieldset_close($extra = '')
	{
		return '</fieldset>'.$extra;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_close'))
{
	/**
	 * Form Close Tag
	 * form表单结束标记
	 * @param	string
	 * @return	string
	 */
	function form_close($extra = '')
	{
		return '</form>'.$extra;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_prep'))
{
	/**
	 * Form Prep
	 * Form预备
	 * Formats text so that it can be safely placed in a form field in the event it has HTML tags.
	 * 格式文本,以便它可以安全地放置在一个表单字段的HTML标记。
	 * @deprecated	3.0.0	An alias for html_escape()  别名为html_escape()
	 * @param	string|string[]	$str		Value to escape 价值逃脱
	 * @return	string|string[]	Escaped values  逃出来的值
	 */
	function form_prep($str)
	{
		return html_escape($str, TRUE);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('set_value'))
{
	/**
	 * Form Value
	 * Form表单值
	 * Grabs a value from the POST array for the specified field so you can
	 * re-populate an input field or textarea. If Form Validation
	 * is active it retrieves the info from the validation class
	 * 抓住一个POST数组的指定字段值,这样你就可以重新填充一个输入字段或文本区域。如果表单验证主动获取信息从验证类
	 * @param	string	$field		Field name  字段名称
	 * @param	string	$default	Default value  默认值
	 * @param	bool	$html_escape	Whether to escape HTML special characters or not  是否逃避HTML特殊字符
	 * @return	string
	 */
	function set_value($field, $default = '', $html_escape = TRUE)
	{
		$CI =& get_instance();

		$value = (isset($CI->form_validation) && is_object($CI->form_validation) && $CI->form_validation->has_rule($field))
			? $CI->form_validation->set_value($field, $default)
			: $CI->input->post($field, FALSE);

		isset($value) OR $value = $default;
		return ($html_escape) ? html_escape($value) : $value;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('set_select'))
{
	/**
	 * Set Select
	 * 机组选型
	 * Let's you set the selected value of a <select> menu via data in the POST array.
	 * If Form Validation is active it retrieves the info from the validation class
	 * 我们设置一个<选择>菜单的选择价值通过POST数组中的数据。 如果表单验证主动获取信息从验证类
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function set_select($field, $value = '', $default = FALSE)
	{
		$CI =& get_instance();

		if (isset($CI->form_validation) && is_object($CI->form_validation) && $CI->form_validation->has_rule($field))
		{
			return $CI->form_validation->set_select($field, $value, $default);
		}
		elseif (($input = $CI->input->post($field, FALSE)) === NULL)
		{
			return ($default === TRUE) ? ' selected="selected"' : '';
		}

		$value = (string) $value;
		if (is_array($input))
		{
			// Note: in_array('', array(0)) returns TRUE, do not use it 注意:in_array(“阵列(0))返回TRUE,不使用它
			foreach ($input as &$v)
			{
				if ($value === $v)
				{
					return ' selected="selected"';
				}
			}

			return '';
		}

		return ($input === $value) ? ' selected="selected"' : '';
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('set_checkbox'))
{
	/**
	 * Set Checkbox
	 * 设置复选框
	 * Let's you set the selected value of a checkbox via the value in the POST array.
	 * If Form Validation is active it retrieves the info from the validation class
	 * 我们设置一个复选框的选择价值通过POST数组中的值。 如果表单验证主动获取信息从验证类
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function set_checkbox($field, $value = '', $default = FALSE)
	{
		$CI =& get_instance();

		if (isset($CI->form_validation) && is_object($CI->form_validation) && $CI->form_validation->has_rule($field))
		{
			return $CI->form_validation->set_checkbox($field, $value, $default);
		}
		elseif (($input = $CI->input->post($field, FALSE)) === NULL)
		{
			return ($default === TRUE) ? ' checked="checked"' : '';
		}

		$value = (string) $value;
		if (is_array($input))
		{
			// Note: in_array('', array(0)) returns TRUE, do not use it 注意:in_array(“阵列(0))返回TRUE,不使用它
			foreach ($input as &$v)
			{
				if ($value === $v)
				{
					return ' checked="checked"';
				}
			}

			return '';
		}

		return ($input === $value) ? ' checked="checked"' : '';
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('set_radio'))
{
	/**
	 * Set Radio
	 * 设置单选
	 * Let's you set the selected value of a radio field via info in the POST array.
	 * If Form Validation is active it retrieves the info from the validation class
	 * 让你设置所选值的单选字段通过POST数组的信息,如果表单验证主动获取信息从验证类
	 * @param	string	$field
	 * @param	string	$value
	 * @param	bool	$default
	 * @return	string
	 */
	function set_radio($field, $value = '', $default = FALSE)
	{
		$CI =& get_instance();

		if (isset($CI->form_validation) && is_object($CI->form_validation) && $CI->form_validation->has_rule($field))
		{
			return $CI->form_validation->set_radio($field, $value, $default);
		}
		elseif (($input = $CI->input->post($field, FALSE)) === NULL)
		{
			return ($default === TRUE) ? ' checked="checked"' : '';
		}

		return ($input === (string) $value) ? ' checked="checked"' : '';
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_error'))
{
	/**
	 * Form Error
	 * 表单错误
	 * Returns the error for a specific form field. This is a helper for the
	 * form validation class.
	 * 为一个特定的表单字段返回错误。这是一个辅助的表单验证类。
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function form_error($field = '', $prefix = '', $suffix = '')
	{
		if (FALSE === ($OBJ =& _get_validation_object()))
		{
			return '';
		}

		return $OBJ->error($field, $prefix, $suffix);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('validation_errors'))
{
	/**
	 * Validation Error String
	 * 验证错误的字符串
	 * Returns all the errors associated with a form submission. This is a helper
	 * function for the form validation class.
	 * 返回所有与表单提交相关的错误。这是一个helper函数的表单验证类。
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function validation_errors($prefix = '', $suffix = '')
	{
		if (FALSE === ($OBJ =& _get_validation_object()))
		{
			return '';
		}

		return $OBJ->error_string($prefix, $suffix);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('_parse_form_attributes'))
{
	/**
	 * Parse the form attributes
	 * 解析表单属性
	 * Helper function used by some of the form helpers
	 * Helper函数所使用的一些形式的帮手
	 * @param	array	$attributes	List of attributes 属性列表
	 * @param	array	$default	Default values 默认值
	 * @return	string
	 */
	function _parse_form_attributes($attributes, $default)
	{
		if (is_array($attributes))
		{
			foreach ($default as $key => $val)
			{
				if (isset($attributes[$key]))
				{
					$default[$key] = $attributes[$key];
					unset($attributes[$key]);
				}
			}

			if (count($attributes) > 0)
			{
				$default = array_merge($default, $attributes);
			}
		}

		$att = '';

		foreach ($default as $key => $val)
		{
			if ($key === 'value')
			{
				$val = html_escape($val);
			}
			elseif ($key === 'name' && ! strlen($default['name']))
			{
				continue;
			}

			$att .= $key.'="'.$val.'" ';
		}

		return $att;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('_attributes_to_string'))
{
	/**
	 * Attributes To String
	 * 属性的字符串
	 * Helper function used by some of the form helpers
	 * Helper函数所使用的一些形式的帮手
	 * @param	mixed
	 * @return	string
	 */
	function _attributes_to_string($attributes)
	{
		if (empty($attributes))
		{
			return '';
		}

		if (is_object($attributes))
		{
			$attributes = (array) $attributes;
		}

		if (is_array($attributes))
		{
			$atts = '';

			foreach ($attributes as $key => $val)
			{
				$atts .= ' '.$key.'="'.$val.'"';
			}

			return $atts;
		}

		if (is_string($attributes))
		{
			return ' '.$attributes;
		}

		return FALSE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('_get_validation_object'))
{
	/**
	 * Validation Object
	 * 校验对象
	 * Determines what the form validation class was instantiated as, fetches
	 * the object and returns it.
	 * 决定了表单验证类被实例化,获取对象,并返回它。
	 * @return	mixed
	 */
	function &_get_validation_object()
	{
		$CI =& get_instance();

		// We set this as a variable since we're returning by reference. 我们设置这个变量因为我们返回引用
		$return = FALSE;

		if (FALSE !== ($object = $CI->load->is_loaded('Form_validation')))
		{
			if ( ! isset($CI->$object) OR ! is_object($CI->$object))
			{
				return $return;
			}

			return $CI->$object;
		}

		return $return;
	}
}
