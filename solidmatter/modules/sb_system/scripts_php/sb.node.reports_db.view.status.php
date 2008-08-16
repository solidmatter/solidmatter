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
class sbView_reports_db_status extends sbView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'display':
				
				$stmtGetStatus = $this->crSession->prepareKnown('sb_system/reports_db/status/status');
				$stmtGetStatus->execute();
				$_RESPONSE->addData($stmtGetStatus->fetchDOM('status'));
				
				$stmtGetVars = $this->crSession->prepareKnown('sb_system/reports_db/status/variables');
				$stmtGetVars->execute();
				$_RESPONSE->addData($stmtGetVars->fetchDOM('variables'));
				
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
	}
	
}


?>