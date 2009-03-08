<?php

//------------------------------------------------------------------------------
/**
* @package	solidMatter[sbFiles]
* @author	()((() [Oliver Müller]
* @version	1.00.00
*/
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
/**
*/
class sbView_autofolder_maintenance extends sbView {
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			case 'display':
				break;
			
			case 'check':
				$aLog = $this->nodeSubject->updateContents(FALSE);
				$_RESPONSE->addData($aLog, 'update_log');
				break;
				
			case 'update':
				$aLog = $this->nodeSubject->updateContents();
				$_RESPONSE->addData($aLog, 'update_log');
				break;
				
			case 'clear':
				$this->nodeSubject->clearContents();
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
	}
	
}


?>