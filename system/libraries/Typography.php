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
 * Typography Class
 * 印刷类 打印类
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/typography.html
 */
class CI_Typography {

	/**
	 * Block level elements that should not be wrapped inside <p> tags
	 * 块级元素,不应包装在< p >标签
	 * @var string
	 */
	public $block_elements = 'address|blockquote|div|dl|fieldset|form|h\d|hr|noscript|object|ol|p|pre|script|table|ul';

	/**
	 * Elements that should not have <p> and <br /> tags within them.
	 * 元素不应该< p >和< br / >标记。
	 * @var string
	 */
	public $skip_elements	= 'p|pre|ol|ul|dl|object|table|h\d';

	/**
	 * Tags we want the parser to completely ignore when splitting the string.
	 * 标签我们希望解析器完全忽视当分割字符串。
	 * @var string
	 */
	public $inline_elements = 'a|abbr|acronym|b|bdo|big|br|button|cite|code|del|dfn|em|i|img|ins|input|label|map|kbd|q|samp|select|small|span|strong|sub|sup|textarea|tt|var';

	/**
	 * array of block level elements that require inner content to be within another block level element
	 * 块级元素的数组需要在另一个块级元素内的内容
	 * @var array
	 */
	public $inner_block_required = array('blockquote');

	/**
	 * the last block element parsed
	 * 最后一块元素解析
	 * @var string
	 */
	public $last_block_element = '';

	/**
	 * whether or not to protect quotes within { curly braces }
	 * 是否保护引用在花括号{ }
	 * @var bool 
	 */
	public $protect_braced_quotes = FALSE;

