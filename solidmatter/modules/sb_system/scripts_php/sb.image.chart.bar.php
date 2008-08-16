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
import('sb.tools.math.scales');

//------------------------------------------------------------------------------
/**
* 
*/
class Barchart extends Image {
	
	//private $imgData	= NULL;
	
	//public $iXSize	= 0;
	//public $iYSize	= 0;
	
	private $aValues			= array();
	private $aLabels			= array();
	private $aColorTable		= array();
	private $aBackgroundColor	= array('r' => 255, 'g' => 255, 'b' => 255);
	private $aBackgroundColor2	= array('r' => 245, 'g' => 245, 'b' => 245);
	private $aGridColor			= array('r' => 0, 'g' => 0, 'b' => 0);
	private $aTextColor			= array('r' => 0, 'g' => 0, 'b' => 0);
	
	private	$flTopValue			= 10;
	private $flBottomValue		= 0;
	private $iBarShadow			= 0;
	private $iBarPadding		= 5;
	private $iMargin			= 0;
	private $iScaleSpikes		= 3;
	private $iLabelSpikes		= 20;
	private $flShadowBrightness	= 0.5;
	private $flLightBrightness	= 1.1;
	
	private $iNumDecimals		= 0;
	private $iNumScales			= 7;
	private $flScaleDistance	= 2;
	private $flMinMinValue		= -6;
	private $flMinMaxValue		= 6;
	
