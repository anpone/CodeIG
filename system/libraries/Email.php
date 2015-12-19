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
 * CodeIgniter Email Class
 * CodeIgniter邮件类
 * Permits email to be sent using Mail, Sendmail, or SMTP.
 * 允许使用邮件发送电子邮件,Sendmail或SMTP。
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/email.html
 */
class CI_Email {

	/**
	 * Used as the User-Agent and X-Mailer headers' value.
	 * 作为代理和X-Mailer头的值。
	 * @var	string
	 */
	public $useragent	= 'CodeIgniter';

	/**
	 * Path to the Sendmail binary.
	 * 路径Sendmail二进制。
	 * @var	string
	 */
	public $mailpath	= '/usr/sbin/sendmail';	// Sendmail path

	/**
	 * Which method to use for sending e-mails.
	 * 使用哪个方法来发送电子邮件。
	 * @var	string	'mail', 'sendmail' or 'smtp'
	 */
	public $protocol	= 'mail';		// mail/sendmail/smtp

	/**
	 * STMP Server host
	 * STMP服务器主机
	 * @var	string
	 */
	public $smtp_host	= '';

	/**
	 * SMTP Username
	 * 你的邮箱用户名 
	 * @var	string
	 */
	public $smtp_user	= '';

	/**
	 * SMTP Password
	 * 在这里输入邮箱的密码 
	 * @var	string
	 */
	public $smtp_pass	= '';

	/**
	 * SMTP Server port
	 * SMTP服务端口
	 * @var	int
	 */
	public $smtp_port	= 25;

	/**
	 * SMTP connection timeout in seconds
	 * SMTP连接超时秒
	 * @var	int
	 */
	public $smtp_timeout	= 5;

	/**
	 * SMTP persistent connection
	 * SMTP持久连接
	 * @var	bool
	 */
	public $smtp_keepalive	= FALSE;

	/**
	 * SMTP Encryption
	 * SMTP加密
	 * @var	string	empty, 'tls' or 'ssl'
	 */
	public $smtp_crypto	= '';

	/**
	 * Whether to apply word-wrapping to the message body.
	 * 是否自动换行适用于消息体。
	 * @var	bool
	 */
	public $wordwrap	= TRUE;

	/**
	 * Number of characters to wrap at.
	 * 包装数量的字符
	 * @see	CI_Email::$wordwrap
	 * @var	int
	 */
	public $wrapchars	= 76;

	/**
	 * Message format.
	 * 消息格式。
	 * @var	string	'text' or 'html'
	 */
	public $mailtype	= 'text';

	/**
	 * Character set (default: utf-8)
	 * 字符集(默认值:utf - 8)
	 * @var	string
	 */
	public $charset		= 'utf-8';

	/**
	 * Multipart message
	 * 多部分消息
	 * @var	string	'mixed' (in the body中) or 'related相关' (separate分离)
	 */
	public $multipart	= 'mixed';		// "mixed" (in the body) or "related" (separate)

	/**
	 * Alternative message (for HTML messages only)
	 * 选择消息(仅为HTML消息)
	 * @var	string
	 */
	public $alt_message	= '';

	/**
	 * Whether to validate e-mail addresses.
	 * 是否验证电子邮件地址。
	 * @var	bool
	 */
	public $validate	= FALSE;

	/**
	 * X-Priority header value.
	 * X-Priority头部优先级的值。
	 * @var	int	1-5
	 */
	public $priority	= 3;			// Default priority (1 - 5)

	/**
	 * Newline character sequence. 换行符序列。
	 * Use "\r\n" to comply with RFC 822. 符合RFC 822。
	 *
	 * @link	http://www.ietf.org/rfc/rfc822.txt
	 * @var	string	"\r\n" or "\n"
	 */
	public $newline		= "\n";			// Default newline默认换行符. "\r\n" or "\n" (Use "\r\n" to comply with RFC 822)

	/**
	 * CRLF character sequence
	 * CRLF字符序列
	 * RFC 2045 specifies that for 'quoted-printable' encoding,
	 * "\r\n" must be used. However, it appears that some servers
	 * (even on the receiving end) don't handle it properly and
	 * switching to "\n", while improper, is the only solution
	 * that seems to work for all environments.
	 * RFC 2045指定为“quoted-printable”编码,必须使用“\ r \ n”。然而,看来某些服务器(即使是在接收端)不妥善处理它,切换到“\ n”,当不当,似乎是唯一的解决方案,适用于所有环境。
	 * @link	http://www.ietf.org/rfc/rfc822.txt
	 * @var	string
	 */
	public $crlf		= "\n";

	/**
	 * Whether to use Delivery Status Notification.
	 * 是否使用交付状态通知。
	 * @var	bool
	 */
	public $dsn		= FALSE;

	/**
	 * Whether to send multipart alternatives.
	 * Yahoo! doesn't seem to like these. 雅虎似乎并不喜欢这些
	 * 是否发送多部分替代。
	 * @var	bool
	 */
	public $send_multipart	= TRUE;

	/**
	 * Whether to send messages to BCC recipients in batches.
	 * 是否发送消息成批BCC接受者。
	 * @var	bool
	 */
	public $bcc_batch_mode	= FALSE;

	/**
	 * BCC Batch max number size.
	 * BCC批最大数量大小。
	 * @see	CI_Email::$bcc_batch_mode
	 * @var	int
	 */
	public $bcc_batch_size	= 200;

	// --------------------------------------------------------------------

	/**
	 * Whether PHP is running in safe mode. Initialized by the class constructor.
	 * PHP是否在安全模式下运行。类构造函数的初始化。
	 * @var	bool
	 */
	protected $_safe_mode		= FALSE;

	/**
	 * Subject header
	 * 主题标题
	 * @var	string
	 */
	protected $_subject		= '';

	/**
	 * Message body
	 * 消息正文 信件内容 
	 * @var	string
	 */
	protected $_body		= '';

	/**
	 * Final message body to be sent.
	 * 最后要发送消息体。
	 * @var	string
	 */
	protected $_finalbody		= '';

	/**
	 * multipart/alternative boundary
	 * 多部分/替代边界
	 * @var	string
	 */
	protected $_alt_boundary	= '';

	/**
	 * Attachment boundary
	 * 附件边界
	 * @var	string
	 */
	protected $_atc_boundary	= '';

	/**
	 * Final headers to send
	 * 最终的发送头部信息
	 * @var	string
	 */
	protected $_header_str		= '';

	/**
	 * SMTP Connection socket placeholder
	 * SMTP连接套接字占位符
	 * @var	resource
	 */
	protected $_smtp_connect	= '';

	/**
	 * Mail encoding
	 * 邮政编码
	 * @var	string	'8bit' or '7bit'
	 */
	protected $_encoding		= '8bit';

	/**
	 * Whether to perform SMTP authentication
	 * 是否执行SMTP认证
	 * @var	bool
	 */
	protected $_smtp_auth		= FALSE;

	/**
	 * Whether to send a Reply-To header
	 * 是否要发送一个应答头
	 * @var	bool
	 */
	protected $_replyto_flag	= FALSE;