	/**
	 * Auto Typography 自动排版
	 *
	 * This function converts text, making it typographically correct: 这个函数转换文本,使其印刷地正确的:
	 *	- Converts double spaces into paragraphs. 将双空间转换成段落。
	 *	- Converts single line breaks into <br /> tags 将单一的换行符转换成< br / >标记
	 *	- Converts single and double quotes into correctly facing curly quote entities. 将单和双引号转换成正确的面对大引用实体。
	 *	- Converts three dots into ellipsis. 将三个点转换成省略
	 *	- Converts double dashes into em-dashes. 将双破折号转换成em-dashes。
	 *  - Converts two spaces into entities 将两个空间转换成实体
	 *
	 * @param	string
	 * @param	bool	whether to reduce more then two consecutive newlines to two 是否要减少两个超过连续两个换行
	 * @return	string
	 */
	public function auto_typography($str, $reduce_linebreaks = FALSE)
	{
		if ($str === '')
		{
			return '';
		}

		// Standardize Newlines to make matching easier 规范简化匹配换行
		if (strpos($str, "\r") !== FALSE)
		{
			$str = str_replace(array("\r\n", "\r"), "\n", $str);
		}

		// Reduce line breaks.  If there are more than two consecutive linebreaks 减少换行符。如果有两个以上的连续美化下
		// we'll compress them down to a maximum of two since there's no benefit to more. 我们将压缩下来最多两个因为没有受益更多。
		if ($reduce_linebreaks === TRUE)
		{
			$str = preg_replace("/\n\n+/", "\n\n", $str);
		}

		// HTML comment tags don't conform to patterns of normal tags, so pull them out separately, only if needed
		// HTML注释标签不符合正常模式标记,所以单独拉出来,如果需要的话
		$html_comments = array();
		if (strpos($str, '<!--') !== FALSE && preg_match_all('#(<!\-\-.*?\-\->)#s', $str, $matches))
		{
			for ($i = 0, $total = count($matches[0]); $i < $total; $i++)
			{
				$html_comments[] = $matches[0][$i];
				$str = str_replace($matches[0][$i], '{@HC'.$i.'}', $str);
			}
		}

		// match and yank <pre> tags if they exist.  It's cheaper to do this separately since most content will
		// not contain <pre> tags, and it keeps the PCRE patterns below simpler and faster
		// 匹配和猛拉< pre >标记如果他们存在。便宜单独做这个,因为大多数内容不包含< pre >标记,它使PCRE模式下面简单快捷
		if (strpos($str, '<pre') !== FALSE)
		{
			$str = preg_replace_callback('#<pre.*?>.*?</pre>#si', array($this, '_protect_characters'), $str);
		}

		// Convert quotes within tags to temporary markers. 引号内的标签转换为临时标记。
		$str = preg_replace_callback('#<.+?>#si', array($this, '_protect_characters'), $str);

		// Do the same with braces if necessary 用括号做同样的如果有必要吗
		if ($this->protect_braced_quotes === TRUE)
		{
			$str = preg_replace_callback('#\{.+?\}#si', array($this, '_protect_characters'), $str);
		}

		// Convert "ignore" tags to temporary marker.  The parser splits out the string at every tag
		// it encounters.  Certain inline tags, like image tags, links, span tags, etc. will be
		// adversely affected if they are split out so we'll convert the opening bracket < temporarily to: {@TAG}
		// “忽略”标记转换为临时标记。解析器将在每个标记它遇到字符串。某些行内标签,比如图像标记、链接、span标签,等等都是负面影响,如果他们分手了所以我们将开括号<暂时:{ @TAG }
		$str = preg_replace('#<(/*)('.$this->inline_elements.')([ >])#i', '{@TAG}\\1\\2\\3', $str);

		/* Split the string at every tag. This expression creates an array with this prototype:
		 * 在每个标签分割的字符串。这个表达式创建一个数组的原型:
		 *	[array]
		 *	{
		 *		[0] = <opening tag> <开始标记>
		 *		[1] = Content... 内容
		 *		[2] = <closing tag> <关闭标签>
		 *		Etc...
		 *	}
		 */
		$chunks = preg_split('/(<(?:[^<>]+(?:"[^"]*"|\'[^\']*\')?)+>)/', $str, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);

		// Build our finalized string.  We cycle through the array, skipping tags, and processing the contained text
		// 构建完成字符串。我们循环数组,跳过标签,和处理所包含的文本
		$str = '';
		$process = TRUE;

		for ($i = 0, $c = count($chunks) - 1; $i <= $c; $i++)
		{
			// Are we dealing with a tag? If so, we'll skip the processing for this cycle. 我们处理一个标签吗?如果是这样,我们将跳过这个循环的处理。
			// Well also set the "process" flag which allows us to skip <pre> tags and a few other things. 也设置了“过程”标识让我们跳过< pre >标记和其他一些东西。
			if (preg_match('#<(/*)('.$this->block_elements.').*?>#', $chunks[$i], $match))
			{
				if (preg_match('#'.$this->skip_elements.'#', $match[2]))
				{
					$process = ($match[1] === '/');
				}

				if ($match[1] === '')
				{
					$this->last_block_element = $match[2];
				}

				$str .= $chunks[$i];
				continue;
			}

			if ($process === FALSE)
			{
				$str .= $chunks[$i];
				continue;
			}

			//  Force a newline to make sure end tags get processed by _format_newlines() 强制换行符确保结束标记被_format_newlines处理()
			if ($i === $c)
			{
				$chunks[$i] .= "\n";
			}

			//  Convert Newlines into <p> and <br /> tags 换行转换成< p >和< br / >标记
			$str .= $this->_format_newlines($chunks[$i]);
		}

		// No opening block level tag? Add it if needed. 没有打开块级标签?如果需要添加它。
		if ( ! preg_match('/^\s*<(?:'.$this->block_elements.')/i', $str))
		{
			$str = preg_replace('/^(.*?)<('.$this->block_elements.')/i', '<p>$1</p><$2', $str);
		}

		// Convert quotes, elipsis, em-dashes, non-breaking spaces, and ampersands 转换引用,elipsis,em-dashes,不换行空格,&符号
		$str = $this->format_characters($str);

		// restore HTML comments 恢复HTML注释
		for ($i = 0, $total = count($html_comments); $i < $total; $i++)
		{
			// remove surrounding paragraph tags, but only if there's an opening paragraph tag 清除周围的段落标记,但前提是有一个段落标记
			// otherwise HTML comments at the ends of paragraphs will have the closing tag removed 否则HTML注释的段落有关闭标签删除
			// if '<p>{@HC1}' then replace <p>{@HC1}</p> with the comment, else replace only {@HC1} with the comment
			// 如果“< p > { @HC1 }”替换< p > { @HC1 } < / p >的评论,只有{ @HC1 }替换为其他评论
			$str = preg_replace('#(?(?=<p>\{@HC'.$i.'\})<p>\{@HC'.$i.'\}(\s*</p>)|\{@HC'.$i.'\})#s', $html_comments[$i], $str);
		}

		// Final clean up 最后的清理
		$table = array(

						// If the user submitted their own paragraph tags within the text
						// we will retain them instead of using our tags.
						// 如果用户提交自己的段落标签内的文本,我们将保留他们,而不是用我们的标签。
						'/(<p[^>*?]>)<p>/'	=> '$1', // <?php BBEdit syntax coloring bug fix BBEdit语法着色bug修复

						// Reduce multiple instances of opening/closing paragraph tags to a single one 减少的多个实例打开/关闭一个段落标记
						'#(</p>)+#'			=> '</p>',
						'/(<p>\W*<p>)+/'	=> '<p>',

						// Clean up stray paragraph tags that appear before block level elements  清理流浪段落标记出现在块级元素
						'#<p></p><('.$this->block_elements.')#'	=> '<$1',

						// Clean up stray non-breaking spaces preceeding block elements 清理流浪不换行空格前夕块元素
						'#(&nbsp;\s*)+<('.$this->block_elements.')#'	=> '  <$2',

						// Replace the temporary markers we added earlier 取代临时标记我们添加了
						'/\{@TAG\}/'		=> '<',
						'/\{@DQ\}/'			=> '"',
						'/\{@SQ\}/'			=> "'",
						'/\{@DD\}/'			=> '--',
						'/\{@NBS\}/'		=> '  ',

						// An unintended consequence of the _format_newlines function is that
						// some of the newlines get truncated, resulting in <p> tags
						// starting immediately after <block> tags on the same line.
						// 带来了意想不到的后果_format_newlines函数是一些换行的截断,导致< p >标记后立即开始<块>标记在同一行。
						// This forces a newline after such occurrences, which looks much nicer.
						// 这就迫使换行后出现这样的情况,看起来好得多。
						"/><p>\n/"			=> ">\n<p>",

						// Similarly, there might be cases where a closing </block> will follow 同样,可能存在情况下关闭< /块>
						// a closing </p> tag, so we'll correct it by adding a newline in between 我们会改正它通过添加一个换行符
						'#</p></#'			=> "</p>\n</"
						);

		// Do we need to reduce empty lines? 我们需要减少空行吗?
		if ($reduce_linebreaks === TRUE)
		{
			$table['#<p>\n*</p>#'] = '';
		}
		else
		{
			// If we have empty paragraph tags we add a non-breaking space
			// otherwise most browsers won't treat them as true paragraphs
			// 如果我们添加一个空的段落标记插入空格,否则大多数浏览器不会把它们当作真正的段落
			$table['#<p></p>#'] = '<p>&nbsp;</p>';
		}

		return preg_replace(array_keys($table), $table, $str);

	}

