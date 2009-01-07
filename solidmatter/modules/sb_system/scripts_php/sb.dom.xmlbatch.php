<?php

//------------------------------------------------------------------------------
/**
* 
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	0.00.00
*/
//------------------------------------------------------------------------------

import('sb.tools.filesystem');

//------------------------------------------------------------------------------
/**
* 
*/
class XMLBatch {
	
	protected $sxmlBatch = NULL;
	
	protected $sIntitialWD = NULL;
	
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($sBatchFile = NULL) {
		
		$this->sInitialWD = getcwd();
		
		if ($sBatchFile != NULL) {
			$this->load($sBatchFile);	
		}
			
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function load($sBatchFile) {
	
		$this->sxmlBatch = simplexml_load_file($sBatchFile);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sSourceDir, $sDestinationDir = '', $aOptions = NULL) {
		
		foreach ($this->sxmlBatch->children() as $sName => $sxmlAction) {
			
			switch ($sName) {
				
				// execute db-query
				case 'query':
					$sQuery = (string) $sxmlAction;
					$sQuery = str_replace('{TABLE_PREFIX}', TABLE_PREFIX, $sQuery);
					$DB->QUERY($sQuery);
					break;
					
				// copy file / dir
				case 'copy':
					$sSource = (string) $sxmlAction;
					$sDestination = (string) $sxmlAction['to'];
					if (!file_exists($sSourceDir.'/'.$sSource)) {
						throw new SBException();
					}
					if (is_dir($sSourceDir.'/'.$sSource)) {
						copydir($sSourceDir.'/'.$sSource, $sDestination, TRUE);
					} else {
						if (file_exists($sDestination)) {
							unlink($sDestination);
						}
						copy($sSourceDir.'/'.$sSource, $sDestination, TRUE);
					}
					break;
					
				// move a file or dir
				case 'move':
					// TODO: implement file/dir moving
					break;
					
				// delete file / dir
				case 'delete':
					$sFSObject = (string) $sxmlAction;
					if (!file_exists($sFSObject)) {
						throw new SBException();
					}
					if (is_dir($sFSObject)) {
						rmdirr($sFSObject);
					} else {
						unlink($sFSObject);
					}
					break;
					
				// execute PHP-script
				case 'script':
					$sScript = (string) $sxmlAction;
					require_once($sSourceDir.'/'.$sScript);
					break;
					
				// update repository information
				case 'repository':
					
					break;
					
			}
		}
	}

}

?>