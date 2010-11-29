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
class sbView_root_backend extends sbView {
	
	/**
	* 
	*/
	protected $bLoginRequired = TRUE;
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	protected function init() {
		parent::init();
	}
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		switch ($sAction) {
			
			// just display the login screen
			case 'display':
				// jump back to previous node/view/action if possible
				$sZombieRequest = sbSession::getData('last_recallable_action');
				if ($sZombieRequest != NULL) {
					$_RESPONSE->addData($sZombieRequest, 'lastRecallableAction');
				}
				break;
				
			default:
				throw new sbException(__CLASS__.': action not recognized ('.$sAction.')');
			
		}
		
		return (NULL);
		
		
	}
	
	
	
}


?>