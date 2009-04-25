<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Tools
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.image');

//------------------------------------------------------------------------------
/**
* This function can resample images to other sizes.
* It needs a resource-link to an gd2 image and the destination sizes, both X
* (horizontal) and Y (vertical). Additionally, one can decide to keep the aspect
* ratio (X devided by Y will be the same in the resulting image) or lose it
* (the destination X and Y size will be met exactly) and if the image should
* be upsampled (enlarged if smaller then destination size), downsampled
* (shrinked if larger than the destination size) or both.
* Some examples: 
* input image is 400*300, destination is 40*40 
* - keepaspect and downsample - the resulting image will be 40*30 
* - loseaspect and downsample - the resulting image will be 40*40 
* - anything and upsample - the resulting image will stay 400*300, as it is
* already larger than 40*40 
* input image is 300*400, destination is 800*600 
* - keepaspect and upsample - the resulting image will be 450*600
* - anything and downsample - the resulting image will stay 300*400, as it is
* already smaller than 800*600
* @access public
* @param resource link to a gd2 image
* @param integer the destination X size
* @param integer the destination Y size
* @param integer resampling mode (aspect ratio), can either be KEEPASPECT or
* LOSEASPECT
* @param integer resampling mode (up/downsampling), can be UPSAMPLE, DOWNSAMPLE
* or UPSAMPLE|DOWNSAMPLE (if the image should fit the destination size
* regardless of it's original size)
* @return resource link to the resized image
*/
function image_resample($resSource, $iDestinationXSize, $iDestinationYSize, $iMode = Image::KEEPASPECT, $iDirection = Image::DOWNSAMPLE) {	

	$iOriginalXSize = imagesx($resSource);
	$iOriginalYSize = imagesy($resSource);
	
	$bChangesNeeded = FALSE;
	if ($iDirection & Image::DOWNSAMPLE) {
		if ($iOriginalXSize > $iDestinationXSize || $iOriginalYSize > $iDestinationYSize) {
			$bChangesNeeded |= TRUE;
		}
	}
	if ($iDirection & Image::UPSAMPLE) {
		if ($iOriginalXSize < $iDestinationXSize || $iOriginalYSize < $iDestinationYSize) {
			$bChangesNeeded |= TRUE;
		}
	}
	
	if ($bChangesNeeded) {
		
		switch ($iMode) {
			
			case Image::KEEPASPECT:
				
				// FIXME: 299x300 => 0xNULL ????
				$flOriginalAspect		= $iOriginalXSize / $iOriginalYSize;
				$flDestinationAspect	= $iDestinationXSize / $iDestinationYSize;
				
				if ($iDestinationXSize == $iDestinationYSize) { // square
				
					if ($flOriginalAspect >= $flDestinationAspect) {
						$iDestinationYSize = floor($iDestinationYSize / $flOriginalAspect);
					} else {
						$iDestinationXSize = floor($iDestinationXSize * $flOriginalAspect);
					}
				
				} else { // rectangular
					
					if ($flOriginalAspect >= 1) {
						$iDestinationYSize = floor($iDestinationXSize / $flOriginalAspect);
					} else {
						$iDestinationXSize = floor($iDestinationYSize * $flOriginalAspect);
					}
										
				}
				break;
			
			case Image::LOSEASPECT:
				break;
	
		}
		
	} else {
		
		$iDestinationXSize = $iOriginalXSize;
		$iDestinationYSize = $iOriginalYSize;
		
	}
	
	/*
	echo $iOriginalXSize.'|';
	echo $iOriginalYSize.'|';
	echo $iDestinationXSize.'|';
	echo $iDestinationYSize.'|';
	echo $flOriginalAspect.'|';
	echo $flDestinationAspect;
	*/
	
	$resDestination = imagecreatetruecolor($iDestinationXSize, $iDestinationYSize);
	$bSuccess = imagecopyresampled($resDestination, $resSource, 0, 0, 0, 0, $iDestinationXSize, $iDestinationYSize, $iOriginalXSize, $iOriginalYSize);
	
	if (!$bSuccess) {
		die('image_resample failed ('.$iOriginalXSize.'x'.$iOriginalYSize.' => '.$iDestinationXSize.'x'.$iDestinationYSize.')');
	}
	
	return ($resDestination);
	
}

