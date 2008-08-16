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
class sbView_root_welcome extends sbView {
	
	//protected $bLoginRequired = TRUE;
	
	protected function __init() {
		parent::__init();
		$this->aQueries['loadUserInfo'] = 'sb_system/root/view/welcome/loadUserdata';
	}
	
	public function execute($sAction) {
		
		global $_RESPONSE;
		
		$stmtInfo = $this->crSession->prepareKnown($this->aQueries['loadUserInfo']);
		$stmtInfo->bindValue('user_id', User::getUUID(), PDO::PARAM_STR);
		$stmtInfo->execute();
		$_RESPONSE->addData($stmtInfo->fetchDOM('userinfo'));
		
		//$_RESPONSE->setStylesheet('root.welcome.xsl');
		
		return (NULL);
		
		
	}
	
	
	
}


?>