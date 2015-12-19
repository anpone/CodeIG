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
 * Image Manipulation class
 * 图像处理类
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Image_lib
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/image_lib.html
 */
class CI_Image_lib {

	/**
	 * PHP extension/library to use for image manipulation PHP扩展/库用于图像处理
	 * Can be: imagemagick, netpbm, gd, gd2  可以:imagemagick,netpbm,gd,阻止gd2吗
	 *
	 * @var string
	 */
	public $image_library		= 'gd2';

	/**
	 * Path to the graphic library (if applicable)
	 * 路径的图形库(如果适用)
	 * @var string
	 */
	public $library_path		= '';

	/**
	 * Whether to send to browser or write to disk
	 * 是否要发送给浏览器或写入磁盘
	 * @var bool
	 */
	public $dynamic_output		= FALSE;

	/**
	 * Path to original image
	 * 原始图像路径
	 * @var string
	 */
	public $source_image		= '';

	/**
	 * Path to the modified image
	 * 修改后的图像
	 * @var string
	 */
	public $new_image		= '';

	/**
	 * Image width
	 * 图像宽度
	 * @var int
	 */
	public $width			= '';

	/**
	 * Image height
	 * 图像高度
	 * @var int
	 */
	public $height			= '';

	/**
	 * Quality percentage of new image
	 * 新形象的质量百分比
	 * @var int
	 */
	public $quality			= 90;

	/**
	 * Whether to create a thumbnail
	 * 是否要创建一个缩略图
	 * @var bool
	 */
	public $create_thumb		= FALSE;

	/**
	 * String to add to thumbnail version of image
	 * 字符串添加到缩略版的形象
	 * @var string
	 */
	public $thumb_marker		= '_thumb';

	/**
	 * Whether to maintain aspect ratio when resizing or use hard values
	 * 是否保持长宽比时调整或使用硬值
	 * @var bool
	 */
	public $maintain_ratio		= TRUE;

	/**
	 * auto, height, or width.  Determines what to use as the master dimension
	 * 自动、高度或宽度。决定如何使用主维度
	 * @var string
	 */
	public $master_dim		= 'auto';

	/**
	 * Angle at to rotate image
	 * 角度旋转图像
	 * @var string
	 */
	public $rotation_angle		= '';

	/**
	 * X Coordinate for manipulation of the current image
	 * X坐标当前图像的操作
	 * @var int
	 */
	public $x_axis			= '';

	/**
	 * Y Coordinate for manipulation of the current image
	 * Y坐标当前图像的操作
	 * @var int
	 */
	public $y_axis			= '';

	// --------------------------------------------------------------------------
	// Watermark Vars 水印var
	// --------------------------------------------------------------------------

	/**
	 * Watermark text if graphic is not used
	 * 如果不使用图形水印文本
	 * @var string
	 */
	public $wm_text			= '';

	/**
	 * Type of watermarking.  Options:  text/overlay
	 * 类型的水印。选择:文本/覆盖
	 * @var string
	 */
	public $wm_type			= 'text';

	/**
	 * Default transparency for watermark
	 * 默认透明水印
	 * @var int
	 */
	public $wm_x_transp		= 4;

	/**
	 * Default transparency for watermark
	 * 默认透明水印
	 * @var int
	 */
	public $wm_y_transp		= 4;

	/**
	 * Watermark image path
	 * 水印图像路径
	 * @var string
	 */
	public $wm_overlay_path		= '';

	/**
	 * TT font
	 * 字体
	 * @var string
	 */
	public $wm_font_path		= '';

	/**
	 * Font size (different versions of GD will either use points or pixels)
	 * 字体大小 (不同版本的GD要么使用点或像素)
	 * @var int
	 */
	public $wm_font_size		= 17;

	/**
	 * Vertical alignment:   T M B
	 * 垂直对齐
	 * @var string
	 */
	public $wm_vrt_alignment	= 'B';

	/**
	 * Horizontal alignment: L R C
	 * 水平对齐
	 * @var string
	 */
	public $wm_hor_alignment	= 'C';

	/**
	 * Padding around text
	 * 周围填充文本
	 * @var int
	 */
	public $wm_padding			= 0;

	/**
	 * Lets you push text to the right
	 * 让你推动文本到右边
	 * @var int
	 */
	public $wm_hor_offset		= 0;

	/**
	 * Lets you push text down
	 * 让你推动文本到下边
	 * @var int
	 */
	public $wm_vrt_offset		= 0;

	/**
	 * Text color
	 *  文本颜色
	 * @var string
	 */
	protected $wm_font_color	= '#ffffff';

	/**
	 * Dropshadow color
	 * 投影色彩
	 * @var string
	 */
	protected $wm_shadow_color	= '';

	/**
	 * Dropshadow distance
	 * 投影距离
	 * @var int
	 */
	public $wm_shadow_distance	= 2;

	/**
	 * Image opacity: 1 - 100  Only works with image
	 * 图片不透明度:1-100仅适用于图像
	 * @var int
	 */
	public $wm_opacity		= 50;

	// --------------------------------------------------------------------------
	// Private Vars  私人增值
	// --------------------------------------------------------------------------

	/**
	 * Source image folder
	 * 源图像的文件夹
	 * @var string
	 */
	public $source_folder		= '';

	/**
	 * Destination image folder
	 * 目的地形象的文件夹
	 * @var string
	 */
	public $dest_folder		= '';

	/**
	 * Image mime-type
	 * 图像mime类型
	 * @var string
	 */
	public $mime_type		= '';

	/**
	 * Original image width
	 * 原始图像的宽度
	 * @var int
	 */
	public $orig_width		= '';

	/**
	 * Original image height
	 * 原始图像的高度
	 * @var int
	 */
	public $orig_height		= '';

	/**
	 * Image format
	 * 图像格式
	 * @var string
	 */
	public $image_type		= '';

	/**
	 * Size of current image
	 * 当前图像的大小
	 * @var string
	 */
	public $size_str		= '';

	/**
	 * Full path to source image
	 * 完整路径源图像
	 * @var string
	 */
	public $full_src_path		= '';

	/**
	 * Full path to destination image
	 * 完整路径的目的地形象
	 * @var string
	 */
	public $full_dst_path		= '';

	/**
	 * File permissions
	 * 文件权限
	 * @var	int
	 */
	public $file_permissions = 0644;

	/**
	 * Name of function to create image
	 * 名字的函数来创建图像
	 * @var string
	 */
	public $create_fnc		= 'imagecreatetruecolor';

