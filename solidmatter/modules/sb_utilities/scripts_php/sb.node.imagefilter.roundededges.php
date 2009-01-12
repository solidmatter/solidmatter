<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter:sbUtilities
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sbUtilities:sb.node.imagefilter');

//------------------------------------------------------------------------------
/**
*/
class sbNode_imagefilter_roundededges extends Imagefilter {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function applyToImage($imgCurrent) {
		
		throw new LazyBastardException();
		
		/*$sOrigin = $this->getProperty('config_origin');
		$iWidth = $this->getProperty('config_width');
		$iHeight = $this->getProperty('config_height');
		$iOffsetX = $this->getProperty('config_offsetx');
		$iOffsetY = $this->getProperty('config_offsety');
		
		$eOrigin = NULL;
		switch ($sOrigin) {
			case 'TOPLEFT': $eOrigin = Image::TOPLEFT; break;	
			case 'TOPCENTER': $eOrigin = Image::TOPCENTER; break;
			case 'TOPRIGHT': $eOrigin = Image::TOPRIGHT; break;
			case 'BOTTOMLEFT': $eOrigin = Image::BOTTOMLEFT; break;
			case 'BOTTOMCENTER': $eOrigin = Image::BOTTOMCENTER; break;
			case 'BOTTOMRIGHT': $eOrigin = Image::BOTTOMRIGHT; break;
			case 'LEFTCENTER': $eOrigin = Image::LEFTCENTER; break;
			case 'RIGHTCENTER': $eOrigin = Image::RIGHTCENTER; break;
			case 'CENTER': $eOrigin = Image::CENTER; break;
		}
		
		$imgCurrent->crop($eOrigin, $iWidth, $iHeight, $iOffsetX, $iOffsetY);*/
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function applyToNode($sNodeID) {
		
		
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function modifyForm($formCurrent, $sMode) {
		
		$formCurrent->setOptions('config_mode', array(
			'CIRCLE' => '$locale/system/nodes/imagefilter_roundededges/config_mode/circle/@label',
		));
		
		return ($formCurrent);
		
	}
	
}

?>