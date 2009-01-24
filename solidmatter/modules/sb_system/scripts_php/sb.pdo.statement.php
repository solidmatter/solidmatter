<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter
*	@subpackage sbPDO
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* 
*/
class sbPDOStatement extends PDOStatement {
	
	private $aDebug = array(
		'statementid' => '',
		'statement' => '',
		'params' => array(),
	);
	
	private $aMetadata = array();
	private $aFilters = array();
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addFilter($sColumn, $eType, $sFormat='') {
		
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addDebugInfo($sQuery, $sID = '') {
		// store query
		$this->aDebug['statement'] = $sQuery;
		$this->aDebug['statementid'] = $sID;
		// store expected params
		$aMatches = array();
		preg_match_all('/(:[a-z0-9_]+)/', $sQuery, $aMatches);
		foreach ($aMatches[1] as $sMatch) {
			$this->aDebug['params'][$sMatch] = '--> NOT BOUND <--';
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function bindValue($sParam, $mValue, $eType) {
		if ($mValue === NULL) {
			$eType = PDO::PARAM_NULL;
		}
		if (substr($sParam, 0, 1) != ':') {
			$sParam = ':'.$sParam;	
		}
		$this->aDebug['params'][$sParam] = $mValue;
		parent::bindValue($sParam, $mValue, $eType);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function bindParam($sParam, &$mValue, $eType) {
		if ($mValue === NULL) {
			$eType = PDO::PARAM_NULL;
		}
		if (substr($sParam, 0, 1) != ':') {
			$sParam = ':'.$sParam;	
		}
		$this->aDebug['params'][$sParam] = $mValue;
		parent::bindParam($sParam, $mValue, $eType);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute() {
		global $_STOPWATCH;
		$_STOPWATCH->checkGroup('php');
		DEBUG::STARTCLOCK('statement');
		try {
			parent::execute();
		} catch (Exception $e) {
			$this->debug($e);
			throw $e;
		}
		DEBUG('PDO: executed statement '.$this->aDebug['statementid'].' ('.DEBUG::STOPCLOCK('statement').'ms)', DEBUG::PDO);
		$_STOPWATCH->checkGroup('pdo');
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function debug($e) {
		var_dumpp($this->aDebug);
		//var_dumpp($e);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function fetchDOM($sRootNodeName = 'resultset', $bIncludeMetadata = FALSE) {
		
		$domGlobal = new DOMDocument('1.0', 'UTF-8');
		$elemResultset = $domGlobal->createElement($sRootNodeName);
		//$elemRows = $domGlobal->createElement('rows');
		
		$i = 0;
		while ($aRow = $this->fetch(PDO::FETCH_ASSOC)) {
			$elemRow = $domGlobal->createElement('row');
			//$elemRow->setAttribute('number', $i);
			$i++;
			foreach ($aRow as $sName => $sData) {
				/*if (substr_count($sData, '&') > 0) {
					$sData = str_replace('&', '&amp;', $sData);
				}*/
				$sData = htmlspecialchars($sData);
				$elemColumn = $domGlobal->createElement($sName, $sData);
				$elemRow->appendChild($elemColumn);
			}
			$elemResultset->appendChild($elemRow);
		}
		
		if ($bIncludeMetadata) {
			$elemMetadata = $domGlobal->createElement('metadata');
			$elemMetadata->setAttribute('rows_affected', $this->rowCount());
			$elemMetadata->setAttribute('row_count', $i);
			$elemResultset->appendChild($elemMetadata);
		}
		
		//$elemResultset->appendChild($elemRows);
		//$elemResultset->appendChild($elemResultset);
		return ($elemResultset);
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function fetchXML() {
		
		$domSubject = $this->fetchDOM();
		return ($domSubject->saveXML());
		
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function fetchElements($sRootNodeName = 'resultset', $bIncludeMetadata = FALSE) {
		
		$domGlobal = new DOMDocument('1.0', 'UTF-8');
		$elemResultset = $domGlobal->createElement($sRootNodeName);
		
		$i = 0;
		while ($aRow = $this->fetch(PDO::FETCH_ASSOC)) {
			$elemRow = $domGlobal->createElement('row');
			//$elemRow->setAttribute('number', $i);
			$i++;
			foreach ($aRow as $sName => $sData) {
				/*if (substr_count($sData, '&') > 0) {
					$sData = str_replace('&', '&amp;', $sData);
				}*/
				//$sData = htmlspecialchars($sData);
				$elemRow->setAttribute($sName, $sData);
			}
			$elemResultset->appendChild($elemRow);
		}
		
		if ($bIncludeMetadata) {
			$elemMetadata = $domGlobal->createElement('metadata');
			$elemMetadata->setAttribute('rows_affected', $this->rowCount());
			$elemMetadata->setAttribute('row_count', $i);
			$elemResultset->appendChild($elemMetadata);
		}
		
		//$elemResultset->appendChild($elemRows);
		//$elemResultset->appendChild($elemResultset);
		return ($elemResultset);
		
	}
	
	
}


?>