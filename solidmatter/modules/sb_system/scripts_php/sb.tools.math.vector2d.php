<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Tools
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

if (!defined('XAXIS'))			define('XAXIS',			260);
if (!defined('YAXIS'))			define('YAXIS',			261);
if (!defined('BISECTOR1'))		define('BISECTOR1',		262);
if (!defined('BISECTOR2'))		define('BISECTOR2',		263);

if (!defined('COORDINATES'))	define('COORDINATES',	265);
if (!defined('LENGTH_ANGLE'))	define('LENGTH_ANGLE',	266);
if (!defined('START_END'))		define('START_END',		267);

if (!defined('OFFSET'))			define('OFFSET',		268);
if (!defined('VECTOR'))			define('VECTOR',		269);
if (!defined('FULL'))			define('FULL',			45);

//------------------------------------------------------------------------------
/**
* 
*/
class Vector2D {
	
	public $flX			= 1;
	public $flY			= 1;
	public $flXOffset	= 0;
	public $flYOffset	= 0;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($flValue1, $flValue2, $eMode, $flXOffset = 0, $flYOffset = 0) {
		switch ($eMode) {
			case COORDINATES:
				$this->flX = $flValue1;
				$this->flY = $flValue2;
				break;
			case LENGTH_ANGLE:
				$this->flX = $flValue1;
				$this->flY = 0;
				$this->Rotate($flValue2);
				break;
			case START_END:
				$this->flX = $flValue1 - $flXOffset;
				$this->flY = $flValue2 - $flYOffset;
				$this->flXOffset = $flXOffset;
				$this->flYOffset = $flYOffset;
				break;
		}
		$this->flXOffset = $flXOffset;
		$this->flYOffset = $flYOffset;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function copy() {
		//$vCopy = new Vector2D($this->flX, $this->flY, COORDINATES, $this->flXOffset, $this->flYOffset);	
		$vCopy = clone $this;
		return ($vCopy);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function add($pPoint) {
		$this->flX += $pPoint->flX;
		$this->flY += $pPoint->flY;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function substract($pPoint) {
		$this->flX -= $pPoint->flX;
		$this->flY -= $pPoint->flY;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function multiply($flFactor) {
		$this->flX *= $flFactor;
		$this->flY *= $flFactor;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setOffset($mValue1, $mValue2 = FULL) {
		if (is_a($mValue1, 'Vector2D')) {
			switch ($mValue2) {
				case OFFSET:
					$this->flXOffset = $mValue1->flXOffset;
					$this->flYOffset = $mValue1->flYOffset;
					break;
				case VECTOR:
					$this->flXOffset = $mValue1->flX;
					$this->flYOffset = $mValue1->flY;
					break;
				case FULL:
					$this->flXOffset = $mValue1->flX + $mValue1->flXOffset;
					$this->flYOffset = $mValue1->flY + $mValue1->flYOffset;
					break;
			}
		} else {
			$this->flXOffset = $mValue1;
			$this->flXOffset = $mValue2;
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function mirror($eType) {
		switch ($eType) {
			case XAXIS:
				$this->flY = -$this->flY;
				break;
			case YAXIS:
				$this->flX = -$this->flX;
				break;
			case BISECTOR1:
				$this->flX = -$this->flX;
				$this->flY = -$this->flY;
				break;
			case BISECTOR2:
				$flTemp = $this->flX;
				$this->flX = $this->flY;
				$this->flY = $flTemp;
				break;
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function round() {
		$this->flX = round($this->flX);
		$this->flY = round($this->flY);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function rotate($flDegree) {
		$flDegree = $flDegree * M_PI / 180;
		$flX = ((cos($flDegree) * $this->flX) + (-sin($flDegree) * $this->flY));
		$flY = ((sin($flDegree) * $this->flX) + (cos($flDegree) * $this->flY));
		$this->flX = $flX;
		$this->flY = $flY;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function scale($flXMultiplier, $flYMultiplier = NULL) {
		if ($flYMultiplier === NULL) {
			$flYMultiplier = $flXMultiplier;
		}
		$this->flX *= $flXMultiplier;
		$this->flY *= $flYMultiplier;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function norm() {
		return (sqrt(pow($this->flX, 2) + pow($this->flY, 2)));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function length() {
		return ($this->Norm());
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setLength($flLength) {
		$flFactor = $flLength / $this->Norm();
		$this->Multiply($flFactor);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function draw($imgData, $rColor) {
		$iX1 = round($this->flXOffset);
		$iY1 = round($this->flYOffset);
		$iX2 = round($this->flXOffset + $this->flX);
		$iY2 = round($this->flYOffset + $this->flY);
		imageline($imgData, $iX1, $iY1, $iX2, $iY2, $rColor);
		
	}
	
}

?>