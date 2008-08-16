<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbView_root_utilities extends sbView {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_REQUEST;
		global $_RESPONSE;
		
		switch($sAction) {
			
			case 'show_progress':
				$sUserUUID = $_REQUEST->getParam('user_uuid');
				$sSubjectUUID = $_REQUEST->getParam('subject_uuid');
				$sUID = $_REQUEST->getParam('uid');
				$stmtGetStatus = $this->crSession->prepareKnown('sb_system/progress/getStatus');
				$stmtGetStatus->bindParam('user_uuid', $sUserUUID, PDO::PARAM_STR);
				$stmtGetStatus->bindParam('subject_uuid', $sSubjectUUID, PDO::PARAM_STR);
				$stmtGetStatus->bindParam('uid', $sUID, PDO::PARAM_STR);
				$stmtGetStatus->execute();
				foreach ($stmtGetStatus as $aRow) {
					$sStatus = $aRow['s_status'];
					$iPercentage = $aRow['n_percentage'];
				}
				$elemStatus = $_RESPONSE->createElement('status');
				$elemStatus->setAttribute('user', $sUserUUID);
				$elemStatus->setAttribute('subject', $sSubjectUUID);
				$elemStatus->setAttribute('uid', $sUID);
				$elemStatus->setAttribute('status', $sStatus);
				$elemStatus->setAttribute('percentage', $iPercentage);
				$_RESPONSE->addData($elemStatus);
				break;
				
			case 'export_branch':
				if (!User::isAdmin()) {
					throw new SecurityException('You are not allowed to export repository content');					
				}
				$nodeSubject = $this->crSession->getNode($_REQUEST->getParam('subject_uuid'));
				//var_dumpp('serializing');
				header('Content-type: text/xml');
				sbCR_Utilities::serializeBranch($nodeSubject);
				exit();
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
			
	}	
	
}

?>