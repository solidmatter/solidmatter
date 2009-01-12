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
class sbNode_imagefilter_resize extends Imagefilter {
	
	//--------------------------------------------------------------------------
	/**
	* TODO: support usage of width/height as inner (minimum) boundaries
	* @param 
	* @return 
	*/
	public function applyToImage($imgCurrent) {
		
		$iWidth = $this->getProperty('config_width');
		$iHeight = $this->getProperty('config_height');
		switch ($this->getProperty('config_mode')) {
			case 'KEEPASPECT':
				$eMode = Image::KEEPASPECT;
				break;
			case 'LOSEASPECT':
				$eMode = Image::LOSEASPECT;
				break;
			default:
				throw new sbException('Mode not recognized');
		}
		switch ($this->getProperty('config_direction')) {
			case 'UPSAMPLE':
				$eDirection = Image::UPSAMPLE;
				break;
			case 'DOWNSAMPLE':
				$eDirection = Image::DOWNSAMPLE;
				break;
			case 'BOTH':
				$eDirection = Image::DOWNSAMPLE | Image::UPSAMPLE;
				break;
			default:
				throw new sbException('Mode not recognized');
		}
		
		$imgCurrent->resample($iWidth, $iHeight, $eMode, $eDirection);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function applyToNode($sNodeID) {
		
		//$nodeSubject = NodeFactory::getInstance($sNodeID);
		//$imgContent = $nodeSubject->getImage();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function modifyForm($formCurrent, $sMode) {
		
		$formCurrent->setOptions('config_mode', array(
			'KEEPASPECT' => '$locale/sbUtilities/Imagefilter_resize/config_mode/keepaspect/@label',
			'LOSEASPECT' => '$locale/sbUtilities/Imagefilter_resize/config_mode/loseaspect/@label'
		));
		$formCurrent->setOptions('config_direction', array(
			'DOWNSAMPLE' => '$locale/sbUtilities/Imagefilter_resize/config_direction/downsample/@label',
			'UPSAMPLE' => '$locale/sbUtilities/Imagefilter_resize/config_direction/upsample/@label',
			'BOTH' => '$locale/sbUtilities/Imagefilter_resize/config_direction/both/@label'
		));
		
		return ($formCurrent);
		
	}
	
	/*protected function modifyForm($formCurrent, $sMode) {
		
		$formCurrent->addInput('config_width;integer;minvalue=1;maxvalue=2000;required=TRUE', '$locale/system/nodes/imagefilter_resize/config_width');
		$formCurrent->addInput('config_height;integer;minvalue=1;maxvalue=2000;required=TRUE', '$locale/system/nodes/imagefilter_resize/config_height');
		$formCurrent->addInput('config_mode;select', '$locale/system/nodes/imagefilter_resize/config_mode');
		$formCurrent->setOptions('config_mode', array(
			'KEEPASPECT' => '$locale/system/nodes/imagefilter_resize/mode_keepaspect',
			'LOSEASPECT' => '$locale/system/nodes/imagefilter_resize/mode_loseaspect'
		));
		$formCurrent->addInput('config_direction;select', '$locale/system/nodes/imagefilter_resize/config_direction');
		$formCurrent->setOptions('config_direction', array(
			'DOWNSAMPLE' => '$locale/system/nodes/imagefilter_resize/direction_downsample',
			'UPSAMPLE' => '$locale/system/nodes/imagefilter_resize/direction_upsample',
			'BOTH' => '$locale/system/nodes/imagefilter_resize/direction_both'
		));
		
		if (!$this->isNew()) {
			$formCurrent->setValue('config_width', $this->getProperty('config_width'));
			$formCurrent->setValue('config_height', $this->getProperty('config_height'));
			$formCurrent->setValue('config_mode', $this->getProperty('config_mode'));
			$formCurrent->setValue('config_direction', $this->getProperty('config_direction'));
		}
		
		return ($formCurrent);
		
	}*/
	
}

?>