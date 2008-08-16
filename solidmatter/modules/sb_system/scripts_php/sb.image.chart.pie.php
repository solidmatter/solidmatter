<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Core
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.image');
import('sb.tools.colors');

//------------------------------------------------------------------------------
/**
* 
*/
class Piechart extends Image {
	
	//private $imgData	= NULL;
	
	//public $iXSize	= 0;
	//public $iYSize	= 0;
	
	private $aValues			= array();
	private $aTitles			= array();
	private $aColorTable		= array();
	private $aBackgroundColor	= array('r' => 255, 'g' => 255, 'b' => 255);
	
	private $iPieShadow			= 0;
	private $iMargin			= 0;
	private $flOversample		= 0;
	private $iStartDegree		= -15;
	private $flShadowBrightness	= 0.5;
	
	private $bDrawTitles		= TRUE;
	private $bDrawPercentages	= TRUE;
	private $bDrawValues		= FALSE;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @access 
	* @param
	* @return 
	*/
	function __construct($iWidth = 300, $iHeight = 200, $iPieShadow = 10, $iMargin = 15, $flOversample = 1) {
		
		if ($iWidth <= 0 || $iHeight <= 0 || $iPieShadow < 0 || $iMargin < 0 || $flOversample < 1 || $flOversample > 4) {
			throw new ImageProcessingException('Piechart::__construct - height and width have to be greater than 0, margin and pieheight have to be 0 or positive, oversample must be between 0 and 4');	
		}
		
		//imageantialias($this->imgData, TRUE);
		$this->iWidth		= $iWidth;
		$this->iHeight		= $iHeight;
		$this->iPieShadow	= $iPieShadow;
		$this->iMargin		= $iMargin;
		$this->flOversample	= $flOversample;
	}
	