//------------------------------------------------------------------------------
/**
* @param 
* @return 
*/
function image_crop($resSource, $eOrigin, $iWidth, $iHeight, $iOffsetX, $iOffsetY) {
	
	switch ($eOrigin) {
		case Image::TOPLEFT:
			$iX = 0; $iY = 0; break;
		case Image::TOPCENTER:
			$iX = abs((imagesx($resSource) - $iWidth) / 2); $iY = 0; break;
		case Image::TOPRIGHT:
			$iX = imagesx($resSource) - $iWidth; $iY = 0; break;
		case Image::BOTTOMLEFT:
			$iX = 0; $iY = imagesy($resSource) - $iHeight; break;
		case Image::BOTTOMCENTER:
			$iX = abs((imagesx($resSource) - $iWidth) / 2); $iY = imagesy($resSource) - $iHeight; break;
		case Image::BOTTOMRIGHT:
			$iX = imagesx($resSource) - $iWidth; $iY = imagesy($resSource) - $iHeight; break;
		case Image::LEFTCENTER:
			$iX = 0; $iY = abs((imagesy($resSource) - $iHeight) / 2); break;
		case Image::RIGHTCENTER:
			$iX = imagesx($resSource) - $iWidth; $iY = abs((imagesy($resSource) - $iHeight) / 2); break;
		case Image::CENTER:
			$iX = abs((imagesx($resSource) - $iWidth) / 2); $iY = abs((imagesy($resSource) - $iHeight) / 2); break;
	}
	
	$iSourceX = $iX + $iOffsetX;
	$iSourceY = $iY + $iOffsetY;
	
	// TODO: apply checks if the cropped image would be larger than the original
	
	$resDestination = imagecreatetruecolor($iWidth, $iHeight);
	imagecopy($resDestination, $resSource, 0, 0, $iSourceX, $iSourceY, $iWidth, $iHeight);
	
	return ($resDestination);
	
}

//------------------------------------------------------------------------------
/**
* @param 
* @return 
*/
function image_roundedges() {
	
	
}

//------------------------------------------------------------------------------
/**
* Image ressource must be truecolor!
* 
* @param 
* @return array Values for hue, saturation(notyet), lightness indexed 'h','s','l'
*/
function image_gethsl($resImage, $iNumSamples = 100, $iWidth = NULL, $iHeight = NULL, $iOffsetX = 0, $iOffsetY = 0) {
	
	// init
	$iMaxX = imagesx($resImage)-1;
	$iMaxY = imagesy($resImage)-1;
	if ($iWidth == NULL) {
		$iWidth = $iMaxX;
	}
	if ($iHeight == NULL) {
		$iHeight = $iMaxY;
	}
	
	//$flHue = 0;
	$flEntropy = 0;
	$flSaturation = 0;
	$flLightness = 0;
	$aSummedRGB = array(
		'r' => 0,
		'g' => 0,
		'b' => 0
	);
	$aLastRGB = NULL;
	
	for ($i=0; $i<$iNumSamples; $i++) {
		
		$iX = mt_rand($iOffsetX, $iOffsetX + $iWidth-1);
		$iY = mt_rand($iOffsetY, $iOffsetY + $iHeight-1);
		
		// secure the values, might be wrong offset/size
		if ($iX > $iMaxX) {	$iX = $iMaxX; }
		if ($iY > $iMaxY) {	$iY = $iMaxY; }
		if ($iX < 0) { $iX = 0; }
		if ($iY < 0) { $iY = 0; }
		
		// get color value
		$iColor = imagecolorat($resImage, $iX, $iY);
		$aRGB['r'] = ($iColor >> 16) & 0xFF;
		$aRGB['g'] = ($iColor >> 8) & 0xFF;
		$aRGB['b'] = $iColor & 0xFF;
		
		// store hsl values
		$aHSL = rgb2hsl($aRGB);
		//$flHue += $aHSL['h'] * 255;
		$flSaturation += $aHSL['s'] * 255;
		$flLightness += ($aRGB['r'] + $aRGB['g'] + $aRGB['b']) / 3;
		$aSummedRGB['r'] += $aRGB['r'];
		$aSummedRGB['g'] += $aRGB['g'];
		$aSummedRGB['b'] += $aRGB['b'];
		
		if ($aLastRGB != NULL) {
			$flEntropy += (abs($aRGB['r'] - $aLastRGB['r']) + abs($aRGB['g'] - $aLastRGB['g']) + abs($aRGB['b'] - $aLastRGB['b'])) / 3;
		}
		
		$aLastRGB = $aRGB;
	}
	
	// get hsl average
	//$aHSL['h'] = round($flHue / $iNumSamples);
	$aSummedRGB['r'] = round($aSummedRGB['r'] / $iNumSamples);
	$aSummedRGB['g'] = round($aSummedRGB['g'] / $iNumSamples);
	$aSummedRGB['b'] = round($aSummedRGB['b'] / $iNumSamples);
	$aTempHSL = rgb2hsl($aSummedRGB);
	$aHSL['h'] = round($aTempHSL['h'] * 255);
	$aHSL['s'] = round($flSaturation / $iNumSamples);
	$aHSL['l'] = round($flLightness / $iNumSamples);
	$aHSL['e'] = round($flEntropy / $iNumSamples);
	
	return ($aHSL);
	
}




