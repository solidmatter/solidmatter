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
class sbNode_tpl_menu extends sbNode {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function gatherContent($bPreview = TRUE) {
		
		//parent::gatherContent();
		
		$sMenuRootUUID = $this->getProperty('config_menuroot');
		$nodeMenuRoot = $this->crSession->getNode($sMenuRootUUID);
		
		$elemData = $this->getMenu($nodeMenuRoot, NULL, $this->getProperty('config_numlevels'));
		$elemData->setAttribute('id', $this->getProperty('name'));
		
		$sPath = $nodeMenuRoot->getPath();
		$sPath = str_replace('::', '/', $sPath);
		$aPath = explode('/', $sPath);
		unset($aPath[0]);
		unset($aPath[1]);
		$sPath = implode('/', $aPath);
		$elemData->setAttribute('path', $sPath);
		
		$this->appendElement($elemData);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getMenu($nodeCurrent, $elemCurrent, $iLevelsLeft) {
		
		if ($elemCurrent == NULL) {
			$elemCurrent = $this->elemSubject->ownerDocument->createElement('menu');	
		}
		
		// exit when level limit reached 
		if ($iLevelsLeft == 0) {
			return(NULL);
		}
		
		$niChildren = $nodeCurrent->loadChildren('tree', FALSE, TRUE, TRUE);
		foreach ($niChildren as $nodeChild) {
			$elemChild = $nodeChild->getElement(FALSE);
			$elemCurrent->appendChild($elemChild);
			$this->getMenu($nodeChild, $elemChild, $iLevelsLeft-1);
		}
		
		return($elemCurrent);
		
	}
	
			
}

?>