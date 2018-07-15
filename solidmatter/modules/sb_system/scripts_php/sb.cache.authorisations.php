<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/** 
*/
class AuthorisationCache {
	
	const AUTH_EFFECTIVE = 1;
	const AUTH_AGGREGATED = 2;
	
	protected $crSession = NULL;
	
	//--------------------------------------------------------------------------
	/**
	 * Creates an Imagecache for the given Session.
	 * @param
	 */
	public function __construct(sbCR_Session $crSession) {
		$this->crSession = $crSession;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function storeAuthorisations($sSubjectUUID, $sEntityUUID, $eAuthType, $aAuthorisations) {
		$stmtStore = $this->crSession->getDatabase()->prepareKnown('sb_system/cache/authorisation/store');
		if ($eAuthType == self::AUTH_EFFECTIVE) {
			$sAuthType = 'EFFECTIVE';
		} else {
			$sAuthType = 'AGGREGATED';
		}
		foreach ($aAuthorisations as $sAuthorisation => $sGrantType) {
			$stmtStore->bindParam('subject_uuid', $sSubjectUUID, PDO::PARAM_STR);
			$stmtStore->bindParam('entity_uuid', $sEntityUUID, PDO::PARAM_STR);
			$stmtStore->bindParam('authorisation', $sAuthorisation, PDO::PARAM_STR);
			$stmtStore->bindParam('granttype', $sGrantType, PDO::PARAM_STR);
			$stmtStore->bindParam('authtype', $sAuthType, PDO::PARAM_STR);
			$stmtStore->execute();
			$stmtStore->closeCursor();
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function loadAuthorisations($sSubjectUUID, $sEntityUUID, $eAuthType) {
		$aAuthorisations = array();
		$stmtLoad = $this->crSession->getDatabase()->prepareKnown('sb_system/cache/authorisation/load');
		if ($eAuthType == self::AUTH_EFFECTIVE) {
			$sAuthType = 'EFFECTIVE';
		} else {
			$sAuthType = 'AGGREGATED';
		}
		$stmtLoad->bindParam('subject_uuid', $sSubjectUUID, PDO::PARAM_STR);
		$stmtLoad->bindParam('entity_uuid', $sEntityUUID, PDO::PARAM_STR);
		$stmtLoad->bindParam('authtype', $sAuthType, PDO::PARAM_STR);
		$stmtLoad->execute();
		foreach ($stmtLoad as $aRow) {
			$aAuthorisations[$aRow['fk_authorisation']] = $aRow['e_granttype'];
		}
		$stmtLoad->closeCursor();
		return ($aAuthorisations);
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function clearAuthorisations($sEntityUUID = NULL) {
		
		if ($sEntityUUID == NULL) {
			$stmtClear = $this->crSession->getDatabase()->prepareKnown('sb_system/cache/authorisation/empty');
		} else {
			$stmtClear = $this->crSession->getDatabase()->prepareKnown('sb_system/cache/authorisation/clear');
			$stmtClear->bindParam('entity_uuid', $sEntityUUID, PDO::PARAM_STR);
		}
		$stmtClear->execute();
		$stmtClear->closeCursor();
	}
	
	//--------------------------------------------------------------------------
	/**
	* Alias for clearAuthorisations() for sbCache interface compliance
	* @param 
	* @return 
	*/
	public function clear() {
		$this->clearAuthorisations();
	}
	
}

?>