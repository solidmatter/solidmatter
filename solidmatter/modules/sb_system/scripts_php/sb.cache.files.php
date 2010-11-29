<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/** TODO: finish the code
*/
class FileCache implements sbCache {
	
	protected $sPrefix = '';
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($sPrefix = NULL) {
		if ($sPrefix != NULL) {
			$this->sPrefix = $sPrefix;
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function storeData($sKey, $mData, $sSubject, $sModifier) {
		
		
		
		return (TRUE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function loadData($sKey) {
		$mData = unserialize(file_get_contents($this->getFilename()));
		return ($mData);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function exists($sKey) {
		if (file_exists($this->getFilename($sKey))) {
			return (TRUE);
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function clear($sKey = '', $s) {
		$sKey = $sKey.'%';
		$stmtClear = System::getDatabase()->prepareKnown('sbSystem/cache/flat/clear');
		$stmtClear->bindValue('key', $this->sPrefix.$sKey, PDO::PARAM_STR);
		$stmtClear->execute();
		$stmtClear->closeCursor();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getInfo() {
		return (array('info' => 'not yet implemented'));	
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function getFilename($sKey, $sSubject = NULL, $sModifier = NULL) {
		return (array('info' => 'not yet implemented'));
	}
	
}

?>