	// --------------------------------------------------------------------

	/**
	 * Format Characters
	 * 格式特点 
	 * This function mainly converts double and single quotes
	 * to curly entities, but it also converts em-dashes,
	 * double spaces, and ampersands
	 * 这个函数主要转换双和单引号卷曲的实体,但它也将em-dashes,双空间,与符号
	 * @param	string
	 * @return	string
	 */
	public function format_characters($str)
	{
		static $table;

		if ( ! isset($table))
		{
			$table = array(
							// nested smart quotes, opening and closing  嵌套印刷体引号、打开和关闭
							// note that rules for grammar (English) allow only for two levels deep 注意语法规则(英语)只允许两层
							// and that single quotes are _supposed_ to always be on the outside 而单引号_supposed_总是在外面
							// but we'll accommodate both  但我们会适应
							// Note that in all cases, whitespace is the primary determining factor 请注意,在所有情况下,空白是主要的决定因素
							// on which direction to curl, with non-word characters like punctuation 在哪个方向卷曲,像标点符号非单词字符
							// being a secondary factor only after whitespace is addressed. 作为一个次要的因素只有在空白处理。
							'/\'"(\s|$)/'					=> '&#8217;&#8221;$1',
							'/(^|\s|<p>)\'"/'				=> '$1&#8216;&#8220;',
							'/\'"(\W)/'						=> '&#8217;&#8221;$1',
							'/(\W)\'"/'						=> '$1&#8216;&#8220;',
							'/"\'(\s|$)/'					=> '&#8221;&#8217;$1',
							'/(^|\s|<p>)"\'/'				=> '$1&#8220;&#8216;',
							'/"\'(\W)/'						=> '&#8221;&#8217;$1',
							'/(\W)"\'/'						=> '$1&#8220;&#8216;',

							// single quote smart quotes  单引号智能引号
							'/\'(\s|$)/'					=> '&#8217;$1',
							'/(^|\s|<p>)\'/'				=> '$1&#8216;',
							'/\'(\W)/'						=> '&#8217;$1',
							'/(\W)\'/'						=> '$1&#8216;',

							// double quote smart quotes 双引号智能引号
							'/"(\s|$)/'						=> '&#8221;$1',
							'/(^|\s|<p>)"/'					=> '$1&#8220;',
							'/"(\W)/'						=> '&#8221;$1',
							'/(\W)"/'						=> '$1&#8220;',

							// apostrophes  省略符号
							"/(\w)'(\w)/"					=> '$1&#8217;$2',

							// Em dash and ellipses dots  长破折号和省略号点
							'/\s?\-\-\s?/'					=> '&#8212;',
							'/(\w)\.{3}/'					=> '$1&#8230;',

							// double space after sentences 双空间后的句子
							'/(\W)  /'						=> '$1&nbsp; ',

							// ampersands, if not a character entity 与符号,如果不是一个字符实体
							'/&(?!#?[a-zA-Z0-9]{2,};)/'		=> '&amp;'
						);
		}

		return preg_replace(array_keys($table), $table, $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Format Newlines
	 * 格式换行
	 * Converts newline characters into either <p> tags or <br />
	 * 换行字符转换成< br / >或< p >标记
	 * @param	string
	 * @return	string
	 */
	protected function _format_newlines($str)
	{
		if ($str === '' OR (strpos($str, "\n") === FALSE && ! in_array($this->last_block_element, $this->inner_block_required)))
		{
			return $str;
		}

		// Convert two consecutive newlines to paragraphs 把两个连续的段落换行
		$str = str_replace("\n\n", "</p>\n\n<p>", $str);

		// Convert single spaces to <br /> tags 单一空间转换为< br / >标记
		$str = preg_replace("/([^\n])(\n)([^\n])/", '\\1<br />\\2\\3', $str);

		// Wrap the whole enchilada in enclosing paragraphs 包装整个用以封闭段落
		if ($str !== "\n")
		{
			// We trim off the right-side new line so that the closing </p> tag
			// will be positioned immediately following the string, matching
			// the behavior of the opening <p> tag
			// 我们修剪右新行,关闭< / p >标记将立即定位字符串后,匹配的行为打开< p >标记
			$str =  '<p>'.rtrim($str).'</p>';
		}

		// Remove empty paragraphs if they are on the first line, as this
		// is a potential unintended consequence of the previous code
		// 删除空段第一行,因为这是一个潜在的意想不到的后果之前的代码
		return preg_replace('/<p><\/p>(.*)/', '\\1', $str, 1);
	}

	// ------------------------------------------------------------------------

	/**
	 * Protect Characters
	 * 保护的角色
	 * Protects special characters from being formatted later 保护特殊字符被格式化后
	 * We don't want quotes converted within tags so we'll temporarily convert them to {@DQ} and {@SQ}
	 * and we don't want double dashes converted to emdash entities, so they are marked with {@DD}
	 * likewise double spaces are converted to {@NBS} to prevent entity conversion
	 * 我们不希望引用转换在标签我们会暂时将它们转换为{ @DQ }和{ @SQ },我们不希望双破折号转化为emdash实体,所以它们是标有{ @DD }同样双重空间转换为{ @NBS }防止实体转换
	 * @param	array
	 * @return	string
	 */
	protected function _protect_characters($match)
	{
		return str_replace(array("'",'"','--','  '), array('{@SQ}', '{@DQ}', '{@DD}', '{@NBS}'), $match[0]);
	}

	// --------------------------------------------------------------------

	/**
	 * Convert newlines to HTML line breaks except within PRE tags
	 * 换行转换为HTML换行除了在之前的标签
	 * @param	string
	 * @return	string
	 */
	public function nl2br_except_pre($str)
	{
		$newstr = '';
		for ($ex = explode('pre>', $str), $ct = count($ex), $i = 0; $i < $ct; $i++)
		{
			$newstr .= (($i % 2) === 0) ? nl2br($ex[$i]) : $ex[$i];
			if ($ct - 1 !== $i)
			{
				$newstr .= 'pre>';
			}
		}

		return $newstr;
	}

}