	function __destruct() {
		Image::__destruct();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @access 
	* @param
	* @return 
	*/
	public function SetValues($aValues, $aTitles = array()) {
		
		if (count($aValues) < 2) {
			die('Piechart::SetValues - there have to be at least 2 values in the array');
		}
		if (count($aTitles) != 0 && count($aValues) != count($aTitles)) {
			die('Piechart::SetValues - the number of values has to be the same as the number of titles');
		}
		
		$this->aValues = $aValues;
		$this->aTitles = $aTitles;
		
		$iNumValues = count($aValues);
		$bAlternate = TRUE;
		
		for ($i=0; $i<$iNumValues; $i++) {
			$aColor['h'] = (1 / $iNumValues) * $i * 0.6;
			if ($bAlternate) {
				$aColor['l'] = 0.45;
				$aColor['s'] = 0.3;
			} else {
				$aColor['l'] = 0.75;
				$aColor['s'] = 0.5;
			}
			$bAlternate = !$bAlternate;
			$this->aColorTable[$i] = hsl2rgb($aColor);
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @access 
	* @param
	* @return 
	*/
	public function SetColors($aColors, $aBackgroundColor = NULL) {
		
		$this->aColorTable = array();
		foreach($aColors as $sColor) {
			$this->aColorTable[] = hex2rgb($sColor);
		}
		
		if ($aBackgroundColor != NULL) {
			$this->aBackgroundColor = $aBackgroundColor;
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @access 
	* @param
	* @return 
	*/
	public function GetColors() {
		
		$aColors = array();
		
		foreach ($this->aColorTable as $aColor) {
			$aColors[] = rgb2hex($aColor);
		}
		
		return ($aColors);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @access 
	* @param
	* @return 
	*/
	public function DrawChart() {
		
		$iPieWidth		= $this->iWidth;
		$iPieHeight		= $this->iHeight;
		$iTotalWidth	= $this->iWidth;
		$iTotalHeight	= $this->iHeight;
		$iLegendWidth	= 0;
		$iLegendHeight	= 0;
		
		// additinal calculations if legend is drawn
		if (count($this->aTitles) != 0) {
			
			$iPercentagesWidth	= 0;
			$iTitleWidth		= 0;
			$iValueLength		= 0;
			
			if ($this->bDrawPercentages) {
				$iPercentagesWidth += 30;
			}
			if ($this->bDrawTitles)	{ 
				$iMaxLength = 0;
				foreach ($this->aTitles as $sTitle) {
					$iMaxLength = max($iMaxLength, mb_strlen($sTitle));
				}
				$iTitleWidth += 6 * $iMaxLength;
			}
			if ($this->bDrawValues) {
				$iValueLength += 30;
			}
			
			$iLegendWidth	= 20 + $iPercentagesWidth + $iTitleWidth + $iValueLength;
			$iLegendHeight	= $this->iMargin + count($this->aTitles) * 15;
			
			$iTotalWidth	= $iTotalWidth + $iLegendWidth;
			$iTotalHeight	= max($iTotalHeight, $iLegendHeight);
			
		}
		
		// create image
		if (!$this->imgData = imagecreatetruecolor($iTotalWidth, $iTotalHeight)) {
			return (FALSE);
		}
		
		// initial variables
		$iCenterX	= round($this->iWidth / 2);
		$iCenterY	= round(($this->iHeight - $this->iPieShadow) / 2);
		$iWidth		= round($this->iWidth - 2 * $this->iMargin);
		$iHeight	= round($this->iHeight - 2 * $this->iMargin - $this->iPieShadow);
		$iPieShadow		= $this->iPieShadow;
		$iImageWidth	= $this->iWidth;
		$iImageHeight	= $this->iHeight;
		$imgChart		= $this->imgData;
		
		// oversample variables if necessary
		if ($this->flOversample != 1) {
			$iCenterX	= round($iCenterX * $this->flOversample);
			$iCenterY	= round($iCenterY * $this->flOversample);
			$iWidth		= round($iWidth * $this->flOversample);
			$iHeight	= round($iHeight * $this->flOversample);
			$iPieShadow		= round($iPieShadow * $this->flOversample);
			$iImageWidth	= round($this->iWidth * $this->flOversample);
			$iImageHeight	= round($this->iHeight * $this->flOversample);
			$imgChart		= imagecreatetruecolor($iImageWidth, $iImageHeight);
		}
		
		// allocate the colors
		$rBackground = imagecolorallocate($this->imgData, $this->aBackgroundColor['r'], $this->aBackgroundColor['g'], $this->aBackgroundColor['b']);
		imagefilledrectangle($this->imgData, 0, 0, $iTotalWidth-1, $iTotalHeight-1, $rBackground);
		if ($this->flOversample != 1) {
			$rBackground = imagecolorallocate($imgChart, $this->aBackgroundColor['r'], $this->aBackgroundColor['g'], $this->aBackgroundColor['b']);
			imagefilledrectangle($imgChart, 0, 0, $iImageWidth-1, $iImageHeight-1, $rBackground);
		}
		foreach ($this->aColorTable as $i => $aRGB) {
			$aColors[$i] = imagecolorallocate($imgChart, $aRGB['r'], $aRGB['g'], $aRGB['b']);
			if ($this->iPieShadow != 0) {
				$aHSL = rgb2hsl($aRGB);
				$aHSL['l'] *= $this->flShadowBrightness;
				if ($aHSL['l'] > 1) {
					$aHSL['l'] = 1;
				}
				$aRGB = hsl2rgb($aHSL);
				$aShadowColors[$i] = imagecolorallocate($imgChart, $aRGB['r'], $aRGB['g'], $aRGB['b']);
			}
		}
		
		// preprocess the degree values
		$flSum = 0;
		foreach ($this->aValues as $flValue) {
			$flSum += $flValue;
		}
		foreach ($this->aValues as $flValue) {
			$aDegrees[]	= round(360 * $flValue / $flSum);
		}
		
		// make the 3D effect
		$iNumValues = count($this->aValues);
		
		for ($iCurrentCenterY=$iCenterY+$iPieShadow; $iCurrentCenterY>=$iCenterY; $iCurrentCenterY--) {
			$iStartDegree = $this->iStartDegree;
			for ($i=0; $i<$iNumValues; $i++) {
				if ($iCurrentCenterY == $iCenterY) {
					$rColor = $aColors[$i];
				} else {
					$rColor = $aShadowColors[$i];
				}
				$iEndDegree = $iStartDegree + $aDegrees[$i];
				imagefilledarc($imgChart, $iCenterX, $iCurrentCenterY, $iWidth, $iHeight, $iStartDegree, $iEndDegree, $rColor, IMG_ARC_EDGED|IMG_ARC_NOFILL);
				$iStartDegree = $iEndDegree;
			}
		}
		
		// draw the pie
		$iStartDegree = $this->iStartDegree;
		for ($i=0; $i<$iNumValues; $i++) {
			$iEndDegree = $iStartDegree + $aDegrees[$i];
			imagefilledarc($imgChart, $iCenterX, $iCenterY, $iWidth, $iHeight, $iStartDegree, $iEndDegree, $aColors[$i], IMG_ARC_PIE);
			$iStartDegree = $iEndDegree;
		}
		
		// resample the pie if necessary
		if ($this->flOversample != 1) {
			imagecopyresampled($this->imgData, $imgChart, 0, 0, 0, 0, $this->iWidth, $this->iHeight, $iImageWidth, $iImageHeight);
			imagedestroy($imgChart);
		}
		
		// draw legend if necessary
		$rBlack = imagecolorallocate($this->imgData, 0, 0, 0);
		for ($i=0; $i<$iNumValues; $i++) {
			
			$iLineHeight = $i*15+$this->iMargin;
			
			imagefilledrectangle($this->imgData, $iPieWidth+6, $iLineHeight+1, $iPieWidth+16, $iLineHeight+11, $aColors[$i]);
			imagerectangle($this->imgData, $iPieWidth+6, $iLineHeight+1, $iPieWidth+16, $iLineHeight+11, $rBlack);
			
			$sLabel = '';
			if ($this->bDrawPercentages) {
				if ($this->aValues[$i] / $flSum < 0.1) {
					$sLabel .= ' ';
				}
				$sLabel .= number_format($this->aValues[$i] / $flSum * 100, 2, gls('FORMAT_DECIMALPOINT'), '').'%';
			}
			if ($this->bDrawTitles && count($this->aTitles) > 0)	{ 
				$sLabel .= ' '.$this->aTitles[$i];
			}
			if ($this->bDrawValues) {
				$sLabel .= ' ('.$this->aValues[$i].')';
			}
			
			imagestring($this->imgData, 2, $iPieWidth+20, $iLineHeight, $sLabel, $rBlack);
			
		}
		
		
	}
	
}

?>