<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter:sb_system
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.node.imagefilter');

//------------------------------------------------------------------------------
/**
*/
class sbNode_imagefilter_crop extends Imagefilter {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function applyToImage($imgCurrent) {
		
		$sOrigin = $this->getProperty('config_origin');
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
		
		$imgCurrent->crop($eOrigin, $iWidth, $iHeight, $iOffsetX, $iOffsetY);
		
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
		
		$formCurrent->setOptions('config_origin', array(
			'CENTER' => '$locale/system/nodes/imagefilter_crop/config_origin/center/@label',
			'TOPLEFT' => '$locale/system/nodes/imagefilter_crop/config_origin/topleft/@label',
			'TOPCENTER' => '$locale/system/nodes/imagefilter_crop/config_origin/topcenter/@label',
			'TOPRIGHT' => '$locale/system/nodes/imagefilter_crop/config_origin/topright/@label',
			'RIGHTCENTER' => '$locale/system/nodes/imagefilter_crop/config_origin/rightcenter/@label',
			'BOTTOMRIGHT' => '$locale/system/nodes/imagefilter_crop/config_origin/bottomright/@label',
			'BOTTOMCENTER' => '$locale/system/nodes/imagefilter_crop/config_origin/bottomcenter/@label',
			'BOTTOMLEFT' => '$locale/system/nodes/imagefilter_crop/config_origin/bottomleft/@label',
			'LEFTCENTER' => '$locale/system/nodes/imagefilter_crop/config_origin/leftcenter/@label',
		));
		
		return ($formCurrent);
		
	}
	
}

?>