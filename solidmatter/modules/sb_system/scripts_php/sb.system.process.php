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
class ProgressBar {
	
	private $sUID;
	private $sUserUUID;
	private $sSubjectUUID;
	
	private $crSession;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function __construct($crSession, $sUID, $sSubjectUUID, $sUserUUID = NULL) {
		$this->crSession = $crSession;
		$this->sUID = $sUID;
		$this->sSubjectUUID = $sSubjectUUID;
		if ($sUserUUID == NULL) {
			$sUserUUID = $crSession->getRootNode()->getProperty('jcr:uuid');
		}
		$this->sUserUUID = $sUserUUID;
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function setStatus($iPercentageDone, $sStatus) {
		$stmtUpdate = $this->crSession->prepareKnown('sb_system/progress/update');
		$stmtUpdate->bindParam('user_uuid', $this->sUserUUID, PDO::PARAM_STR);
		$stmtUpdate->bindParam('subject_uuid', $this->sSubjectUUID, PDO::PARAM_STR);
		$stmtUpdate->bindParam('uid', $this->sUID, PDO::PARAM_STR);
		$stmtUpdate->bindParam('status', $sStatus, PDO::PARAM_STR);
		$stmtUpdate->bindParam('percentage', $iPercentageDone, PDO::PARAM_STR);
		$stmtUpdate->execute();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function getStatus() {
		$stmtGetStatus = $this->crSession->prepareKnown('sb_system/progress/getStatus');
		$stmtGetStatus->bindParam('user_uuid', $this->sUserUUID, PDO::PARAM_STR);
		$stmtGetStatus->bindParam('subject_uuid', $this->sSubjectUUID, PDO::PARAM_STR);
		$stmtGetStatus->bindParam('uid', $this->sUID, PDO::PARAM_STR);
		$stmtGetStatus->execute();
		foreach ($stmtGetStatus as $aRow) {
			$sStatus = $aRow['s_status'];
			$iPercentage = $aRow['n_percentage'];
		}
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function finish() {
		
	}
	
}

?>