	private $bDrawLabels		= TRUE;
	private $bDrawScale			= TRUE;
	private $bDrawValues		= FALSE;
	private $bDrawXGrid			= FALSE;
	private $bDrawYGrid			= TRUE;
	private $bDrawXSpikes		= TRUE;
	private $bDrawYSpikes		= TRUE;
	private $bVerticalLabels	= TRUE;
	private $bAvoidContact		= FALSE;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @access 
	* @param
	* @return 
	*/
	function __construct($iWidth = 300, $iHeight = 200, $iBarShadow = 10, $iMargin = 15) {
		
		if ($iWidth <= 0 || $iHeight <= 0 || $iBarShadow < 0 || $iMargin < 0) {
			throw new ImageProcessingException('Image::__construct - height and width have to be greater than 0, margin and pieheight have to be 0 or positive');	
		}
		
		//imageantialias($this->imgData, TRUE);
		$this->iWidth		= $iWidth;
		$this->iHeight		= $iHeight;
		$this->iBarShadow	= $iBarShadow;
		$this->iMargin		= $iMargin;
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
	public function setValues($aValues, $aLabels = array()) {
		
		if (count($aValues) < 2) {
			throw new ImageProcessingException('Barchart::SetValues - there have to be at least 2 values in the array');
		}
		if (count($aLabels) != 0 && count($aValues) != count($aLabels)) {
			throw new ImageProcessingException('Barchart::SetValues - the number of values has to be the same as the number of titles');
		}
		
		$this->aValues = $aValues;
		$this->aLabels = $aLabels;
		
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
	public function setColors($aColors, $aBackgroundColor = NULL) {
		
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
	public function getColors() {
		
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
	public function drawChart() {
		
		$iDiagramWidth	= $this->iWidth;
		$iDiagramHeight	= $this->iHeight;
		$iTotalWidth	= $this->iWidth;
		$iTotalHeight	= $this->iHeight;
		$iLegendWidth	= 0;
		$iLegendHeight	= 0;
		$iScaleWidth	= 0;
		$iScaleHeight	= 0;
		
		// calculate diagram dimensions
		$flMax = 0;
		$flMin = 0;
		
		foreach ($this->aValues as $flValue) {
			$flMax = max($flMax, $flValue);
			$flMin = min($flMin, $flValue);
		}
		if ($this->bAvoidContact) {
			$flMax *= 1.05;
			$flMin *= 1.05;
		}
		if ($flMin > $this->flMinMinValue) {
			$flMin = $this->flMinMinValue;
		}
		if ($flMax < $this->flMinMaxValue) {
			$flMax = $this->flMinMaxValue;
		}
		
		$aScale = get_diagramscale($flMax, $flMin, $this->iNumScales, SCALE_QUANTITY);
		if ($this->flScaleDistance != 0) {
			$aScale = get_diagramscale($flMax, $flMin, $this->flScaleDistance, SCALE_DISTANCE);
		}
		
		// draw scale
		if ($this->bDrawScale) {
			
			$iNumDigits = 0;
			
			$iNumDigits = mb_strlen($flMax);
			$iNumDigits = max($iNumDigits, mb_strlen($flMin));
			if ($this->iNumDecimals != 0) {
				$iNumDigits += 1 + $this->iNumDecimals;
			}
			$iScaleWidth	= $iNumDigits * 8 + 6;
			$iDiagramWidth	-= $iScaleWidth;
		}
		
		$iDiagramWidth	-= 2 * $this->iMargin;
		$iDiagramHeight	-= 2 * $this->iMargin;
		
		$iTLX = $this->iMargin + $iScaleWidth;
		$iTLY = $this->iMargin;
		$iTRX = $iTLX + $iDiagramWidth - 1;
		$iTRY = $this->iMargin;
		$iBLX = $iTLX;
		$iBLY = $iTLY + $iDiagramHeight - 1;
		$iBRX = $iTRX;
		$iBRY = $iBLY;
		
		if ($this->bDrawScale && $this->iMargin < 5) {
			$iDiagramHeight -= 6;
			$iTLY += 6;
			$iTRY += 6;
		}
		
		$flSectionWidth = $iDiagramWidth / count($this->aValues);
		$flSectionHeight = $iDiagramHeight / ($aScale['quantity'] - 1);
		$iBarWidth = floor($flSectionWidth - (2 * $this->iBarPadding) - $this->iBarShadow);
		
		// calculate total dimensions
		if ($this->bVerticalLabels && $this->bDrawLabels) {
			$iMaxLength = 0;
			foreach ($this->aLabels as $sLabel) {
				$iMaxLength = max($iMaxLength, mb_strlen($sLabel));
			}
			$iLegendHeight += 8 * $iMaxLength + 8;
		}
		
		//$iTotalWidth	+= $iScaleWidth;
		$iTotalHeight	+= $iLegendHeight;
		
		// create image
		if (!$this->imgData = imagecreatetruecolor($iTotalWidth, $iTotalHeight)) {
			return (FALSE);
		}
		
		// allocate the colors
		$rBackground = imagecolorallocate($this->imgData, $this->aBackgroundColor['r'], $this->aBackgroundColor['g'], $this->aBackgroundColor['b']);
		$rBackground2 = imagecolorallocate($this->imgData, $this->aBackgroundColor2['r'], $this->aBackgroundColor2['g'], $this->aBackgroundColor2['b']);
		$rGrid = imagecolorallocate($this->imgData, $this->aGridColor['r'], $this->aGridColor['g'], $this->aGridColor['b']);
		$rText = imagecolorallocate($this->imgData, $this->aTextColor['r'], $this->aTextColor['g'], $this->aTextColor['b']);
		imagefilledrectangle($this->imgData, 0, 0, $iTotalWidth-1, $iTotalHeight-1, $rBackground);
		foreach ($this->aColorTable as $i => $aRGB) {
			$aColors[$i] = imagecolorallocate($this->imgData, $aRGB['r'], $aRGB['g'], $aRGB['b']);
			if ($this->iBarShadow != 0) {
				$aHSLDark = rgb2hsl($aRGB);
				$aHSLLight = rgb2hsl($aRGB);
				$aHSLDark['l'] *= $this->flShadowBrightness;
				$aHSLLight['l'] *= $this->flLightBrightness;
				if ($aHSLDark['l'] > 1) {
					$aHSLDark['l'] = 1;
				}
				if ($aHSLLight['l'] > 1) {
					$aHSLLight['l'] = 1;
				}
				$aRGBDark = hsl2rgb($aHSLDark);
				$aRGBLight = hsl2rgb($aHSLLight);
				$aShadowColors[$i] = imagecolorallocate($this->imgData, $aRGBDark['r'], $aRGBDark['g'], $aRGBDark['b']);
				$aLightColors[$i] = imagecolorallocate($this->imgData, $aRGBLight['r'], $aRGBLight['g'], $aRGBLight['b']);
				
			}
		}
		
		// draw the frame
		imagefilledrectangle($this->imgData, $iTLX, $iTLY, $iBRX, $iBRY, $rBackground2);
		imageline($this->imgData, $iTLX, $iTLY, $iTRX, $iTRY, $rGrid);
		imageline($this->imgData, $iTRX, $iTRY, $iBRX, $iBRY, $rGrid);
		imageline($this->imgData, $iTLX, $iTLY, $iBLX, $iBLY, $rGrid);
		imageline($this->imgData, $iBLX, $iBLY, $iBRX, $iBRY, $rGrid);
		
		// draw horizontal (scale) lines
		$iLLX = $iTLX;
		$iLRX = $iTRX;
		for ($i=0; $i<$aScale['quantity']; $i++) {
			$iLY = round($iBLY - $i * $flSectionHeight);
			if ($iLY < $iTRY) {
				$iLY = $iTRY;
			}
			if ($this->bDrawYGrid) {
				imageline($this->imgData, $iLLX, $iLY, $iLRX, $iLY, $rGrid);
			}
			if ($this->bDrawYSpikes) {
				imageline($this->imgData, $iLLX - $this->iScaleSpikes, $iLY, $iLLX, $iLY, $rGrid);
			}
			$flValue = $aScale['bottom_value'] + $i * $aScale['distance'];
			if ($flValue == 0) {
				$iZeroY = $iLY;
			}
			$sValue = number_format($flValue, $this->iNumDecimals, gls('DECIMAL_POINT'), '');
			if ($this->bDrawScale) {
				$iTY = $iLY - 6;
				$iTX = $this->iMargin;
				$iTX += 6 * ($iNumDigits - mb_strlen((string) $sValue));
				imagestring($this->imgData, 2, $iTX, $iTY, $sValue, $rText);	
			}
		}
		
		// draw vertical (section) lines
		$iLBY = $iBLY;
		$iLTY = $iTLY;
		for ($i=0; $i<$aScale['quantity']; $i++) {
			$iLX = round($iTLX + $i * $flSectionWidth);
			if ($iLX > $iTRX) {
				$iLX = $iTRX;
			}
			if ($this->bDrawXGrid) {
				imageline($this->imgData, $iLX, $iLTY, $iLX, $iLBY, $rGrid);
			}
			if ($this->bDrawXSpikes) {
				imageline($this->imgData, $iLX, $iLBY, $iLX, $iLBY + $this->iLabelSpikes, $rGrid);
			}
			if ($this->bDrawLabels && $i != 0) {
				if ($this->bVerticalLabels) {
					$iTX = round($iLX - 0.5 * $flSectionWidth) - 4;
					$iTY = $iBLY + 8;
					$sLabel = $this->aLabels[$i-1];
					$iTY += 6 * mb_strlen($sLabel);
					imagestringup($this->imgData, 2, $iTX, $iTY, $sLabel, $rText);
				}
				
			}
			
		}
		
		// draw bars
		$i = 0;
		$flOneDistance = $iDiagramHeight / $aScale['range'];
		for ($i=0; $i<count($this->aValues); $i++) {
			$iBarWidth = round($iDiagramWidth / count($this->aValues) - 2 * $this->iBarPadding);
			$iBarLeftX = round($iTLX + $i * $iDiagramWidth / count($this->aValues) + $this->iBarPadding);
			$iBarRightX = $iBarLeftX + $iBarWidth;
			if ($this->aValues[$i] >= 0) {
				$iBarTopY = round($iZeroY - abs($this->aValues[$i]) * $flOneDistance);
				$iBarTLX = $iBarLeftX;
				$iBarTLY = $iBarTopY;
				$iBarBRX = $iBarRightX;
				$iBarBRY = $iZeroY;
			} else {
				$iBarBottomY = round($iZeroY + abs($this->aValues[$i]) * $flOneDistance);
				$iBarTLX = $iBarLeftX;
				$iBarTLY = $iZeroY;
				$iBarBRX = $iBarRightX;
				$iBarBRY = $iBarBottomY;
			}
			
			if ($iBarTLY < $iTLY) {
				$iBarTLY = $iTLY;
			}
			if ($iBarBRY > $iBRY) {
				$iBarBRY = $iBRY;
			}
			
			imagefilledrectangle($this->imgData, $iBarTLX, $iBarTLY, $iBarBRX, $iBarBRY, $aColors[$i]);
			
			if ($this->iBarShadow != 0) {
				for ($j=0; $j<$this->iBarShadow; $j++) {
					imageline($this->imgData, $iBarBRX-$j, $iBarTLY+1+$j, $iBarBRX-$j, $iBarBRY-$j, $aShadowColors[$i]);
					imageline($this->imgData, $iBarTLX+1+$j, $iBarBRY-$j, $iBarBRX-$j, $iBarBRY-$j, $aShadowColors[$i]);
					imageline($this->imgData, $iBarTLX+$j, $iBarTLY+$j, $iBarTLX+$j, $iBarBRY-1-$j, $aLightColors[$i]);
					imageline($this->imgData, $iBarTLX+$j, $iBarTLY+$j, $iBarBRX-1-$j, $iBarTLY+$j, $aLightColors[$i]);
				}
				
			}
			
		}
			
	}
	
}

?>