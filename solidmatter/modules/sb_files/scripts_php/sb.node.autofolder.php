<?php
//------------------------------------------------------------------------------
/**
* @package	solidMatter:sb_system
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sbFiles:sb.node.folder');
import('sbSystem:sb.tools.filesystem.directory');

//------------------------------------------------------------------------------
/**
*/
class sbNode_autofolder extends sbNode_folder {
	
	protected $aUpdateLog = array();
	
	//--------------------------------------------------------------------------
	/**
	* Imports the local filesystem folder given in property 'config_realpath' (non-recursive).
	*/
	public function updateContents($bPersistChanges = TRUE) {
		
		// init update log
		$this->aUpdateLog = array();
		
		// read filenames in directory
		$dirCurrent = new sbDirectory($this->getProperty('config_realfolder'));
		
		// filter out well-known unwanted files
		$dirCurrent->filterFiles('/Thumbs\.db/', TRUE);
		
		// remove inexistent files (filename has to be identical to node name)
		foreach ($this->getChildren('list') as $nodeFile) {
			if (!$dirCurrent->hasFile($nodeFile->getName())) {
				if ($bPersistChanges) {
					$nodeFile->remove();
				}
				$this->logEntry('REMOVED', $nodeFile->getName());
			}
		}
		
		// import new files
		foreach ($dirCurrent->getFiles(TRUE) as $fileCurrent) {
			
			// don't import files with unsafe names
			if (str2urlsafe($fileCurrent->getName()) != $fileCurrent->getName()) {
				$this->logEntry('SKIPPED_NOTURLSAFE', $fileCurrent->getName());
//				throw new sbException(__CLASS__.': file name "'.$fileCurrent->getName().'" is not url-safe');
				continue;
			}
			
			// skip existing nodes
			if ($this->hasNode($fileCurrent->getName())) {
				// TODO: check mtime and update content if necessary
				$this->logEntry('SKIPPED_UNCHANGED', $fileCurrent->getName());
				continue;
			}
			
			// prepare information
			$aFileInfo['name'] = $fileCurrent->getName();
			$aFileInfo['type'] = $fileCurrent->getMimetype();
			$aFileInfo['size'] = $fileCurrent->getSize();
			$aFileInfo['file'] = $fileCurrent->getAbsPath();
			
			if ($bPersistChanges) {
				$this->addFile($aFileInfo);
			}
			$this->logEntry('ADDED', $fileCurrent->getName());
			
		}
		
		if ($bPersistChanges) {
			$this->crSession->save();
		}
		
		return ($this->aUpdateLog);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Imports the local filesystem folder given in property 'config_realpath' (non-recursive).
	*/
	public function clearContents() {
		
		$niContent = $this->getChildren('clear');
		
		foreach ($niContent as $nodeContent) {
			$nodeContent->remove();
		}
		
		$this->crSession->save();
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* Imports the local filesystem folder given in property 'config_realpath' (non-recursive).
	*/
	public function logEntry($sType, $sData) {
		
		$this->aUpdateLog[] = array(
			'type' => $sType,
			'filename' => $sData
		);
			
	}
	
}

?>