	/**
	 * Debug messages
	 * 调试消息
	 * @see	CI_Email::print_debugger()
	 * @var	string
	 */
	protected $_debug_msg		= array();

	/**
	 * Recipients
	 * 收件人
	 * @var	string[]
	 */
	protected $_recipients		= array();

	/**
	 * CC Recipients
	 * CC接受者
	 * @var	string[]
	 */
	protected $_cc_array		= array();

	/**
	 * BCC Recipients
	 * BCC接受者 暗送
	 * @var	string[]
	 */
	protected $_bcc_array		= array();

	/**
	 * Message headers
	 * 其中返回的消息头中
	 * @var	string[]
	 */
	protected $_headers		= array();

	/**
	 * Attachment data
	 * 附件数据
	 * @var	array
	 */
	protected $_attachments		= array();

	/**
	 * Valid $protocol values
	 * 有效的协议$protocol
	 * @see	CI_Email::$protocol 协议
	 * @var	string[]
	 */
	protected $_protocols		= array('mail', 'sendmail', 'smtp');

	/**
	 * Base charsets
	 * 基础数据集
	 * Character sets valid for 7-bit encoding, 字符集有效期为7位编码,
	 * excluding language suffix. 排除语言后缀。
	 *
	 * @var	string[]
	 */
	protected $_base_charsets	= array('us-ascii', 'iso-2022-');

	/**
	 * Bit depths
	 * 位深
	 * Valid mail encodings 有效的邮件编码
	 *
	 * @see	CI_Email::$_encoding
	 * @var	string[]
	 */
	protected $_bit_depths		= array('7bit', '8bit');

	/**
	 * $priority translations
	 * 优先级翻译
	 * Actual values to send with the X-Priority header
	 * 实际值发送X-Priority头
	 * @var	string[]
	 */
	protected $_priorities = array(
		1 => '1 (Highest)',
		2 => '2 (High)',
		3 => '3 (Normal)',
		4 => '4 (Low)',
		5 => '5 (Lowest)'
	);

	// --------------------------------------------------------------------

	/**
	 * Constructor - Sets Email Preferences
	 * 构造函数,设置电子邮件偏好
	 * The constructor can be passed an array of config values
	 * 构造函数可以通过配置值的数组
	 * @param	array	$config = array()
	 * @return	void
	 */
	public function __construct(array $config = array())
	{
		$this->charset = config_item('charset');

		if (count($config) > 0)
		{
			$this->initialize($config);
		}
		else
		{
			$this->_smtp_auth = ! ($this->smtp_user === '' && $this->smtp_pass === '');
		}

		$this->_safe_mode = ( ! is_php('5.4') && ini_get('safe_mode'));
		$this->charset = strtoupper($this->charset);

		log_message('info', 'Email Class Initialized电子邮件类初始化');
	}

	// --------------------------------------------------------------------

	/**
	 * Destructor - Releases Resources
	 * 析构函数释放的资源
	 * @return	void
	 */
	public function __destruct()
	{
		if (is_resource($this->_smtp_connect))
		{
			$this->_send_command('quit');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize preferences
	 * 初始化参数
	 * @param	array
	 * @return	CI_Email
	 */
	public function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$method = 'set_'.$key;

				if (method_exists($this, $method))
				{
					$this->$method($val);
				}
				else
				{
					$this->$key = $val;
				}
			}
		}
		$this->clear();

