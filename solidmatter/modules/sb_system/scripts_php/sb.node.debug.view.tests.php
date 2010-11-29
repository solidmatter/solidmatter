<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbSystem]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

import('sb.system.debug');

//------------------------------------------------------------------------------
/**
*/
class sbView_debug_tests extends sbView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'display':
				
				break;
			
			case 'test_progressbar':
				$aParams['init_url'] = '/'.$this->nodeSubject->getProperty('jcr:uuid').'/tests/init_progressbar';
				$aParams['user_uuid'] = User::getUUID();
				$aParams['subject_uuid'] = $this->nodeSubject->getProperty('jcr:uuid');
				$aParams['uid'] = 'testcounter';
				$_RESPONSE->addCommand('showProgress', $aParams);
				break;
				
			case 'init_progressbar':
				$sUserUUID = User::getUUID();
				$sSubjectUUID = $this->nodeSubject->getProperty('jcr:uuid');
				$sUID = 'testcounter';
				for ($i=0; $i<=100; $i++) {
					$sStatus = $i.'% done';
					$stmtUpdate = $this->crSession->prepareKnown('sb_system/progress/update');
					$stmtUpdate->bindParam('user_uuid', $sUserUUID, PDO::PARAM_STR);
					$stmtUpdate->bindParam('subject_uuid', $sSubjectUUID, PDO::PARAM_STR);
					$stmtUpdate->bindParam('uid', $sUID, PDO::PARAM_STR);
					$stmtUpdate->bindParam('status', $sStatus, PDO::PARAM_STR);
					$stmtUpdate->bindParam('percentage', $i, PDO::PARAM_STR);
					$stmtUpdate->execute();
					sleep(1);
				}
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
				
		}
	}
	
}

?>