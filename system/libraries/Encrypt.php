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
 * CodeIgniter Encryption Class
 * CodeIgniter加密类
 * Provides two-way keyed encoding using Mcrypt
 * 提供双向使用Mcrypt键控编码
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/encryption.html
 */
class CI_Encrypt {

	/**
	 * Reference to the user's encryption key
	 * 参考用户的加密密钥
	 * @var string
	 */
	public $encryption_key		= '';

	/**
	 * Type of hash operation
	 * 类型的散列操作
	 * @var string
	 */
	protected $_hash_type		= 'sha1';

	/**
	 * Flag for the existence of mcrypt
	 * mcrypt存在的标记
	 * @var bool
	 */
	protected $_mcrypt_exists	= FALSE;

	/**
	 * Current cipher to be used with mcrypt
	 * 与mcrypt当前使用密码
	 * @var string
	 */
	protected $_mcrypt_cipher;

	/**
	 * Method for encrypting/decrypting data
	 * 加密/解密数据的方法
	 * @var int
	 */
	protected $_mcrypt_mode;

	/**
	 * Initialize Encryption class
	 * 初始化加密类
	 * @return	void
	 */
	public function __construct()
	{
		if (($this->_mcrypt_exists = function_exists('mcrypt_encrypt')) === FALSE)
		{
			show_error('The Encrypt library requires the Mcrypt extension加密库需要Mcrypt扩展.');
		}

		log_message('info', 'Encrypt Class Initialized加密类初始化');
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch the encryption key
	 * 获取加密密钥
	 * Returns it as MD5 in order to have an exact-length 128 bit key.
	 * Mcrypt is sensitive to keys that are not the correct length
	 * 回报是MD5为了有一个长度精确128位的关键 Mcrypt敏感密钥不正确的长度
	 * @param	string
	 * @return	string
	 */
	public function get_key($key = '')
	{
		if ($key === '')
		{
			if ($this->encryption_key !== '')
			{
				return $this->encryption_key;
			}

			$key = config_item('encryption_key');

			if ( ! strlen($key))
			{
				show_error('In order to use the encryption class requires that you set an encryption key in your config file为了使用加密类要求你在配置文件中设置一个加密密钥.');
			}
		}

		return md5($key);
	}

	// --------------------------------------------------------------------

	/**
	 * Set the encryption key
	 * 设置加密密钥
	 * @param	string
	 * @return	CI_Encrypt CI加密
	 */
	public function set_key($key = '')
	{
		$this->encryption_key = $key;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Encode
	 * 编码
	 * Encodes the message string using bitwise XOR encoding.使用位XOR编码编码消息字符串。
	 * The key is combined with a random hash, and then it
	 * too gets converted using XOR. The whole thing is then run
	 * through mcrypt using the randomized key. The end result
	 * is a double-encrypted message string that is randomized
	 * with each call to this function, even if the supplied
	 * message and key are the same.
	 * 关键是加上一个随机散列,然后它也会使用XOR转换。整件事情通过mcrypt使用随机键然后运行。最终的结果是一个double-encrypted消息字符串与每次调用这个函数,随机即使提供的信息和关键是相同的。
	 * @param	string	the string to encode 字符串进行编码
	 * @param	string	the key  关键字密匙
	 * @return	string
	 */
	public function encode($string, $key = '')
	{
		return base64_encode($this->mcrypt_encode($string, $this->get_key($key)));
	}

	// --------------------------------------------------------------------

	/**
	 * Decode
	 * 解码 译码
	 * Reverses the above process
	 * 逆转上述过程
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function decode($string, $key = '')
	{
		if (preg_match('/[^a-zA-Z0-9\/\+=]/', $string) OR base64_encode(base64_decode($string)) !== $string)
		{
			return FALSE;
		}

		return $this->mcrypt_decode(base64_decode($string), $this->get_key($key));
	}

	// --------------------------------------------------------------------

	/**
	 * Encode from Legacy
	 * 编码从遗留
	 * Takes an encoded string from the original Encryption class algorithms and
	 * returns a newly encoded string using the improved method added in 2.0.0
	 * This allows for backwards compatibility and a method to transition to the
	 * new encryption algorithms.
	 * 需要一个从原始加密编码的字符串类算法新编码的字符串,并返回一个使用改进的方法中添加2.0。0这允许向后兼容和方法过渡到新的加密算法。
	 * For more details,更多详情,参考 see http://codeigniter.com/user_guide/installation/upgrade_200.html#encryption
	 *
	 * @param	string
	 * @param	int		(mcrypt mode constant) (不变)mcrypt模式
	 * @param	string
	 * @return	string
	 */
	public function encode_from_legacy($string, $legacy_mode = MCRYPT_MODE_ECB, $key = '')
	{
		if (preg_match('/[^a-zA-Z0-9\/\+=]/', $string))
		{
			return FALSE;
		}

		// decode it first 首先解码它
		// set mode temporarily to what it was when string was encoded with the legacy
		// algorithm - typically MCRYPT_MODE_ECB
		// 设置模式暂时是当字符串与传统编码算法——通常MCRYPT_MODE_ECB
		$current_mode = $this->_get_mode();
		$this->set_mode($legacy_mode);

		$key = $this->get_key($key);
		$dec = base64_decode($string);
		if (($dec = $this->mcrypt_decode($dec, $key)) === FALSE)
		{
			$this->set_mode($current_mode);
			return FALSE;
		}

		$dec = $this->_xor_decode($dec, $key);

		// set the mcrypt mode back to what it should be, typically MCRYPT_MODE_CBC 设置mcrypt模式回到它应该是什么,通常MCRYPT_MODE_CBC
		$this->set_mode($current_mode);

		// and re-encode 再次编码
		return base64_encode($this->mcrypt_encode($dec, $key));
	}

	// --------------------------------------------------------------------

	/**
	 * XOR Decode
	 * XOR解码
	 * Takes an encoded string and key as input and generates the
	 * plain-text original message
	 * 以一个编码的字符串,关键作为输入,并生成文本原始消息
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _xor_decode($string, $key)
	{
		$string = $this->_xor_merge($string, $key);

		$dec = '';
		for ($i = 0, $l = strlen($string); $i < $l; $i++)
		{
			$dec .= ($string[$i++] ^ $string[$i]);
		}

		return $dec;
	}

	// --------------------------------------------------------------------

	/**
	 * XOR key + string Combiner
	 * XOR键+字符串组合器
	 * Takes a string and key as input and computes the difference using XOR
	 * 接受一个字符串和关键作为输入,计算使用XOR的区别
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _xor_merge($string, $key)
	{
		$hash = $this->hash($key);
		$str = '';
		for ($i = 0, $ls = strlen($string), $lh = strlen($hash); $i < $ls; $i++)
		{
			$str .= $string[$i] ^ $hash[($i % $lh)];
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Encrypt using Mcrypt
	 * 加密使用Mcrypt
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function mcrypt_encode($data, $key)
	{
		$init_size = mcrypt_get_iv_size($this->_get_cipher(), $this->_get_mode());
		$init_vect = mcrypt_create_iv($init_size, MCRYPT_RAND);
		return $this->_add_cipher_noise($init_vect.mcrypt_encrypt($this->_get_cipher(), $key, $data, $this->_get_mode(), $init_vect), $key);
	}

	// --------------------------------------------------------------------

	/**
	 * Decrypt using Mcrypt
	 * 解密使用Mcrypt
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function mcrypt_decode($data, $key)
	{
		$data = $this->_remove_cipher_noise($data, $key);
		$init_size = mcrypt_get_iv_size($this->_get_cipher(), $this->_get_mode());

		if ($init_size > strlen($data))
		{
			return FALSE;
		}

		$init_vect = substr($data, 0, $init_size);
		$data = substr($data, $init_size);
		return rtrim(mcrypt_decrypt($this->_get_cipher(), $key, $data, $this->_get_mode(), $init_vect), "\0");
	}

	// --------------------------------------------------------------------

	/**
	 * Adds permuted noise to the IV + encrypted data to protect
	 * against Man-in-the-middle attacks on CBC mode ciphers
	 * http://www.ciphersbyritter.com/GLOSSARY.HTM#IV
	 * 将排列噪声添加到IV +加密数据防止中间人攻击CBC模式密码
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _add_cipher_noise($data, $key)
	{
		$key = $this->hash($key);
		$str = '';

		for ($i = 0, $j = 0, $ld = strlen($data), $lk = strlen($key); $i < $ld; ++$i, ++$j)
		{
			if ($j >= $lk)
			{
				$j = 0;
			}

			$str .= chr((ord($data[$i]) + ord($key[$j])) % 256);
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Removes permuted noise from the IV + encrypted data, reversing
	 * _add_cipher_noise()
	 * 删除排列从IV +加密数据噪声,扭转
	 * Function description 功能描述 
	 *
	 * @param	string	$data
	 * @param	string	$key
	 * @return	string
	 */
	protected function _remove_cipher_noise($data, $key)
	{
		$key = $this->hash($key);
		$str = '';

		for ($i = 0, $j = 0, $ld = strlen($data), $lk = strlen($key); $i < $ld; ++$i, ++$j)
		{
			if ($j >= $lk)
			{
				$j = 0;
			}

			$temp = ord($data[$i]) - ord($key[$j]);

			if ($temp < 0)
			{
				$temp += 256;
			}

			$str .= chr($temp);
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Mcrypt Cipher
	 * 设置Mcrypt密码
	 * @param	int
	 * @return	CI_Encrypt
	 */
	public function set_cipher($cipher)
	{
		$this->_mcrypt_cipher = $cipher;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Mcrypt Mode
	 * 设置Mcrypt模式
	 * @param	int
	 * @return	CI_Encrypt
	 */
	public function set_mode($mode)
	{
		$this->_mcrypt_mode = $mode;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Mcrypt cipher Value
	 * 得到Mcrypt密码值
	 * @return	int
	 */
	protected function _get_cipher()
	{
		if ($this->_mcrypt_cipher === NULL)
		{
			return $this->_mcrypt_cipher = MCRYPT_RIJNDAEL_256;
		}

		return $this->_mcrypt_cipher;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Mcrypt Mode Value
	 * 获得Mcrypt模式的价值
	 * @return	int
	 */
	protected function _get_mode()
	{
		if ($this->_mcrypt_mode === NULL)
		{
			return $this->_mcrypt_mode = MCRYPT_MODE_CBC;
		}

		return $this->_mcrypt_mode;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Hash type
	 * 设置散列类型
	 * @param	string
	 * @return	void
	 */
	public function set_hash($type = 'sha1')
	{
		$this->_hash_type = in_array($type, hash_algos()) ? $type : 'sha1';
	}

	// --------------------------------------------------------------------

	/**
	 * Hash encode a string
	 * 哈希编码一个字符串
	 * @param	string
	 * @return	string
	 */
	public function hash($str)
	{
		return hash($this->_hash_type, $str);
	}

}
