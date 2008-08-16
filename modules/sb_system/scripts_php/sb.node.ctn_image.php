<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbNode_ctn_image extends sbNode {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function gatherContent($bPreview = FALSE) {
		
		$sImageUUID = $this->getProperty('content_image');
		//var_dumpp($sImageUUID);
		if ($sImageUUID != NULL) {
			$nodeImage = $this->crSession->getNodeByIdentifier($sImageUUID);
			$nodeImage->setAttribute('path', $nodeImage->getPath());
			//var_dumpp($nodeImage->getElement());
			$this->appendElement($nodeImage->getElement());
		}
	}
	
	
}

	

?>