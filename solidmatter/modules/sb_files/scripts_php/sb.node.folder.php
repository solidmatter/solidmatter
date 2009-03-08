<?php
//------------------------------------------------------------------------------
/**
* @package	solidMatter:sb_system
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sbSystem:sb.tools.strings.conversion');

//------------------------------------------------------------------------------
/**
*/
class sbNode_folder extends sbNode {
	
	protected $aMimetypeMapping = NULL;
	
	//--------------------------------------------------------------------------
	/**
	* Initializes the object, replacing queries.
	*/
	protected function __setQueries() {
		parent::__setQueries();
		$this->aQueries['loadChildren']['byMode'] = 'sbCR/node/loadChildren/mode/standard/byLabel';
	}
	
	//--------------------------------------------------------------------------
	/**
	* Overloads the default method.
	* Returns the last used view (list or thumbnail) if it was set before
	* @return string name of the default view
	*/
	protected function getDefaultViewName() {
		$sDefaultView = sbSession::getData('sbFiles:Folder:defaultView');
		if ($sDefaultView != NULL) {
			return ($sDefaultView);
		} else {
			return (parent::getDefaultViewName());
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* Overloads the default method.
	* Saves the last used view (list or thumbnail) and calls the parent method
	* afterwards.
	* @param string the view to be called, or NULL if default should be called
	* @param string the action to be called, or NULL if default should be called
	* @return DOMElement the view element
	*/
	public function callView($sView = NULL, $sAction = NULL) {
		if ($sView == 'thumbnails' || $sView == 'list') {
			sbSession::addData('sbFiles:Folder:defaultView', $sView);
		}
		return (parent::callView($sView, $sAction));
	}
	
	//--------------------------------------------------------------------------
	/**
	* name => the file name only
	* size => the file size
	* type => the mimetype
	* file => the full, absolute filename
	* @param 
	* @return 
	*/
	public function addFile($aFileInfo) {
		
		// decide which nodetype should be used
		$aMimetypeMapping = $this->getMimetypeMapping();
		if (isset($aMimetypeMapping[$aFileInfo['type']])) {
			$sNodetype = $aMimetypeMapping[$aFileInfo['type']];
		} else {
			$sNodetype = 'sbFiles:Asset';
		}
		
		// if no imported name is specified, transform the original
		if (!isset($aFileInfo['name_imported'])) {
			$aFileInfo['name_imported'] = str2urlsafe($aFileInfo['name']);
		}
		
		// TODO: behave differently if already existing node is a folder
		if ($this->hasNode($aFileInfo['name_imported'])) {
			
			// TODO: implement question what to do
			throw new LazyBastardException(__CLASS__.': a node named "'.$aFileInfo['name_imported'].'" already exists under "'.$this->getName().'"');
			
		} else {
			$nodeNew = $this->addNode($aFileInfo['name_imported'], $sNodetype);
			$nodeNew->setProperty('label', $aFileInfo['name']);
			$nodeNew->setProperty('properties_size', $aFileInfo['size']);
			$nodeNew->setProperty('properties_mimetype', $aFileInfo['type']);
			$this->save();
			$fpAsset = fopen($aFileInfo['file'], 'rb');
			$nodeNew->saveBinaryProperty('properties_content', $fpAsset);
			fclose($fpAsset);
		}

	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getMimetypeMapping() {
		
		static $aMimetypeMapping = NULL;
		
		if ($aMimetypeMapping != NULL) {
			return ($aMimetypeMapping);
		}
		
		$aMimetypeMapping = array();
		$stmtMimetypes = $this->crSession->prepareKnown('sb_system/folder/view/upload/getMimetypeMapping');
		$stmtMimetypes->execute();
		foreach ($stmtMimetypes as $aRow) {
			$aMimetypeMapping[$aRow['s_mimetype']] = $aRow['fk_nodetype'];			
		}
		$stmtMimetypes->closeCursor();
		
		return ($aMimetypeMapping);
		
	}
	
}

?>