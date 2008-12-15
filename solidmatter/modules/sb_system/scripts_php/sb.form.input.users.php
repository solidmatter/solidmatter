<?php

//------------------------------------------------------------------------------
/**
*	@package solidMatter[sbSystem]
*	@subpackage sbForm
*	@author	()((() [Oliver Müller]
*	@version 1.00.00
*/
//------------------------------------------------------------------------------

import('sb.form.input.select');

//------------------------------------------------------------------------------
/**
*/
class sbInput_users extends sbInput_select {
	
	protected $sType = 'users';
	
	protected $aConfig = array(
		'size' => '1',
		'multiple' => 'FALSE',
		'maxselected' => 'unlimited',
		'includeself' => 'FALSE', //TODO: actually use this and following options
		'includehidden' => 'FALSE',
		'allowempty' => 'FALSE',
	);
	
	//--------------------------------------------------------------------------
	/**
	* 
	* @param 
	* @return 
	*/
	public function initConfig() {
		
		$nodeUserAccounts = $this->crSession->getNode('//*[@uid="sb_system:useraccounts"]');;
		
		$stmtUsers = $this->crSession->prepareKnown('sbCR/node/loadChildren/mode/standard/byLabel');
		$stmtUsers->bindValue('parent_uuid', $nodeUserAccounts->getProperty('jcr:uuid'), PDO::PARAM_INT);
		$stmtUsers->bindValue('mode', 'loadusers', PDO::PARAM_STR);
		$stmtUsers->execute();
		
		$aOptions = array();
		foreach ($stmtUsers as $aUser) {
			$aOptions[$aUser['uuid']] = $aUser['s_label'];
		}
		
		$this->setOptions($aOptions);
		
	}
		
}




?>