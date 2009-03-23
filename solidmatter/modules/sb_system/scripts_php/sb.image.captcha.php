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

//------------------------------------------------------------------------------

if (!defined('ALPHA'))			define('ALPHA',			230);
if (!defined('NUMERIC'))		define('NUMERIC',		231);
if (!defined('ALPHANUMERIC'))	define('ALPHANUMERIC',	232);
if (!defined('EXTENDED'))		define('EXTENDED',		233);
if (!defined('REDUCED'))		define('REDUCED',		234);

if (!defined('CASE_UPPER'))		define('CASE_UPPER',	235);
if (!defined('CASE_LOWER'))		define('CASE_LOWER',	236);
if (!defined('CASE_BOTH'))		define('CASE_BOTH',		237);

if (!defined('NOISE_STRIPES'))	define('NOISE_STRIPES',	240);
if (!defined('NOISE_LINES'))	define('NOISE_LINES',	241);
if (!defined('NOISE_CIRCLES'))	define('NOISE_CIRCLES',	242);

//------------------------------------------------------------------------------
/**
* A CaptchaImage is that type of image that is seen in various places in the
* web that's used to verify the one filling out a form is really a human beeing.
* This ist done by drawing a sequence of characters that are then obfuscated
* graphically by various methods, so that automated graphical analysis
* programs can't decipher them. If the sequence filled into the form differs
* from the generated one the form is not processed.
* When using this class, the standard procedure would look like this:
* <code>
* $imgCheck = new CaptchaImage($iWidth, $iHeight, 4, ALPHANUMERIC, CASE_UPPER, NOISE_LINES);
* $imgCheck->SetCharacters('ABCDEFGHJKLMNPQRSTUXYZ2345689'); // optional
* $imgCheck->Generate();
* $_SESSION['checkhuman_value'] = $imgCheck->GetSequence(); // store sequence
* $imgCheck->Output(GIF);</code>
* When processing the form afterwards, the given sequence is then compared to
* the one saved in the session.
*/
class CaptchaImage extends Image {
	
	//--------------------------------------------------------------------------
	/**
	* Contains all possible characters that can be used for building the
	* image.
	* Can be customized by calling SetCharacters()-method.
	* @var string
	*/
	private $sCharacters	= '';
	/**
	* After generating an image, the character-sequence can be found here.
	* Can be read by calling GetSequence()-method.
	* @var string
	*/
	private $sSequence		= '';
	
	//--------------------------------------------------------------------------
	/**
	* The number of characters to include in the generated sequence.
	* @var integer
	*/
	private $iNumCharacters	= 4;
	/**
	* The type of characters to be used for generation of the sequence.
	* Can be one of:
	* - ALPHA (letters)
	* - NUMERIC (numbers)
	* - ALPHANUMERIC (letters & numbers)
	* - REDUCED (letters & numbers without ambiguous chars)
	* - EXTENDED (letters, numbers & additional characters)
	* @var integer
	*/
	private $eStyle			= REDUCED;
	/**
	* When using letters, this value determines the possible cases of these.
	* Can be one of:
	* - CASE_UPPER (only uppercase letters)
	* - CASE_LOWER (only lowercase letters)
	* - CASE_BOTH (both cases possible)
	* @var integer
	*/
	private $eCase			= CASE_BOTH;
	/**
	* The type of noise to be used for obfuscating the sequence.
	* Can be one of:
	* - NOISE_STRIPES (vertical/horizontal lines)
	* - NOISE_LINES (freely placed lines)
	* - NOISE_CIRCLES (ellipses)
	* @var integer
	*/
	private $eNoiseType		= NOISE_LINES;
	/**
	* The maximum angle in degrees the characters are tilted.
	* they are tilted cw AND ccw, so a value of 90 means a total range of 180
	* degrees. 
	* @var integer
	*/
	private $iMaxAngle		= 30;
	/**
	* Name of the font to be used for drawing the sequence
	* @var string
	*/
	private $sFont			= NULL;
	private $bUseAA			= TRUE;
	