		$this->_smtp_auth = ! ($this->smtp_user === '' && $this->smtp_pass === '');

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize the Email Data
	 * 初始化邮件数据
	 * @param	bool
	 * @return	CI_Email
	 */
	public function clear($clear_attachments = FALSE)
	{
		$this->_subject		= '';
		$this->_body		= '';
		$this->_finalbody	= '';
		$this->_header_str	= '';
		$this->_replyto_flag	= FALSE;
		$this->_recipients	= array();
		$this->_cc_array	= array();
		$this->_bcc_array	= array();
		$this->_headers		= array();
		$this->_debug_msg	= array();

		$this->set_header('User-Agent', $this->useragent);
		$this->set_header('Date', $this->_set_date());

		if ($clear_attachments !== FALSE)
		{
			$this->_attachments = array();
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set FROM
	 * 设置FROM表单
	 * @param	string	$from
	 * @param	string	$name
	 * @param	string	$return_path = NULL	Return-Path 回复地址 退信地址 
	 * @return	CI_Email
	 */
	public function from($from, $name = '', $return_path = NULL)
	{
		if (preg_match('/\<(.*)\>/', $from, $match))
		{
			$from = $match[1];
		}

		if ($this->validate)
		{
			$this->validate_email($this->_str_to_array($from));
			if ($return_path)
			{
				$this->validate_email($this->_str_to_array($return_path));
			}
		}

		// prepare the display name 准备显示名称
		if ($name !== '')
		{
			// only use Q encoding if there are characters that would require it 只使用问如果有字符编码,需要它
			if ( ! preg_match('/[\200-\377]/', $name))
			{
				// add slashes for non-printing characters, slashes, and double quotes, and surround it in double quotes
				// 添加斜杠的打印字符、斜线和双引号,双引号包围
				$name = '"'.addcslashes($name, "\0..\37\177'\"\\").'"';
			}
			else
			{
				$name = $this->_prep_q_encoding($name);
			}
		}

		$this->set_header('From', $name.' <'.$from.'>');

		isset($return_path) OR $return_path = $from;
		$this->set_header('Return-Path', '<'.$return_path.'>');

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Reply-to
	 * 设置应答
	 * @param	string
	 * @param	string
	 * @return	CI_Email
	 */
	public function reply_to($replyto, $name = '')
	{
		if (preg_match('/\<(.*)\>/', $replyto, $match))
		{
			$replyto = $match[1];
		}

		if ($this->validate)
		{
			$this->validate_email($this->_str_to_array($replyto));
		}

		if ($name === '')
		{
			$name = $replyto;
		}

		if (strpos($name, '"') !== 0)
		{
			$name = '"'.$name.'"';
		}

		$this->set_header('Reply-To', $name.' <'.$replyto.'>');
		$this->_replyto_flag = TRUE;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Recipients
	 * 设置收件人
	 * @param	string
	 * @return	CI_Email
	 */
	public function to($to)
	{
		$to = $this->_str_to_array($to);
		$to = $this->clean_email($to);

		if ($this->validate)
		{
			$this->validate_email($to);
		}

		if ($this->_get_protocol() !== 'mail')
		{
			$this->set_header('To', implode(', ', $to));
		}

		$this->_recipients = $to;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set CC
	 * 设置CC抄送
	 * @param	string
	 * @return	CI_Email
	 */
	public function cc($cc)
	{
		$cc = $this->clean_email($this->_str_to_array($cc));

		if ($this->validate)
		{
			$this->validate_email($cc);
		}

		$this->set_header('Cc', implode(', ', $cc));

		if ($this->_get_protocol() === 'smtp')
		{
			$this->_cc_array = $cc;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set BCC
	 * 设置BCC暗送
	 * @param	string
	 * @param	string
	 * @return	CI_Email
	 */
	public function bcc($bcc, $limit = '')
	{
		if ($limit !== '' && is_numeric($limit))
		{
			$this->bcc_batch_mode = TRUE;
			$this->bcc_batch_size = $limit;
		}

		$bcc = $this->clean_email($this->_str_to_array($bcc));

		if ($this->validate)
		{
			$this->validate_email($bcc);
		}

		if ($this->_get_protocol() === 'smtp' OR ($this->bcc_batch_mode && count($bcc) > $this->bcc_batch_size))
		{
			$this->_bcc_array = $bcc;
		}
		else
		{
			$this->set_header('Bcc', implode(', ', $bcc));
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Email Subject
	 * 设置邮件主题
	 * @param	string
	 * @return	CI_Email
	 */
	public function subject($subject)
	{
		$subject = $this->_prep_q_encoding($subject);
		$this->set_header('Subject', $subject);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Body
	 * 设置Body
	 * @param	string
	 * @return	CI_Email
	 */
	public function message($body)
	{
		$this->_body = rtrim(str_replace("\r", '', $body));

		/* strip slashes only if magic quotes is ON
		   if we do it with magic quotes OFF, it strips real, user-inputted chars.
		          带斜杠只有神奇的报价　　如果我们用魔法做报价,用户输入字符,这条真实。   
		   NOTE: In PHP 5.4 get_magic_quotes_gpc() will always return 0 and
			 it will probably not exist in future versions at all.
			 在PHP 5.4 get_magic_quotes_gpc()将始终返回0 　　它将在未来的版本中可能不存在。
		*/
		if ( ! is_php('5.4') && get_magic_quotes_gpc())
		{
			$this->_body = stripslashes($this->_body);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Assign file attachments
	 * 指定文件附件
	 * @param	string	$file	Can be local path, URL or buffered content 可以是本地路径,URL或缓冲内容
	 * @param	string	$disposition = 'attachment' 附件
	 * @param	string	$newname = NULL
	 * @param	string	$mime = ''
	 * @return	CI_Email
	 */
	public function attach($file, $disposition = '', $newname = NULL, $mime = '')
	{
		if ($mime === '')
		{
			if (strpos($file, '://') === FALSE && ! file_exists($file))
			{
				$this->_set_error_message('lang:email_attachment_missing', $file);
				return FALSE;
			}

			if ( ! $fp = @fopen($file, 'rb'))
			{
				$this->_set_error_message('lang:email_attachment_unreadable', $file);
				return FALSE;
			}

			$file_content = stream_get_contents($fp);
			$mime = $this->_mime_types(pathinfo($file, PATHINFO_EXTENSION));
			fclose($fp);
		}
		else
		{
			$file_content =& $file; // buffered file  有缓冲颇文件
		}

		$this->_attachments[] = array(
			'name'		=> array($file, $newname),
			'disposition'	=> empty($disposition) ? 'attachment' : $disposition,  // Can also be 'inline'  Not sure if it matters 也可以“内联”不确定事项吗
			'type'		=> $mime,
			'content'	=> chunk_split(base64_encode($file_content))
		);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set and return attachment Content-ID
	 * 设置并返回附件内容识别
	 * Useful for attached inline pictures
	 * 用于连接内联图片
	 * @param	string	$filename
	 * @return	string
	 */
	public function attachment_cid($filename)
	{
		if ($this->multipart !== 'related')
		{
			$this->multipart = 'related'; // Thunderbird need this for inline images 雷鸟为内联图像需要这个
		}

		for ($i = 0, $c = count($this->_attachments); $i < $c; $i++)
		{
			if ($this->_attachments[$i]['name'][0] === $filename)
			{
				$this->_attachments[$i]['cid'] = uniqid(basename($this->_attachments[$i]['name'][0]).'@');
				return $this->_attachments[$i]['cid'];
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Add a Header Item
	 * 添加一个标题项
	 * @param	string
	 * @param	string
	 * @return	CI_Email
	 */
	public function set_header($header, $value)
	{
		$this->_headers[$header] = str_replace(array("\n", "\r"), '', $value);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Convert a String to an Array
	 * 将一个字符串转换为一个数组
	 * @param	string
	 * @return	array
	 */
	protected function _str_to_array($email)
	{
		if ( ! is_array($email))
		{
			return (strpos($email, ',') !== FALSE)
				? preg_split('/[\s,]/', $email, -1, PREG_SPLIT_NO_EMPTY)
				: (array) trim($email);
		}

		return $email;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Multipart Value
	 * 设置多部分的值
	 * @param	string
	 * @return	CI_Email
	 */
	public function set_alt_message($str)
	{
		$this->alt_message = (string) $str;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Mailtype
	 * 设置Mailtype
	 * @param	string
	 * @return	CI_Email
	 */
	public function set_mailtype($type = 'text')
	{
		$this->mailtype = ($type === 'html') ? 'html' : 'text';
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Wordwrap
	 * 设置自动换行
	 * @param	bool
	 * @return	CI_Email
	 */
	public function set_wordwrap($wordwrap = TRUE)
	{
		$this->wordwrap = (bool) $wordwrap;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Protocol
	 * SET协议
	 * @param	string
	 * @return	CI_Email
	 */
	public function set_protocol($protocol = 'mail')
	{
		$this->protocol = in_array($protocol, $this->_protocols, TRUE) ? strtolower($protocol) : 'mail';
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Priority
	 * 设置优先级
	 * @param	int
	 * @return	CI_Email
	 */
	public function set_priority($n = 3)
	{
		$this->priority = preg_match('/^[1-5]$/', $n) ? (int) $n : 3;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Newline Character
	 * 设置换行符
	 * @param	string
	 * @return	CI_Email
	 */
	public function set_newline($newline = "\n")
	{
		$this->newline = in_array($newline, array("\n", "\r\n", "\r")) ? $newline : "\n";
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set CRLF
	 * 设置CRLF
	 * @param	string
	 * @return	CI_Email
	 */
	public function set_crlf($crlf = "\n")
	{
		$this->crlf = ($crlf !== "\n" && $crlf !== "\r\n" && $crlf !== "\r") ? "\n" : $crlf;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Message Boundary
	 * 设置消息边界
	 * @return	void
	 */
	protected function _set_boundaries()
	{
		$this->_alt_boundary = 'B_ALT_'.uniqid(''); // multipart/alternative 多部分替代
		$this->_atc_boundary = 'B_ATC_'.uniqid(''); // attachment boundary  附件边界
	}

	// --------------------------------------------------------------------

	/**
	 * Get the Message ID
	 * 得到消息ID
	 * @return	string
	 */
	protected function _get_message_id()
	{
		$from = str_replace(array('>', '<'), '', $this->_headers['Return-Path']);
		return '<'.uniqid('').strstr($from, '@').'>';
	}

	// --------------------------------------------------------------------

	/**
	 * Get Mail Protocol
	 * 获得邮件协议
	 * @param	bool
	 * @return	mixed
	 */
	protected function _get_protocol($return = TRUE)
	{
		$this->protocol = strtolower($this->protocol);
		in_array($this->protocol, $this->_protocols, TRUE) OR $this->protocol = 'mail';

		if ($return === TRUE)
		{
			return $this->protocol;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get Mail Encoding
	 * 获得邮件编码
	 * @param	bool
	 * @return	string
	 */
	protected function _get_encoding($return = TRUE)
	{
		in_array($this->_encoding, $this->_bit_depths) OR $this->_encoding = '8bit';

		foreach ($this->_base_charsets as $charset)
		{
			if (strpos($charset, $this->charset) === 0)
			{
				$this->_encoding = '7bit';
			}
		}

		if ($return === TRUE)
		{
			return $this->_encoding;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get content type (text/html/attachment)
	 * 内容类型(text / html /附件)
	 * @return	string
	 */
	protected function _get_content_type()
	{
		if ($this->mailtype === 'html')
		{
			return (count($this->_attachments) === 0) ? 'html' : 'html-attach';
		}
		elseif	($this->mailtype === 'text' && count($this->_attachments) > 0)
		{
			return 'plain-attach';
		}
		else
		{
			return 'plain';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set RFC 822 Date
	 * 设置RFC 822 日期
	 * @return	string
	 */
	protected function _set_date()
	{
		$timezone = date('Z');
		$operator = ($timezone[0] === '-') ? '-' : '+';
		$timezone = abs($timezone);
		$timezone = floor($timezone/3600) * 100 + ($timezone % 3600) / 60;

		return sprintf('%s %s%04d', date('D, j M Y H:i:s'), $operator, $timezone);
	}

	// --------------------------------------------------------------------

	/**
	 * Mime message
	 * Mime消息
	 * @return	string
	 */
	protected function _get_mime_message()
	{
		return 'This is a multi-part message in MIME format这是一个多部分消息MIME格式.'.$this->newline.'Your email application may not support this format你的电子邮件应用程序可能不支持这种格式.';
	}

	// --------------------------------------------------------------------

	/**
	 * Validate Email Address
	 * 验证电子邮件地址
	 * @param	string
	 * @return	bool
	 */
	public function validate_email($email)
	{
		if ( ! is_array($email))
		{
			$this->_set_error_message('lang:email_must_be_array');
			return FALSE;
		}

		foreach ($email as $val)
		{
			if ( ! $this->valid_email($val))
			{
				$this->_set_error_message('lang:email_invalid_address', $val);
				return FALSE;
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Email Validation
	 * 确认电邮
	 * @param	string
	 * @return	bool
	 */
	public function valid_email($email)
	{
		if (function_exists('idn_to_ascii') && $atpos = strpos($email, '@'))
		{
			$email = substr($email, 0, ++$atpos).idn_to_ascii(substr($email, $atpos));
		}

		return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
	}

	// --------------------------------------------------------------------

	/**
	 * Clean Extended Email Address: Joe Smith <joe@smith.com>
	 * 清洁扩展的电子邮件地址
	 * @param	string
	 * @return	string
	 */
	public function clean_email($email)
	{
		if ( ! is_array($email))
		{
			return preg_match('/\<(.*)\>/', $email, $match) ? $match[1] : $email;
		}

		$clean_email = array();

		foreach ($email as $addy)
		{
			$clean_email[] = preg_match('/\<(.*)\>/', $addy, $match) ? $match[1] : $addy;
		}

		return $clean_email;
	}

	// --------------------------------------------------------------------

	/**
	 * Build alternative plain text message
	 * 建立替代纯文本消息
	 * Provides the raw message for use in plain-text headers of
	 * HTML-formatted emails.
	 * 提供的原始消息用于纯文本头html格式的电子邮件。
	 * If the user hasn't specified his own alternative message
	 * it creates one by stripping the HTML
	 * 如果用户没有指定自己的替代消息它创建一个通过剥离HTML
	 * @return	string
	 */
	protected function _get_alt_message()
	{
		if ( ! empty($this->alt_message))
		{
			return ($this->wordwrap)
				? $this->word_wrap($this->alt_message, 76)
				: $this->alt_message;
		}

		$body = preg_match('/\<body.*?\>(.*)\<\/body\>/si', $this->_body, $match) ? $match[1] : $this->_body;
		$body = str_replace("\t", '', preg_replace('#<!--(.*)--\>#', '', trim(strip_tags($body))));

		for ($i = 20; $i >= 3; $i--)
		{
			$body = str_replace(str_repeat("\n", $i), "\n\n", $body);
		}

		// Reduce multiple spaces 减少多重空间
		$body = preg_replace('| +|', ' ', $body);

		return ($this->wordwrap)
			? $this->word_wrap($body, 76)
			: $body;
	}

	// --------------------------------------------------------------------

	/**
	 * Word Wrap
	 * 自动换行
	 * @param	string
	 * @param	int	line-length limit  字幕限制
	 * @return	string
	 */
	public function word_wrap($str, $charlim = NULL)
	{
		// Set the character limit, if not already present 设置字符的限制,如果不是已经存在
		if (empty($charlim))
		{
			$charlim = empty($this->wrapchars) ? 76 : $this->wrapchars;
		}

		// Standardize newlines  标准化换行
		if (strpos($str, "\r") !== FALSE)
		{
			$str = str_replace(array("\r\n", "\r"), "\n", $str);
		}

		// Reduce multiple spaces at end of line 减少行的多个空间结束
		$str = preg_replace('| +\n|', "\n", $str);

		// If the current word is surrounded by {unwrap} tags we'll  如果当前的单词是{打开}标签我们包围
		// strip the entire chunk and replace it with a marker.  带整个块和替换标记
		$unwrap = array();
		if (preg_match_all('|\{unwrap\}(.+?)\{/unwrap\}|s', $str, $matches))
		{
			for ($i = 0, $c = count($matches[0]); $i < $c; $i++)
			{
				$unwrap[] = $matches[1][$i];
				$str = str_replace($matches[0][$i], '{{unwrapped'.$i.'}}', $str);
			}
		}

		// Use PHP's native function to do the initial wordwrap. 使用PHP的本机函数初始自动换行。
		// We set the cut flag to FALSE so that any individual words that are
		// too long get left alone. In the next step we'll deal with them.
		// 我们减少标志设置为FALSE,任何单词太长时间独处。在下一步我们会处理这些问题。
		$str = wordwrap($str, $charlim, "\n", FALSE);

		// Split the string into individual lines of text and cycle through them 字符串分割成单独的行文本和周期
		$output = '';
		foreach (explode("\n", $str) as $line)
		{
			// Is the line within the allowed character count?  是在允许的字符计数线?
			// If so we'll join it to the output and continue  如果我们将其加入到产出和继续
			if (mb_strlen($line) <= $charlim)
			{
				$output .= $line.$this->newline;
				continue;
			}

			$temp = '';
			do
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
			while (mb_strlen($line) > $charlim);

			// If $temp contains data it means we had to split up an over-length
			// word into smaller chunks so we'll add it back to our current line
			// 如果临时美元包含数据意味着我们不得不分手一个超长的词成小块我们添加它回到我们的当前行
			if ($temp !== '')
			{
				$output .= $temp.$this->newline;
			}

			$output .= $line.$this->newline;
		}

		// Put our markers back 把我们的标记
		if (count($unwrap) > 0)
		{
			foreach ($unwrap as $key => $val)
			{
				$output = str_replace('{{unwrapped'.$key.'}}', $val, $output);
			}
		}

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * Build final headers
	 * 构建最终的头
	 * @return	string
	 */
	protected function _build_headers()
	{
		$this->set_header('X-Sender', $this->clean_email($this->_headers['From']));
		$this->set_header('X-Mailer', $this->useragent);
		$this->set_header('X-Priority', $this->_priorities[$this->priority]);
		$this->set_header('Message-ID', $this->_get_message_id());
		$this->set_header('Mime-Version', '1.0');
	}

	// --------------------------------------------------------------------

	/**
	 * Write Headers as a string
	 * 把标题写成一个字符串
	 * @return	void
	 */
	protected function _write_headers()
	{
		if ($this->protocol === 'mail')
		{
			if (isset($this->_headers['Subject']))
			{
				$this->_subject = $this->_headers['Subject'];
				unset($this->_headers['Subject']);
			}
		}

		reset($this->_headers);
		$this->_header_str = '';

		foreach ($this->_headers as $key => $val)
		{
			$val = trim($val);

			if ($val !== '')
			{
				$this->_header_str .= $key.': '.$val.$this->newline;
			}
		}

		if ($this->_get_protocol() === 'mail')
		{
			$this->_header_str = rtrim($this->_header_str);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Build Final Body and attachments
	 * 构建最终的身体和附件
	 * @return	bool
	 */
	protected function _build_message()
	{
		if ($this->wordwrap === TRUE && $this->mailtype !== 'html')
		{
			$this->_body = $this->word_wrap($this->_body);
		}

		$this->_set_boundaries();
		$this->_write_headers();

		$hdr = ($this->_get_protocol() === 'mail') ? $this->newline : '';
		$body = '';

		switch ($this->_get_content_type())
		{
			case 'plain' :

				$hdr .= 'Content-Type: text/plain; charset='.$this->charset.$this->newline
					.'Content-Transfer-Encoding: '.$this->_get_encoding();

				if ($this->_get_protocol() === 'mail')
				{
					$this->_header_str .= $hdr;
					$this->_finalbody = $this->_body;
				}
				else
				{
					$this->_finalbody = $hdr.$this->newline.$this->newline.$this->_body;
				}

				return;

			case 'html' :

				if ($this->send_multipart === FALSE)
				{
					$hdr .= 'Content-Type: text/html; charset='.$this->charset.$this->newline
						.'Content-Transfer-Encoding: quoted-printable';
				}
				else
				{
					$hdr .= 'Content-Type: multipart/alternative; boundary="'.$this->_alt_boundary.'"';

					$body .= $this->_get_mime_message().$this->newline.$this->newline
						.'--'.$this->_alt_boundary.$this->newline

						.'Content-Type: text/plain; charset='.$this->charset.$this->newline
						.'Content-Transfer-Encoding: '.$this->_get_encoding().$this->newline.$this->newline
						.$this->_get_alt_message().$this->newline.$this->newline.'--'.$this->_alt_boundary.$this->newline

						.'Content-Type: text/html; charset='.$this->charset.$this->newline
						.'Content-Transfer-Encoding: quoted-printable'.$this->newline.$this->newline;
				}

				$this->_finalbody = $body.$this->_prep_quoted_printable($this->_body).$this->newline.$this->newline;

				if ($this->_get_protocol() === 'mail')
				{
					$this->_header_str .= $hdr;
				}
				else
				{
					$this->_finalbody = $hdr.$this->newline.$this->newline.$this->_finalbody;
				}

				if ($this->send_multipart !== FALSE)
				{
					$this->_finalbody .= '--'.$this->_alt_boundary.'--';
				}

				return;

			case 'plain-attach' :

				$hdr .= 'Content-Type: multipart/'.$this->multipart.'; boundary="'.$this->_atc_boundary.'"';

				if ($this->_get_protocol() === 'mail')
				{
					$this->_header_str .= $hdr;
				}

				$body .= $this->_get_mime_message().$this->newline
					.$this->newline
					.'--'.$this->_atc_boundary.$this->newline
					.'Content-Type: text/plain; charset='.$this->charset.$this->newline
					.'Content-Transfer-Encoding: '.$this->_get_encoding().$this->newline
					.$this->newline
					.$this->_body.$this->newline.$this->newline;

			break;
			case 'html-attach' :

				$hdr .= 'Content-Type: multipart/'.$this->multipart.'; boundary="'.$this->_atc_boundary.'"';

				if ($this->_get_protocol() === 'mail')
				{
					$this->_header_str .= $hdr;
				}

				$body .= $this->_get_mime_message().$this->newline.$this->newline
					.'--'.$this->_atc_boundary.$this->newline

					.'Content-Type: multipart/alternative; boundary="'.$this->_alt_boundary.'"'.$this->newline.$this->newline
					.'--'.$this->_alt_boundary.$this->newline

					.'Content-Type: text/plain; charset='.$this->charset.$this->newline
					.'Content-Transfer-Encoding: '.$this->_get_encoding().$this->newline.$this->newline
					.$this->_get_alt_message().$this->newline.$this->newline.'--'.$this->_alt_boundary.$this->newline

					.'Content-Type: text/html; charset='.$this->charset.$this->newline
					.'Content-Transfer-Encoding: quoted-printable'.$this->newline.$this->newline

					.$this->_prep_quoted_printable($this->_body).$this->newline.$this->newline
					.'--'.$this->_alt_boundary.'--'.$this->newline.$this->newline;

			break;
		}

		$attachment = array();
		for ($i = 0, $c = count($this->_attachments), $z = 0; $i < $c; $i++)
		{
			$filename = $this->_attachments[$i]['name'][0];
			$basename = ($this->_attachments[$i]['name'][1] === NULL)
				? basename($filename) : $this->_attachments[$i]['name'][1];

			$attachment[$z++] = '--'.$this->_atc_boundary.$this->newline
				.'Content-type: '.$this->_attachments[$i]['type'].'; '
				.'name="'.$basename.'"'.$this->newline
				.'Content-Disposition: '.$this->_attachments[$i]['disposition'].';'.$this->newline
				.'Content-Transfer-Encoding: base64'.$this->newline
				.(empty($this->_attachments[$i]['cid']) ? '' : 'Content-ID: <'.$this->_attachments[$i]['cid'].'>'.$this->newline);

			$attachment[$z++] = $this->_attachments[$i]['content'];
		}

		$body .= implode($this->newline, $attachment).$this->newline.'--'.$this->_atc_boundary.'--';
		$this->_finalbody = ($this->_get_protocol() === 'mail')
			? $body
			: $hdr.$this->newline.$this->newline.$body;

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Prep Quoted Printable
	 * 准备引用可打印
	 * Prepares string for Quoted-Printable Content-Transfer-Encoding
	 * Refer to RFC 2045 http://www.ietf.org/rfc/rfc2045.txt
	 * 准备字符串Quoted-Printable Content-Transfer-Encoding指RFC 2045
	 * @param	string
	 * @return	string
	 */
	protected function _prep_quoted_printable($str)
	{
		// We are intentionally wrapping so mail servers will encode characters
		// properly and MUAs will behave, so {unwrap} must go!
		// 我们是故意包装所以邮件服务器将会正确地编码字符,邮件用户代理的行为,所以{打开}必须走!
		$str = str_replace(array('{unwrap}', '{/unwrap}'), '', $str);

		// RFC 2045 specifies指定 CRLF as "\r\n".
		// However, many developers choose to override that and violate
		// the RFC rules due to (apparently) a bug in MS Exchange,
		// which only works with "\n".
		// 然而,许多开发人员选择覆盖和违反RFC规则(显然)MS交换的bug,它只适用于“\ n”。
		if ($this->crlf === "\r\n")
		{
			if (is_php('5.3'))
			{
				return quoted_printable_encode($str);
			}
			elseif (function_exists('imap_8bit'))
			{
				return imap_8bit($str);
			}
		}

		// Reduce multiple spaces & remove nulls  减少多个空间&删除null
		$str = preg_replace(array('| +|', '/\x00+/'), array(' ', ''), $str);

		// Standardize newlines  标准化换行
		if (strpos($str, "\r") !== FALSE)
		{
			$str = str_replace(array("\r\n", "\r"), "\n", $str);
		}

		$escape = '=';
		$output = '';

		foreach (explode("\n", $str) as $line)
		{
			$length = strlen($line);
			$temp = '';

			// Loop through each character in the line to add soft-wrap
			// characters at the end of a line " =\r\n" and add the newly
			// processed line(s) to the output (see comment on $crlf class property)
			// 循环中的每个字符行添加soft-wrap字符最后一行“= \ r \ n”和新加工线(s)添加到输出(见评论美元crlf类属性)
			for ($i = 0; $i < $length; $i++)
			{
				// Grab the next character 抓住下一个字符
				$char = $line[$i];
				$ascii = ord($char);

				// Convert spaces and tabs but only if it's the end of the line 空格和制表符转换但前提是线的结束
				if ($i === ($length - 1) && ($ascii === 32 OR $ascii === 9))
				{
					$char = $escape.sprintf('%02s', dechex($ascii));
				}
				elseif ($ascii === 61) // encode = signs  编码=标志
				{
					$char = $escape.strtoupper(sprintf('%02s', dechex($ascii)));  // =3D
				}

				// If we're at the character limit, add the line to the output, 如果我们在字符的限制,将一行添加到输出,
				// reset our temp variable, and keep on chuggin'  重置我们的临时变量,继续chuggin”
				if ((strlen($temp) + strlen($char)) >= 76)
				{
					$output .= $temp.$escape.$this->crlf;
					$temp = '';
				}

				// Add the character to our temporary line 字符添加到我们的临时线路
				$temp .= $char;
			}

			// Add our completed line to the output 我们完成了线添加到输出
			$output .= $temp.$this->crlf;
		}

		// get rid of extra CRLF tacked onto the end 去掉多余的CRLF钉到结束
		return substr($output, 0, strlen($this->crlf) * -1);
	}

	// --------------------------------------------------------------------

	/**
	 * Prep Q Encoding
	 * 准备问编码
	 * Performs "Q Encoding" on a string for use in email headers. 对字符串执行Q编码用于电子邮件标题
	 * It's related but not identical to quoted-printable, so it has its
	 * own method.
	 * quoted-printable引用可打印是相关但不相同的,所以它有自己的方法。
	 * @param	string
	 * @return	string
	 */
	protected function _prep_q_encoding($str)
	{
		$str = str_replace(array("\r", "\n"), '', $str);

		if ($this->charset === 'UTF-8')
		{
			if (MB_ENABLED === TRUE)
			{
				return mb_encode_mimeheader($str, $this->charset, 'Q', $this->crlf);
			}
			elseif (ICONV_ENABLED === TRUE)
			{
				$output = @iconv_mime_encode('', $str,
					array(
						'scheme' => 'Q',
						'line-length' => 76,
						'input-charset' => $this->charset,
						'output-charset' => $this->charset,
						'line-break-chars' => $this->crlf
					)
				);

				// There are reports that iconv_mime_encode() might fail and return FALSE 有报道称iconv_mime_encode()可能会失败,返回FALSE
				if ($output !== FALSE)
				{
					// iconv_mime_encode() will always put a header field name. iconv_mime_encode()总是把一个头字段名。
					// We've passed it an empty one, but it still prepends our
					// encoded string with ': ', so we need to strip it.
					// 我们通过一个空一个,但它仍然突出与‘:’我们的编码的字符串,所以我们需要带它。
					return substr($output, 2);
				}

				$chars = iconv_strlen($str, 'UTF-8');
			}
		}

		// We might already have this set for UTF-8 我们可能已经为utf - 8
		isset($chars) OR $chars = strlen($str);

		$output = '=?'.$this->charset.'?Q?';
		for ($i = 0, $length = strlen($output); $i < $chars; $i++)
		{
			$chr = ($this->charset === 'UTF-8' && ICONV_ENABLED === TRUE)
				? '='.implode('=', str_split(strtoupper(bin2hex(iconv_substr($str, $i, 1, $this->charset))), 2))
				: '='.strtoupper(bin2hex($str[$i]));

			// RFC 2045 sets a limit of 76 characters per line. RFC 2045集76个字符每行的限制。
			// We'll append ?= to the end of each line though. 我们将追加吗?=每一行的结束。
			if ($length + ($l = strlen($chr)) > 74)
			{
				$output .= '?='.$this->crlf // EOL
					.' =?'.$this->charset.'?Q?'.$chr; // New line 新一行
				$length = 6 + strlen($this->charset) + $l; // Reset the length for the new line 重置为新的行长度
			}
			else
			{
				$output .= $chr;
				$length += $l;
			}
		}

		// End the header
		return $output.'?=';
	}

	// --------------------------------------------------------------------

	/**
	 * Send Email
	 * 发送邮件
	 * @param	bool	$auto_clear = TRUE
	 * @return	bool
	 */
	public function send($auto_clear = TRUE)
	{
		if ( ! isset($this->_headers['From']))
		{
			$this->_set_error_message('lang:email_no_from');
			return FALSE;
		}

		if ($this->_replyto_flag === FALSE)
		{
			$this->reply_to($this->_headers['From']);
		}

		if ( ! isset($this->_recipients) && ! isset($this->_headers['To'])
			&& ! isset($this->_bcc_array) && ! isset($this->_headers['Bcc'])
			&& ! isset($this->_headers['Cc']))
		{
			$this->_set_error_message('lang:email_no_recipients');
			return FALSE;
		}

		$this->_build_headers();

		if ($this->bcc_batch_mode && count($this->_bcc_array) > $this->bcc_batch_size)
		{
			$result = $this->batch_bcc_send();

			if ($result && $auto_clear)
			{
				$this->clear();
			}

			return $result;
		}

		if ($this->_build_message() === FALSE)
		{
			return FALSE;
		}

		$result = $this->_spool_email();

		if ($result && $auto_clear)
		{
			$this->clear();
		}

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Batch Bcc Send. Sends groups of BCCs in batches
	 * 批量暗送,群发暗送基于轮番
	 * @return	void
	 */
	public function batch_bcc_send()
	{
		$float = $this->bcc_batch_size - 1;
		$set = '';
		$chunk = array();

		for ($i = 0, $c = count($this->_bcc_array); $i < $c; $i++)
		{
			if (isset($this->_bcc_array[$i]))
			{
				$set .= ', '.$this->_bcc_array[$i];
			}

			if ($i === $float)
			{
				$chunk[] = substr($set, 1);
				$float += $this->bcc_batch_size;
				$set = '';
			}

			if ($i === $c-1)
			{
				$chunk[] = substr($set, 1);
			}
		}

		for ($i = 0, $c = count($chunk); $i < $c; $i++)
		{
			unset($this->_headers['Bcc']);

			$bcc = $this->clean_email($this->_str_to_array($chunk[$i]));

			if ($this->protocol !== 'smtp')
			{
				$this->set_header('Bcc', implode(', ', $bcc));
			}
			else
			{
				$this->_bcc_array = $bcc;
			}

			if ($this->_build_message() === FALSE)
			{
				return FALSE;
			}

			$this->_spool_email();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Unwrap special elements
	 * 打开特殊元素
	 * @return	void
	 */
	protected function _unwrap_specials()
	{
		$this->_finalbody = preg_replace_callback('/\{unwrap\}(.*?)\{\/unwrap\}/si', array($this, '_remove_nl_callback'), $this->_finalbody);
	}

	// --------------------------------------------------------------------

	/**
	 * Strip line-breaks via callback
	 * 通过回调带换行符
	 * @param	string	$matches
	 * @return	string
	 */
	protected function _remove_nl_callback($matches)
	{
		if (strpos($matches[1], "\r") !== FALSE OR strpos($matches[1], "\n") !== FALSE)
		{
			$matches[1] = str_replace(array("\r\n", "\r", "\n"), '', $matches[1]);
		}

		return $matches[1];
	}

	// --------------------------------------------------------------------

	/**
	 * Spool mail to the mail server
	 * 线轴邮件到邮件服务器
	 * @return	bool
	 */
	protected function _spool_email()
	{
		$this->_unwrap_specials();

		$method = '_send_with_'.$this->_get_protocol();
		if ( ! $this->$method())
		{
			$this->_set_error_message('lang:email_send_failure_'.($this->_get_protocol() === 'mail' ? 'phpmail' : $this->_get_protocol()));
			return FALSE;
		}

		$this->_set_error_message('lang:email_sent', $this->_get_protocol());
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Send using mail()
	 * 使用邮件发送()
	 * @return	bool
	 */
	protected function _send_with_mail()
	{
		if (is_array($this->_recipients))
		{
			$this->_recipients = implode(', ', $this->_recipients);
		}

		if ($this->_safe_mode === TRUE)
		{
			return mail($this->_recipients, $this->_subject, $this->_finalbody, $this->_header_str);
		}
		else
		{
			// most documentation of sendmail using the "-f" flag lacks a space after it, however 大多数文档使用“- f”旗帜的sendmail缺乏空间后,然而
			// we've encountered servers that seem to require it to be in place. 我们似乎遇到了服务器需要的地方。
			return mail($this->_recipients, $this->_subject, $this->_finalbody, $this->_header_str, '-f '.$this->clean_email($this->_headers['Return-Path']));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Send using Sendmail
	 * 发送使用Sendmail
	 * @return	bool
	 */
	protected function _send_with_sendmail()
	{
		// is popen() enabled? popen()启用?
		if ( ! function_usable('popen')
			OR FALSE === ($fp = @popen(
						$this->mailpath.' -oi -f '.$this->clean_email($this->_headers['From'])
							.' -t -r '.$this->clean_email($this->_headers['Return-Path'])
						, 'w'))
		) // server probably has popen disabled, so nothing we can do to get a verbose error. 服务器可能有popen残疾,所以我们不可能得到一个详细的错误。
		{
			return FALSE;
		}

		fputs($fp, $this->_header_str);
		fputs($fp, $this->_finalbody);

		$status = pclose($fp);

		if ($status !== 0)
		{
			$this->_set_error_message('lang:email_exit_status', $status);
			$this->_set_error_message('lang:email_no_socket');
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Send using SMTP
	 * 使用SMTP发送
	 * @return	bool
	 */
	protected function _send_with_smtp()
	{
		if ($this->smtp_host === '')
		{
			$this->_set_error_message('lang:email_no_hostname');
			return FALSE;
		}

		if ( ! $this->_smtp_connect() OR ! $this->_smtp_authenticate())
		{
			return FALSE;
		}

		if ( ! $this->_send_command('from', $this->clean_email($this->_headers['From'])))
		{
			return FALSE;
		}

		foreach ($this->_recipients as $val)
		{
			if ( ! $this->_send_command('to', $val))
			{
				return FALSE;
			}
		}

		if (count($this->_cc_array) > 0)
		{
			foreach ($this->_cc_array as $val)
			{
				if ($val !== '' && ! $this->_send_command('to', $val))
				{
					return FALSE;
				}
			}
		}

		if (count($this->_bcc_array) > 0)
		{
			foreach ($this->_bcc_array as $val)
			{
				if ($val !== '' && ! $this->_send_command('to', $val))
				{
					return FALSE;
				}
			}
		}

		if ( ! $this->_send_command('data'))
		{
			return FALSE;
		}

		// perform dot transformation on any lines that begin with a dot 执行点转换任何线从一个点开始
		$this->_send_data($this->_header_str.preg_replace('/^\./m', '..$1', $this->_finalbody));

		$this->_send_data('.');

		$reply = $this->_get_smtp_data();

		$this->_set_error_message($reply);

		if (strpos($reply, '250') !== 0)
		{
			$this->_set_error_message('lang:email_smtp_error', $reply);
			return FALSE;
		}

		if ($this->smtp_keepalive)
		{
			$this->_send_command('reset');
		}
		else
		{
			$this->_send_command('quit');
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * SMTP Connect
	 * SMTP连接
	 * @return	string
	 */
	protected function _smtp_connect()
	{
		if (is_resource($this->_smtp_connect))
		{
			return TRUE;
		}

		$ssl = ($this->smtp_crypto === 'ssl') ? 'ssl://' : '';

		$this->_smtp_connect = fsockopen($ssl.$this->smtp_host,
							$this->smtp_port,
							$errno,
							$errstr,
							$this->smtp_timeout);

		if ( ! is_resource($this->_smtp_connect))
		{
			$this->_set_error_message('lang:email_smtp_error', $errno.' '.$errstr);
			return FALSE;
		}

		stream_set_timeout($this->_smtp_connect, $this->smtp_timeout);
		$this->_set_error_message($this->_get_smtp_data());

		if ($this->smtp_crypto === 'tls')
		{
			$this->_send_command('hello');
			$this->_send_command('starttls');

			$crypto = stream_socket_enable_crypto($this->_smtp_connect, TRUE, STREAM_CRYPTO_METHOD_TLS_CLIENT);

			if ($crypto !== TRUE)
			{
				$this->_set_error_message('lang:email_smtp_error', $this->_get_smtp_data());
				return FALSE;
			}
		}

		return $this->_send_command('hello');
	}

	// --------------------------------------------------------------------

	/**
	 * Send SMTP command
	 * SMTP命令发送
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _send_command($cmd, $data = '')
	{
		switch ($cmd)
		{
			case 'hello' :

						if ($this->_smtp_auth OR $this->_get_encoding() === '8bit')
						{
							$this->_send_data('EHLO '.$this->_get_hostname());
						}
						else
						{
							$this->_send_data('HELO '.$this->_get_hostname());
						}

						$resp = 250;
			break;
			case 'starttls'	:

						$this->_send_data('STARTTLS');
						$resp = 220;
			break;
			case 'from' :

						$this->_send_data('MAIL FROM:<'.$data.'>');
						$resp = 250;
			break;
			case 'to' :

						if ($this->dsn)
						{
							$this->_send_data('RCPT TO:<'.$data.'> NOTIFY=SUCCESS,DELAY,FAILURE ORCPT=rfc822;'.$data);
						}
						else
						{
							$this->_send_data('RCPT TO:<'.$data.'>');
						}

						$resp = 250;
			break;
			case 'data'	:

						$this->_send_data('DATA');
						$resp = 354;
			break;
			case 'reset':

						$this->_send_data('RSET');
						$resp = 250;
			break;
			case 'quit'	:

						$this->_send_data('QUIT');
						$resp = 221;
			break;
		}

		$reply = $this->_get_smtp_data();

		$this->_debug_msg[] = '<pre>'.$cmd.': '.$reply.'</pre>';

		if ((int) substr($reply, 0, 3) !== $resp)
		{
			$this->_set_error_message('lang:email_smtp_error', $reply);
			return FALSE;
		}

		if ($cmd === 'quit')
		{
			fclose($this->_smtp_connect);
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * SMTP Authenticate
	 * SMTP认证
	 * @return	bool
	 */
	protected function _smtp_authenticate()
	{
		if ( ! $this->_smtp_auth)
		{
			return TRUE;
		}

		if ($this->smtp_user === '' && $this->smtp_pass === '')
		{
			$this->_set_error_message('lang:email_no_smtp_unpw');
			return FALSE;
		}

		$this->_send_data('AUTH LOGIN');

		$reply = $this->_get_smtp_data();

		if (strpos($reply, '503') === 0)	// Already authenticated
		{
			return TRUE;
		}
		elseif (strpos($reply, '334') !== 0)
		{
			$this->_set_error_message('lang:email_failed_smtp_login', $reply);
			return FALSE;
		}

		$this->_send_data(base64_encode($this->smtp_user));

		$reply = $this->_get_smtp_data();

		if (strpos($reply, '334') !== 0)
		{
			$this->_set_error_message('lang:email_smtp_auth_un', $reply);
			return FALSE;
		}

		$this->_send_data(base64_encode($this->smtp_pass));

		$reply = $this->_get_smtp_data();

		if (strpos($reply, '235') !== 0)
		{
			$this->_set_error_message('lang:email_smtp_auth_pw', $reply);
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Send SMTP data
	 * SMTP发送数据
	 * @param	string	$data
	 * @return	bool
	 */
	protected function _send_data($data)
	{
		$data .= $this->newline;
		for ($written = $timestamp = 0, $length = strlen($data); $written < $length; $written += $result)
		{
			if (($result = fwrite($this->_smtp_connect, substr($data, $written))) === FALSE)
			{
				break;
			}
			// See https://bugs.php.net/bug.php?id=39598 and http://php.net/manual/en/function.fwrite.php#96951
			elseif ($result === 0)
			{
				if ($timestamp === 0)
				{
					$timestamp = time();
				}
				elseif ($timestamp < (time() - $this->smtp_timeout))
				{
					$result = FALSE;
					break;
				}

				usleep(250000);
				continue;
			}
			else
			{
				$timestamp = 0;
			}
		}

		if ($result === FALSE)
		{
			$this->_set_error_message('lang:email_smtp_data_failure', $data);
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get SMTP data
	 * 获得SMTP数据
	 * @return	string
	 */
	protected function _get_smtp_data()
	{
		$data = '';

		while ($str = fgets($this->_smtp_connect, 512))
		{
			$data .= $str;

			if ($str[3] === ' ')
			{
				break;
			}
		}

		return $data;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Hostname
	 * 获得主机名
	 * There are only two legal types of hostname - either a fully
	 * qualified domain name (eg: "mail.example.com") or an IP literal
	 * 只有两种法律类型的主机名,要么完全限定域名(例如:“mail.example.com”)或IP文字
	 * (eg: "[1.2.3.4]").
	 *
	 * @link	https://tools.ietf.org/html/rfc5321#section-2.3.5
	 * @link	http://cbl.abuseat.org/namingproblems.html
	 * @return	string
	 */
	protected function _get_hostname()
	{
		if (isset($_SERVER['SERVER_NAME']))
		{
			return $_SERVER['SERVER_NAME'];
		}

		return isset($_SERVER['SERVER_ADDR']) ? '['.$_SERVER['SERVER_ADDR'].']' : '[127.0.0.1]';
	}

	// --------------------------------------------------------------------

	/**
	 * Get Debug Message
	 * 得到调试信息
	 * @param	array	$include	List of raw data chunks to include in the output 的原始数据块列表包含在输出中
	 *					Valid options are: 'headers', 'subject', 'body' 有效的选项是:“头”、“主题”、“身体”
	 * @return	string
	 */
	public function print_debugger($include = array('headers', 'subject', 'body'))
	{
		$msg = '';

		if (count($this->_debug_msg) > 0)
		{
			foreach ($this->_debug_msg as $val)
			{
				$msg .= $val;
			}
		}

		// Determine which parts of our raw data needs to be printed  确定哪部分我们的原始数据需要被打印出来
		$raw_data = '';
		is_array($include) OR $include = array($include);

		if (in_array('headers', $include, TRUE))
		{
			$raw_data = htmlspecialchars($this->_header_str)."\n";
		}

		if (in_array('subject', $include, TRUE))
		{
			$raw_data .= htmlspecialchars($this->_subject)."\n";
		}

		if (in_array('body', $include, TRUE))
		{
			$raw_data .= htmlspecialchars($this->_finalbody);
		}

		return $msg.($raw_data === '' ? '' : '<pre>'.$raw_data.'</pre>');
	}

	// --------------------------------------------------------------------

	/**
	 * Set Message
	 * 需要亲自启动
	 * @param	string	$msg
	 * @param	string	$val = ''
	 * @return	void
	 */
	protected function _set_error_message($msg, $val = '')
	{
		$CI =& get_instance();
		$CI->lang->load('email');

		if (sscanf($msg, 'lang:%s', $line) !== 1 OR FALSE === ($line = $CI->lang->line($line)))
		{
			$this->_debug_msg[] = str_replace('%s', $val, $msg).'<br />';
		}
		else
		{
			$this->_debug_msg[] = str_replace('%s', $val, $line).'<br />';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Mime Types
	 * 互联网媒体类型 
	 * @param	string
	 * @return	string
	 */
	protected function _mime_types($ext = '')
	{
		$ext = strtolower($ext);

		$mimes =& get_mimes();

		if (isset($mimes[$ext]))
		{
			return is_array($mimes[$ext])
				? current($mimes[$ext])
				: $mimes[$ext];
		}

		return 'application/x-unknown-content-type';
	}

}
