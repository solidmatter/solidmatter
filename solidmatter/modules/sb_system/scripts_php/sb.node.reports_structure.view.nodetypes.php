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
class sbView_reports_structure_nodetypes extends sbView {
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'overview':
				
				$stmtGetInfo = $this->nodeSubject->getSession()->prepareKnown('sbSystem/reports_structure/nodetypes/overview');
				$stmtGetInfo->execute();
				$_RESPONSE->addData($stmtGetInfo->fetchElements('nodetypes'));
				// TODO: use funcion to get only module names
				$aModules = System::getModules();
				foreach ($aModules as $sModule => $unused) {
					$_RESPONSE->addLocale($sModule);
				}
				break;
				
			case 'details':
				
				
				break;
			
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
	}
	
}


?>