/*
function colorize($img_src,$img_dest, $r, $g, $b) 
{
if(!$im = imagecreatefromgif($img_src))
 return "Could not use image $img_src"; 

//We will create a monochromatic palette based on
//the input color
//which will go from black to white
//Input color luminosity: this is equivalent to the 
//position of the input color in the monochromatic
//palette
$lum_inp=round(255*($r+$g+$b)/765); //765=255*3
//We fill the palette entry with the input color at its 
//corresponding position
$pal[$lum_inp]['r']=$r;
$pal[$lum_inp]['g']=$g;
$pal[$lum_inp]['b']=$b;
//Now we complete the palette, first we'll do it to
//the black,and then to the white.
//FROM input to black
//===================
//how many colors between black and input
$steps_to_black=$lum_inp; 
//The step size for each component
if($steps_to_black)
{
$step_size_red=$r/$steps_to_black; 
$step_size_green=$g/$steps_to_black; 
$step_size_blue=$b/$steps_to_black; 
}
for($i=$steps_to_black;$i>=0;$i--)
{
$pal[$steps_to_black-$i]['r']=$r-round($step_size_red*$i);
$pal[$steps_to_black-$i]['g']=$g-round($step_size_green*$i);
$pal[$steps_to_black-$i]['b']=$b-round($step_size_blue*$i);
}
//From input to white:
//===================
//how many colors between input and white
$steps_to_white=255-$lum_inp;
if($steps_to_white)
 {
 $step_size_red=(255-$r)/$steps_to_white; 
 $step_size_green=(255-$g)/$steps_to_white; 
 $step_size_blue=(255-$b)/$steps_to_white; 
 }
else
 $step_size_red=$step_size_green=$step_size_blue=0;
//The step size for each component
for($i=($lum_inp+1);$i<=255;$i++)
 {
 $pal[$i]['r']=$r + round($step_size_red*($i-$lum_inp));
 $pal[$i]['g']=$g + round($step_size_green*($i-$lum_inp));
 $pal[$i]['b']=$b + round($step_size_blue*($i-$lum_inp));
 }
//--- End of palette creation
//Now,let's change the original palette into the one we
//created
for($c = 0; $c < $palette_size; $c++)
{ 
$col = imagecolorsforindex($im, $c);          
$lum_src=round(255*($col['red']+$col['green']
               +$col['blue'])/765);
$col_out=$pal[$lum_src];
imagecolorset($im, $c, $col_out['r'], 
                               $col_out['g'],
                               $col_out['b']);
}
//save the image file
imagepng($im,$img_dest);
imagedestroy($im);
}//end function colorize

*/




?>