	/**
	 * Name of function to copy image
	 * 函数名称复制图像
	 * @var string
	 */
	public $copy_fnc		= 'imagecopyresampled';

	/**
	 * Error messages
	 * 错误信息
	 * @var array
	 */
	public $error_msg		= array();

	/**
	 * Whether to have a drop shadow on watermark
	 * 是否有水印的阴影
	 * @var bool
	 */
	protected $wm_use_drop_shadow	= FALSE;

	/**
	 * Whether to use truetype fonts
	 * 是否使用truetype字体
	 * @var bool
	 */
	public $wm_use_truetype	= FALSE;

	/**
	 * Initialize Image Library
	 * 初始化图像库
	 * @param	array	$props
	 * @return	void
	 */
	public function __construct($props = array())
	{
		if (count($props) > 0)
		{
			$this->initialize($props);
		}

		log_message('info', 'Image Lib Class Initialized图像Lib类初始化');
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize image properties
	 * 初始化图像属性
	 * Resets values in case this class is used in a loop
	 * 重置价值在这个类中使用一个循环
	 * @return	void
	 */
	public function clear()
	{
		$props = array('thumb_marker', 'library_path', 'source_image', 'new_image', 'width', 'height', 'rotation_angle', 'x_axis', 'y_axis', 'wm_text', 'wm_overlay_path', 'wm_font_path', 'wm_shadow_color', 'source_folder', 'dest_folder', 'mime_type', 'orig_width', 'orig_height', 'image_type', 'size_str', 'full_src_path', 'full_dst_path');

		foreach ($props as $val)
		{
			$this->$val = '';
		}

		$this->image_library 		= 'gd2';
		$this->dynamic_output 		= FALSE;
		$this->quality 				= 90;
		$this->create_thumb 		= FALSE;
		$this->thumb_marker 		= '_thumb';
		$this->maintain_ratio 		= TRUE;
		$this->master_dim 			= 'auto';
		$this->wm_type 				= 'text';
		$this->wm_x_transp 			= 4;
		$this->wm_y_transp 			= 4;
		$this->wm_font_size 		= 17;
		$this->wm_vrt_alignment 	= 'B';
		$this->wm_hor_alignment 	= 'C';
		$this->wm_padding 			= 0;
		$this->wm_hor_offset 		= 0;
		$this->wm_vrt_offset 		= 0;
		$this->wm_font_color		= '#ffffff';
		$this->wm_shadow_distance 	= 2;
		$this->wm_opacity 			= 50;
		$this->create_fnc 			= 'imagecreatetruecolor';
		$this->copy_fnc 			= 'imagecopyresampled';
		$this->error_msg 			= array();
		$this->wm_use_drop_shadow 	= FALSE;
		$this->wm_use_truetype 		= FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * initialize image preferences
	 * 初始化图像的偏好
	 * @param	array
	 * @return	bool
	 */
	public function initialize($props = array())
	{
		// Convert array elements into class variables 数组元素转换成类变量
		if (count($props) > 0)
		{
			foreach ($props as $key => $val)
			{
				if (property_exists($this, $key))
				{
					if (in_array($key, array('wm_font_color', 'wm_shadow_color')))
					{
						if (preg_match('/^#?([0-9a-f]{3}|[0-9a-f]{6})$/i', $val, $matches))
						{
							/* $matches[1] contains our hex color value, but it might be
							 * both in the full 6-length format or the shortened 3-length
							 * value.
							 * 匹配[1]包含我们的十六进制颜色值,但它可能是在整个6-length格式或缩短3-length值。
							 * We'll later need the full version, so we keep it if it's
							 * already there and if not - we'll convert to it. We can
							 * access string characters by their index as in an array,
							 * so we'll do that and use concatenation to form the final
							 * value:
							 * 我们以后需要完整版,所以我们把它如果它已经在那里了,如果没有,我们会转换。我们可以访问字符串字符的索引数组,所以我们要做的,并使用连接以形成最终的价值:
							 */
							$val = (strlen($matches[1]) === 6)
								? '#'.$matches[1]
								: '#'.$matches[1][0].$matches[1][0].$matches[1][1].$matches[1][1].$matches[1][2].$matches[1][2];
						}
						else
						{
							continue;
						}
					}

					$this->$key = $val;
				}
			}
		}

		// Is there a source image? If not, there's no reason to continue 源图像吗?如果没有,没有理由继续下去
		if ($this->source_image === '')
		{
			$this->set_error('imglib_source_image_required');
			return FALSE;
		}

		/* Is getimagesize() available?
		 * 是得到图片大小?可用的么
		 * We use it to determine the image properties (width/height).
		 * Note: We need to figure out how to determine image
		 * properties using ImageMagick and NetPBM
		 * 我们使用它来确定图像的属性(宽/高)。注意:我们需要弄清楚如何确定使用ImageMagick和NetPBM图像属性
		 */
		if ( ! function_exists('getimagesize'))
		{
			$this->set_error('imglib_gd_required_for_props');
			return FALSE;
		}

		$this->image_library = strtolower($this->image_library);

		/* Set the full server path
		 * 设置完整的服务器路径
		 * The source image may or may not contain a path.
		 * Either way, we'll try use realpath to generate the
		 * full server path in order to more reliably read it.
		 * 源图像可能会或可能不会包含路径。不管怎样,我们会尝试使用realpath生成完整的服务器路径为了更可靠地读它。
		 */
		if (($full_source_path = realpath($this->source_image)) !== FALSE)
		{
			$full_source_path = str_replace('\\', '/', $full_source_path);
		}
		else
		{
			$full_source_path = $this->source_image;
		}

		$x = explode('/', $full_source_path);
		$this->source_image = end($x);
		$this->source_folder = str_replace($this->source_image, '', $full_source_path);

		// Set the Image Properties 设置图像属性
		if ( ! $this->get_image_properties($this->source_folder.$this->source_image))
		{
			return FALSE;
		}

		/*
		 * Assign the "new" image name/path
		 * 分配的“新”形象名称/路径
		 * If the user has set a "new_image" name it means
		 * we are making a copy of the source image. If not
		 * it means we are altering the original. We'll
		 * set the destination filename and path accordingly.
		 * 如果用户设置一个“new_image”名称这意味着我们正在源图像的一个副本。如果不是它的意思是我们改变原来的。我们会相应地设置目标文件名和路径。
		 */
		if ($this->new_image === '')
		{
			$this->dest_image = $this->source_image;
			$this->dest_folder = $this->source_folder;
		}
		elseif (strpos($this->new_image, '/') === FALSE)
		{
			$this->dest_folder = $this->source_folder;
			$this->dest_image = $this->new_image;
		}
		else
		{
			if (strpos($this->new_image, '/') === FALSE && strpos($this->new_image, '\\') === FALSE)
			{
				$full_dest_path = str_replace('\\', '/', realpath($this->new_image));
			}
			else
			{
				$full_dest_path = $this->new_image;
			}

			// Is there a file name? 有一个文件的名字吗?
			if ( ! preg_match('#\.(jpg|jpeg|gif|png)$#i', $full_dest_path))
			{
				$this->dest_folder = $full_dest_path.'/';
				$this->dest_image = $this->source_image;
			}
			else
			{
				$x = explode('/', $full_dest_path);
				$this->dest_image = end($x);
				$this->dest_folder = str_replace($this->dest_image, '', $full_dest_path);
			}
		}

		/* Compile the finalized filenames/paths
		 * 编译完成文件名/路径
		 * We'll create two master strings containing the
		 * full server path to the source image and the
		 * full server path to the destination image.
		 * We'll also split the destination image name
		 * so we can insert the thumbnail marker if needed.
		 * 我们将创建两个主字符串包含完整的服务器路径源图像和完整的服务器路径到目标图像。我们还将把目标图像名称我们可以插入缩略图标记。
		 */
		if ($this->create_thumb === FALSE OR $this->thumb_marker === '')
		{
			$this->thumb_marker = '';
		}

		$xp = $this->explode_name($this->dest_image);

		$filename = $xp['name'];
		$file_ext = $xp['ext'];

		$this->full_src_path = $this->source_folder.$this->source_image;
		$this->full_dst_path = $this->dest_folder.$filename.$this->thumb_marker.$file_ext;

		/* Should we maintain image proportions?
		 * 我们应该保持图像比例?
		 * When creating thumbs or copies, the target width/height
		 * might not be in correct proportion with the source
		 * image's width/height. We'll recalculate it here.
		 * 当创建拇指或副本,目标宽度/高度可能不是正确的比例与源图像的宽度/高度。我们将重新计算。
		 */
		if ($this->maintain_ratio === TRUE && ($this->width !== 0 OR $this->height !== 0))
		{
			$this->image_reproportion();
		}

		/* Was a width and height specified?
		 * 指定的宽度和高度吗?
		 * If the destination width/height was not submitted we
		 * will use the values from the actual file
		 * 如果目的地宽度/高度不是提交我们将使用实际文件的值
		 */
		if ($this->width === '')
		{
			$this->width = $this->orig_width;
		}

		if ($this->height === '')
		{
			$this->height = $this->orig_height;
		}

		// Set the quality 设置质量
		$this->quality = trim(str_replace('%', '', $this->quality));

		if ($this->quality === '' OR $this->quality === 0 OR ! ctype_digit($this->quality))
		{
			$this->quality = 90;
		}

		// Set the x/y coordinates 设置x / y坐标
		is_numeric($this->x_axis) OR $this->x_axis = 0;
		is_numeric($this->y_axis) OR $this->y_axis = 0;

		// Watermark-related Stuff... 与相关水印的东西
		if ($this->wm_overlay_path !== '')
		{
			$this->wm_overlay_path = str_replace('\\', '/', realpath($this->wm_overlay_path));
		}

		if ($this->wm_shadow_color !== '')
		{
			$this->wm_use_drop_shadow = TRUE;
		}
		elseif ($this->wm_use_drop_shadow === TRUE && $this->wm_shadow_color === '')
		{
			$this->wm_use_drop_shadow = FALSE;
		}

		if ($this->wm_font_path !== '')
		{
			$this->wm_use_truetype = TRUE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Image Resize
	 * 大小调整
	 * This is a wrapper function that chooses the proper
	 * resize function based on the protocol specified
	 * 这是一个包装器函数,选择适当的调整函数基于指定的协议
	 * @return	bool
	 */
	public function resize()
	{
		$protocol = ($this->image_library === 'gd2') ? 'image_process_gd' : 'image_process_'.$this->image_library;
		return $this->$protocol('resize');
	}

	// --------------------------------------------------------------------

	/**
	 * Image Crop
	 * 图像分割 
	 * This is a wrapper function that chooses the proper
	 * cropping function based on the protocol specified
	 * 这是一个包装器函数,选择适当的裁剪功能基于指定的协议
	 * @return	bool
	 */
	public function crop()
	{
		$protocol = ($this->image_library === 'gd2') ? 'image_process_gd' : 'image_process_'.$this->image_library;
		return $this->$protocol('crop');
	}

	// --------------------------------------------------------------------

	/**
	 * Image Rotate
	 * 影像旋转
	 * This is a wrapper function that chooses the proper
	 * rotation function based on the protocol specified
	 * 这是一个包装器函数,选择适当的旋转功能基于指定的协议
	 * @return	bool
	 */
	public function rotate()
	{
		// Allowed rotation values 允许旋转值
		$degs = array(90, 180, 270, 'vrt', 'hor');

		if ($this->rotation_angle === '' OR ! in_array($this->rotation_angle, $degs))
		{
			$this->set_error('imglib_rotation_angle_required');
			return FALSE;
		}

		// Reassign the width and height 重新分配的宽度和高度
		if ($this->rotation_angle === 90 OR $this->rotation_angle === 270)
		{
			$this->width	= $this->orig_height;
			$this->height	= $this->orig_width;
		}
		else
		{
			$this->width	= $this->orig_width;
			$this->height	= $this->orig_height;
		}

		// Choose resizing function 选择调整功能
		if ($this->image_library === 'imagemagick' OR $this->image_library === 'netpbm')
		{
			$protocol = 'image_process_'.$this->image_library;
			return $this->$protocol('rotate');
		}

		return ($this->rotation_angle === 'hor' OR $this->rotation_angle === 'vrt')
			? $this->image_mirror_gd()
			: $this->image_rotate_gd();
	}

	// --------------------------------------------------------------------

	/**
	 * Image Process Using GD/GD2
	 * 使用GD /阻止GD2形象的过程
	 * This function will resize or crop
	 * 这个函数将调整或作物
	 * @param	string
	 * @return	bool
	 */
	public function image_process_gd($action = 'resize')
	{
		$v2_override = FALSE;

		// If the target width/height match the source, AND if the new file name is not equal to the old file name
		// 如果目标宽度/高度匹配源,如果新文件名不等于旧文件名
		// we'll simply make a copy of the original with the new name... assuming dynamic rendering is off.
		// 我们将简单地复制原始的新名字……假设动态呈现。
		if ($this->dynamic_output === FALSE && $this->orig_width === $this->width && $this->orig_height === $this->height)
		{
			if ($this->source_image !== $this->new_image && @copy($this->full_src_path, $this->full_dst_path))
			{
				chmod($this->full_dst_path, $this->file_permissions);
			}

			return TRUE;
		}

		// Let's set up our values based on the action 让我们建立我们的价值观的基础上的行动
		if ($action === 'crop')
		{
			// Reassign the source width/height if cropping 重新分配源如果裁剪宽度/高度
			$this->orig_width  = $this->width;
			$this->orig_height = $this->height;

			// GD 2.0 has a cropping bug so we'll test for it GD 2.0裁剪错误我们会测试
			if ($this->gd_version() !== FALSE)
			{
				$gd_version = str_replace('0', '', $this->gd_version());
				$v2_override = ($gd_version == 2);
			}
		}
		else
		{
			// If resizing the x/y axis must be zero 如果调整x / y轴必须是零
			$this->x_axis = 0;
			$this->y_axis = 0;
		}

		// Create the image handle 创建图像处理
		if ( ! ($src_img = $this->image_create_gd()))
		{
			return FALSE;
		}

		/* Create the image
		 * 创建图像
		 * Old conditional which users report cause problems with shared GD libs who report themselves as "2.0 or greater"
		 * 老有条件共享用户报告导致问题GD库报告自己的“2.0或更高版本”
		 * it appears that this is no longer the issue that it was in 2004, so we've removed it, retaining it in the comment
		 * below should that ever prove inaccurate.
		 * 看来,这不再是问题,它是在2004年,我们已经删除,保留它在下面的评论中应该被证明是不准确的。
		 * if ($this->image_library === 'gd2' && function_exists('imagecreatetruecolor') && $v2_override === FALSE)
		 */
		if ($this->image_library === 'gd2' && function_exists('imagecreatetruecolor'))
		{
			$create	= 'imagecreatetruecolor';
			$copy	= 'imagecopyresampled';
		}
		else
		{
			$create	= 'imagecreate';
			$copy	= 'imagecopyresized';
		}

		$dst_img = $create($this->width, $this->height);

		if ($this->image_type === 3) // png we can actually preserve transparency png我们可以保持透明度
		{
			imagealphablending($dst_img, FALSE);
			imagesavealpha($dst_img, TRUE);
		}

		$copy($dst_img, $src_img, 0, 0, $this->x_axis, $this->y_axis, $this->width, $this->height, $this->orig_width, $this->orig_height);

		// Show the image 显示图像
		if ($this->dynamic_output === TRUE)
		{
			$this->image_display_gd($dst_img);
		}
		elseif ( ! $this->image_save_gd($dst_img)) // Or save it
		{
			return FALSE;
		}

		// Kill the file handles 杀死文件句柄
		imagedestroy($dst_img);
		imagedestroy($src_img);

		chmod($this->full_dst_path, $this->file_permissions);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Image Process Using ImageMagick
	 *
	 * This function will resize, crop or rotate
	 *
	 * @param	string
	 * @return	bool
	 */
	public function image_process_imagemagick($action = 'resize')
	{
		// Do we have a vaild library path? 我们有一个库路径有效吗?
		if ($this->library_path === '')
		{
			$this->set_error('imglib_libpath_invalid');
			return FALSE;
		}

		if ( ! preg_match('/convert$/i', $this->library_path))
		{
			$this->library_path = rtrim($this->library_path, '/').'/convert';
		}

		// Execute the command 执行命令
		$cmd = $this->library_path.' -quality '.$this->quality;

		if ($action === 'crop')
		{
			$cmd .= ' -crop '.$this->width.'x'.$this->height.'+'.$this->x_axis.'+'.$this->y_axis.' "'.$this->full_src_path.'" "'.$this->full_dst_path .'" 2>&1';
		}
		elseif ($action === 'rotate')
		{
			$angle = ($this->rotation_angle === 'hor' OR $this->rotation_angle === 'vrt')
					? '-flop' : '-rotate '.$this->rotation_angle;

			$cmd .= ' '.$angle.' "'.$this->full_src_path.'" "'.$this->full_dst_path.'" 2>&1';
		}
		else // Resize
		{
			if($this->maintain_ratio === TRUE)
			{
				$cmd .= ' -resize '.$this->width.'x'.$this->height.' "'.$this->full_src_path.'" "'.$this->full_dst_path.'" 2>&1';
			}
			else
			{
				$cmd .= ' -resize '.$this->width.'x'.$this->height.'\! "'.$this->full_src_path.'" "'.$this->full_dst_path.'" 2>&1';
			}
		}

		$retval = 1;
		// exec() might be disabled 执行程序exec可能被禁用
		if (function_usable('exec'))
		{
			@exec($cmd, $output, $retval);
		}

		// Did it work? 它工作了吗?
		if ($retval > 0)
		{
			$this->set_error('imglib_image_process_failed');
			return FALSE;
		}

		chmod($this->full_dst_path, $this->file_permissions);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Image Process Using NetPBM
	 * 使用NetPBM图像流程
	 * This function will resize, crop or rotate
	 * 这个函数将调整,作物或旋转
	 * @param	string
	 * @return	bool
	 */
	public function image_process_netpbm($action = 'resize')
	{
		if ($this->library_path === '')
		{
			$this->set_error('imglib_libpath_invalid');
			return FALSE;
		}

		// Build the resizing command 建立调整命令
		switch ($this->image_type)
		{
			case 1 :
				$cmd_in		= 'giftopnm';
				$cmd_out	= 'ppmtogif';
				break;
			case 2 :
				$cmd_in		= 'jpegtopnm';
				$cmd_out	= 'ppmtojpeg';
				break;
			case 3 :
				$cmd_in		= 'pngtopnm';
				$cmd_out	= 'ppmtopng';
				break;
		}

		if ($action === 'crop')
		{
			$cmd_inner = 'pnmcut -left '.$this->x_axis.' -top '.$this->y_axis.' -width '.$this->width.' -height '.$this->height;
		}
		elseif ($action === 'rotate')
		{
			switch ($this->rotation_angle)
			{
				case 90:	$angle = 'r270';
					break;
				case 180:	$angle = 'r180';
					break;
				case 270:	$angle = 'r90';
					break;
				case 'vrt':	$angle = 'tb';
					break;
				case 'hor':	$angle = 'lr';
					break;
			}

			$cmd_inner = 'pnmflip -'.$angle.' ';
		}
		else // Resize 调整大小
		{
			$cmd_inner = 'pnmscale -xysize '.$this->width.' '.$this->height;
		}

		$cmd = $this->library_path.$cmd_in.' '.$this->full_src_path.' | '.$cmd_inner.' | '.$cmd_out.' > '.$this->dest_folder.'netpbm.tmp';

		$retval = 1;
		// exec() might be disabled 执行程序可能被禁用
		if (function_usable('exec'))
		{
			@exec($cmd, $output, $retval);
		}

		// Did it work? 它工作了?
		if ($retval > 0)
		{
			$this->set_error('imglib_image_process_failed');
			return FALSE;
		}

		// With NetPBM we have to create a temporary image. NetPBM我们需要创建一个临时的形象。
		// If you try manipulating the original it fails so
		// we have to rename the temp file.
		// 如果你尝试操纵原始失败所以我们必须重命名临时文件。
		copy($this->dest_folder.'netpbm.tmp', $this->full_dst_path);
		unlink($this->dest_folder.'netpbm.tmp');
		chmod($this->full_dst_path, $this->file_permissions);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Image Rotate Using GD
	 * 图像旋转使用GD
	 * @return	bool
	 */
	public function image_rotate_gd()
	{
		// Create the image handle 创建图像处理
		if ( ! ($src_img = $this->image_create_gd()))
		{
			return FALSE;
		}

		// Set the background color 设置背景颜色 
		// This won't work with transparent PNG files so we are
		// going to have to figure out how to determine the color
		// of the alpha channel in a future release.
		// 这不会使用透明的PNG文件,所以我们要找出如何确定α的颜色通道在将来发布的版本中。

		$white = imagecolorallocate($src_img, 255, 255, 255);

		// Rotate it! 旋转!
		$dst_img = imagerotate($src_img, $this->rotation_angle, $white);

		// Show the image 显示图像
		if ($this->dynamic_output === TRUE)
		{
			$this->image_display_gd($dst_img);
		}
		elseif ( ! $this->image_save_gd($dst_img)) // ... or save it
		{
			return FALSE;
		}

		// Kill the file handles 杀死文件句柄
		imagedestroy($dst_img);
		imagedestroy($src_img);

		chmod($this->full_dst_path, $this->file_permissions);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Create Mirror Image using GD
	 * 使用GD创建镜像
	 * This function will flip horizontal or vertical
	 * 该函数将水平或垂直翻转
	 * @return	bool
	 */
	public function image_mirror_gd()
	{
		if ( ! $src_img = $this->image_create_gd())
		{
			return FALSE;
		}

		$width  = $this->orig_width;
		$height = $this->orig_height;

		if ($this->rotation_angle === 'hor')
		{
			for ($i = 0; $i < $height; $i++)
			{
				$left = 0;
				$right = $width - 1;

				while ($left < $right)
				{
					$cl = imagecolorat($src_img, $left, $i);
					$cr = imagecolorat($src_img, $right, $i);

					imagesetpixel($src_img, $left, $i, $cr);
					imagesetpixel($src_img, $right, $i, $cl);

					$left++;
					$right--;
				}
			}
		}
		else
		{
			for ($i = 0; $i < $width; $i++)
			{
				$top = 0;
				$bottom = $height - 1;

				while ($top < $bottom)
				{
					$ct = imagecolorat($src_img, $i, $top);
					$cb = imagecolorat($src_img, $i, $bottom);

					imagesetpixel($src_img, $i, $top, $cb);
					imagesetpixel($src_img, $i, $bottom, $ct);

					$top++;
					$bottom--;
				}
			}
		}

		// Show the image 显示图像
		if ($this->dynamic_output === TRUE)
		{
			$this->image_display_gd($src_img);
		}
		elseif ( ! $this->image_save_gd($src_img)) // ... or save it
		{
			return FALSE;
		}

		// Kill the file handles 杀死文件句柄
		imagedestroy($src_img);

		chmod($this->full_dst_path, $this->file_permissions);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Image Watermark
	 * 图像水印
	 * This is a wrapper function that chooses the type
	 * of watermarking based on the specified preference.
	 * 这是一个包装器函数,选择水印的类型根据指定的偏好。
	 * @return	bool
	 */
	public function watermark()
	{
		return ($this->wm_type === 'overlay') ? $this->overlay_watermark() : $this->text_watermark();
	}

	// --------------------------------------------------------------------

	/**
	 * Watermark - Graphic Version
	 * 水印——图形版本
	 * @return	bool
	 */
	public function overlay_watermark()
	{
		if ( ! function_exists('imagecolortransparent'))
		{
			$this->set_error('imglib_gd_required');
			return FALSE;
		}

		// Fetch source image properties 获取源图像属性
		$this->get_image_properties();

		// Fetch watermark image properties 提取水印图像属性
		$props		= $this->get_image_properties($this->wm_overlay_path, TRUE);
		$wm_img_type	= $props['image_type'];
		$wm_width	= $props['width'];
		$wm_height	= $props['height'];

		// Create two image resources 创建两个图像资源
		$wm_img  = $this->image_create_gd($this->wm_overlay_path, $wm_img_type);
		$src_img = $this->image_create_gd($this->full_src_path);

		// Reverse the offset if necessary  必要时反向偏移量
		// When the image is positioned at the bottom
		// we don't want the vertical offset to push it
		// further down. We want the reverse, so we'll
		// invert the offset. Same with the horizontal
		// offset when the image is at the right
		// 当底部的形象定位是我们不想要的垂直偏移将进一步下降。我们想要的相反的,因此我们将反偏移量。水平偏移图像时也一样

		$this->wm_vrt_alignment = strtoupper($this->wm_vrt_alignment[0]);
		$this->wm_hor_alignment = strtoupper($this->wm_hor_alignment[0]);

		if ($this->wm_vrt_alignment === 'B')
			$this->wm_vrt_offset = $this->wm_vrt_offset * -1;

		if ($this->wm_hor_alignment === 'R')
			$this->wm_hor_offset = $this->wm_hor_offset * -1;

		// Set the base x and y axis values 设置基本的x和y轴值
		$x_axis = $this->wm_hor_offset + $this->wm_padding;
		$y_axis = $this->wm_vrt_offset + $this->wm_padding;

		// Set the vertical position 设置垂直位置
		if ($this->wm_vrt_alignment === 'M')
		{
			$y_axis += ($this->orig_height / 2) - ($wm_height / 2);
		}
		elseif ($this->wm_vrt_alignment === 'B')
		{
			$y_axis += $this->orig_height - $wm_height;
		}

		// Set the horizontal position 设置水平位置
		if ($this->wm_hor_alignment === 'C')
		{
			$x_axis += ($this->orig_width / 2) - ($wm_width / 2);
		}
		elseif ($this->wm_hor_alignment === 'R')
		{
			$x_axis += $this->orig_width - $wm_width;
		}

		// Build the finalized image 建立完成的图像
		if ($wm_img_type === 3 && function_exists('imagealphablending'))
		{
			@imagealphablending($src_img, TRUE);
		}

		// Set RGB values for text and shadow 设置文本和阴影的RGB值
		$rgba = imagecolorat($wm_img, $this->wm_x_transp, $this->wm_y_transp);
		$alpha = ($rgba & 0x7F000000) >> 24;

		// make a best guess as to whether we're dealing with an image with alpha transparency or no/binary transparency
		// 做出最好的猜测是否我们处理图像alpha透明度或根本没有/二进制透明度
		if ($alpha > 0)
		{
			// copy the image directly, the image's alpha transparency being the sole determinant of blending
			// 直接复制图像,图像的alpha透明度被混合的唯一决定因素
			imagecopy($src_img, $wm_img, $x_axis, $y_axis, 0, 0, $wm_width, $wm_height);
		}
		else
		{
			// set our RGB value from above to be transparent and merge the images with the specified opacity
			// 从上面的设置我们的RGB值与指定的透明和合并的图像不透明度
			imagecolortransparent($wm_img, imagecolorat($wm_img, $this->wm_x_transp, $this->wm_y_transp));
			imagecopymerge($src_img, $wm_img, $x_axis, $y_axis, 0, 0, $wm_width, $wm_height, $this->wm_opacity);
		}

		// We can preserve transparency for PNG images
		// 我们可以保持透明PNG图像
		if ($this->image_type === 3)
		{
			imagealphablending($src_img, FALSE);
			imagesavealpha($src_img, TRUE);
		}

		// Output the image 输出的图像
		if ($this->dynamic_output === TRUE)
		{
			$this->image_display_gd($src_img);
		}
		elseif ( ! $this->image_save_gd($src_img)) // ... or save it
		{
			return FALSE;
		}

		imagedestroy($src_img);
		imagedestroy($wm_img);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Watermark - Text Version
	 * 水印文本版本
	 * @return	bool
	 */
	public function text_watermark()
	{
		if ( ! ($src_img = $this->image_create_gd()))
		{
			return FALSE;
		}

		if ($this->wm_use_truetype === TRUE && ! file_exists($this->wm_font_path))
		{
			$this->set_error('imglib_missing_font');
			return FALSE;
		}

		// Fetch source image properties 获取源图像属性
		$this->get_image_properties();

		// Reverse the vertical offset 逆向垂直偏移量
		// When the image is positioned at the bottom
		// we don't want the vertical offset to push it
		// further down. We want the reverse, so we'll
		// invert the offset. Note: The horizontal
		// offset flips itself automatically
		// 当底部的形象定位是我们不想要的垂直偏移将进一步下降。我们想要的相反的,因此我们将反偏移量。注意:水平抵消自动翻转

		if ($this->wm_vrt_alignment === 'B')
		{
			$this->wm_vrt_offset = $this->wm_vrt_offset * -1;
		}

		if ($this->wm_hor_alignment === 'R')
		{
			$this->wm_hor_offset = $this->wm_hor_offset * -1;
		}

		// Set font width and height 设置字体的宽度和高度
		// These are calculated differently depending on
		// whether we are using the true type font or not
		// 这些计算不同取决于我们使用真正的字体
		if ($this->wm_use_truetype === TRUE)
		{
			if (empty($this->wm_font_size))
			{
				$this->wm_font_size = 17;
			}

			if (function_exists('imagettfbbox'))
			{
				$temp = imagettfbbox($this->wm_font_size, 0, $this->wm_font_path, $this->wm_text);
				$temp = $temp[2] - $temp[0];

				$fontwidth = $temp / strlen($this->wm_text);
			}
			else
			{
				$fontwidth = $this->wm_font_size - ($this->wm_font_size / 4);
			}

			$fontheight = $this->wm_font_size;
			$this->wm_vrt_offset += $this->wm_font_size;
		}
		else
		{
			$fontwidth  = imagefontwidth($this->wm_font_size);
			$fontheight = imagefontheight($this->wm_font_size);
		}

		// Set base X and Y axis values 设置基本X和Y轴值
		$x_axis = $this->wm_hor_offset + $this->wm_padding;
		$y_axis = $this->wm_vrt_offset + $this->wm_padding;

		if ($this->wm_use_drop_shadow === FALSE)
		{
			$this->wm_shadow_distance = 0;
		}

		$this->wm_vrt_alignment = strtoupper($this->wm_vrt_alignment[0]);
		$this->wm_hor_alignment = strtoupper($this->wm_hor_alignment[0]);

		// Set vertical alignment 设置垂直对齐
		if ($this->wm_vrt_alignment === 'M')
		{
			$y_axis += ($this->orig_height / 2) + ($fontheight / 2);
		}
		elseif ($this->wm_vrt_alignment === 'B')
		{
			$y_axis += $this->orig_height - $fontheight - $this->wm_shadow_distance - ($fontheight / 2);
		}

		// Set horizontal alignment 设置水平对齐
		if ($this->wm_hor_alignment === 'R')
		{
			$x_axis += $this->orig_width - ($fontwidth * strlen($this->wm_text)) - $this->wm_shadow_distance;
		}
		elseif ($this->wm_hor_alignment === 'C')
		{
			$x_axis += floor(($this->orig_width - ($fontwidth * strlen($this->wm_text))) / 2);
		}

		if ($this->wm_use_drop_shadow)
		{
			// Offset from text 抵消从文本
			$x_shad = $x_axis + $this->wm_shadow_distance;
			$y_shad = $y_axis + $this->wm_shadow_distance;

			/* Set RGB values for shadow
			 * 设置阴影的RGB值
			 * First character is #, so we don't really need it. 第一个字符是#,所以我们并不真正需要的东西。
			 * Get the rest of the string and split it into 2-length
			 * hex values:
			 * 得到字符串的其余部分并把它分割为长度十六进制值:
			 */
			$drp_color = str_split(substr($this->wm_shadow_color, 1, 6), 2);
			$drp_color = imagecolorclosest($src_img, hexdec($drp_color[0]), hexdec($drp_color[1]), hexdec($drp_color[2]));

			// Add the shadow to the source image 添加源图像的阴影
			if ($this->wm_use_truetype)
			{
				imagettftext($src_img, $this->wm_font_size, 0, $x_shad, $y_shad, $drp_color, $this->wm_font_path, $this->wm_text);
			}
			else
			{
				imagestring($src_img, $this->wm_font_size, $x_shad, $y_shad, $this->wm_text, $drp_color);
			}
		}

		/* Set RGB values for text
		 * 设置文本的RGB值
		 * First character is #, so we don't really need it. 第一个字符是#,所以我们并不真正需要的东西。
		 * Get the rest of the string and split it into 2-length
		 * hex values:
		 * 得到字符串的其余部分并把它分割为长度十六进制值:
		 */
		$txt_color = str_split(substr($this->wm_font_color, 1, 6), 2);
		$txt_color = imagecolorclosest($src_img, hexdec($txt_color[0]), hexdec($txt_color[1]), hexdec($txt_color[2]));

		// Add the text to the source image 将文本添加到源图像
		if ($this->wm_use_truetype)
		{
			imagettftext($src_img, $this->wm_font_size, 0, $x_axis, $y_axis, $txt_color, $this->wm_font_path, $this->wm_text);
		}
		else
		{
			imagestring($src_img, $this->wm_font_size, $x_axis, $y_axis, $this->wm_text, $txt_color);
		}

		// We can preserve transparency for PNG images 我们可以保持透明PNG图像
		if ($this->image_type === 3)
		{
			imagealphablending($src_img, FALSE);
			imagesavealpha($src_img, TRUE);
		}

		// Output the final image 输出最终的图像
		if ($this->dynamic_output === TRUE)
		{
			$this->image_display_gd($src_img);
		}
		else
		{
			$this->image_save_gd($src_img);
		}

		imagedestroy($src_img);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Create Image - GD
	 * 创建图像- GD
	 * This simply creates an image resource handle
	 * based on the type of image being processed
	 * 这只是创建一个图像资源处理基于图像处理的类型
	 * @param	string
	 * @param	string
	 * @return	resource
	 */
	public function image_create_gd($path = '', $image_type = '')
	{
		if ($path === '')
		{
			$path = $this->full_src_path;
		}

		if ($image_type === '')
		{
			$image_type = $this->image_type;
		}

		switch ($image_type)
		{
			case 1:
				if ( ! function_exists('imagecreatefromgif'))
				{
					$this->set_error(array('imglib_unsupported_imagecreate', 'imglib_gif_not_supported'));
					return FALSE;
				}

				return imagecreatefromgif($path);
			case 2:
				if ( ! function_exists('imagecreatefromjpeg'))
				{
					$this->set_error(array('imglib_unsupported_imagecreate', 'imglib_jpg_not_supported'));
					return FALSE;
				}

				return imagecreatefromjpeg($path);
			case 3:
				if ( ! function_exists('imagecreatefrompng'))
				{
					$this->set_error(array('imglib_unsupported_imagecreate', 'imglib_png_not_supported'));
					return FALSE;
				}

				return imagecreatefrompng($path);
			default:
				$this->set_error(array('imglib_unsupported_imagecreate'));
				return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Write image file to disk - GD
	 * 图像文件写入磁盘- GD
	 * Takes an image resource as input and writes the file
	 * to the specified destination
	 * 需要一个图像资源作为输入,并将文件写到指定的目的地
	 * @param	resource
	 * @return	bool
	 */
	public function image_save_gd($resource)
	{
		switch ($this->image_type)
		{
			case 1:
				if ( ! function_exists('imagegif'))
				{
					$this->set_error(array('imglib_unsupported_imagecreate', 'imglib_gif_not_supported'));
					return FALSE;
				}

				if ( ! @imagegif($resource, $this->full_dst_path))
				{
					$this->set_error('imglib_save_failed');
					return FALSE;
				}
			break;
			case 2:
				if ( ! function_exists('imagejpeg'))
				{
					$this->set_error(array('imglib_unsupported_imagecreate', 'imglib_jpg_not_supported'));
					return FALSE;
				}

				if ( ! @imagejpeg($resource, $this->full_dst_path, $this->quality))
				{
					$this->set_error('imglib_save_failed');
					return FALSE;
				}
			break;
			case 3:
				if ( ! function_exists('imagepng'))
				{
					$this->set_error(array('imglib_unsupported_imagecreate', 'imglib_png_not_supported'));
					return FALSE;
				}

				if ( ! @imagepng($resource, $this->full_dst_path))
				{
					$this->set_error('imglib_save_failed');
					return FALSE;
				}
			break;
			default:
				$this->set_error(array('imglib_unsupported_imagecreate'));
				return FALSE;
			break;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Dynamically outputs an image
	 * 动态输出图像
	 * @param	resource
	 * @return	void
	 */
	public function image_display_gd($resource)
	{
		header('Content-Disposition: filename='.$this->source_image.';');
		header('Content-Type: '.$this->mime_type);
		header('Content-Transfer-Encoding: binary');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT');

		switch ($this->image_type)
		{
			case 1	:	imagegif($resource);
				break;
			case 2	:	imagejpeg($resource, NULL, $this->quality);
				break;
			case 3	:	imagepng($resource);
				break;
			default:	echo 'Unable to display the image';
				break;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Re-proportion Image Width/Height
	 * 重制比例图像宽度/高度
	 * When creating thumbs, the desired width/height
	 * can end up warping the image due to an incorrect
	 * ratio between the full-sized image and the thumb.
	 * 在创建拇指时,所需的宽度/高度可以最终扭曲图像由于不正确的全尺寸图像和拇指之间的比率。
	 * This function lets us re-proportion the width/height
	 * if users choose to maintain the aspect ratio when resizing.
	 * 这个功能让我们re-proportion宽度/高度如果用户选择时保持长宽比的调整。
	 * @return	void
	 */
	public function image_reproportion()
	{
		if (($this->width === 0 && $this->height === 0) OR $this->orig_width === 0 OR $this->orig_height === 0
			OR ( ! ctype_digit((string) $this->width) && ! ctype_digit((string) $this->height))
			OR ! ctype_digit((string) $this->orig_width) OR ! ctype_digit((string) $this->orig_height))
		{
			return;
		}

		// Sanitize 清洁
		$this->width = (int) $this->width;
		$this->height = (int) $this->height;

		if ($this->master_dim !== 'width' && $this->master_dim !== 'height')
		{
			if ($this->width > 0 && $this->height > 0)
			{
				$this->master_dim = ((($this->orig_height/$this->orig_width) - ($this->height/$this->width)) < 0)
							? 'width' : 'height';
			}
			else
			{
				$this->master_dim = ($this->height === 0) ? 'width' : 'height';
			}
		}
		elseif (($this->master_dim === 'width' && $this->width === 0)
			OR ($this->master_dim === 'height' && $this->height === 0))
		{
			return;
		}

		if ($this->master_dim === 'width')
		{
			$this->height = (int) ceil($this->width*$this->orig_height/$this->orig_width);
		}
		else
		{
			$this->width = (int) ceil($this->orig_width*$this->height/$this->orig_height);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get image properties
	 * 获取图像属性
	 * A helper function that gets info about the file
	 * 一个helper函数信息文件
	 * @param	string
	 * @param	bool
	 * @return	mixed
	 */
	public function get_image_properties($path = '', $return = FALSE)
	{
		// For now we require GD but we should 现在我们需要GD但我们应该
		// find a way to determine this using IM or NetPBM 找到一种方法来确定使用IM或NetPBM

		if ($path === '')
		{
			$path = $this->full_src_path;
		}

		if ( ! file_exists($path))
		{
			$this->set_error('imglib_invalid_path');
			return FALSE;
		}

		$vals = getimagesize($path);
		$types = array(1 => 'gif', 2 => 'jpeg', 3 => 'png');
		$mime = (isset($types[$vals[2]])) ? 'image/'.$types[$vals[2]] : 'image/jpg';

		if ($return === TRUE)
		{
			return array(
					'width' =>	$vals[0],
					'height' =>	$vals[1],
					'image_type' =>	$vals[2],
					'size_str' =>	$vals[3],
					'mime_type' =>	$mime
				);
		}

		$this->orig_width	= $vals[0];
		$this->orig_height	= $vals[1];
		$this->image_type	= $vals[2];
		$this->size_str		= $vals[3];
		$this->mime_type	= $mime;

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Size calculator
	 * 大小的计算器
	 * This function takes a known width x height and
	 * recalculates it to a new size. Only one
	 * new variable needs to be known
	 * 这个函数接受一个已知宽度x高度,重新计算一个新的大小。只有一个新变量需要知道
	 *	$props = array(
	 *			'width'		=> $width,
	 *			'height'	=> $height,
	 *			'new_width'	=> 40,
	 *			'new_height'	=> ''
	 *		);
	 *
	 * @param	array
	 * @return	array
	 */
	public function size_calculator($vals)
	{
		if ( ! is_array($vals))
		{
			return;
		}

		$allowed = array('new_width', 'new_height', 'width', 'height');

		foreach ($allowed as $item)
		{
			if (empty($vals[$item]))
			{
				$vals[$item] = 0;
			}
		}

		if ($vals['width'] === 0 OR $vals['height'] === 0)
		{
			return $vals;
		}

		if ($vals['new_width'] === 0)
		{
			$vals['new_width'] = ceil($vals['width']*$vals['new_height']/$vals['height']);
		}
		elseif ($vals['new_height'] === 0)
		{
			$vals['new_height'] = ceil($vals['new_width']*$vals['height']/$vals['width']);
		}

		return $vals;
	}

	// --------------------------------------------------------------------

	/**
	 * Explode source_image
	 * 分解图片来源
	 * This is a helper function that extracts the extension
	 * from the source_image.  This function lets us deal with
	 * source_images with multiple periods, like: my.cool.jpg
	 * It returns an associative array with two elements:
	 * 这是一个helper函数,提取从source_image扩展。这个函数允许我们处理source_images与多个时期,像:my.cool。jpg它返回一个关联数组中有两个元素:
	 * $array['ext']  = '.jpg';
	 * $array['name'] = 'my.cool';
	 *
	 * @param	array
	 * @return	array
	 */
	public function explode_name($source_image)
	{
		$ext = strrchr($source_image, '.');
		$name = ($ext === FALSE) ? $source_image : substr($source_image, 0, -strlen($ext));

		return array('ext' => $ext, 'name' => $name);
	}

	// --------------------------------------------------------------------

	/**
	 * Is GD Installed?
	 * GD安装吗?
	 * @return	bool
	 */
	public function gd_loaded()
	{
		if ( ! extension_loaded('gd'))
		{
			/* As it is stated in the PHP manual, dl() is not always available
			 * and even if so - it could generate an E_WARNING message on failure
			 * 在PHP手册,dl()并不总是可用,即使如此——它可以生成一个E_WARNING消息失败
			 */
			return (function_exists('dl') && @dl('gd.so'));
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get GD version
	 * 得到GD的版本
	 * @return	mixed
	 */
	public function gd_version()
	{
		if (function_exists('gd_info'))
		{
			$gd_version = @gd_info();
			return preg_replace('/\D/', '', $gd_version['GD Version']);
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set error message
	 * 设置错误消息
	 * @param	string
	 * @return	void
	 */
	public function set_error($msg)
	{
		$CI =& get_instance();
		$CI->lang->load('imglib');

		if (is_array($msg))
		{
			foreach ($msg as $val)
			{
				$msg = ($CI->lang->line($val) === FALSE) ? $val : $CI->lang->line($val);
				$this->error_msg[] = $msg;
				log_message('error', $msg);
			}
		}
		else
		{
			$msg = ($CI->lang->line($msg) === FALSE) ? $msg : $CI->lang->line($msg);
			$this->error_msg[] = $msg;
			log_message('error', $msg);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Show error messages
	 * 显示错误消息
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function display_errors($open = '<p>', $close = '</p>')
	{
		return (count($this->error_msg) > 0) ? $open.implode($close.$open, $this->error_msg).$close : '';
	}

}
