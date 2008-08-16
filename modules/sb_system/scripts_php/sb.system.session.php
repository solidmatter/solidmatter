<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage Core
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
* 
*/
class sbSession {
	
	private static $sSessionID = '';
	public static $aData = array();
	
	private static $bStoreOnDestruct = TRUE;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($sSessionID) {
		self::$sSessionID = $sSessionID;
		self::loadSession();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __destruct() {
		if (self::$bStoreOnDestruct && self::$sSessionID != NULL) {
			self::storeSession();
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function isLoggedIn() {
		if (isset(self::$aData['userdata']['user_id'])) {
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
	public static function getUserId() {
		if (isset(self::$aData['userdata']['user_id'])) {
			return (self::$aData['userdata']['user_id']);
		}
		return (FALSE);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function addData($sKey, $mData) {
		self::$aData[$sKey] = $mData;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getData($sKey) {
		if (isset(self::$aData[$sKey])) {
			return (self::$aData[$sKey]);
		}
		return (NULL);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getSessionID() {
		return (self::$sSessionID);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function getCurrentLocale() {
		return ('ger');	
	}

	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	private static function loadSession() {
		$stmtLoadSession = System::getDatabase()->prepareKnown('sbSystem/session/load');
		$stmtLoadSession->bindParam('session_id', self::$sSessionID, PDO::PARAM_STR);
		$stmtLoadSession->execute();
		foreach ($stmtLoadSession as $aRow) {
			self::$aData = unserialize($aRow['s_data']);
		}
		$stmtLoadSession->closeCursor();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public static function storeSession() {
		if (self::$sSessionID == NULL) {
			return (FALSE);
		}
		$stmtStoreSession = System::getDatabase()->prepareKnown('sbSystem/session/store');
		$stmtStoreSession->bindParam('session_id', self::$sSessionID, PDO::PARAM_STR);
		$sSerializedData = serialize(self::$aData);
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
	public static function disableStoring() {
		self::$bStoreOnDestruct = FALSE;
	}
	
	//--------------------------------------------------------------------------
	/**
	* FIXME: 
	* @param 
	* @return 
	*/
	public static function destroy() {
		$stmtDestroySession = System::getDatabase()->prepareKnown('sbSystem/session/destroy');
		$stmtDestroySession->bindParam('session_id', self::$sSessionID, PDO::PARAM_STR);
		$stmtDestroySession->execute();
		$stmtDestroySession->closeCursor();
		//var_dumpp($stmtDestroySession->rowCount());die();
		self::$aData = NULL;
		self::$sSessionID = NULL;
	}

}

?>