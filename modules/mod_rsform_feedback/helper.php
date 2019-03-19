<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class RSFormProImageText
{
	var $string = '';
	var $string_size = 10;
	var $string_angle = 0;
	var $colors = array('text-color' => '#000000', 'bg-color' => '#FFFFFF');
	var $font = '';
	
	var $_image = null;
	var $_image_w = 0;
	var $_image_h = 0;
	var $_image_types = array();
	var $_image_type = 'png';
	var $_image_transparent = false;
	
	var $caching = true;
	var $hash = '';
	
	function RSFormProImageText($options=array())
	{
		$this->_getSupportedImages();
		
		if (isset($options['string']))
			$this->string = $options['string'];
		if (isset($options['size']))
			$this->string_size = (int) $options['size'];
		if (isset($options['angle']))
			$this->string_angle = $options['angle'];
		if (isset($options['text-color']))
			$this->colors['text-color'] = $options['text-color'];
		if (isset($options['bg-color']))
			$this->colors['bg-color'] = $options['bg-color'];
		if (isset($options['font']))
			$this->font = JPATH_SITE.'/components/com_rsform/assets/fonts/'.$options['font'];
		
		// Set image type
		if (isset($options['type']) && in_array(strtolower($options['type']), $this->_image_types))
			$this->_image_type = strtolower($options['type']);
			
		if (isset($options['transparent']) && $this->_image_type != 'jpeg')
			$this->_image_transparent = (bool) $options['transparent'];
		
		if (isset($options['caching']))
			$this->caching = (bool) $options['caching'];
		
		$this->hash = md5(serialize($options));
	}
	
	function _getSupportedImages()
	{
		$supported = imagetypes();
		if ($supported && IMG_GIF)
			$this->_image_types[] = 'gif';
		if ($supported && IMG_JPG)
			$this->_image_types[] = 'jpeg';
		if ($supported && IMG_PNG)
			$this->_image_types[] = 'png';
	}
	
	function _convertColor($color)
	{
		$rgb = sscanf($color, '#%2x%2x%2x');
		return imagecolorallocate($this->_image, $rgb[0], $rgb[1], $rgb[2]);
	}
	
	function _createImageFromCache()
	{
		jimport('joomla.filesystem.folder');
		
		if (!JFolder::exists(JPATH_SITE.'/cache/mod_rsform_feedback'))
			JFolder::create(JPATH_SITE.'/cache/mod_rsform_feedback');
		
		if (file_exists(JPATH_SITE.'/cache/mod_rsform_feedback/'.$this->hash.'.'.$this->_image_type))
		{
			jimport('joomla.filesystem.file');
			
			header('Content-type: image/'.$this->_image_type);
			echo JFile::read(JPATH_SITE.'/cache/mod_rsform_feedback/'.$this->hash.'.'.$this->_image_type);
			$app = JFactory::getApplication();
			$app->close();
		}
	}
	
	function _createImage()
	{
		$this->_image_box = imagettfbbox($this->string_size, 0, $this->font, $this->properText($this->string));
		$this->_image_w = abs($this->_image_box[0]) + abs($this->_image_box[2]);
		$this->_image_h = abs($this->_image_box[1]) + abs($this->_image_box[5]);
		
		if (function_exists('imagecreatetruecolor'))
			return $this->_image = imagecreatetruecolor($this->_image_w + 5, $this->_image_h);
		
		return $this->_image = imagecreate($this->_image_w + 5, $this->_image_h);
	}
	
	function _showImage()
	{
		$function = 'image'.$this->_image_type;
		if (function_exists($function) && is_callable($function))
		{
			@ob_end_clean();
			header('Content-type: image/'.$this->_image_type);
			if ($this->caching)
				@call_user_func($function, $this->_image, JPATH_SITE.'/cache/mod_rsform_feedback/'.$this->hash.'.'.$this->_image_type);
			
			return @call_user_func($function, $this->_image);
		}
		
		return false;
	}
	
	function _clearImage()
	{
		return imagedestroy($this->_image);
	}
	
	function _fillBackground()
	{
		return imagefilledrectangle($this->_image, 0, 0, imagesx($this->_image), imagesy($this->_image), $this->_convertColor($this->colors['bg-color']));
	}
	
	function _transparentBackground()
	{
		return imagecolortransparent($this->_image, $this->_convertColor($this->colors['bg-color']));
	}
	
	function _rotateImage()
	{
		$this->_image = imagerotate($this->_image, $this->string_angle, $this->_convertColor($this->colors['bg-color']));
	}
	
	function _writeText()
	{
		$x = $this->_image_box[0];
		$y = $this->_image_h / 2 * 1.8;
		
		return imagettftext($this->_image, $this->string_size, 0, $x, $y, $this->_convertColor($this->colors['text-color']), $this->font, $this->properText($this->string));
	}
	
	function properText($text){
		if (function_exists('mb_convert_encoding')) {
			$text = mb_convert_encoding($text, "HTML-ENTITIES", "UTF-8");
			$text = preg_replace('~^(&([a-zA-Z0-9]);)~',htmlentities('${1}'),$text);
		}
		return $text;
	}
	
	function showImage()
	{
		if ($this->caching)
			$this->_createImageFromCache();
		if ($this->_createImage())
		{
			$this->_fillBackground();
			$this->_writeText();
			if ($this->string_angle > 0)
				$this->_rotateImage();
			if ($this->_image_transparent)
				$this->_transparentBackground();
			$this->_showImage();
			$this->_clearImage();
		}
	}
}