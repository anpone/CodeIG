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
 * CodeIgniter Text Helpers
 * CodeIgniter文本助手
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/text_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('word_limiter'))
{
	/**
	 * Word Limiter
	 * 词限制器
	 * Limits a string to X number of words.
	 * 限制字符串X数量的单词。
	 * @param	string
	 * @param	int
	 * @param	string	the end character. Usually an ellipsis 结束字符。通常一个省略号
	 * @return	string
	 */
	function word_limiter($str, $limit = 100, $end_char = '&#8230;')
	{
		if (trim($str) === '')
		{
			return $str;
		}

		preg_match('/^\s*+(?:\S++\s*+){1,'.(int) $limit.'}/', $str, $matches);

		if (strlen($str) === strlen($matches[0]))
		{
			$end_char = '';
		}

		return rtrim($matches[0]).$end_char;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('character_limiter'))
{
	/**
	 * Character Limiter
	 * 字符限制器
	 * Limits the string based on the character count.  Preserves complete words
	 * so the character count may not be exactly as specified.
	 * 限制了基于字符计数的字符串。保存完整的单词所以指定的字符计数可能不是完全一样。
	 * @param	string
	 * @param	int
	 * @param	string	the end character. Usually an ellipsis 结束字符。通常一个省略号
	 * @return	string
	 */
	function character_limiter($str, $n = 500, $end_char = '&#8230;')
	{
		if (mb_strlen($str) < $n)
		{
			return $str;
		}

		// a bit complicated, but faster than preg_replace with \s+ 有点复杂,但速度比preg_replace \ s +
		$str = preg_replace('/ {2,}/', ' ', str_replace(array("\r", "\n", "\t", "\x0B", "\x0C"), ' ', $str));

		if (mb_strlen($str) <= $n)
		{
			return $str;
		}

		$out = '';
		foreach (explode(' ', trim($str)) as $val)
		{
			$out .= $val.' ';

			if (mb_strlen($out) >= $n)
			{
				$out = trim($out);
				return (mb_strlen($out) === mb_strlen($str)) ? $out : $out.$end_char;
			}
		}
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('ascii_to_entities'))
{
	/**
	 * High ASCII to Entities
	 * 高ASCII实体
	 * Converts high ASCII text and MS Word special characters to character entities
	 * 将高ASCII文本和微软的Word特殊字符转换为字符实体
	 * @param	string	$str
	 * @return	string
	 */
	function ascii_to_entities($str)
	{
		$out = '';
		for ($i = 0, $s = strlen($str) - 1, $count = 1, $temp = array(); $i <= $s; $i++)
		{
			$ordinal = ord($str[$i]);

			if ($ordinal < 128)
			{
				/*
					If the $temp array has a value but we have moved on, then it seems only
					fair that we output that entity and restart $temp before continuing. -Paul
					如果$临时数组有价值但我们继续前进,那么似乎　　公平,我们输出实体美元并重新启动临时在继续之前。保罗
				*/
				if (count($temp) === 1)
				{
					$out .= '&#'.array_shift($temp).';';
					$count = 1;
				}

				$out .= $str[$i];
			}
			else
			{
				if (count($temp) === 0)
				{
					$count = ($ordinal < 224) ? 2 : 3;
				}

				$temp[] = $ordinal;

				if (count($temp) === $count)
				{
					$number = ($count === 3)
						? (($temp[0] % 16) * 4096) + (($temp[1] % 64) * 64) + ($temp[2] % 64)
						: (($temp[0] % 32) * 64) + ($temp[1] % 64);

					$out .= '&#'.$number.';';
					$count = 1;
					$temp = array();
				}
				// If this is the last iteration, just output whatever we have 如果这是最后一次迭代,就输出我们所拥有的一切
				elseif ($i === $s)
				{
					$out .= '&#'.implode(';', $temp).';';
				}
			}
		}

		return $out;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('entities_to_ascii'))
{
	/**
	 * Entities to ASCII
	 * 实体ASCII
	 * Converts character entities back to ASCII
	 * 转换回ASCII字符实体
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function entities_to_ascii($str, $all = TRUE)
	{
		if (preg_match_all('/\&#(\d+)\;/', $str, $matches))
		{
			for ($i = 0, $s = count($matches[0]); $i < $s; $i++)
			{
				$digits = $matches[1][$i];
				$out = '';

				if ($digits < 128)
				{
					$out .= chr($digits);

				}
				elseif ($digits < 2048)
				{
					$out .= chr(192 + (($digits - ($digits % 64)) / 64)).chr(128 + ($digits % 64));
				}
				else
				{
					$out .= chr(224 + (($digits - ($digits % 4096)) / 4096))
						.chr(128 + ((($digits % 4096) - ($digits % 64)) / 64))
						.chr(128 + ($digits % 64));
				}

				$str = str_replace($matches[0][$i], $out, $str);
			}
		}

		if ($all)
		{
			return str_replace(
				array('&amp;', '&lt;', '&gt;', '&quot;', '&apos;', '&#45;'),
				array('&', '<', '>', '"', "'", '-'),
				$str
			);
		}

		return $str;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('word_censor'))
{
	/**
	 * Word Censoring Function
	 * 词审查功能
	 * Supply a string and an array of disallowed words and any
	 * matched words will be converted to #### or to the replacement
	 * word you've submitted.
	 * 提供一个字符串和数组不允许任何匹配的单词和词汇将转换为# # # #你提交或替换词。
	 * @param	string	the text string 文本字符串
	 * @param	string	the array of censored words 审查字的数组
	 * @param	string	the optional replacement value 可选的替换值
	 * @return	string
	 */
	function word_censor($str, $censored, $replacement = '')
	{
		if ( ! is_array($censored))
		{
			return $str;
		}

		$str = ' '.$str.' ';

		// \w, \b and a few others do not match on a unicode character
		// set for performance reasons. As a result words like über
		// will not match on a word boundary. Instead, we'll assume that
		// a bad word will be bookeneded by any of these characters.
		// \w, \b 其他几个人在unicode字符集不匹配性能的原因。由于这样的词超级不会匹配单词边界。相反,我们假设一个坏词将bookeneded这些字符。
		$delim = '[-_\'\"`(){}<>\[\]|!?@#%&,.:;^~*+=\/ 0-9\n\r\t]';

		foreach ($censored as $badword)
		{
			if ($replacement !== '')
			{
				$str = preg_replace("/({$delim})(".str_replace('\*', '\w*?', preg_quote($badword, '/')).")({$delim})/i", "\\1{$replacement}\\3", $str);
			}
			else
			{
				$str = preg_replace("/({$delim})(".str_replace('\*', '\w*?', preg_quote($badword, '/')).")({$delim})/ie", "'\\1'.str_repeat('#', strlen('\\2')).'\\3'", $str);
			}
		}

		return trim($str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('highlight_code'))
{
	/**
	 * Code Highlighter
	 * 代码高亮显示
	 * Colorizes code strings
	 * 彩色化代码字符串
	 * @param	string	the text string 文本字符串
	 * @return	string
	 */
	function highlight_code($str)
	{
		/* The highlight string function encodes and highlights
		 * brackets so we need them to start raw.
		 * 字符串函数编码并突出亮点括号开始生所以我们需要他们。
		 * Also replace any existing PHP tags to temporary markers
		 * so they don't accidentally break the string out of PHP,
		 * and thus, thwart the highlighting.
		 * 也替换任何现有的PHP标签来临时标记,这样他们就不会打破PHP字符串,因此,阻挠高亮显示。
		 */
		$str = str_replace(
			array('&lt;', '&gt;', '<?', '?>', '<%', '%>', '\\', '</script>'),
			array('<', '>', 'phptagopen', 'phptagclose', 'asptagopen', 'asptagclose', 'backslashtmp', 'scriptclose'),
			$str
		);

		// The highlight_string function requires that the text be surrounded
		// by PHP tags, which we will remove later
		// 中将函数要求PHP包围文本标签,我们将稍后删除
		$str = highlight_string('<?php '.$str.' ?>', TRUE);

		// Remove our artificially added PHP, and the syntax highlighting that came with it
		// 删除我们人为地添加PHP和与它的语法高亮显示
		$str = preg_replace(
			array(
				'/<span style="color: #([A-Z0-9]+)">&lt;\?php(&nbsp;| )/i',
				'/(<span style="color: #[A-Z0-9]+">.*?)\?&gt;<\/span>\n<\/span>\n<\/code>/is',
				'/<span style="color: #[A-Z0-9]+"\><\/span>/i'
			),
			array(
				'<span style="color: #$1">',
				"$1</span>\n</span>\n</code>",
				''
			),
			$str
		);

		// Replace our markers back to PHP tags. 取代我们的标记回PHP标签。
		return str_replace(
			array('phptagopen', 'phptagclose', 'asptagopen', 'asptagclose', 'backslashtmp', 'scriptclose'),
			array('&lt;?', '?&gt;', '&lt;%', '%&gt;', '\\', '&lt;/script&gt;'),
			$str
		);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('highlight_phrase'))
{
	/**
	 * Phrase Highlighter
	 * 短语萤光笔
	 * Highlights a phrase within a text string
	 * 突出了一个短语在一个文本字符串
	 * @param	string	$str		the text string   文本字符串 
	 * @param	string	$phrase		the phrase you'd like to highlight  这句话你想突出
	 * @param	string	$tag_open	the openging tag to precede the phrase with  openging标签之前这句话
	 * @param	string	$tag_close	the closing tag to end the phrase with  关闭标签结束这个词
	 * @return	string
	 */
	function highlight_phrase($str, $phrase, $tag_open = '<mark>', $tag_close = '</mark>')
	{
		return ($str !== '' && $phrase !== '')
			? preg_replace('/('.preg_quote($phrase, '/').')/i'.(UTF8_ENABLED ? 'u' : ''), $tag_open.'\\1'.$tag_close, $str)
			: $str;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('convert_accented_characters'))
{
	/**
	 * Convert Accented Foreign Characters to ASCII
	 * 重音外国字符转换为ASCII
	 * @param	string	$str	Input string  输入串
	 * @return	string
	 */
	function convert_accented_characters($str)
	{
		static $array_from, $array_to;

		if ( ! is_array($array_from))
		{
			if (file_exists(APPPATH.'config/foreign_chars.php'))
			{
				include(APPPATH.'config/foreign_chars.php');
			}

			if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/foreign_chars.php'))
			{
				include(APPPATH.'config/'.ENVIRONMENT.'/foreign_chars.php');
			}

			if (empty($foreign_characters) OR ! is_array($foreign_characters))
			{
				$array_from = array();
				$array_to = array();

				return $str;
			}

			$array_from = array_keys($foreign_characters);
			$array_to = array_values($foreign_characters);
		}

		return preg_replace($array_from, $array_to, $str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('word_wrap'))
{
	/**
	 * Word Wrap
	 * 自动换行
	 * Wraps text at the specified character. Maintains the integrity of words.
	 * Anything placed between {unwrap}{/unwrap} will not be word wrapped, nor
	 * will URLs.
	 * 包装文本在指定的字符。保持文字的完整性。东西放在{打开} { /打开}之间不会包装,也不会url。
	 * @param	string	$str		the text string   文本字符串
	 * @param	int	$charlim = 76	the number of characters to wrap at  包装的字符数
	 * @return	string
	 */
	function word_wrap($str, $charlim = 76)
	{
		// Set the character limit   设置字符的限制
		is_numeric($charlim) OR $charlim = 76;

		// Reduce multiple spaces  减少多重空间
		$str = preg_replace('| +|', ' ', $str);

		// Standardize newlines  标准化换行
		if (strpos($str, "\r") !== FALSE)
		{
			$str = str_replace(array("\r\n", "\r"), "\n", $str);
		}

		// If the current word is surrounded by {unwrap} tags we'll
		// strip the entire chunk and replace it with a marker.
		// 如果当前词周围是{打开}标签我们可以将整个块和替换标记。
		$unwrap = array();
		if (preg_match_all('|\{unwrap\}(.+?)\{/unwrap\}|s', $str, $matches))
		{
			for ($i = 0, $c = count($matches[0]); $i < $c; $i++)
			{
				$unwrap[] = $matches[1][$i];
				$str = str_replace($matches[0][$i], '{{unwrapped'.$i.'}}', $str);
			}
		}

		// Use PHP's native function to do the initial wordwrap.  使用PHP的本机函数初始自动换行。
		// We set the cut flag to FALSE so that any individual words that are
		// too long get left alone. In the next step we'll deal with them.
		// 我们减少标志设置为FALSE,任何单词太长时间独处。在下一步我们会处理这些问题。
		$str = wordwrap($str, $charlim, "\n", FALSE);

		// Split the string into individual lines of text and cycle through them  字符串分割成单独的行文本和周期
		$output = '';
		foreach (explode("\n", $str) as $line)
		{
			// Is the line within the allowed character count?  是在允许的字符计数线?
			// If so we'll join it to the output and continue  如果我们将其加入到产出和继续
			if (mb_strlen($line) <= $charlim)
			{
				$output .= $line."\n";
				continue;
			}

			$temp = '';
			while (mb_strlen($line) > $charlim)
			{
				// If the over-length word is a URL we won't wrap it  如果超长词是一个URL我们不会结束
				if (preg_match('!\[url.+\]|://|www\.!', $line))
				{
					break;
				}

				// Trim the word down  修剪下来这个词
				$temp .= mb_substr($line, 0, $charlim - 1);
				$line = mb_substr($line, $charlim - 1);
			}

			// If $temp contains data it means we had to split up an over-length  如果临时美元包含数据意味着我们不得不分手一个后备长度
			// word into smaller chunks so we'll add it back to our current line  字成小块我们添加它回到我们的当前行
			if ($temp !== '')
			{
				$output .= $temp."\n".$line."\n";
			}
			else
			{
				$output .= $line."\n";
			}
		}
		
		// Put our markers back  把我们的标记
		if (count($unwrap) > 0)
		{
			foreach ($unwrap as $key => $val)
			{
				$output = str_replace('{{unwrapped'.$key.'}}', $val, $output);
			}
		}

		return $output;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('ellipsize'))
{
	/**
	 * Ellipsize String
	 * Ellipsize字符串
	 * This function will strip tags from a string, split it at its max_length and ellipsize
	 * 该函数将从一个字符串,带标签把它max_length和ellipsize
	 * @param	string	string to ellipsize  字符串ellipsize
	 * @param	int	max length of string  字符串长度
	 * @param	mixed	int (1|0) or float, .5, .2, etc for position to split  浮动。5,。2、分裂等位置
	 * @param	string	ellipsis ; Default '...'  省略,默认“……”
	 * @return	string	ellipsized string  ellipsized字符串
	 */
	function ellipsize($str, $max_length, $position = 1, $ellipsis = '&hellip;')
	{
		// Strip tags  带标签
		$str = trim(strip_tags($str));

		// Is the string long enough to ellipsize?  ellipsize字符串足够长的时间吗?
		if (mb_strlen($str) <= $max_length)
		{
			return $str;
		}

		$beg = mb_substr($str, 0, floor($max_length * $position));
		$position = ($position > 1) ? 1 : $position;

		if ($position === 1)
		{
			$end = mb_substr($str, 0, -($max_length - mb_strlen($beg)));
		}
		else
		{
			$end = mb_substr($str, -($max_length - mb_strlen($beg)));
		}

		return $beg.$ellipsis.$end;
	}
}
