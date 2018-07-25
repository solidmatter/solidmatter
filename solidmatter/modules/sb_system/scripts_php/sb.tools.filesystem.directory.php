<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Tools
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.tools.filesystem');
import('sb.tools.filesystem.object');
import('sb.tools.filesystem.file');

//------------------------------------------------------------------------------
/**
*/
class sbDirectory extends sbFilesystemObject {
	
	// child directories
	protected $aDirectories = array();
	protected $aDirectoriesBackup = array();
	
	// child files
	protected $aFiles = array();
	protected $aFilesBackup = array();
	
	// are the sizes already examined?
	protected $bSizesRead = FALSE;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function __init(string $sRelPath) {
		parent::__init($sRelPath);
		$this->read();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function read(bool $bSkipParents = TRUE) {
		
		// checks
		if ($this->aInfo['abs_path'] == NULL) {
			throw new sbException('directory is not yet specified');
		}
		if (!file_exists($this->aInfo['abs_path']) || !is_dir($this->aInfo['abs_path'])) {
			throw new sbException('directory "'.$this->aInfo['abs_path'].'" does not exist');
		}
		
		// clear in case read() is called multiple times
		$this->aDirectories = array();
		$this->aDirectoriesBackup = array();
		$this->aFiles = array();
		$this->aFilesBackup = array();
		
		// read and store
		$aEntries = scandir($this->aInfo['abs_path']);
		foreach ($aEntries as $sEntry) {
			if (is_dir($this->aInfo['abs_path'].$sEntry)) {
				if ($bSkipParents) {
					if ($sEntry == '.' || $sEntry == '..') {
						continue;
					}
				}
				$this->aDirectories[] = array('name' => $sEntry);
			} else {
				$this->aFiles[] = array('name' => $sEntry);
			}
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function readSizes(bool $bIncludeDirs = FALSE) {
		
		// get file sizes
		foreach ($this->aFiles as $iIndex => $aFile) {
			$this->aFiles[$iIndex]['size'] = filesize($this->aInfo['abs_path'].$aFile['name']);
			$this->aFiles[$iIndex]['hrsize'] = filesize2display($this->aFiles[$iIndex]['size']);
		}
		
		// get optional dirsize
		if ($bIncludeDirs) {
			foreach ($this->aDirectories as $iIndex => $aDirectory) {
				$drCurrent = new sbDirectory($this->aInfo['abs_path'].$aDirectory['name'].'/');
				$this->aDirectories[$iIndex]['size'] = $drCurrent->getAccumulatedSize();
				$this->aDirectories[$iIndex]['hrsize'] = filesize2display($this->aDirectories[$iIndex]['size']);
			}
		}
		
		$this->bSizesRead = TRUE;
		
		$this->aDirectory['size'] = $this->getAccumulatedSize();
		$this->aDirectory['hrsize'] = filesize2display($this->aDirectory['size']);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function readTimes(bool $bIncludeDirs = FALSE) {
		
		// get file times
		foreach ($this->aFiles as $iIndex => $aFile) {
			$this->aFiles[$iIndex]['ctime'] = filectime($this->aInfo['abs_path'].$aFile['name']);
			$this->aFiles[$iIndex]['mtime'] = filemtime($this->aInfo['abs_path'].$aFile['name']);
			$this->aFiles[$iIndex]['atime'] = fileatime($this->aInfo['abs_path'].$aFile['name']);
		}
		
		// get optional dir times
		if ($bIncludeDirs) {
			foreach ($this->aDirectories as $iIndex => $aDirectory) {
				$this->aDirectories[$iIndex]['ctime'] = filectime($this->aInfo['abs_path'].$aDirectory['name'].'/');
				$this->aDirectories[$iIndex]['mtime'] = filemtime($this->aInfo['abs_path'].$aDirectory['name'].'/');
				$this->aDirectories[$iIndex]['atime'] = fileatime($this->aInfo['abs_path'].$aDirectory['name'].'/');
			}
		}
		
		$this->bTimesRead = TRUE;
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function readDirectorySpecialTimes(string $sFilename, string $sStorageKey) {
		
		// get dir special times
		foreach ($this->aDirectories as $iIndex => $aDirectory) {
			$sFullFilename = $this->aInfo['abs_path'].$aDirectory['name'].'/'.$sFilename; 
			if (file_exists($sFullFilename)) {
				$this->aDirectories[$iIndex][$sStorageKey] = file_get_contents($sFullFilename);
			} else {
				$this->aDirectories[$iIndex][$sStorageKey] = '2000000000';
			}
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getAccumulatedSize() : int {
		
		if (!$this->bSizesRead) {
			$this->readSizes();
		}
		
		$iSize = 0;
		foreach ($this->aFiles as $aFile) {
			$iSize += $aFile['size'];
		}
		
		return ($iSize);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function filterFiles(string $sRegEx, bool $bExcludeMatches = FALSE) {
		
		foreach ($this->aFiles as $iIndex => $aFile) {
			if ($bExcludeMatches && preg_match($sRegEx, $aFile['name'])) {
				unset($this->aFiles[$iIndex]);
			}
			if (!$bExcludeMatches && !preg_match($sRegEx, $aFile['name'])) {
				unset($this->aFiles[$iIndex]);
			}
		}
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function countFiles() : int {
		return (count($this->aFiles));
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function resetFilter() {
		$this->aFiles = $this->aFilesBackup;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param boolean If TRUE, delivers an array of sbFile object, otherwise delivers an array of filenames.
	* @return 
	*/
	public function getFiles(bool $bAsFiles = FALSE) : array {
		
		$aFiles = array();
		
		foreach ($this->aFiles as $aFile) {
			if ($bAsFiles) {
				if ($fileCurrent = $this->getFile($aFile['name'])) {
					$aFiles[] = $fileCurrent;
				}
			} else {
				$aFiles[] = $aFile['name'];
			}
		}
		
		return ($aFiles);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getFile(string $sRelPath) : sbFile {
		
		// fill search array if necessary
		$aSearch = array();
		if (!is_array($sRelPath)) {
			$aSearch[0] = $sRelPath;
		} else {
			$aSearch = $sRelPath;
		}
		
		foreach ($aSearch as $sRelPath) {
			if (file_exists($this->aInfo['abs_path'].$sRelPath)) {
				return (new sbFile($this->aInfo['abs_path'].$sRelPath));
			}
		}
		
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function hasFile(string $sRelPath) : bool {
		foreach ($this->aFiles as $aFileInfo) {
			if ($aFileInfo['name'] == $sRelPath) {
				return (TRUE);
			}
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getDirectories(bool $bAsDirectoryObjects = FALSE) : array {
		
		$aDirectories = array();
		
		if (!$bAsDirectoryObjects) {
			foreach ($this->aDirectories as $aDirectory) {
				$aDirectories[] = $aDirectory['name'];
			}
		} else {
			foreach ($this->aDirectories as $aDirectory) {
				$aDirectories[] = new sbDirectory($this->aInfo['abs_path'].$aDirectory['name']);
			}
		}
		
		return ($aDirectories);
	
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function sort(string $sSortcriterium = 'name', bool $bDescending = FALSE) {
		import('sb.tools.arrays');
		ivsort($this->aFiles, $sSortcriterium, TRUE);
		ivsort($this->aDirectories, $sSortcriterium, TRUE);
		if ($bDescending) {
			array_reverse($this->aFiles);
			array_reverse($this->aDirectories);
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getElement(string $sContainerName, bool $bIncludeDirs = FALSE) : DOMElement {
		
		$this->aInfo['totalfiles'] = count($this->aFiles);
		$this->aInfo['totaldirs'] = count($this->aDirectories);
		
		$domFiles = new DOMDocument();
		$elemContainer = $domFiles->createElement($sContainerName);
		foreach ($this->aInfo as $sKey => $sValue) {
			$elemContainer->setAttribute($sKey, $sValue);
		}
		
		// generate dir elements
		if ($bIncludeDirs) {
			$elemDirectories = $domFiles->createElement('directories');
			$elemDirectories->setAttribute('count', count($this->aDirectories));
			foreach ($this->aDirectories as $aDirectory) {
				$elemDir = $domFiles->createElement('directory');
				foreach ($aDirectory as $sAttribute => $sValue) {
					$elemDir->setAttribute($sAttribute, $sValue);	
				}
				$elemContainer->appendChild($elemDir);
			}
		}
		
		// generate file elements
		$elemFiles = $domFiles->createElement('files');
		$elemFiles->setAttribute('count', count($this->aFiles));
		foreach ($this->aFiles as $aFile) {
			$elemFile = $domFiles->createElement('file');
			foreach ($aFile as $sAttribute => $sValue) {
				$elemFile->setAttribute($sAttribute, $sValue);
			}
			$elemFiles->appendChild($elemFile);
		}
		$elemContainer->appendChild($elemFiles);
		
		return ($elemContainer);
		
	}
	
}

?>