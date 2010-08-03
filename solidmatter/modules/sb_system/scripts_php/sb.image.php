<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Core
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

if (!defined('JPG'))			define('JPG', 1);
if (!defined('GIF'))			define('GIF', 2);
if (!defined('PNG'))			define('PNG', 4);

//------------------------------------------------------------------------------
/**
* 
*/
class Image {
	
	const KEEPASPECT = 210;
	const LOSEASPECT = 211;
	const DOWNSAMPLE = 1;
	const UPSAMPLE = 2;
	
	const TOPLEFT = 220;
	const TOPCENTER = 221;
	const TOPRIGHT = 222;
	const BOTTOMLEFT = 223;
	const BOTTOMCENTER = 224;
	const BOTTOMRIGHT = 225;
	const LEFTCENTER = 226;
	const RIGHTCENTER = 227;
	const CENTER = 228;
	
	const FROMFILE = 400;
	const FROMSTRING = 401;
	const BLANK = 402;
	
	//--------------------------------------------------------------------------
	/**
	* The resource-link to the image.
	* @var image-resource
	*/
	protected $resData	= NULL;
	
	//--------------------------------------------------------------------------
	/**
	* Width of the image
	* @var integer
	*/
	public $iWidth	= 0;
	/**
	* Height of the image.
	* @var integer
	*/
	public $iHeight	= 0;
	/**
	* Quality to use with JPG-Images
	* @var integer
	*/
	private $iQuality = 95;
	/**
	* Output as progressive jpg?
	* @var integer
	*/
	private $bProgressive = TRUE;
	