	//--------------------------------------------------------------------------
	/**
	* Array containing the RGB values of the background color
	* @var array
	*/
	private $aBackgroundColor	= array('r' => 255, 'g' => 255, 'b' => 255);
	/**
	* Array containing the RGB values of the foreground (text) color
	* @var array
	*/
	private $aTextColor			= array('r' => 0, 'g' => 0, 'b' => 0);
	
	//--------------------------------------------------------------------------
	//##########################################################################
	//--------------------------------------------------------------------------
	/**
	* Initializes the CheckHuman image.
	* Assigns the parameters and creates the image-resource.
	* @access 
	* @param integer Image width
	* @param integer Image Height
	* @param integer Number of characters in the sequence
	* @param integer Possible characters in sequence 
	* (@see CheckHumanImage::$eStyle)
	* @param integer Possible cases for letters (@see CheckHumanImage::$eCase)
	* @param integer Noise-type to be used (@see CheckHumanImage::$eNoiseType)
	* @return boolean TRUE in case of success, FALSE otherwise
	*/
	function __construct($iWidth = 0, $iHeight = 0, $iNumCharacters = 4, $eStyle = ALPHANUMERIC, $eCase = CASE_UPPER, $eNoiseType = NOISE_LINES, $sFont = NULL, $bUseAA = FALSE) {
		
		if ($iWidth <= 0 || $iHeight <= 0 || $iNumCharacters <= 0) {
			die('Image::__construct - height, width and numcharacters have to be greater than 0');	
		}
		if ($bUseAA) {
			$iWidth *= 2;
			$iHeight *= 2;	
		}
		if (!$this->resData = imagecreatetruecolor($iWidth, $iHeight)) {
			return (FALSE);
		}
		
		// store arguments
		$this->iWidth			= $iWidth;
		$this->iHeight			= $iHeight;
		$this->iNumCharacters	= $iNumCharacters;
		$this->eStyle			= $eStyle;
		$this->eCase			= $eCase;
		$this->eNoiseType		= $eNoiseType;
		$this->sFont			= $sFont;
		$this->bUseAA			= $bUseAA;
		
		// select possible characters
		if ($this->eStyle == ALPHA || $this->eStyle == ALPHANUMERIC) {
			if ($this->eCase == CASE_UPPER || $this->eCase == CASE_BOTH) {
				$this->sCharacters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			}
			if ($this->eCase == CASE_LOWER || $this->eCase == CASE_BOTH) {
				$this->sCharacters .= 'abcdefghijklmnopqrstuvwxyz';
			}
		}
		
		if ($this->eStyle == NUMERIC || $this->eStyle == ALPHANUMERIC) {
			$this->sCharacters .= '0123456789';
		}
		
		if ($this->eStyle == EXTENDED) {
			$this->sCharacters .= '!$%&()=#*?@';
		}
		
		if ($this->eStyle == REDUCED) {
			$this->sCharacters = '123456789ABCDEFGHKLMNOPRTUWXYZ';
		}
		
		if ($this->sFont === NULL) {
			$this->sFont = dirname(__FILE__).'/../data/spotlight.ttf';
			//echo ($this->sFont);
		}
		
		return (TRUE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Sets the available characters for the sequence.
	* By using characters multiple times in the string you can increase the
	* propability these are used. E.g. 'AAAB' means 'A' has a three times higher
	* propability to occur than 'B'.
	* @param string Contains the possible characters.
	*/
	public function setCharacters($sCharacters) {
		$this->sCharacters = $sCharacters;	
	}
	
	//--------------------------------------------------------------------------
	/**
	* Returns a string containing the chosen sequence for the most recently
	* generated image. Should be called after generate()-method to process the
	* randomly generated sequence further.
	* @return string The sequence used for the image.
	*/
	public function getSequence() {
		return ($this->sSequence);
	}
	
	//--------------------------------------------------------------------------
	/**
	* Generates the CAPTCHA, which is then stored in member $imgData.
	*/
	public function generate() {
		
		// initialize vars
		$iCharWidth		= (integer) $this->iWidth / ($this->iNumCharacters + 2);
		$iCharHeight	= $this->iHeight * 0.3;
		
		$iWidthVariant	= $iCharWidth / 10;
		$iHeightVariant	= $iCharHeight / 10;
				
		$iMaxRand		= strlen($this->sCharacters) - 1;
		
		// allocate colors
		$rBackground = imagecolorallocate($this->resData, $this->aBackgroundColor['r'], $this->aBackgroundColor['g'], $this->aBackgroundColor['b']);
		$rText = imagecolorallocate($this->resData, $this->aTextColor['r'], $this->aTextColor['g'], $this->aTextColor['b']);
		imagefilledrectangle($this->resData, 0, 0, $this->iWidth-1, $this->iHeight-1, $rBackground);
		
		// draw text
		for ($i=1; $i<=$this->iNumCharacters; $i++) {
			
			$sChar = $this->sCharacters[(int) mt_rand(0, $iMaxRand+0.5)];
			$flAngle = mt_rand(-$this->iMaxAngle, $this->iMaxAngle);
			
			$iXPos = (int) $i * $iCharWidth + mt_rand(-$iWidthVariant, $iWidthVariant);
			$iYPos = (int) $iCharHeight + ($this->iHeight - $iCharHeight) / 2 + mt_rand(-$iHeightVariant, $iHeightVariant);
			
			//$iXPos -= (int) abs($flAngle / $this->iMaxAngle * $iCharWidth);
			
			imagettftext($this->resData, $iCharHeight, $flAngle, $iXPos, $iYPos, -$rText, $this->sFont, $sChar);
			
			$this->sSequence .= $sChar;
			
		}
		
		// add noise
		switch ($this->eNoiseType) {
			
			case NOISE_STRIPES:
				$iNumStripes = ($this->iWidth * $this->iHeight) / 150;
				for ($i=0; $i<$iNumStripes; $i++) {
					$iLength = (int) mt_rand(5, 1.5*$this->iHeight);
					$iX1 = (int) mt_rand(-0.3*$this->iWidth, $this->iWidth);
					$iY1 = (int) mt_rand(-0.3*$this->iHeight, $this->iHeight);
					$iX2 = $iX1;
					$iY2 = $iY1;
					if ($i % 2 == 0) {
						$rColor = $rBackground;
						$iX2 += $iLength;
						$iY1 = abs($iY1);
						$iY2 = $iY1;
					} else {
						$rColor = $rText;
						$iX1 = abs($iX1);
						$iX2 = $iX1;
						$iY2 += $iLength;
					}
					imageline($this->resData, $iX1, $iY1, $iX2, $iY2, $rColor);
				}
				break;
			
			case NOISE_LINES:
				$iNumLines = ($this->iWidth * $this->iHeight) / 300;
				for ($i=0; $i<$iNumLines; $i++) {
					if ($i % 2 == 0) {
						$rColor = $rBackground;
					} else {
						$rColor = $rText;
					}
					$iX1 = (int) mt_rand(-20, $this->iWidth + 20);
					$iX2 = (int) mt_rand(-20, $this->iWidth + 20);
					$iY1 = (int) mt_rand(-20, $this->iHeight + 20);
					$iY2 = (int) mt_rand(-20, $this->iHeight + 20);
					imageline($this->resData, $iX1, $iY1, $iX2, $iY2, $rColor);
				}
				break;
				
			case NOISE_CIRCLES:
				$iNumCircles = ($this->iWidth * $this->iHeight) / 350;
				for ($i=0; $i<$iNumCircles; $i++) {
					if ($i % 2 == 0) {
						$rColor = $rBackground;
					} else {
						$rColor = $rText;
					}
					$iCX = (int) mt_rand(1, $this->iWidth - 2);
					$iCY = (int) mt_rand(1, $this->iHeight - 2);
					$iW = (int) mt_rand(3, $this->iHeight);
					$iH = (int) mt_rand(3, $this->iHeight);
					imageellipse($this->resData, $iCX, $iCY, $iW, $iH, $rColor);
				}
				break;
				
		}
		
		if ($this->bUseAA) {
			$this->resample($this->iWidth / 2, $this->iHeight / 2);	
		}
		
		// draw frame
		imagerectangle($this->resData, 0, 0, $this->iWidth-1, $this->iHeight-1, $rText);
		
	}

}

?>