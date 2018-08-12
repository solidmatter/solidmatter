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
	
	private $pdoOrigin = NULL;
	
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
// 	public function addFilter($sColumn, $eType, $sFormat='') {
		
		
// 	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @param
	 * @return
	 */
	public function setPDO(sbPDO $pdoParent) {
		$this->pdoOrigin = $pdoParent;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addDebugInfo(string $sQuery, string $sID = '') {
		// store query
		$this->aDebug['statement'] = $sQuery;
		$this->aDebug['statementid'] = $sID;
		// store expected params
// 		$aMatches = array();
// 		preg_match_all('/(:[a-z0-9_]+)/', $sQuery, $aMatches);
// 		foreach ($aMatches[1] as $sMatch) {
// 			$this->aDebug['params'][$sMatch] = '--> NOT BOUND <--';
// 		}
	}
	
	//--------------------------------------------------------------------------
	/**
	 *
	 * @return
	 */
	public function getStatementID() : string {
		if (isset($this->aDebug['statementid'])) {
			return ($this->aDebug['statementid']);
		} else {
			return ('Query has no ID');
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param string 
	* @param mutliple 
	* @param int
	* @return 
	*/
	public function bindValue($sParam, $mValue, $eType = PDO::PARAM_STR) {
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
	* @param string
	* @param 
	* @param int
	* @param int 
	* @param 
	* @return 
	*/
	public function bindParam($sParam, &$mValue, $eType = PDO::PARAM_STR, $iLength = NULL, $mDriverOptions = NULL) {
		if ($mValue === NULL) {
			$eType = PDO::PARAM_NULL;
		}
		if (substr($sParam, 0, 1) != ':') {
			$sParam = ':'.$sParam;	
		}
		$this->aDebug['params'][$sParam] = $mValue;
		parent::bindParam($sParam, $mValue, $eType, $iLength, $mDriverOptions);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param array 
	* @return 
	*/
	public function execute($aInputParameters = NULL) {
		Stopwatch::checkGroup('php');
		DEBUG::STARTCLOCK('statement');
		try {
			parent::execute($aInputParameters);
		} catch (Exception $e) {
			DEBUG('PDO: statement failed '.$this->aDebug['statementid'].' ('.DEBUG::STOPCLOCK('statement').'ms)', DEBUG::PDO);
			$this->debug();
			throw $e;
		}
		DEBUG('PDO: executed statement '.$this->aDebug['statementid'].' ('.DEBUG::STOPCLOCK('statement').'ms)', DEBUG::PDO);
		Stopwatch::checkGroup('pdo');
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Outputs debug information for this query.
	 * @param bool 
	 * @return
	 */
	public function debug(bool $bSaveToLog = FALSE) {
		if ($bSaveToLog) {
			// Todo: extend this to match
			DEBUG(var_export($this->aDebug, TRUE), TRUE);
		} else {
			if ($this->aDebug['statementid'] != '') {
				var_dumpp($this->aDebug['statementid'], 'StatementID');
			}
			var_dumpp($this->aDebug['statement'], 'Statement');
			var_dumpp($this->aDebug['params'], 'Parameters');
			var_dumpp(parent::errorCode(), 'Error Code');
			var_dumpp(parent::errorInfo(), 'Error Info');
// 			var_dumpp(parent::debugDumpParams(), 'PDOStatement::debugDumParams()');
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Executes an EXPLAIN for the current prepared statement, dumps the details and .
	 * Relies on an initial call to addDebugInfo and fully bound parameters!
	 */
	public function explain() {
		$stmtExplain = $this->getExplainStatment();
		$stmtExplain->execute();
		var_dumpp($stmtExplain);
		var_dumppp($stmtExplain->fetchAll(PDO::FETCH_ASSOC));
	}
	
	//--------------------------------------------------------------------------
	/**
	 * Generates an EXPLAIN statement for the current statement.
	 * Relies on an initial call to addDebugInfo and fully bound parameters!
	 * @param
	 * @return
	 */
	public function getExplainStatement() {
		$sQuery = 'EXPLAIN '.$this->queryString;
		$stmtExplain = $this->pdoOrigin->prepare($sQuery);
		foreach ($this->aDebug['params'] as $sKey => $sValue) {
			$stmtExplain->bindValue($sKey, $sValue);
		}
		return ($stmtExplain);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param string the name of the resultset root element
	* @param array a set of name mapping instructions for node building ('!row' => 'Element' or 'Column' => 'Element')
	* @param boolean Determines if metadata should be included or not (e.g. affected rows)
	* @return 
	*/
	public function fetchDOM(string $sRootNodeName = 'resultset', array $aMapping = NULL, bool $bIncludeMetadata = FALSE) : DOMElement {
		
		$domGlobal = new DOMDocument('1.0', 'UTF-8');
		$elemResultset = $domGlobal->createElement($sRootNodeName);
		
		$sRowElementName = 'row';
		if (isset($aMapping['!row'])) {
			$sRowElementName = $aMapping['!row'];
		}
		
		$i = 0;
		while ($aRow = $this->fetch(PDO::FETCH_ASSOC)) {
			$elemRow = $domGlobal->createElement($sRowElementName);
			//$elemRow->setAttribute('number', $i);
			$i++;
			foreach ($aRow as $sName => $sData) {
				// don't use db-columname if a mapping is provided
				if (isset($aMapping[$sName])) {
					$sName = $aMapping[$sName];	
				}
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
	* TODO: fetchDOM and fetchElement server the same purpose and both work on DOMDocuments, eliminate one of them
	* @param 
	* @return 
	*/
	public function fetchElements(string $sRootNodeName = 'resultset', array $aMapping = NULL, bool $bIncludeMetadata = FALSE) {
		
		$domGlobal = new DOMDocument('1.0', 'UTF-8');
		$elemResultset = $domGlobal->createElement($sRootNodeName);
		
		$sRowElementName = 'row';
		if (isset($aMapping['!row'])) {
			$sRowElementName = $aMapping['!row'];
		}
		
		$i = 0;
		while ($aRow = $this->fetch(PDO::FETCH_ASSOC)) {
			$elemRow = $domGlobal->createElement($sRowElementName);
			//$elemRow->setAttribute('number', $i);
			$i++;
			foreach ($aRow as $sName => $sData) {
				// don't use db-columname if a mapping is provided
				if (isset($aMapping[$sName])) {
					$sName = $aMapping[$sName];	
				}
				//htmlspecialchars($sData)
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
		
		return ($elemResultset);
		
	}
	
}

?>