	//--------------------------------------------------------------------------
	//##########################################################################
	//--------------------------------------------------------------------------
	/**
	* 
	* @param
	* @return 
	*/
	public function __construct($eCreationType = Image::BLANK, $mVar1 = 0, $mVar2 = 0) {
		
		switch ($eCreationType) {
			
			case Image::BLANK:
				if ($mVar1 <= 0 || $mVar2 <= 0) {
					throw new ImageProcessingException('height and width have to be greater than 0');
				}
				if (!$this->resData = imagecreatetruecolor($mVar1, $mVar2)) {
					throw new ImageProcessingException('imagecreatetruecolor() failed');
				}
				break;
				
			case Image::FROMFILE:
				$this->load($mVar1);
				break;
				
				
			case Image::FROMSTRING:
				if (!$this->resData = imagecreatefromstring($mVar1)) {
					throw new ImageProcessingException('imagecreatetruecolor() failed');
				}
				break;
				
			
			
		}		
		
		$this->iWidth	= imagesx($this->resData);
		$this->iHeight	= imagesy($this->resData);
		
		return (TRUE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param
	* @return 
	*/
	public function __destruct() {
		if ($this->resData != NULL) {
			imagedestroy($this->resData);
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param
	* @return 
	*/
	public function __get($sMemberName) {
		switch ($sMemberName) {
			case 'iQuality':
			case 'resData':
				throw new ImageProcessingException('unable to access member '.$sMemberName);
			case 'iWidth':
				return (imagesx($this->resData));
			case 'iHeight':
				return (imagesy($this->resData));
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param
	* @return 
	*/
	/*public function __set($sMemberName, $mValue) {
		if ($sMemberName != 'iQuality') {
			throw new sbException($sMemberName.' is readonly');	
		}
		$this->iQuality = $mValue;
	}*/
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param
	* @return 
	*/
	public function copy() {
		$imgNew = new Image(Image::BLANK, imagesx($this->resData), imagesy($this->resData));
		imagecopy($imgNew->getData(), $this->resData, 0, 0, 0, 0, imagesx($this->resData), imagesy($this->resData));
		return ($imgNew);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param
	* @return 
	*/
	public function setOptions($aOptions) {
		
		if (isset($aOptions['antialias'])) {
			if ($aOptions['antialias'] == TRUE) {
				imageantialias($this->resData, 1);
			} else {
				imageantialias($this->resData, 0);
			}
		}
		
		if (isset($aOptions['interlace'])) {
			if ($aOptions['interlace'] == TRUE) {
				imageinterlace($this->resData, 1);
			} else {
				imageinterlace($this->resData, 0);
			}
		}
		
		if (isset($aOptions['quality'])) {
			if (is_numeric($aOptions['quality'])) {
				$this->iQuality = (integer) $aOptions['quality'];
			}
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param
	* @return 
	*/
	public function load($sFilename) {
		
		import('sb.tools.mime');
		
		switch (get_mimetype($sFilename)) {
			case 'image/gif':
				$this->resData = imagecreatefromgif($sFilename);
				break;
			case 'image/jpeg':
				$this->resData = imagecreatefromjpeg($sFilename);
				break;
			case 'image/x-png':
				$this->resData = imagecreatefrompng($sFilename);
				break;
			default:
				throw new ImageProcessingException('invalid mimetype ('.mime_content_type($sFilename).')');
		}
		
		$this->iWidth = imagesx($this->resData);
		$this->iHeight = imagesy($this->resData);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @access 
	* @param
	* @return 
	*/
	public function save($sFilename, $eType) {
		
		switch ($eType) {
			case GIF:
				$bSuccess = imagegif($this->resData, $sFilename);
				break;
			case JPG:
				$bSuccess = imagejpeg($this->resData, $sFilename);
				break;
			case PNG:
				$bSuccess = imagepng($this->resData, $sFilename);
				break;
		}
		
		return ($bSuccess);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @access 
	* @param
	* @return 
	*/
	public function output($eType, $bProgressive = FALSE) {
		
		switch ($eType) {
			case GIF:
				$sMimeType = 'image/gif';
				break;
			case JPG:
				$sMimeType = 'image/jpeg';
				break;
			case PNG:
				$sMimeType = 'image/png';
				break;
		}
		
		header('Content-Type: '.$sMimeType);
		
		switch ($sMimeType) {
			case 'image/gif':
				imagegif($this->resData);
				break;
			case 'image/jpeg':
				imageinterlace($this->resData, (int) $this->bProgressive);
				imagejpeg($this->resData, '', $this->iQuality);
				break;
			case 'image/png':
				imageinterlace($this->resData, 1);
				imagepng($this->resData);
				break;
		}
		
		exit();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @access 
	* @param
	* @return 
	*/
	public function resample($iWidth, $iHeight, $eMode = Image::KEEPASPECT, $eDirection = NULL) {
		
		import('sb.tools.images');
		
		if ($eDirection == NULL) {
			$eDirection = Image::DOWNSAMPLE | Image::UPSAMPLE;
		}
		
		$resNew = image_resample($this->resData, $iWidth, $iHeight, $eMode, $eDirection);
		
		$this->iWidth = $iWidth;
		$this->iHeight = $iHeight;
		
		imagedestroy($this->resData);
		$this->resData = $resNew;
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param
	* @return 
	*/
	public function crop($eOrigin, $iWidth, $iHeight, $iOffsetX, $iOffsetY) {
		
		import('sb.tools.images');
		
		$resNew = image_crop($this->resData, $eOrigin, $iWidth, $iHeight, $iOffsetX, $iOffsetY);
		
		$this->iWidth = $iWidth;
		$this->iHeight = $iHeight;
		
		imagedestroy($this->resData);
		$this->resData = $resNew;
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @access 
	* @param
	* @return 
	*/
	public function getHSL($iNumSamples = 200, $iNumQuardantsX = 1, $iNumQuadrantsY = 1, $iQuadrantX = 0, $iQuadrantY = 0) {
		
		import('sb.tools.images');
		import('sb.tools.colors');
		
		$iWidth = $this->iWidth / $iNumQuardantsX;
		$iHeight = $this->iHeight / $iNumQuadrantsY;
		
		$iOffsetX = $iQuadrantX * $iWidth;
		$iOffsetY = $iQuadrantY * $iHeight;
		
		$aHSL = image_gethsl($this->resData, $iNumSamples, round($iWidth), round($iHeight), round($iOffsetX), round($iOffsetY));
		
		return ($aHSL);
		
	}
	
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @access 
	* @param
	* @return 
	*/
	public function applyFilter($eFilterType, $mArg1 = 0, $mArg2 = 0, $mArg3 = 0) {
		
		if ($eFilterType == IMG_FILTER_BRIGHTNESS || $eFilterType == IMG_FILTER_CONTRAST) {
			$bSuccess = imagefilter($this->resData, $eFilterType, $mArg1);
		} else {
			$bSuccess = imagefilter($this->resData, $eFilterType, $mArg1, $mArg2, $mArg3);
		}
		
		if (!$bSuccess) {
			throw new ImageProcessingException('filter was not applied ('.$eFilterType.'|'.$mArg1.'|'.$mArg2.'|'.$mArg3.')');	
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @access 
	* @param
	* @return 
	*/
	public function mix($imgOther, $iStrength, $iDestX = 0, $iDestY = 0, $iSourceX = 0, $iSourceY = 0, $iWidth = NULL, $iHeight = NULL) {
		
		$imgDataDest = $this->getData();
		$imgDataSource = $imgOther->getData();
		if ($iWidth === NULL) {
			$iWidth = imagesx($imgDataSource);
		}
		if ($iHeight === NULL) {
			$iHeight = imagesy($imgDataSource);
		}
		imagecopymerge($imgDataDest, $imgDataSource, $iDestX, $iDestY, $iSourceX, $iSourceY, $iWidth, $iHeight, $iStrength);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @access 
	* @param
	* @return 
	*/
	public function getData() {
		return ($this->resData);
	}
		
}

?>