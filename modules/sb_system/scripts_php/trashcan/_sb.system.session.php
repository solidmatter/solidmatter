<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter:sb_system
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------


class sbSession {
	
	public $aData = array();
	
	private $sSessionID = '';
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($sSessionID) {
		$this->sSessionID = $sSessionID;
		$this->loadSession();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __destruct() {
		$this->storeSession();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function isLoggedIn() {
		if (isset($this->aData['userdata']['user_id'])) {
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
	function getUserId() {
		if (isset($this->aData['userdata']['user_id'])) {
			return ($this->aData['userdata']['user_id']);
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function addData($sKey, $mData) {
		$this->aData[$sKey] = $mData;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getData($sKey) {
		if (isset($this->aData[$sKey])) {
			return ($this->aData[$sKey]);
		}
		return (NULL);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getCurrentLocale() {
		return ('ger');	
	}

	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	private function loadSession() {
		$DB = DBFactory::getInstance('system');
		$stmtLoadSession = $DB->prepareKnown('sb_system/session/load');
		$stmtLoadSession->bindParam('session_id', $this->sSessionID, PDO::PARAM_STR);
		$stmtLoadSession->execute();
		foreach ($stmtLoadSession as $aRow) {
			$this->aData = unserialize($aRow['s_data']);
		}
		$stmtLoadSession->closeCursor();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function storeSession() {
		if ($this->sSessionID == NULL) {
			return (FALSE);	
		}
		$DB = DBFactory::getInstance('system');
		$stmtStoreSession = $DB->prepareKnown('sb_system/session/store');
		$stmtStoreSession->bindParam('session_id', $this->sSessionID, PDO::PARAM_STR);
		$sSerializedData = serialize($this->aData);
		$stmtStoreSession->bindParam('data', $sSerializedData, PDO::PARAM_STR);
		try {
			$stmtStoreSession->execute();
			$stmtStoreSession->closeCursor();
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function destroy() {
		$DB = DBFactory::getInstance('system');
		$stmtDestroySession = $DB->prepareKnown('sb_system/session/destroy');
		$stmtDestroySession->bindParam('session_id', $this->sSessionID, PDO::PARAM_STR);
		$stmtDestroySession->execute();
		$stmtDestroySession->closeCursor();
		$this->sSessionID = NULL;
	}

}

?>