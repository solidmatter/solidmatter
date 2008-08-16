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
import('sb.math.vector2d');
import('sb.tools.colors');

//------------------------------------------------------------------------------
/**
* 
*/
class Radarchart extends Image {
	
	private $aValues			= array();
	private $aTitles			= array();
	private $aColorTable		= array();
	private $aBackgroundColor	= array('r' => 255, 'g' => 255, 'b' => 255);
	private $aGridColor			= array('r' => 0, 'g' => 0, 'b' => 0);
	private $aScaleColor		= array('r' => 0, 'g' => 0, 'b' => 0);
	private $aLineColor			= array('r' => 0, 'g' => 0, 'b' => 200);
	private $aTextColor			= array('r' => 0, 'g' => 0, 'b' => 0);
	
	private $iPointDiameter		= 9;
	private $iMargin			= 0;
	private $iScaleLength		= 10;
	//private $flOversample		= 0;
	private $iStartDegree		= -80;
	private $iNumScales			= 6;
	private $flScaleDistance	= 2;
	private $iNumDigits			= 0;
	//private $flShadowBrightness	= 0.5;
	
	private $bUseAntialias		= TRUE;
	private $bDrawTitles		= TRUE;
	private $bDrawPercentages	= TRUE;
	private $bDrawScale			= TRUE;
	private $bDrawValues		= FALSE;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @access 
	* @param
	* @return 
	*/
	function __construct($iWidth = 300, $iHeight = 200, $iPointDiameter = 10, $iMargin = 15) {
		
		if ($iWidth <= 0 || $iHeight <= 0 || $iPointDiameter < 1 || $iMargin < 0) {
			throw new ImageProcessingException('Radarchart::__construct - height, width and margin have to be greater than 0, pointradius has to be 1 or greater');
		}
		
		$this->iWidth			= $iWidth;
		$this->iHeight			= $iHeight;
		$this->iPointDiameter	= $iPointDiameter;
		$this->iMargin			= $iMargin;

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
		
		if (count($aValues) < 3) {
			throw new ImageProcessingException('Radarchart::SetValues - there have to be at least 3 values in the array');
		}
		if (count($aTitles) != 0 && count($aValues) != count($aTitles)) {
			throw new ImageProcessingException('Radarchart::SetValues - the number of values has to be the same as the number of titles');
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
		
		$iDiagramWidth		= $this->iWidth;
		$iDiagramHeight		= $this->iHeight;
		$iTotalWidth		= $this->iWidth;
		$iTotalHeight		= $this->iHeight;
		$iLegendWidth		= 0;
		$iLegendHeight		= 0;
		
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
		
		if ($this->bUseAntialias) {
			imageantialias($this->imgData, TRUE);	
		}
		
		// initial variables
		$iCenterX		= round($this->iWidth / 2);
		$iCenterY		= round($this->iHeight / 2);
		$iWidth			= round($this->iWidth - 2 * $this->iMargin);
		$iHeight		= round($this->iHeight - 2 * $this->iMargin);
		$iPointRadius	= $this->iPointRadius;
		$iImageWidth	= $this->iWidth;
		$iImageHeight	= $this->iHeight;
		$imgChart		= $this->imgData;
		
		// allocate the colors
		$rBlack = imagecolorallocate($this->imgData, 0, 0, 0);
		$rBackground = imagecolorallocate($this->imgData, $this->aBackgroundColor['r'], $this->aBackgroundColor['g'], $this->aBackgroundColor['b']);
		$rScale = imagecolorallocate($this->imgData, $this->aScaleColor['r'], $this->aScaleColor['g'], $this->aScaleColor['b']);
		$rLine = imagecolorallocate($this->imgData, $this->aLineColor['r'], $this->aLineColor['g'], $this->aLineColor['b']);
		$rText = imagecolorallocate($this->imgData, $this->aTextColor['r'], $this->aTextColor['g'], $this->aTextColor['b']);
		
		imagefilledrectangle($this->imgData, 0, 0, $iTotalWidth-1, $iTotalHeight-1, $rBackground);
		if ($this->flOversample != 1) {
			$rBackground = imagecolorallocate($this->imgData, $this->aBackgroundColor['r'], $this->aBackgroundColor['g'], $this->aBackgroundColor['b']);
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
		
		// calculate the degrees for each radar arm
		$iNumValues = count($this->aValues);
		$flArmDegrees = 360 / $iNumValues;
		
		// calculate base length
		$iBaseLength = min(round(($this->iWidth - $this->iMargin) / 2), round(($this->iHeight - $this->iMargin) / 2)) - ceil($this->iPointDiameter / 2) - 1;
		
		// get minimum and maximum
		$flMax = max($this->aValues);
		$flMin = min($this->aValues);
		
		// calculate scale
		$aScale = get_diagramscale($flMax, $flMin, $this->iNumScales, SCALE_QUANTITY);
		if ($this->flScaleDistance != 0) {
			$aScale = get_diagramscale($flMax, $flMin, $this->flScaleDistance, SCALE_DISTANCE);
		}
		
		// remap values to scale
		if ($flMin < 0) {
			for ($i=0; $i<count($this->aValues); $i++) {
				$aMappedValues[$i] = $this->aValues[$i]	- $flMin;
			}
		} else {
			$aMappedValues = $this->aValues;	
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
		
		//$iAxisLength = $iCenterX;
		
		for ($i=0; $i<count($this->aValues); $i++) {
			
			$flArmdegree = $this->iStartDegree + $i * $flArmDegrees;
			
			$vAxis = new Vector2D($iBaseLength, $flArmdegree, LENGTH_ANGLE, $iCenterX, $iCenterY);
			$vAxis->Draw($this->imgData, $rScale);
			
			if ($this->bDrawScale) {
				
				$flDistanceLength = $iBaseLength / ($aScale['quantity'] - 1);
				
				for ($j=1; $j<$aScale['quantity']; $j++) {
					
					$vScaleUnitOffset = new Vector2D($flDistanceLength * $j, $flArmdegree, LENGTH_ANGLE, $iCenterX, $iCenterY);
					$vScaleUnit1 = new Vector2D($this->iScaleLength / 2, $flArmdegree + 90, LENGTH_ANGLE);
					$vScaleUnit2 = new Vector2D($this->iScaleLength / 2, $flArmdegree - 90, LENGTH_ANGLE);
					$vScaleUnit1->SetOffset($vScaleUnitOffset, FULL);
					$vScaleUnit2->SetOffset($vScaleUnitOffset, FULL);
					$vScaleUnit1->Draw($this->imgData, $rScale);
					$vScaleUnit2->Draw($this->imgData, $rScale);
					
					if ($i == 0) {
						$vTextOrigin = $vScaleUnit1->Copy();
						$vTextOrigin->Multiply(2);
						$iTX = round($vTextOrigin->flXOffset + $vTextOrigin->flX);
						$iTY = round($vTextOrigin->flYOffset + $vTextOrigin->flY) - 6;
						$flValue = $aScale['bottom_value'] + $j * $aScale['distance'];
						$sValue = number_format($flValue, $this->iNumDecimals, gls('DECIMAL_POINT'), '');
						imagestring($this->imgData, 2, $iTX, $iTY, $sValue, $rText);
						
					}
					
				}
				
			}
			
			$aValueVectors[$i] = new Vector2D($iBaseLength / $aScale['range'] * $aMappedValues[$i], $this->iStartDegree+$i*$flArmDegrees, LENGTH_ANGLE, $iCenterX, $iCenterY);
			
			
			
		}
		
		for ($i=0; $i<count($this->aValues); $i++) {
			$flXStart = $aValueVectors[$i]->flXOffset + $aValueVectors[$i]->flX;
			$flYStart = $aValueVectors[$i]->flYOffset + $aValueVectors[$i]->flY;
			if ($i != count($this->aValues) - 1) {
				$flXEnd = $aValueVectors[$i+1]->flXOffset + $aValueVectors[$i+1]->flX;
				$flYEnd = $aValueVectors[$i+1]->flYOffset + $aValueVectors[$i+1]->flY;
			} else {
				$flXEnd = $aValueVectors[0]->flXOffset + $aValueVectors[0]->flX;
				$flYEnd = $aValueVectors[0]->flYOffset + $aValueVectors[0]->flY;
			}
			
			$vConnector = new Vector2D($flXEnd, $flYEnd, START_END, $flXStart, $flYStart);
			$vConnector->Draw($this->imgData, $rLine);
		}
		
		for ($i=0; $i<count($this->aValues); $i++) {
			$iXCenter = round($aValueVectors[$i]->flXOffset + $aValueVectors[$i]->flX);
			$iYCenter = round($aValueVectors[$i]->flYOffset + $aValueVectors[$i]->flY);
			imagefilledellipse($this->imgData, $iXCenter, $iYCenter, $this->iPointDiameter, $this->iPointDiameter, $aColors[$i]);
			imageellipse($this->imgData, $iXCenter, $iYCenter, $this->iPointDiameter+2, $this->iPointDiameter+2, $rBlack);
		}
		
		// draw legend if necessary
		$rTextColor = imagecolorallocate($this->imgData, $this->aTextColor['r'], $this->aTextColor['g'], $this->aTextColor['b']);
		for ($i=0; $i<$iNumValues; $i++) {
			
			$iLineHeight = $i*15+$this->iMargin;
			
			imagefilledrectangle($this->imgData, $iDiagramWidth+6, $iLineHeight+1, $iDiagramWidth+16, $iLineHeight+11, $aColors[$i]);
			imagerectangle($this->imgData, $iDiagramWidth+6, $iLineHeight+1, $iDiagramWidth+16, $iLineHeight+11, $rBlack);
			
			$sLabel = '';
			
			if ($this->bDrawTitles && count($this->aTitles) > 0)	{ 
				$sLabel .= ' '.$this->aTitles[$i];
			}
			if ($this->bDrawValues) {
				$sLabel .= ' ('.$this->aValues[$i].')';
			}
			
			imagestring($this->imgData, 2, $iDiagramWidth+20, $iLineHeight, $sLabel, $rBlack);
			
		}
		
		
	}
	
}

?>